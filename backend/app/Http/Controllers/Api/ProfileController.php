<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Report;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function me(Request $request)
    {
        return response()->json(['user' => $request->user()->load('photos')]);
    }

    public function updateSafety(Request $request)
    {
        $data = $request->validate([
            'hide_location' => ['sometimes', 'boolean'],
            'incognito' => ['sometimes', 'boolean'],
        ]);

        abort_if($request->user()->gender !== 'female' && array_key_exists('incognito', $data), 403);
        $request->user()->update($data);

        return response()->json(['user' => $request->user()->fresh()]);
    }

    public function report(Request $request)
    {
        $data = $request->validate([
            'reported_id' => ['required', 'exists:users,id'],
            'reason' => ['required', 'string', 'max:80'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        $report = Report::create(['reporter_id' => $request->user()->id] + $data + ['status' => 'open']);
        return response()->json(['report' => $report], 201);
    }

    public function block(Request $request)
    {
        $data = $request->validate(['blocked_id' => ['required', 'exists:users,id']]);
        $block = Block::firstOrCreate(['blocker_id' => $request->user()->id, 'blocked_id' => $data['blocked_id']]);
        return response()->json(['block' => $block], 201);
    }
}
