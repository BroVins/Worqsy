<?php
namespace App\Policies;
use App\Models\Project;
use App\Models\User;
use App\Services\PermissionService;

class ProjectPolicy
{
    public function __construct(private PermissionService $permissions) {}
    public function view(User $user, Project $project): bool { return $this->permissions->canViewProject($user,$project); }
    public function update(User $user, Project $project): bool { return $this->permissions->canManageProject($user,$project); }
    public function createTask(User $user, Project $project): bool { return $this->permissions->canCreateTask($user,$project); }
}
