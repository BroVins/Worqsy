<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class FileLink extends Model
{
    use UsesUuid;
    protected $fillable=['file_id','resource_type','resource_id','can_download','guest_visible','guest_can_download'];
    protected function casts(): array { return ['can_download'=>'bool','guest_visible'=>'bool','guest_can_download'=>'bool']; }
    public function file(){ return $this->belongsTo(StoredFile::class,'file_id'); }
}
