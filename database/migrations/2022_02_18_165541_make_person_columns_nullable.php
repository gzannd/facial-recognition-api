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
      Schema::table('people', function ($table) {
          $table->json("middle_name")->nullable()->change();
          $table->string("email")->nullable()->change();
          $table->string("primary_phone")->nullable()->change();
          $table->string("secondary_phone")->nullable()->change();
          $table->string("description")->nullable()->change();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
