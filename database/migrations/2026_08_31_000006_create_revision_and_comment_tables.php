<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('task_revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('revision_number');
            $table->foreignUuid('requested_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('overall_deadline')->nullable();
            $table->string('status', 32)->default('OPEN')->index();
            $table->text('request_note')->nullable();
            $table->timestamp('resubmitted_at')->nullable();
            $table->timestamps();
            $table->unique(['task_id','revision_number']);
        });
        Schema::create('revision_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('revision_id')->constrained('task_revisions')->cascadeOnDelete();
            $table->text('description');
            $table->string('priority', 32)->default('MEDIUM');
            $table->string('urgency', 32)->default('NORMAL');
            $table->dateTime('deadline')->nullable();
            $table->string('status', 32)->default('OPEN')->index();
            $table->foreignUuid('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('task_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable()->constrained('task_comments')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
        Schema::create('project_discussions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable()->constrained('project_discussions')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('project_discussions');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('revision_items');
        Schema::dropIfExists('task_revisions');
    }
};
