<?php
namespace App\Models;

use App\Enums\AccountStatus;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, UsesUuid;

    protected $fillable = ['google_subject_id','email','password','name','avatar','status'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['password'=>'hashed','status'=>AccountStatus::class]; }

    public function workspaceMemberships() { return $this->hasMany(WorkspaceMembership::class); }
    public function workspaces() { return $this->belongsToMany(Workspace::class, 'workspace_memberships')->withPivot(['organization_role','company_position','status'])->withTimestamps(); }
    public function projectMemberships() { return $this->hasMany(ProjectMembership::class); }
    public function projects() { return $this->belongsToMany(Project::class, 'project_memberships')->withPivot(['project_role','project_handle','permission_mode','status'])->withTimestamps(); }
    public function assignedTasks() { return $this->belongsToMany(Task::class, 'task_assignees'); }
    public function reviewTasks() { return $this->belongsToMany(Task::class, 'task_reviewers', 'reviewer_id', 'task_id'); }
    public function approvalTasks() { return $this->belongsToMany(Task::class, 'task_approvers', 'approver_id', 'task_id'); }
}
