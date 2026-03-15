<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            ['name' => 'blow molding'],
            ['name' => 'glass jar sell'],
            ['name' => 'raw material'],
        ];

        foreach ($departments as $department) {
            \App\Models\Department::firstOrCreate($department);
        }
    }
}
