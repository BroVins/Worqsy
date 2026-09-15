<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class TaskSubmission extends Model
{
    use UsesUuid;
    protected $fillable=['task_id','submitted_by','submission_number','work_summary','deliverables','links','completion_notes','submitted_at'];
    protected function casts(): array { return ['deliverables'=>'array','links'=>'array','submitted_at'=>'datetime']; }
    public function task(){ return $this->belongsTo(Task::class); }
    public function submitter(){ return $this->belongsTo(User::class,'submitted_by'); }
}
