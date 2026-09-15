<?php
namespace App\Models;
use App\Enums\RevisionItemStatus;
use App\Enums\RevisionPriority;
use App\Enums\RevisionUrgency;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class RevisionItem extends Model
{
    use UsesUuid;
    protected $fillable=['revision_id','description','priority','urgency','deadline','status','completed_by','completed_at'];
    protected function casts(): array { return ['priority'=>RevisionPriority::class,'urgency'=>RevisionUrgency::class,'status'=>RevisionItemStatus::class,'deadline'=>'datetime','completed_at'=>'datetime']; }
    public function revision(){ return $this->belongsTo(TaskRevision::class,'revision_id'); }
    public function completedBy(){ return $this->belongsTo(User::class,'completed_by'); }
}
