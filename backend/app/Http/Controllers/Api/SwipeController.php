<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeartMatch;
use App\Models\Swipe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SwipeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'swiped_id' => ['required', 'exists:users,id'],
            'direction' => ['required', Rule::in(['left', 'right'])],
        ]);

        $swipe = Swipe::updateOrCreate(
            ['swiper_id' => $request->user()->id, 'swiped_id' => $data['swiped_id']],
            ['direction' => $data['direction']]
        );

        $match = null;
        if ($data['direction'] === 'right') {
            $likedBack = Swipe::where('swiper_id', $data['swiped_id'])
                ->where('swiped_id', $request->user()->id)
                ->where('direction', 'right')
                ->exists();

            if ($likedBack) {
                $ids = collect([$request->user()->id, $data['swiped_id']])->sort()->values();
                $match = HeartMatch::firstOrCreate(
                    ['user_one_id' => $ids[0], 'user_two_id' => $ids[1]],
                    ['matched_at' => now()]
                );
            }
        }

        return response()->json(['swipe' => $swipe, 'match' => $match]);
    }
}
