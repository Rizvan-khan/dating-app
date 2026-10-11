<?php

namespace Database\Seeders;

use App\Models\CreditPackage;
use Illuminate\Database\Seeder;

class CreditPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['name' => 'One Day Pass', 'price_paise' => 3900, 'credits' => 50, 'bonus_credits' => 0, 'valid_days' => 1],
            ['name' => 'Starter', 'price_paise' => 9900, 'credits' => 150, 'bonus_credits' => 0, 'valid_days' => 7],
            ['name' => 'Popular', 'price_paise' => 29900, 'credits' => 500, 'bonus_credits' => 100, 'valid_days' => 30, 'badge' => 'MOST POPULAR'],
            ['name' => 'Premium', 'price_paise' => 39900, 'credits' => 800, 'bonus_credits' => 300, 'valid_days' => 45, 'features' => ['verified_badge', 'see_who_liked_you']],
        ];

        foreach ($packages as $package) {
            CreditPackage::updateOrCreate(['name' => $package['name']], $package + ['is_active' => true]);
        }
    }
}
