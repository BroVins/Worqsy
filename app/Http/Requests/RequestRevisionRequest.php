<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestRevisionRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'overall_deadline'=>['nullable','date'],
            'request_note'=>['nullable','string','max:3000'],
            'items'=>['required','array','min:1'],
            'items.*.description'=>['required','string','max:3000'],
            'items.*.priority'=>['required',Rule::in(['LOW','MEDIUM','HIGH','CRITICAL'])],
            'items.*.urgency'=>['required',Rule::in(['NORMAL','ASAP','IMMEDIATE'])],
            'items.*.deadline'=>['nullable','date'],
        ];
    }
}
