@can($permissionPrefix.'-status')
    <x-action-button
        type="button"
        id="assign-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ module_label('status', $singularLabel) }}"
        label="{{ module_label('status', $singularLabel) }}"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.update-status', $model->ulid),
            'data-edit-url' => route($routeInitialize.'.action.status', ['event' => $model->ulid])
        ]"
    />
@endif

{{-- @can($permissionPrefix.'-status') --}}
    <x-action-button
        type="button"
        id="edit-btn"
        btn-class="dropdown-item edit-btn"
        title="Add Payment"
        label="Add Payment"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.store-payment', $model->ulid),
            'data-edit-url' => route($routeInitialize.'.action.payment', ['event' => $model->ulid])
        ]"
    />
{{-- @endif --}}

{{-- @can($permissionPrefix.'-status') --}}
    <x-action-button
        type="button"
        id="edit-btn"
        btn-class="dropdown-item edit-btn"
        title="Add New Order"
        label="Add New Order"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.store-order', $model->ulid),
            'data-edit-url' => route($routeInitialize.'.action.order', ['event' => $model->ulid])
        ]"
    />
{{-- @endif --}}

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
