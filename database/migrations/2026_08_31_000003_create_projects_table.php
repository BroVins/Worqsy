<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('project_code', 30);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('project_type')->nullable();
            $table->string('visibility', 32)->default('PRIVATE');
            $table->string('current_phase', 32)->default('CREATE')->index();
            $table->string('status', 32)->default('ACTIVE')->index();
            $table->foreignUuid('created_by')->constrained('users')->restrictOnDelete();
            $table->date('start_date')->nullable();
            $table->date('target_completion')->nullable();
            $table->timestamps();
            $table->unique(['workspace_id','project_code']);
        });
        Schema::create('project_positions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->unique(['project_id','slug']);
        });
        Schema::create('project_memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('project_role', 32)->index();
            $table->foreignUuid('project_position_id')->nullable()->constrained('project_positions')->nullOnDelete();
            $table->string('project_handle')->nullable();
            $table->string('permission_mode', 32)->default('STANDARD')->index();
            $table->json('permission_overrides')->nullable();
            $table->string('status', 32)->default('ACTIVE')->index();
            $table->timestamps();
            $table->unique(['project_id','user_id']);
            $table->unique(['project_id','project_handle']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('project_memberships');
        Schema::dropIfExists('project_positions');
        Schema::dropIfExists('projects');
    }
};
