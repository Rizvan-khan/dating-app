<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OnboardRequest;
use App\Models\User;
use App\Services\CreditService;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $data = $request->validate(['phone' => ['required', 'regex:/^[6-9][0-9]{9}$/']]);
        $otp = app()->isProduction() ? random_int(100000, 999999) : 123456;
        Cache::put('otp:' . $data['phone'], (string) $otp, now()->addMinutes(5));

        return response()->json(['message' => 'OTP sent', 'dev_otp' => app()->isProduction() ? null : $otp]);
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'otp' => ['required', 'digits:6'],
        ]);

        if (Cache::get('otp:' . $data['phone']) !== $data['otp']) {
            throw ValidationException::withMessages(['otp' => 'Invalid or expired OTP']);
        }

        $user = User::firstOrCreate(
            ['phone' => $data['phone']],
            ['role' => 'user', 'credits' => 0, 'phone_verified_at' => now()]
        );

        $user->forceFill(['phone_verified_at' => now()])->save();
        Cache::forget('otp:' . $data['phone']);

        return response()->json(['token' => $user->createToken('mobile')->plainTextToken, 'user' => $user]);
    }

    public function onboard(OnboardRequest $request, LocationService $location, CreditService $credits)
    {
        $user = $request->user();
        if ($user->gender) {
            throw ValidationException::withMessages(['gender' => 'Gender cannot be changed after signup']);
        }

        $data = $request->validated();
        if (! $location->isInsideBareilly($data['lat'], $data['long'])) {
            throw ValidationException::withMessages(['location' => 'Bareilly Hearts is only available within 40km of Bareilly']);
        }

        $user->fill(collect($data)->except('photos')->all());
        $user->credits = $data['gender'] === 'male' ? 10 : 0;
        $user->save();

        foreach ($data['photos'] as $index => $path) {
            $user->photos()->create([
                'path' => $path,
                'is_primary' => $index === 0,
                'status' => 'pending',
                'sort_order' => $index,
            ]);
        }

        if ($user->gender === 'male') {
            $credits->credit($user, 10, 'welcome_bonus');
        }

        return response()->json(['user' => $user->load('photos')], 201);
    }
}
