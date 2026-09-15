<?php
namespace App\Models;

use App\Enums\MembershipStatus;
use App\Enums\PermissionMode;
use App\Enums\ProjectRole;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class ProjectMembership extends Model
{
    use UsesUuid;
    protected $fillable=['project_id','user_id','project_role','project_position_id','project_handle','permission_mode','permission_overrides','status'];
    protected function casts(): array { return ['project_role'=>ProjectRole::class,'permission_mode'=>PermissionMode::class,'permission_overrides'=>'array','status'=>MembershipStatus::class]; }
    public function project(){ return $this->belongsTo(Project::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function position(){ return $this->belongsTo(ProjectPosition::class,'project_position_id'); }
    public function phaseAccess(){ return $this->hasMany(ProjectPhaseAccess::class); }
}
