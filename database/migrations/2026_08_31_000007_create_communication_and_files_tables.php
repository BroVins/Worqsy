<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('direct_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('task_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('direct_conversation_members', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('conversation_id')->constrained('direct_conversations')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['conversation_id','user_id']);
        });
        Schema::create('direct_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->constrained('direct_conversations')->cascadeOnDelete();
            $table->foreignUuid('sender_id')->constrained('users')->restrictOnDelete();
            $table->text('body');
            $table->timestamps();
        });
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('disk', 40)->default('public');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('visibility', 32)->default('PRIVATE');
            $table->timestamps();
        });
        Schema::create('file_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('file_id')->constrained('files')->cascadeOnDelete();
            $table->string('resource_type', 80)->index();
            $table->uuid('resource_id')->index();
            $table->boolean('can_download')->default(true);
            $table->boolean('guest_visible')->default(false)->index();
            $table->boolean('guest_can_download')->default(false);
            $table->timestamps();
            $table->index(['resource_type','resource_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('file_links');
        Schema::dropIfExists('files');
        Schema::dropIfExists('direct_messages');
        Schema::dropIfExists('direct_conversation_members');
        Schema::dropIfExists('direct_conversations');
    }
};
