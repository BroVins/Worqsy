<?php
namespace App\Services;

use App\Enums\OrganizationRole;
use App\Enums\PermissionMode;
use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\ProjectMembership;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMembership;

class PermissionService
{
    public function workspaceMembership(User $user, Workspace $workspace): ?WorkspaceMembership
    {
        return WorkspaceMembership::query()
            ->where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with('adminPermission')
            ->first();
    }

    public function projectMembership(User $user, Project $project): ?ProjectMembership
    {
        return ProjectMembership::query()
            ->where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with(['phaseAccess.phaseCycle','position'])
            ->first();
    }


    public function accessibleProjects(User $user, Workspace $workspace)
    {
        $workspaceMembership = $this->workspaceMembership($user,$workspace);
        if (! $workspaceMembership) return collect();

        if (in_array($workspaceMembership->organization_role,[OrganizationRole::OWNER,OrganizationRole::ADMIN],true)) {
            return Project::where('workspace_id',$workspace->id)->get();
        }

        return Project::query()
            ->where('workspace_id',$workspace->id)
            ->whereHas('memberships', function ($q) use ($user) {
                $q->where('user_id',$user->id)->where('status','ACTIVE');
            })
            ->where(function ($q) use ($user) {
                $q->where('status','CLOSED')
                  ->orWhereHas('memberships', function ($m) use ($user) {
                      $m->where('user_id',$user->id)
                        ->where('status','ACTIVE')
                        ->whereHas('phaseAccess', function ($a) {
                            $a->where('access_status','ACTIVE')
                              ->whereHas('phaseCycle',fn($c)=>$c->where('status','ACTIVE'));
                        });
                  });
            })
            ->get();
    }

    public function canViewProject(User $user, Project $project): bool
    {
        if (session()->has('workspace_id') && session('workspace_id') !== $project->workspace_id) return false;
        $workspaceMembership = $this->workspaceMembership($user, $project->workspace);

        if (! $workspaceMembership) return false;

        if (in_array($workspaceMembership->organization_role, [OrganizationRole::OWNER, OrganizationRole::ADMIN], true)) {
            return true;
        }

        $projectMembership = $this->projectMembership($user, $project);
        if (! $projectMembership) return false;

        $currentCycle = $project->currentPhaseCycle;
        if (! $currentCycle) return true;

        return $projectMembership->phaseAccess
            ->where('phase_cycle_id', $currentCycle->id)
            ->where('access_status', 'ACTIVE')
            ->isNotEmpty();
    }

    public function canManageProject(User $user, Project $project): bool
    {
        if (session()->has('workspace_id') && session('workspace_id') !== $project->workspace_id) return false;
        $wm = $this->workspaceMembership($user, $project->workspace);
        if (! $wm) return false;

        if ($wm->organization_role === OrganizationRole::ADMIN) {
            return (bool) ($wm->adminPermission?->create_project || $wm->adminPermission?->manage_members || $wm->adminPermission?->manage_lifecycle);
        }

        $pm = $this->projectMembership($user, $project);
        return $pm?->project_role === ProjectRole::PROJECT_MANAGER
            && $pm?->permission_mode === PermissionMode::STANDARD;
    }

    public function canCreateTask(User $user, Project $project): bool
    {
        if (session()->has('workspace_id') && session('workspace_id') !== $project->workspace_id) return false;
        $wm = $this->workspaceMembership($user, $project->workspace);
        if ($wm?->organization_role === OrganizationRole::ADMIN) return true;

        $pm = $this->projectMembership($user, $project);
        return $pm && in_array($pm->project_role, [ProjectRole::PROJECT_MANAGER, ProjectRole::LEAD], true)
            && $pm->permission_mode === PermissionMode::STANDARD;
    }


    public function isProjectGuest(User $user, Project $project): bool
    {
        return $this->projectMembership($user,$project)?->project_role === ProjectRole::GUEST;
    }

    public function canViewTask(User $user, Task $task): bool
    {
        if (! $this->canViewProject($user,$task->project)) return false;
        if ($this->isProjectGuest($user,$task->project)) return (bool) $task->guest_visible;
        return true;
    }

    public function canWorkTask(User $user, Task $task): bool
    {
        if (! $this->canViewTask($user, $task)) return false;

        $membership = $this->projectMembership($user, $task->project);
        if ($membership && $membership->permission_mode !== PermissionMode::STANDARD) return false;

        return $task->assignees()->where('users.id', $user->id)->exists();
    }

    public function canReviewTask(User $user, Task $task): bool
    {
        if (! $this->canViewProject($user, $task->project)) return false;

        $membership = $this->projectMembership($user, $task->project);
        if ($membership && $membership->permission_mode !== PermissionMode::STANDARD) return false;

        return $task->reviewers()->where('users.id', $user->id)->exists()
            || $task->approvers()->where('users.id', $user->id)->exists();
    }

    public function canComment(User $user, Project $project): bool
    {
        if (! $this->canViewProject($user, $project)) return false;
        $wm = $this->workspaceMembership($user, $project->workspace);
        if ($wm?->organization_role === OrganizationRole::OWNER) return false;
        if ($wm?->organization_role === OrganizationRole::ADMIN) return true;
        $membership = $this->projectMembership($user, $project);
        if (! $membership) return false;
        return in_array($membership->permission_mode, [PermissionMode::STANDARD, PermissionMode::COMMENT_ONLY], true);
    }


    public function canUploadProjectFile(User $user, Project $project): bool
    {
        $wm = $this->workspaceMembership($user, $project->workspace);
        if ($wm?->organization_role === OrganizationRole::ADMIN) return true;
        if ($wm?->organization_role === OrganizationRole::OWNER) return false;
        $membership = $this->projectMembership($user, $project);
        return $membership
            && $membership->project_role !== ProjectRole::GUEST
            && $membership->permission_mode === PermissionMode::STANDARD;
    }

    public function canDownloadProjectFile(User $user, Project $project): bool
    {
        if (! $this->canViewProject($user, $project)) return false;
        return true;
    }

    public function hasAdminPermission(User $user, Workspace $workspace, string $permission): bool
    {
        $membership = $this->workspaceMembership($user, $workspace);
        if (! $membership) return false;
        if ($membership->organization_role === OrganizationRole::OWNER) return false;
        if ($membership->organization_role !== OrganizationRole::ADMIN) return false;
        return (bool) data_get($membership->adminPermission, $permission, false);
    }
}
