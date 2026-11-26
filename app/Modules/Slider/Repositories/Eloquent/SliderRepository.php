<?php

namespace App\Modules\Slider\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Slider\Repositories\Contracts\SliderContract;
use App\Modules\Slider\Models\Slider;

class SliderRepository extends BaseRepository implements SliderContract
{
    public function __construct(Slider $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {
        $model = $this->model->make();
        $model->toFill($payload, ['attachment']);
        $model->save();

        if (isset($payload['attachment']) && $payload['attachment'] instanceof \Illuminate\Http\UploadedFile) {
            $model->attachment_id = FileUploader::uploadFile($payload['attachment'], $model, 'attachments', size: 64)?->id;
            $model->save();
        }

        return $model;
    }

    public function updateModel(Model $model, array $payload): Model
    {
        // Update Employee Information
        $model->toFill($payload, ['attachment']);
        $model->save();

        // attachment
        if (
            isset($payload['attachment']) &&
            $payload['attachment'] instanceof \Illuminate\Http\UploadedFile
        ) {
            $model->attachment_id = FileUploader::uploadFile(
                $payload['attachment'],
                $model,
                'attachments',
                size: 64
            )?->id;

            $model->save();
        }

        return $model->refresh();
    }

    public function updateStatus(Model $model, array $payload)
    {
        $model->status_id = $payload['status_id'];
        $model->description = $payload['description'] ?? null;
        $model->save();

        return $model->refresh();
    }
}
