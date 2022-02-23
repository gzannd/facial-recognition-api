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
        Schema::create('security_info', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean("is_active");
            $table->unsignedBigInteger("person_id");
            $table->boolean("is_primary");
        });

        Schema::table('security_info', function ($table) {
          $table->foreign('person_id', 'fk_person_id')->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('security_info', function (Blueprint $table) {
          $table->dropForeign(['fk_person_id']);
        });

      Schema::dropIfExists('security_info');
    }
};
