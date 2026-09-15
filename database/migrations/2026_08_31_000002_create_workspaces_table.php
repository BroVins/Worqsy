<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status', 32)->default('ACTIVE')->index();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('workspace_memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('organization_role', 32)->default('MEMBER')->index();
            $table->string('company_position')->nullable();
            $table->string('scope')->nullable();
            $table->string('status', 32)->default('ACTIVE')->index();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['workspace_id','user_id']);
        });
        Schema::create('workspace_admin_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_membership_id')->unique()->constrained('workspace_memberships')->cascadeOnDelete();
            foreach (['create_project','manage_members','manage_admins','manage_guests','manage_project_managers','manage_permissions','manage_lifecycle','view_audit','manage_billing','manage_organization_settings'] as $field) {
                $table->boolean($field)->default(false);
            }
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('workspace_admin_permissions');
        Schema::dropIfExists('workspace_memberships');
        Schema::dropIfExists('workspaces');
    }
};
