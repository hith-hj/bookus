<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('centers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->boolean("status")->default(0);
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('currency')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->unique();

            $table->text('about')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE centers ADD FULLTEXT idx_full_name_phone_number_email (name, phone_number, email)');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centers');
    }
};
