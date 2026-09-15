<?php
namespace App\Models;

use App\Enums\MembershipStatus;
use App\Enums\OrganizationRole;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class WorkspaceMembership extends Model
{
    use UsesUuid;
    protected $fillable=['workspace_id','user_id','organization_role','company_position','scope','status','joined_at'];
    protected function casts(): array { return ['organization_role'=>OrganizationRole::class,'status'=>MembershipStatus::class,'joined_at'=>'datetime']; }
    public function workspace(){ return $this->belongsTo(Workspace::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function adminPermission(){ return $this->hasOne(WorkspaceAdminPermission::class); }
}
