<?php
namespace App\Policies;
use App\Models\Task;
use App\Models\User;
use App\Services\PermissionService;

class TaskPolicy
{
    public function __construct(private PermissionService $permissions) {}
    public function view(User $user, Task $task): bool { return $this->permissions->canViewTask($user,$task); }
    public function work(User $user, Task $task): bool { return $this->permissions->canWorkTask($user,$task); }
    public function review(User $user, Task $task): bool { return $this->permissions->canReviewTask($user,$task); }
}
