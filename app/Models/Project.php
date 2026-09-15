<?php
namespace App\Models;

use App\Enums\ProjectPhaseType;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use UsesUuid;
    protected $fillable=['workspace_id','project_code','name','description','project_type','visibility','current_phase','status','created_by','start_date','target_completion'];
    protected function casts(): array { return ['current_phase'=>ProjectPhaseType::class,'start_date'=>'date','target_completion'=>'date']; }
    public function workspace(){ return $this->belongsTo(Workspace::class); }
    public function creator(){ return $this->belongsTo(User::class,'created_by'); }
    public function memberships(){ return $this->hasMany(ProjectMembership::class); }
    public function users(){ return $this->belongsToMany(User::class,'project_memberships')->withPivot(['project_role','project_handle','permission_mode','status'])->withTimestamps(); }
    public function positions(){ return $this->hasMany(ProjectPosition::class); }
    public function phaseCycles(){ return $this->hasMany(ProjectPhaseCycle::class); }
    public function currentPhaseCycle(){ return $this->hasOne(ProjectPhaseCycle::class)->where('status','ACTIVE')->orderByDesc('started_at'); }
    public function tasks(){ return $this->hasMany(Task::class); }
    public function discussions(){ return $this->hasMany(ProjectDiscussion::class); }
}
