<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class Invitation extends Model
{
    use UsesUuid;
    protected $fillable=['workspace_id','project_id','email','guest_type','project_role','permission_mode','token_hash','expires_at','accepted_at','revoked_at','invited_by'];
    protected function casts(): array { return ['expires_at'=>'datetime','accepted_at'=>'datetime','revoked_at'=>'datetime']; }
}
