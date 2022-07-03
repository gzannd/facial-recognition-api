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
        Schema::create('event_data', function (Blueprint $table) {
            $table->string("id")->default(DB::raw('(UUID())'));
            $table->string("device_id")->nullable(); //ID of device that generated the event (if any)
            $table->string("event_type")->nullable(false); //The event message type. This is required.
            $table->dateTime("device_date")->nullable(); //The date the event was recorded by the device (if any)
            $table->string("data")->nullable();  //Any additional data associated with the event.
            $table->timestamps();
            $table->primary("id");
            $table->index("event_type", "device_id");
            $table->index("device_date");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_data');
    }
};
