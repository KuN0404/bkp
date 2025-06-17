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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subdistrict_id')->constrained('subdistricts');
            $table->enum('school_type', ['SD', 'SMP', 'SMA']);
            $table->enum('school_status', ['Negeri', 'Swasta']);
            $table->string('school_name');
            $table->string('principal_name')->nullable();
            $table->string('principal_nip')->nullable();
            $table->string('treasurer_name')->nullable();
            $table->string('treasurer_nip')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
