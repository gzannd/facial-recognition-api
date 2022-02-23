<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Person;

class PeopleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      //Person::truncate();
      DB::table("people")->delete();

      $faker = \Faker\Factory::create();
      DB::table("people")->insert([
        ["first_name" => "Primary", "last_name" => "User"],
        ["first_name" => "Someother", "last_name" => "Rando"],
        ["first_name" => "Mary", "last_name" => "Foo"]
      ]);
    }
}
