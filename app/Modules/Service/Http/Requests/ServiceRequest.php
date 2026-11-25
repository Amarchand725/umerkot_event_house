<?php

namespace App\Modules\Service\Http\Requests;

use App\Models\Status;
use App\Modules\Unit\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->has('unit_id')) {
            $this->merge([
                'unit_id' => Unit::where('ulid', $this->input('unit_id'))->value('id')
            ]);
        }
    }
}
