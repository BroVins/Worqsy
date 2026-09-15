<?php
namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use UsesUuid;
    protected $fillable=['project_id','phase_cycle_id','task_code','title','description','status','priority','weight','guest_visible','estimate_minutes','start_date','due_date','created_by','approved_at'];
    protected function casts(): array { return ['status'=>TaskStatus::class,'priority'=>TaskPriority::class,'weight'=>'integer','guest_visible'=>'bool','estimate_minutes'=>'integer','start_date'=>'date','due_date'=>'datetime','approved_at'=>'datetime']; }
    public function project(){ return $this->belongsTo(Project::class); }
    public function phaseCycle(){ return $this->belongsTo(ProjectPhaseCycle::class,'phase_cycle_id'); }
    public function creator(){ return $this->belongsTo(User::class,'created_by'); }
    public function assignees(){ return $this->belongsToMany(User::class,'task_assignees')->withTimestamps(); }
    public function reviewers(){ return $this->belongsToMany(User::class,'task_reviewers','task_id','reviewer_id')->withTimestamps(); }
    public function approvers(){ return $this->belongsToMany(User::class,'task_approvers','task_id','approver_id')->withTimestamps(); }
    public function subtasks(){ return $this->hasMany(Subtask::class); }
    public function submissions(){ return $this->hasMany(TaskSubmission::class); }
    public function revisions(){ return $this->hasMany(TaskRevision::class); }
    public function comments(){ return $this->hasMany(TaskComment::class); }
}
