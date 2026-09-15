<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;

class DemoLoginRequest extends FormRequest
{
    public function authorize(): bool { return config('worqsy.allow_demo_login'); }
    public function rules(): array { return ['email'=>['required','email'],'password'=>['required','string']]; }
}
