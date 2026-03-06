<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'title' => 'Introduction to Laravel',
                'speaker' => 'Taylor Otwell',
                'location' => 'Room A1',
                'total_seats' => 50,
            ],
            [
                'title' => 'Advanced Livewire Patterns',
                'speaker' => 'Caleb Porzio',
                'location' => 'Room B2',
                'total_seats' => 30,
            ],
            [
                'title' => 'Mastering Vue.js',
                'speaker' => 'Evan You',
                'location' => 'Room C3',
                'total_seats' => 40,
            ],
            [
                'title' => 'Docker for Developers',
                'speaker' => 'John Doe',
                'location' => 'Online',
                'total_seats' => 100,
            ],
            [
                'title' => 'Modern CSS with Tailwind',
                'speaker' => 'Adam Wathan',
                'location' => 'Room D4',
                'total_seats' => 25,
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}
