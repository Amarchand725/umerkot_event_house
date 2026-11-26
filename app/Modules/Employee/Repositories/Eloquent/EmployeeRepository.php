<?php

namespace App\Modules\Employee\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Employee\Repositories\Contracts\EmployeeContract;
use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use App\Models\Refrence;
use App\Helpers\FileUploader;

class EmployeeRepository extends BaseRepository implements EmployeeContract
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {
        $model = $this->model->make();
        $model->toFill($payload, ['compensation', 'reference']);
        $model->save();

        if($payload['reference']){
            $refrence = Refrence::firstOrCreate(
                [
                    'full_name' => $payload['reference']['full_name'],
                    'phone' => $payload['reference']['phone'],
                ],
                [
                    'address' => $payload['reference']['address'] ?? null,
                ]
            );

            if($refrence){
                $model->referred_by_id = $refrence->id;
                $model->save();
            }
        }

        // Compensation
        if (!empty($payload['compensation'])) {
            $model->compensation()->create($payload['compensation']);
        }

        if (isset($payload['avatar']) && $payload['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            $model->avatar_id = FileUploader::uploadFile($payload['avatar'], $model, 'avatars', size: 64)?->id;
            $model->save();
        }

        if (isset($payload['cnic_front']) && $payload['cnic_front'] instanceof \Illuminate\Http\UploadedFile) {
            $model->cnic_front_id = FileUploader::uploadFile($payload['cnic_front'], $model, 'cnics', size: 64)?->id;
            $model->save();
        }

        if (isset($payload['cnic_back']) && $payload['cnic_back'] instanceof \Illuminate\Http\UploadedFile) {
            $model->cnic_back_id = FileUploader::uploadFile($payload['cnic_back'], $model, 'cnics', size: 64)?->id;
            $model->save();
        }

        return $model;
    }

    public function updateModel(Model $model, array $payload): Model
    {
        // Update Employee Information
        $model->toFill($payload, ['compensation', 'reference']);
        $model->save();

        // Reference
        if (!empty($payload['reference'])) {
            $referenceData = $payload['reference'];

            $reference = Refrence::firstOrCreate(
                [
                    'full_name' => $referenceData['full_name'] ?? null,
                    'phone' => $referenceData['phone'] ?? null,
                ],
                [
                    'address' => $referenceData['address'] ?? null,
                ]
            );

            $model->referred_by_id = $reference->id;
            $model->save();
        } else {
            $model->referred_by_id = null;
            $model->save();
        }

        // Compensation: Update existing record or create new one
        if (!empty($payload['compensation'])) {
            $model->compensation()->updateOrCreate(
                ['employee_id' => $model->id],
                $payload['compensation']
            );
        }

        // Avatar
        if (
            isset($payload['avatar']) &&
            $payload['avatar'] instanceof \Illuminate\Http\UploadedFile
        ) {
            $model->avatar_id = FileUploader::uploadFile(
                $payload['avatar'],
                $model,
                'avatars',
                size: 64
            )?->id;

            $model->save();
        }

        // CNIC Front
        if (
            isset($payload['cnic_front']) &&
            $payload['cnic_front'] instanceof \Illuminate\Http\UploadedFile
        ) {
            $model->cnic_front_id = FileUploader::uploadFile(
                $payload['cnic_front'],
                $model,
                'cnics',
                size: 64
            )?->id;

            $model->save();
        }

        // CNIC Back
        if (
            isset($payload['cnic_back']) &&
            $payload['cnic_back'] instanceof \Illuminate\Http\UploadedFile
        ) {
            $model->cnic_back_id = FileUploader::uploadFile(
                $payload['cnic_back'],
                $model,
                'cnics',
                size: 64
            )?->id;

            $model->save();
        }

        return $model->refresh();
    }

    public function updateStatus(Model $model, array $payload)
    {
        $model->status_id = $payload['status_id'];
        $model->leave_date = $payload['leave_date'] ?? null;
        $model->note = $payload['note'] ?? null;
        $model->save();

        return $model->refresh();
    }
}
