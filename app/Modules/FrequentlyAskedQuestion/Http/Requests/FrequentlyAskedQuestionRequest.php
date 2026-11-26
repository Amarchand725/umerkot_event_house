<?php

namespace App\Modules\FrequentlyAskedQuestion\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class FrequentlyAskedQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'question' => ['nullable'],
            'answer' => ['nullable'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }
    }
}
