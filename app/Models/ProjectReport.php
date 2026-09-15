<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class ProjectReport extends Model
{
    use UsesUuid;
    protected $fillable=['project_id','generated_by','report_type','payload','generated_at'];
    protected function casts(): array { return ['payload'=>'array','generated_at'=>'datetime']; }
}
