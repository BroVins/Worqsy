<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class DirectConversation extends Model
{
    use UsesUuid;
    protected $fillable=['workspace_id','project_id','task_id','created_by'];
    public function members(){ return $this->belongsToMany(User::class,'direct_conversation_members','conversation_id','user_id')->withTimestamps(); }
    public function messages(){ return $this->hasMany(DirectMessage::class,'conversation_id'); }
}
