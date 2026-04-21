<?php

use App\Models\Upload;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\BehaviourRecord\Entities\StudentIncidentAssignAppeal;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appeal_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(StudentIncidentAssignAppeal::class, 'appeal_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->foreignIdFor(Upload::class, 'attachment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'sender_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'receiver_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appeal_conversations');
    }
};
