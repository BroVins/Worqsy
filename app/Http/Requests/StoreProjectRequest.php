<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        $workspaceId = $this->attributes->get('workspace')?->id;
        return [
            'name'=>['required','string','max:255'],
            'project_code'=>['required','string','max:30', Rule::unique('projects')->where(fn($q)=>$q->where('workspace_id',$workspaceId))],
            'description'=>['nullable','string'],
            'project_type'=>['nullable','string','max:120'],
            'visibility'=>['required',Rule::in(['PRIVATE','WORKSPACE'])],
            'project_manager_id'=>['required','uuid','exists:users,id'],
            'start_date'=>['nullable','date'],
            'target_completion'=>['nullable','date','after_or_equal:start_date'],
        ];
    }
}
