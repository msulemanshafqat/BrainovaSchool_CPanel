<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make homework_students.homework nullable so quiz submissions (which have
     * no file upload) can be recorded without a placeholder upload ID.
     */
    public function up(): void
    {
        Schema::table('homework_students', function (Blueprint $table) {
            $table->unsignedBigInteger('homework')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('homework_students', function (Blueprint $table) {
            $table->unsignedBigInteger('homework')->nullable(false)->change();
        });
    }
};
