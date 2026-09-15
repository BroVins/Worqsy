<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('phase_cycle_id')->nullable()->constrained('project_phase_cycles')->nullOnDelete();
            $table->string('task_code', 50);
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('status', 32)->default('ASSIGNED')->index();
            $table->string('priority', 32)->default('MEDIUM')->index();
            $table->unsignedTinyInteger('weight')->default(1);
            $table->boolean('guest_visible')->default(false)->index();
            $table->unsignedInteger('estimate_minutes')->nullable();
            $table->date('start_date')->nullable();
            $table->dateTime('due_date')->nullable()->index();
            $table->foreignUuid('created_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['project_id','task_code']);
        });
        Schema::create('subtasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['task_id','user_id']);
        });
        Schema::create('task_reviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('reviewer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['task_id','reviewer_id']);
        });
        Schema::create('task_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('approver_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['task_id','approver_id']);
        });
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('submitted_by')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('submission_number')->default(1);
            $table->text('work_summary');
            $table->json('deliverables')->nullable();
            $table->json('links')->nullable();
            $table->text('completion_notes')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('task_submissions');
        Schema::dropIfExists('task_approvers');
        Schema::dropIfExists('task_reviewers');
        Schema::dropIfExists('task_assignees');
        Schema::dropIfExists('subtasks');
        Schema::dropIfExists('tasks');
    }
};
