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
      Schema::create('device_types', function(Blueprint $table){
          $table->id();
          $table->string("type_name");
      });

      Schema::create('devices', function (Blueprint $table) {
          $table->id();
          $table->string('name')->unique()->notnull();
          $table->string('systemId')->unique()->notnull();
          $table->string('description');
          $table->integer('type')->notnull()->references('id')->on('device_types');
          $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
        Schema::dropIfExists('device_types');
    }
};
