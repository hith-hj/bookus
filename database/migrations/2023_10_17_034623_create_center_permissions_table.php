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
        Schema::create('center_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_permission_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('center_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('basic')->default(true);
            $table->boolean('low')->default(false);
            $table->boolean('medium')->default(false);
            $table->boolean('high')->default(false);
            $table->boolean('owner')->default(false);
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
        Schema::dropIfExists('center_permissions');
    }
};
