<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class StoredFile extends Model
{
    use UsesUuid;
    protected $table='files';
    protected $fillable=['workspace_id','uploaded_by','disk','path','original_name','mime_type','size','visibility'];
    public function uploader(){ return $this->belongsTo(User::class,'uploaded_by'); }
    public function links(){ return $this->hasMany(FileLink::class,'file_id'); }
}
