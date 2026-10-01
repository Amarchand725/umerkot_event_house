<?php

namespace App\Modules\Customer\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'name' => ['required', 'string', 'max:255'],
            'caste' => ['required', 'string', 'max:255'],
            'cnic_no' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'alter_phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
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
