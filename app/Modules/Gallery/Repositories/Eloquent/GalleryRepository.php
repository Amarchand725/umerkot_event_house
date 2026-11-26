<?php

namespace App\Modules\Gallery\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Gallery\Repositories\Contracts\GalleryContract;
use App\Modules\Gallery\Models\Gallery;
use App\Helpers\FileUploader;
use Illuminate\Database\Eloquent\Model;

class GalleryRepository extends BaseRepository implements GalleryContract
{
    public function __construct(Gallery $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {
        $model = $this->model->make();
        $model->toFill($payload, ['attachments']);
        $model->save();

        if (isset($payload['attachments'])) {
            foreach ($payload['attachments'] as $attachment) {
                if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                    $model->attachment_id = FileUploader::uploadFile($attachment, $model, 'galleries', size: 64)?->id;
                    $model->save();
                }
            }
        }

        return $model;
    }

    public function updateModel(Model $model, array $payload): Model
    {
        // Update model information
        $model->toFill($payload, ['attachments']);
        $model->save();

        // Upload multiple attachments
        if (!empty($payload['attachments'])) {
            foreach ($payload['attachments'] as $attachment) {
                if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                    FileUploader::uploadFile(
                        $attachment,
                        $model,
                        'galleries',
                        size: 64
                    );
                }
            }
        }

        return $model->refresh();
    }
}
