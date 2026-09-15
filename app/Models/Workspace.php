<?php
namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use UsesUuid;
    protected $fillable=['name','slug','status','created_by'];
    public function creator(){ return $this->belongsTo(User::class,'created_by'); }
    public function memberships(){ return $this->hasMany(WorkspaceMembership::class); }
    public function users(){ return $this->belongsToMany(User::class,'workspace_memberships')->withPivot(['organization_role','company_position','status'])->withTimestamps(); }
    public function projects(){ return $this->hasMany(Project::class); }
}
