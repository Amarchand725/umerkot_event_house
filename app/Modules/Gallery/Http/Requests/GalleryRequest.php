<?php

namespace App\Modules\Gallery\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Event\Models\Event;

class GalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'event_id' => ['nullable', 'exists:events,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],

            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }
        if ($this->has('event_id')) {
            $this->merge([
                'event_id' => Event::where('ulid', $this->input('event_id'))->value('id')
            ]);
        }
    }
}
