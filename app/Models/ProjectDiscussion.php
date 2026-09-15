<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class ProjectDiscussion extends Model
{
    use UsesUuid;
    protected $fillable=['project_id','user_id','parent_id','body'];
    public function project(){ return $this->belongsTo(Project::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
