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
        Schema::create('special_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type',['P','F'])->default('P')->comment('P = Percentage, F = Fixed');
            $table->float('discount');
            $table->float('min_discount_amount')->nullable();
            $table->float('max_discount_amount')->nullable();
            $table->float('min_eligible_amount')->nullable();
            $table->float('max_eligible_amount')->nullable();
            $table->string('short_description')->nullable();
            $table->boolean('active_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_discounts');
    }
};
