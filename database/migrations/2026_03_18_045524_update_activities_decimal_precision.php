<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix #3: Meningkatkan presisi decimal dari (8,2) ke (15,2)
     * agar mendukung nominal keuangan Indonesia yang besar.
     * decimal(8,2) hanya mendukung maks 999.999,99
     * decimal(15,2) mendukung hingga 9.999.999.999.999,99
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->decimal('dpp', 15, 2)->default(0.00)->change();
            $table->decimal('ppn', 15, 2)->default(0.00)->change();
            $table->decimal('pph', 15, 2)->default(0.00)->change();
            $table->decimal('total', 15, 2)->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->decimal('dpp', 8, 2)->default(0.00)->change();
            $table->decimal('ppn', 8, 2)->default(0.00)->change();
            $table->decimal('pph', 8, 2)->default(0.00)->change();
            $table->decimal('total', 8, 2)->default(0.00)->change();
        });
    }
};
