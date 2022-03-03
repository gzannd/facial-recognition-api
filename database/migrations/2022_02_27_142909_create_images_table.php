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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("file_path");
            $table->unsignedBigInteger("parent_id")->nullable()->references("id")->on("images");
            $table->unsignedBigInteger("person_id")->nullable()->references("id")->on("people");
            $table->integer("top")->default(0);
            $table->integer("left")->default(0);
            $table->integer("width")->default(0);
            $table->integer("height")->default(0);
            $table->string("description");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
};
