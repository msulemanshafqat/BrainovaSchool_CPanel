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
        Schema::create('mobile_app_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['slider', 'menu'])->default('slider');
            $table->enum('user_type', ['student', 'teacher', 'guardian'])->default('student');
            $table->string('title');
            $table->string('slug');
            $table->string('icon_path');
            $table->boolean('is_active')->default(true);
            $table->integer('serial')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_app_settings');
    }
};
