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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity_name');
            $table->decimal('dpp', 8, 2)->default(0.00);
            $table->decimal('ppn', 8, 2)->default(0.00);
            $table->decimal('pph', 8, 2)->default(0.00);
            $table->decimal('total', 8, 2)->default(0.00);
            $table->string('director_name');
            $table->timestamps();
            $table->softDeletes()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
