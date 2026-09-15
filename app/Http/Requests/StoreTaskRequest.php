<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'title'=>['required','string','max:255'],
            'description'=>['nullable','string'],
            'priority'=>['required',Rule::in(['LOW','MEDIUM','HIGH','CRITICAL'])],
            'weight'=>['required',Rule::in([1,2,3,5])],
            'guest_visible'=>['nullable','boolean'],
            'estimate_minutes'=>['nullable','integer','min:1'],
            'start_date'=>['nullable','date'],
            'due_date'=>['nullable','date'],
            'assignee_ids'=>['required','array','min:1'],
            'assignee_ids.*'=>['uuid','exists:users,id'],
            'reviewer_ids'=>['required','array','min:1'],
            'reviewer_ids.*'=>['uuid','exists:users,id'],
            'approver_ids'=>['required','array','min:1'],
            'approver_ids.*'=>['uuid','exists:users,id'],
        ];
    }
}
