<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('email')->index();
            $table->string('guest_type')->nullable();
            $table->string('project_role', 32)->nullable();
            $table->string('permission_mode', 32)->default('COMMENT_ONLY');
            $table->string('token_hash', 64)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignUuid('invited_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('project_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('report_type', 80);
            $table->json('payload');
            $table->timestamp('generated_at');
            $table->timestamps();
        });
        Schema::create('project_health_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->string('health_status', 32)->index();
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->unsignedInteger('overdue_tasks')->default(0);
            $table->unsignedInteger('blocked_tasks')->default(0);
            $table->unsignedInteger('review_backlog')->default(0);
            $table->unsignedInteger('revision_backlog')->default(0);
            $table->timestamp('captured_at')->index();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('project_health_snapshots');
        Schema::dropIfExists('project_reports');
        Schema::dropIfExists('invitations');
    }
};
