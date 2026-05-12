<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stores each student's selected option per quiz question (tenant DB).
     * Used by quiz analytics; rows are written on interactive quiz submit.
     */
    public function up(): void
    {
        if (Schema::hasTable('homework_quiz_answers')) {
            return;
        }

        Schema::create('homework_quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('homework_id')->index();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('question_id')->index();
            $table->string('selected_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->unique(['homework_id', 'student_id', 'question_id'], 'hw_quiz_answers_hw_student_q_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_quiz_answers');
    }
};
