<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class AccessRecord extends Model
{
    use UsesUuid;
    public $timestamps=false;
    protected $fillable=['workspace_id','user_id','resource_type','resource_id','first_access_at','last_access_at','access_count'];
    protected function casts(): array { return ['first_access_at'=>'datetime','last_access_at'=>'datetime','access_count'=>'integer']; }
    public function user(){ return $this->belongsTo(User::class); }
}
