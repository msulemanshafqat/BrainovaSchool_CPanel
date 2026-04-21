<?php

use App\Models\StudentInfo\Student;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\BehaviourRecord\Entities\Incident;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_incident_assigns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Incident::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Student::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor( User::class, 'request_by')->constrained()->cascadeOnDelete();
            $table->foreignIdFor( User::class, 'approved_by')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('status', ['initiate', 'approved', 'rejected', 'withdraw'])->default('initiate');
            $table->string('short_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_incident_assigns');
    }
};
