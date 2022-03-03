<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('images', function ($table) {
        $table->string('device_id');
        $table->dateTime("date_created_by_device");
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('images', function ($table) {
        $table->drop('device_id');
        $table->drop("date_created_by_device");
      });
    }
};
