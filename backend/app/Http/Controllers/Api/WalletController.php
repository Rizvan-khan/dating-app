<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CreditPackage;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

class WalletController extends Controller
{
    public function packages()
    {
        return response()->json(['data' => CreditPackage::where('is_active', true)->get()]);
    }

    public function history(Request $request)
    {
        return response()->json([
            'credits' => $request->user()->credits,
            'history' => $request->user()->transactions()->latest()->paginate(25),
        ]);
    }

    public function createOrder(Request $request)
    {
        abort_if($request->user()->gender === 'female', 403, 'Female profiles never need payments.');
        $data = $request->validate(['package_id' => ['required', 'exists:credit_packages,id']]);
        $package = CreditPackage::findOrFail($data['package_id']);

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $order = $api->order->create([
            'receipt' => 'bh_' . $request->user()->id . '_' . now()->timestamp,
            'amount' => $package->price_paise,
            'currency' => 'INR',
        ]);

        Transaction::create([
            'user_id' => $request->user()->id,
            'credit_package_id' => $package->id,
            'razorpay_order_id' => $order['id'],
            'amount_paise' => $package->price_paise,
            'credits' => $package->credits + $package->bonus_credits,
            'type' => 'recharge',
            'status' => 'created',
        ]);

        return response()->json(['order' => $order, 'package' => $package]);
    }

    public function verifyPayment(Request $request, CreditService $credits)
    {
        $data = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $api->utility->verifyPaymentSignature($data);

        return DB::transaction(function () use ($request, $data, $credits) {
            $tx = Transaction::where('razorpay_order_id', $data['razorpay_order_id'])
                ->where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($tx->status !== 'paid') {
                $tx->update(['status' => 'paid', 'razorpay_payment_id' => $data['razorpay_payment_id']]);
                $credits->credit($request->user(), $tx->credits, 'recharge', ['amount_paise' => $tx->amount_paise]);
                $package = CreditPackage::find($tx->credit_package_id);
                Subscription::create([
                    'user_id' => $request->user()->id,
                    'credit_package_id' => $package->id,
                    'starts_at' => now(),
                    'expires_at' => now()->addDays($package->valid_days),
                    'features' => $package->features ?? [],
                ]);
            }

            return response()->json(['message' => 'Wallet credited', 'credits' => $request->user()->fresh()->credits]);
        });
    }
}
