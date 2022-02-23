<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Configuration;

class ConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Configuration::truncate();

        $faker = \Faker\Factory::create();
        Configuration::create([
          "system_name" => "Test System Name",
          "system_description" => "This is a test configuration.",
          "primary_user_id" => 1
        ]);
    }
}
