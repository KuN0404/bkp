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
        Schema::create('cash_proof_of_expenditures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools');
            $table->foreignId('activity_id')->constrained('activities');
            $table->unsignedSmallInteger('number_of_students')->default(0); // Kolom yang sudah ada, kita manfaatkan
            $table->decimal('nominal', 15, 2)->default(0.00); // <-- TAMBAHKAN KOLOM INI
            $table->text('sorted');
            $table->timestamps();
            $table->softDeletes(); // ->nullable() tidak diperlukan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_proof_of_expenditures');
    }
};
