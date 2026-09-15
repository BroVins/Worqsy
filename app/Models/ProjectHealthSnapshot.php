<?php
namespace App\Models;
use App\Enums\ProjectHealthStatus;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class ProjectHealthSnapshot extends Model
{
    use UsesUuid;
    protected $fillable=['project_id','health_status','progress_percent','overdue_tasks','blocked_tasks','review_backlog','revision_backlog','captured_at'];
    protected function casts(): array { return ['health_status'=>ProjectHealthStatus::class,'progress_percent'=>'decimal:2','captured_at'=>'datetime']; }
}
