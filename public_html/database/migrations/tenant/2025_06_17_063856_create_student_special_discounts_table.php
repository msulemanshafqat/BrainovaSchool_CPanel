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
        Schema::create('student_special_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('special_discount_id');

            $table->foreign('special_discount_id')->references('id')->on('special_discounts')->onDelete('cascade');
            $table->boolean('active_status')->default(1);

            $table->string('short_description')->nullable();

            $table->unsignedBigInteger('assigned_by');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');

            $table->date('assigned_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_special_discounts');
    }
};
