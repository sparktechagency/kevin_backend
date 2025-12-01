<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $categories = [
            [
                'name' => 'Adventure Dreams',
                'icon' => 'default/10.png',
                'description' => 'Travel the world, experience new cultures, take on challenges.'
            ],
            [
                'name' => 'Legacy Dreams',
                'icon' => 'default/11.png',
                'description' => 'Make lasting impact, mentor others, leave your mark.'
            ],
            [
                'name' => 'Character Dreams',
                'icon' => 'default/12.png',
                'description' => 'Develop virtues, strengthen integrity, become your best self.'
            ],
            [
                'name' => 'Physical Dreams',
                'icon' => 'default/1.png',
                'description' => 'Run a marathon, achieve optimal health, transform your fitness.'
            ],
            [
                'name' => 'Emotional Dreams',
                'icon' => 'default/2.png',
                'description' => 'Build deeper relationships, find love, strengthen family bonds.'
            ],
            [
                'name' => 'Intellectual Dreams',
                'icon' => 'default/3.png',
                'description' => 'Earn a degree, master new skills, write a book, learn languages.'
            ],
            [
                'name' => 'Spiritual Dreams',
                'icon' => 'default/4.png',
                'description' => 'Find purpose, deepen faith, contribute to something greater.'
            ],
            [
                'name' => 'Professional Dreams',
                'icon' => 'default/5.png',
                'description' => 'Advance your career, start a business, become an industry leader.'
            ],
            [
                'name' => 'Financial Dreams',
                'icon' => 'default/6.png',
                'description' => 'Achieve financial freedom, eliminate debt, build wealth.'
            ],
            [
                'name' => 'Creative Dreams',
                'icon' => 'default/7.png',
                'description' => 'Express yourself artistically, innovate, bring ideas to life.'
            ],
            [
                'name' => 'Material Dreams',
                'icon' => 'default/8.png',
                'description' => 'Own your dream home, create ideal living spaces, acquire meaningful possessions.'
            ],
            [
                'name' => 'Psychological Dreams',
                'icon' => 'default/9.png',
                'description' => 'Overcome fears, build confidence, achieve mental wellness.'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

    }
}
