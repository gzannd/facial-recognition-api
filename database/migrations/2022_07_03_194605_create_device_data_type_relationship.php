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
      Schema::table('event_data_type', function (Blueprint $table) {
          $table->unsignedBigInteger('device_id')->change();
          $table->foreign('device_id')->references('id')->on('devices');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

      Schema::table('event_data_type', function(Blueprint $table){
          $table->dropForeign(['devices_id_foreign']);
          $table->dropColumn('device_id');
      });
    }
};
