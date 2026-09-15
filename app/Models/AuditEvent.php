<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class AuditEvent extends Model
{
    use UsesUuid;
    public $timestamps=false;
    protected $fillable=['workspace_id','actor_user_id','actor_project_identity','event_type','resource_type','resource_id','before_data','after_data','ip_address','user_agent','created_at'];
    protected function casts(): array { return ['before_data'=>'array','after_data'=>'array','created_at'=>'datetime']; }
    protected static function booted(): void
    {
        static::updating(fn () => throw new \RuntimeException('Audit events are immutable.'));
        static::deleting(fn () => throw new \RuntimeException('Audit events are immutable.'));
    }
    public function actor(){ return $this->belongsTo(User::class,'actor_user_id'); }
}
