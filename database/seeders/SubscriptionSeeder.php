<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
         $plans = [
            [
                'name' => 'Starter',
                'price' => 0.00,
                'interval' => 'month',
                'features' => [
                    '3 active dreams',
                    'Basic progress tracking',
                    'Monthly check-ins',
                    'Mobile app access',
                    'Community forum',
                    // 'No AI insights',
                    // 'Limited analytics'
                ]
            ],
            [
                'name' => 'Builder',
                'price' => 9.99,
                'interval' => 'month',
                'features' => [
                    'Everything in Starter',
                    'Unlimited dreams',
                    'AI-powered insights',
                    'Weekly check-ins',
                    'Progress analytics',
                    'All 12 dream categories',
                    'Habit tracking'
                ]
            ],
            [
                'name' => 'Master',
                'price' => 19.99,
                'interval' => 'month',
                'features' => [
                    'Everything in Builder',
                    'Personal AI coach',
                    'Voice journaling',
                    'Advanced analytics',
                    'Accountability partners',
                    'Early feature access'
                ]
            ]
        ];

        foreach ($plans as $plan) {
            // In a real app, you'd create these in Stripe first and get the IDs
            Plan::create([
                'name' => $plan['name'],
                'price' => $plan['price'],
                'interval' => $plan['interval'],
                'features' => $plan['features'],
                'stripe_price_id' => 'price_' . strtolower($plan['name']), // Replace with actual Stripe price ID
                'stripe_product_id' => 'prod_' . strtolower($plan['name']) // Replace with actual Stripe product ID
            ]);
        }
    }
}
