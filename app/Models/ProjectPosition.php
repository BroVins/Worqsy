<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class ProjectPosition extends Model
{
    use UsesUuid;
    protected $fillable=['project_id','name','slug'];
    public function project(){ return $this->belongsTo(Project::class); }
}
