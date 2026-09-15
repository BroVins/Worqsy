<?php
namespace App\Models;
use App\Enums\ProjectPhaseType;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class ProjectPhaseCycle extends Model
{
    use UsesUuid;
    protected $fillable=['project_id','phase_type','cycle_number','title','started_at','completed_at','status'];
    protected function casts(): array { return ['phase_type'=>ProjectPhaseType::class,'started_at'=>'datetime','completed_at'=>'datetime']; }
    public function project(){ return $this->belongsTo(Project::class); }
    public function accesses(){ return $this->hasMany(ProjectPhaseAccess::class,'phase_cycle_id'); }
    public function tasks(){ return $this->hasMany(Task::class,'phase_cycle_id'); }
}
