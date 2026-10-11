<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\HeartMatch;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CallController extends Controller
{
    public function start(Request $request, HeartMatch $match, CreditService $credits)
    {
        $data = $request->validate(['type' => ['required', Rule::in(['audio', 'video'])]]);
        $credits->ensureCanSpend($request->user(), $data['type'] === 'audio' ? 5 : 10);

        $call = Call::create([
            'match_id' => $match->id,
            'caller_id' => $request->user()->id,
            'receiver_id' => $match->user_one_id === $request->user()->id ? $match->user_two_id : $match->user_one_id,
            'type' => $data['type'],
            'started_at' => now(),
            'status' => 'ringing',
        ]);

        return response()->json([
            'call' => $call,
            'rtc' => ['provider' => 'agora', 'channel' => 'match-' . $match->id, 'token' => null],
        ], 201);
    }

    public function end(Request $request, Call $call, CreditService $credits)
    {
        $duration = max(1, (int) ceil($call->started_at->diffInSeconds(now()) / 60));
        $rate = $call->type === 'audio' ? CreditService::AUDIO_PER_MINUTE : CreditService::VIDEO_PER_MINUTE;
        $charge = $duration * $rate;
        $credits->debit($request->user(), $charge, $call->type . '_call', ['call_id' => $call->id, 'minutes' => $duration]);

        $call->update([
            'ended_at' => now(),
            'duration_seconds' => $call->started_at->diffInSeconds(now()),
            'credits_charged' => $charge,
            'status' => 'ended',
        ]);

        return response()->json(['call' => $call, 'wallet' => $request->user()->fresh()->credits]);
    }
}
