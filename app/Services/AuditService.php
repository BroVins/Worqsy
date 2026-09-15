<?php
namespace App\Services;

use App\Models\AuditEvent;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditService
{
    public function record(
        string $eventType,
        string $resourceType,
        ?string $resourceId,
        ?User $actor = null,
        ?Workspace $workspace = null,
        ?array $before = null,
        ?array $after = null,
        ?string $projectIdentity = null,
        ?Request $request = null,
    ): AuditEvent {
        return AuditEvent::create([
            'workspace_id' => $workspace?->id,
            'actor_user_id' => $actor?->id,
            'actor_project_identity' => $projectIdentity,
            'event_type' => $eventType,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'before_data' => $before,
            'after_data' => $after,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
