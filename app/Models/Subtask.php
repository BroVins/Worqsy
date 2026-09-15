<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class Subtask extends Model
{
    use UsesUuid;
    protected $fillable=['task_id','title','is_completed','completed_at'];
    protected function casts(): array { return ['is_completed'=>'bool','completed_at'=>'datetime']; }
    public function task(){ return $this->belongsTo(Task::class); }
}
