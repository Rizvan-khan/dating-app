<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeartMatch;
use App\Services\CreditService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(HeartMatch $match)
    {
        return response()->json(['data' => $match->messages()->latest()->paginate(30)]);
    }

    public function store(Request $request, HeartMatch $match, CreditService $credits)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:1200']]);
        $credits->debit($request->user(), CreditService::CHAT_MESSAGE, 'chat_message', ['match_id' => $match->id]);
        $message = $match->messages()->create(['sender_id' => $request->user()->id, 'body' => $data['body']]);

        return response()->json(['message' => $message, 'wallet' => $request->user()->fresh()->credits], 201);
    }
}
