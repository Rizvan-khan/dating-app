<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CreditService
{
    public const CHAT_MESSAGE = 2;
    public const AUDIO_PER_MINUTE = 5;
    public const VIDEO_PER_MINUTE = 10;

    public function ensureCanSpend(User $user, int $credits): void
    {
        if ($user->gender === 'female') {
            return;
        }

        if ($user->credits < $credits) {
            throw ValidationException::withMessages([
                'credits' => 'Recharge to continue',
            ]);
        }
    }

    public function debit(User $user, int $credits, string $reason, array $meta = []): void
    {
        if ($user->gender === 'female') {
            return;
        }

        $this->ensureCanSpend($user, $credits);
        $user->decrement('credits', $credits);

        Transaction::create([
            'user_id' => $user->id,
            'amount_paise' => 0,
            'credits' => -$credits,
            'type' => $reason,
            'status' => 'paid',
            'meta' => $meta,
        ]);
    }

    public function credit(User $user, int $credits, string $reason, array $meta = []): void
    {
        $user->increment('credits', $credits);
        Transaction::create([
            'user_id' => $user->id,
            'amount_paise' => $meta['amount_paise'] ?? 0,
            'credits' => $credits,
            'type' => $reason,
            'status' => $meta['status'] ?? 'paid',
            'meta' => $meta,
        ]);
    }
}
