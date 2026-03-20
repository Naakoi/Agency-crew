<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crew_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('crew_title');           // e.g. Captain, Engineer, Cook
            $table->dateTime('check_in');
            $table->dateTime('check_out');
            $table->string('invoice_number')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['in_hotel', 'departed'])->default('in_hotel');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
