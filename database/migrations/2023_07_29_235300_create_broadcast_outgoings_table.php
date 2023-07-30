<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBroadcastOutgoingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broadcast_outgoings', function (Blueprint $table) {
            // Identifications
            $table->id();
            $table->uuid('broadcast_id')->index();
            $table->unsignedBigInteger('contact_id');

            // Properties - broadcast outgoings
            $table->string('batch', 100)->nullable();

            // Status
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
        Schema::dropIfExists('broadcast_outgoings');
    }
}
