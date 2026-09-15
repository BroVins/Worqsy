<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class TaskComment extends Model
{
    use UsesUuid;
    protected $fillable=['task_id','user_id','parent_id','body'];
    public function task(){ return $this->belongsTo(Task::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function parent(){ return $this->belongsTo(TaskComment::class,'parent_id'); }
    public function replies(){ return $this->hasMany(TaskComment::class,'parent_id'); }
}
