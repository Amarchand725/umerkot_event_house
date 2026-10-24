@can($permissionPrefix.'-change_password')
    <x-action-button
        type="button"
        id="assign-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ module_label('password', $singularLabel) }}"
        label="{{ module_label('password', $singularLabel) }}"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.action.update-password', $model->ulid),
            'data-edit-url' => route($routeInitialize.'.action.edit', $model->ulid)
        ]"
    />
@endcan

//Status
@can($permissionPrefix.'-status')
    <x-action-button
        type="button"
        id="status-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ module_label('status', $singularLabel) }}"
        label="{{ module_label('status', $singularLabel) }}"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.update-status', $model->uuid),
            'data-edit-url' => route($routeInitialize.'.action.edit', ['action' => 'status', 'meeting' => $model->uuid])
        ]"
    />
@endcan
{{--
//Payment   ----- create invoice than add record in payment ------
@can($permissionPrefix.'-payment')
    <x-action-button
        type="button"
        id="assign-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ module_label('password', $singularLabel) }}"
        label="{{ module_label('password', $singularLabel) }}"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.action.update-password', $model->ulid),
            'data-edit-url' => route($routeInitialize.'.action.edit', $model->ulid)
        ]"
    />
@endcan

//add new order
@can($permissionPrefix.'-create')
    <x-action-button
        type="button"
        id="assign-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ module_label('password', $singularLabel) }}"
        label="{{ module_label('password', $singularLabel) }}"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.action.update-password', $model->ulid),
            'data-edit-url' => route($routeInitialize.'.action.edit', $model->ulid)
        ]"
    />
@endcan

//return order
@can($permissionPrefix.'-create')
    <x-action-button
        type="button"
        id="assign-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ module_label('password', $singularLabel) }}"
        label="{{ module_label('password', $singularLabel) }}"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.action.update-password', $model->ulid),
            'data-edit-url' => route($routeInitialize.'.action.edit', $model->ulid)
        ]"
    />
@endcan --}}
