<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class WorkspaceAdminPermission extends Model
{
    use UsesUuid;
    protected $fillable=['workspace_membership_id','create_project','manage_members','manage_admins','manage_guests','manage_project_managers','manage_permissions','manage_lifecycle','view_audit','manage_billing','manage_organization_settings'];
    protected function casts(): array { return ['create_project'=>'bool','manage_members'=>'bool','manage_admins'=>'bool','manage_guests'=>'bool','manage_project_managers'=>'bool','manage_permissions'=>'bool','manage_lifecycle'=>'bool','view_audit'=>'bool','manage_billing'=>'bool','manage_organization_settings'=>'bool']; }
    public function membership(){ return $this->belongsTo(WorkspaceMembership::class,'workspace_membership_id'); }
}
