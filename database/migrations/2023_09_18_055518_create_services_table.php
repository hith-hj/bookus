<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('Treatment_type')->nullable();
            $table->foreignId("category_id")->nullable()->constrained()->onDelete('set null');
            $table->text('description')->nullable();
            $table->text('Aftercare_description')->nullable();
            $table->enum('service_gender',['Everyone','Females','Males'])->default('Everyone');
            $table->boolean('online_booking')->default(1);
            //Duration
            $table->string('Duration')->default('45min');
            $table->enum('price_type',['Fixed','From','Free'])->default('Fixed');
            $table->decimal('retail_price', 10, 2)->nullable()->default(0);
            $table->boolean('extra_time')->default(0);//enabl extra time after duration
            $table->boolean('status')->default(1);//enabl extra time after duration
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
