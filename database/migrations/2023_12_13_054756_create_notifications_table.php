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
        Schema::create('notifications', function (Blueprint $table) {
            // $table->id();
            // $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            // $table->string("title")->nullable();
            // $table->text("message")->nullable();
            // $table->text('image')->nullable();
            // $table->timestamps();
            $table->id();
            $table->foreignId('notifiable_id')->nullable();
            $table->string("notifiable_type")->nullable();
            $table->json("payload")->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('notifications');
    }
};
