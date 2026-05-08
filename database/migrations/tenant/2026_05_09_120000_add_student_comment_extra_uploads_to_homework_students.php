<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homework_students', function (Blueprint $table) {
            if (!Schema::hasColumn('homework_students', 'student_comment')) {
                $table->text('student_comment')->nullable();
            }
            if (!Schema::hasColumn('homework_students', 'extra_upload_ids')) {
                $table->json('extra_upload_ids')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('homework_students', function (Blueprint $table) {
            if (Schema::hasColumn('homework_students', 'student_comment')) {
                $table->dropColumn('student_comment');
            }
            if (Schema::hasColumn('homework_students', 'extra_upload_ids')) {
                $table->dropColumn('extra_upload_ids');
            }
        });
    }
};
