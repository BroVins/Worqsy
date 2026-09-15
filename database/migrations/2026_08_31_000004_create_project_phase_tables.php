<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_phase_cycles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->string('phase_type', 32)->index();
            $table->unsignedInteger('cycle_number')->default(1);
            $table->string('title')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 32)->default('ACTIVE')->index();
            $table->timestamps();
            $table->unique(['project_id','phase_type','cycle_number']);
        });
        Schema::create('project_phase_access', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('phase_cycle_id')->constrained('project_phase_cycles')->cascadeOnDelete();
            $table->foreignUuid('project_membership_id')->constrained('project_memberships')->cascadeOnDelete();
            $table->string('access_status', 32)->default('ACTIVE')->index();
            $table->json('permission_override')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();
            $table->unique(['phase_cycle_id','project_membership_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('project_phase_access');
        Schema::dropIfExists('project_phase_cycles');
    }
};
