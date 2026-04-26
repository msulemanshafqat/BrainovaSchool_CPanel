<?php

namespace Modules\MainApp\Database\Seeders;

use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //USA, INDIA,Saudi Arabia
        $countries = [224,94,183];

        $usa_cities = [
            'New York', 'San Francisco', 'Los Angeles', 'Chicago', 'Houston',
        ];

        $india_cities = [
            'Mumbai', 'Delhi', 'Bangalore', 'Kolkata', 'Chennai',
        ];

        $saudi_cities = [
            'Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Tabuk',
        ];

        foreach ($countries as $country_id) {
            if ($country_id == 224) {
                foreach ($usa_cities as $city) {
                    \Modules\MainApp\Entities\City::create([
                        'name' => $city,
                        'country_id' => $country_id,
                    ]);
                }
            } elseif ($country_id == 94) {
                foreach ($india_cities as $city) {
                    \Modules\MainApp\Entities\City::create([
                        'name' => $city,
                        'country_id' => $country_id,
                    ]);
                }
            } elseif ($country_id == 183) {
                foreach ($saudi_cities as $city) {
                    \Modules\MainApp\Entities\City::create([
                        'name' => $city,
                        'country_id' => $country_id,
                    ]);
                }
            }
        }
    }
}
