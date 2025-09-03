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
        Schema::create('center_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->nullable();
            $table->bool('status');
            $table->integer('timeframe')->default(1);
            $table->bool('via_email')->default(0);
            $table->bool('via_notification')->default(0);
            $table->string('note')->nullable();
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
        Schema::dropIfExists('center_reminders');
    }
};
