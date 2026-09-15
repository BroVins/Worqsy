<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class ProjectPhaseAccess extends Model
{
    use UsesUuid;
    protected $table='project_phase_access';
    protected $fillable=['phase_cycle_id','project_membership_id','access_status','permission_override','activated_at','deactivated_at'];
    protected function casts(): array { return ['permission_override'=>'array','activated_at'=>'datetime','deactivated_at'=>'datetime']; }
    public function phaseCycle(){ return $this->belongsTo(ProjectPhaseCycle::class,'phase_cycle_id'); }
    public function membership(){ return $this->belongsTo(ProjectMembership::class,'project_membership_id'); }
}
