<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class TaskRevision extends Model
{
    use UsesUuid;
    protected $fillable=['task_id','revision_number','requested_by','overall_deadline','status','request_note','created_at','resubmitted_at'];
    protected function casts(): array { return ['overall_deadline'=>'datetime','resubmitted_at'=>'datetime']; }
    public function task(){ return $this->belongsTo(Task::class); }
    public function requester(){ return $this->belongsTo(User::class,'requested_by'); }
    public function items(){ return $this->hasMany(RevisionItem::class,'revision_id'); }
}
