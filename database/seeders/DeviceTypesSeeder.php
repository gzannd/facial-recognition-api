<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('device_types')->insert([
      'type_name' => 'Camera']);
      DB::table('device_types')->insert([
      'type_name' => 'IR Sensor']);
      DB::table('device_types')->insert([
      'type_name' => 'Weight Sensor']);
      DB::table('device_types')->insert([
      'type_name' => 'Lock']);
    }
}
