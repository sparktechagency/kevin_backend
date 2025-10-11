<?php

namespace Database\Seeders;

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
        DB::table('subscriptions')->insert([
            [
                'name' => 'Dream Starter',
                'tagline' => 'Begin your journey',
                'price' => 0.00,
                'duration' => 'monthly',
                'has_trial' => false,
                'trial_days' => null,
                'features' => json_encode([
                    '3 active dreams',
                    'Basic progress tracking',
                    'Monthly check-ins',
                    'Mobile app access',
                    'Community forum',
                    'No AI insights',
                    'Limited analytics',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dream Builder',
                'tagline' => 'Committed to success',
                'price' => 9.99,
                'duration' => 'monthly',
                'has_trial' => true,
                'trial_days' => 7,
                'features' => json_encode([
                    'Everything in Starter',
                    'Unlimited dreams',
                    'AI-powered insights',
                    'Weekly check-ins',
                    'Progress analytics',
                    'All 12 dream categories',
                    'Habit tracking',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dream Master',
                'tagline' => 'Accelerate your success',
                'price' => 19.99,
                'duration' => 'monthly',
                'has_trial' => true,
                'trial_days' => 7,
                'features' => json_encode([
                    'Everything in Builder',
                    'Personal AI coach',
                    'Voice journaling',
                    'Advanced analytics',
                    'Accountability partners',
                    'Early feature access',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
