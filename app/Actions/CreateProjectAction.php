<?php
namespace App\Actions;

use App\Enums\PermissionMode;
use App\Enums\ProjectPhaseType;
use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\ProjectMembership;
use App\Models\ProjectPhaseAccess;
use App\Models\ProjectPhaseCycle;
use App\Models\ProjectPosition;
use App\Models\User;
use App\Models\Workspace;
use App\Services\AuditService;
use App\Services\ProjectIdentityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateProjectAction
{
    public function __construct(
        private ProjectIdentityService $identity,
        private AuditService $audit,
    ) {}

    public function execute(User $actor, Workspace $workspace, array $data): Project
    {
        return DB::transaction(function () use ($actor, $workspace, $data) {
            $project = Project::create([
                'workspace_id' => $workspace->id,
                'project_code' => Str::upper($data['project_code']),
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'project_type' => $data['project_type'] ?? 'General Project',
                'visibility' => $data['visibility'] ?? 'PRIVATE',
                'current_phase' => ProjectPhaseType::CREATE,
                'status' => 'ACTIVE',
                'created_by' => $actor->id,
                'start_date' => $data['start_date'] ?? now()->toDateString(),
                'target_completion' => $data['target_completion'] ?? null,
            ]);

            $pm = User::findOrFail($data['project_manager_id']);
            abort_unless(\App\Models\WorkspaceMembership::where('workspace_id',$workspace->id)->where('user_id',$pm->id)->where('status','ACTIVE')->exists(),422,'Project Manager must be an active workspace member.');
            $position = ProjectPosition::create([
                'project_id' => $project->id,
                'name' => 'Project Manager',
                'slug' => 'project-manager',
            ]);

            $membership = ProjectMembership::create([
                'project_id' => $project->id,
                'user_id' => $pm->id,
                'project_role' => ProjectRole::PROJECT_MANAGER,
                'project_position_id' => $position->id,
                'project_handle' => $this->identity->make($pm, $project, 'ProjectManager'),
                'permission_mode' => PermissionMode::STANDARD,
                'status' => 'ACTIVE',
            ]);

            $phase = ProjectPhaseCycle::create([
                'project_id' => $project->id,
                'phase_type' => ProjectPhaseType::CREATE,
                'cycle_number' => 1,
                'title' => 'Initial Create',
                'started_at' => now(),
                'status' => 'ACTIVE',
            ]);

            ProjectPhaseAccess::create([
                'phase_cycle_id' => $phase->id,
                'project_membership_id' => $membership->id,
                'access_status' => 'ACTIVE',
                'activated_at' => now(),
            ]);

            $this->audit->record('project.created', 'project', $project->id, $actor, $workspace, null, $project->toArray());

            return $project->fresh(['currentPhaseCycle','memberships.user']);
        });
    }
}
