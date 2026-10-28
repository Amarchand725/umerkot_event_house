@can($permissionPrefix . '-invoice')
    <x-action-button
        type="link"
        btn-class="dropdown-item"
        title="Download Invoice PDF"
        label="Invoice"
        :href="route($routeInitialize.'.action.invoice', ['event' => $model->ulid])"
    />
@endcan

@can($permissionPrefix.'-status')
    <x-action-button
        type="button"
        id="edit-btn"
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

@can($permissionPrefix.'-payment')
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
@endif

@can($permissionPrefix.'-order')
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
@endif
@can($permissionPrefix.'-return-order')
    <x-action-button
        type="button"
        id="edit-btn"
        btn-class="dropdown-item edit-btn"
        title="Return Order"
        label="Return Order"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-x-large-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.store-return-order', $model->ulid),
            'data-edit-url' => route($routeInitialize.'.action.return-order', ['event' => $model->ulid])
        ]"
    />
@endif
