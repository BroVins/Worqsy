<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class SubmitTaskRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'work_summary'=>['required','string','max:5000'],
            'deliverables_text'=>['nullable','string'],
            'links_text'=>['nullable','string'],
            'completion_notes'=>['nullable','string','max:5000'],
        ];
    }
    public function normalized(): array {
        $data=$this->validated();
        $data['deliverables']=array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',$data['deliverables_text']??''))));
        $data['links']=array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',$data['links_text']??''))));
        unset($data['deliverables_text'],$data['links_text']);
        return $data;
    }
}
