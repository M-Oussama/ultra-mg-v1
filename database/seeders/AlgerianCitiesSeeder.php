<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlgerianCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = [
            ['code' => 1, 'name' => 'Adrar'],
            ['code' => 2, 'name' => 'Chlef'],
            ['code' => 3, 'name' => 'Laghouat'],
            ['code' => 4, 'name' => 'Ouargla'],
            ['code' => 5, 'name' => 'Batna'],
            ['code' => 6, 'name' => 'Bejaia'],
            ['code' => 7, 'name' => 'Biskra'],
            ['code' => 8, 'name' => 'Bechar'],
            ['code' => 9, 'name' => 'Blida'],
            ['code' => 10, 'name' => 'Bouira'],
            ['code' => 11, 'name' => 'Tamanrasset'],
            ['code' => 12, 'name' => 'Tébessa'],
            ['code' => 13, 'name' => 'Tlemcen'],
            ['code' => 14, 'name' => 'Tiaret'],
            ['code' => 15, 'name' => 'Tizi Ouzou'],
            ['code' => 16, 'name' => 'Alger'],
            ['code' => 17, 'name' => 'Djelfa'],
            ['code' => 18, 'name' => 'Jijel'],
            ['code' => 19, 'name' => 'Setif'],
            ['code' => 20, 'name' => 'Saida'],
            ['code' => 21, 'name' => 'Skikda'],
            ['code' => 22, 'name' => 'Sidi Bel Abbes'],
            ['code' => 23, 'name' => 'Annaba'],
            ['code' => 24, 'name' => 'Guelma'],
            ['code' => 25, 'name' => 'Constantine'],
            ['code' => 26, 'name' => 'Médéa'],
            ['code' => 27, 'name' => 'Mostaganem'],
            ['code' => 28, 'name' => 'MSila'],
            ['code' => 29, 'name' => 'Mascara'],
            ['code' => 30, 'name' => 'Ouargla'],
            ['code' => 31, 'name' => 'Oran'],
            ['code' => 32, 'name' => 'El Bayadh'],
            ['code' => 33, 'name' => 'Illizi'],
            ['code' => 34, 'name' => 'Bordj Bou Arreridj'],
            ['code' => 35, 'name' => 'Boumerdes'],
            ['code' => 36, 'name' => 'El Tarf'],
            ['code' => 37, 'name' => 'Tindouf'],
            ['code' => 38, 'name' => 'Tissemsilt'],
            ['code' => 39, 'name' => 'El Oued'],
            ['code' => 40, 'name' => 'Khenchela'],
            ['code' => 41, 'name' => 'Souk Ahras'],
            ['code' => 42, 'name' => 'Tipaza'],
            ['code' => 43, 'name' => 'Mila'],
            ['code' => 44, 'name' => 'Ain Defla'],
            ['code' => 45, 'name' => 'Naama'],
            ['code' => 46, 'name' => 'Ain Temouchent'],
            ['code' => 47, 'name' => 'Ghardaia'],
            ['code' => 48, 'name' => 'Relizane'],
            ['code' => 49, 'name' => 'Tebessa'],
            ['code' => 50, 'name' => 'Ain Smara'],
            ['code' => 51, 'name' => 'El MGhair'],
            ['code' => 52, 'name' => 'Ouled Djellal'],
            ['code' => 53, 'name' => 'Hassi Messaoud'],
            ['code' => 54, 'name' => 'Tebesbest'],
            ['code' => 55, 'name' => 'Megarine'],
            ['code' => 56, 'name' => 'Hassi RMel'],
            ['code' => 57, 'name' => 'Oued Irara'],
            ['code' => 58, 'name' => 'In Amguel'],
        ];

        foreach ($cities as $city) {
            City::create([
                'code' => $city['code'],
                'name' => $city['name'],
                'country' => 'Algeria',
                // You can add more columns as needed
            ]);
        }

    }
}
