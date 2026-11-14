<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            ['date' => '2026-01-01', 'name' => 'New Year'],
            ['date' => '2026-01-12', 'name' => 'Yennayer'],
            ['date' => '2026-05-01', 'name' => 'Labour Day'],
            ['date' => '2026-07-05', 'name' => 'Independence Day'],
            ['date' => '2026-11-01', 'name' => 'Revolution Day'],
            ['date' => '2027-01-01', 'name' => 'New Year'],
            ['date' => '2027-01-12', 'name' => 'Yennayer'],
            ['date' => '2027-05-01', 'name' => 'Labour Day'],
            ['date' => '2027-07-05', 'name' => 'Independence Day'],
            ['date' => '2027-11-01', 'name' => 'Revolution Day'],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(
                ['date' => $holiday['date']],
                ['name' => $holiday['name']]
            );
        }
    }
}
