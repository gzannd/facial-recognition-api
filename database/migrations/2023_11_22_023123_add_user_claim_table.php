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
        Schema::create('user_claim', function (Blueprint $table) {
            $table->unsignedInteger("userId");
            $table->string("claim");
            $table->date("valid_begin")->nullable();
            $table->date("valid_end")->nullable();
            $table->primary(array("userId", "claim"));
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_claim');
    }
};
