<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\User;
use App\Services\LocationService;
use Illuminate\Http\Request;

class DiscoveryController extends Controller
{
    public function index(Request $request, LocationService $location)
    {
        $viewer = $request->user();
        $filters = $request->validate([
            'min_age' => ['nullable', 'integer', 'min:18', 'max:80'],
            'max_age' => ['nullable', 'integer', 'min:18', 'max:80'],
            'area' => ['nullable', 'string'],
            'verified' => ['nullable', 'boolean'],
        ]);

        $blockedIds = Block::where('blocker_id', $viewer->id)->orWhere('blocked_id', $viewer->id)
            ->get()->flatMap(fn ($b) => [$b->blocker_id, $b->blocked_id])->unique();

        $profiles = User::query()
            ->with(['photos' => fn ($q) => $q->where('status', 'approved')])
            ->where('id', '!=', $viewer->id)
            ->where('gender', $viewer->gender === 'male' ? 'female' : 'male')
            ->where('is_banned', false)
            ->whereNotIn('id', $blockedIds)
            ->whereDoesntHave('photos', fn ($q) => $q->where('status', 'rejected'))
            ->when($filters['area'] ?? null, fn ($q, $area) => $q->where('area', $area))
            ->when($filters['verified'] ?? null, fn ($q) => $q->where('is_verified', true))
            ->latest('online_at')
            ->limit(30)
            ->get()
            ->filter(fn ($profile) => $location->isInsideBareilly($profile->lat, $profile->long))
            ->values()
            ->map(function ($profile) use ($viewer, $location) {
                $profile->distance_km = $profile->hide_location ? null : $location->distanceFromBareilly($profile->lat, $profile->long);
                $profile->is_online = $profile->online_at?->gt(now()->subMinutes(5)) ?? false;
                return $profile;
            });

        return response()->json(['data' => $profiles]);
    }
}
