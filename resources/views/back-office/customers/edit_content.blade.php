@method('PUT')
<div class="row g-3 mb-4">
    <!-- Name Input -->
    <div class="col-12">
        <label for="name" class="form-label fw-semibold">
            Name <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-control form-control-lg"
            placeholder="Enter name"
            value="{{ old('name', $model->name) }}"
        />
        <span id="name_error" class="text-danger error">{{ $errors->first('name') }}</span>
    </div>
    <div class="col-12">
        <label for="caste" class="form-label fw-semibold">
            Caste <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            id="caste"
            name="caste"
            class="form-control form-control-lg"
            placeholder="Enter caste"
            value="{{ old('caste', $model->caste) }}"
        />
        <span id="caste_error" class="text-danger error">{{ $errors->first('caste') }}</span>
    </div>
    <div class="col-12">
        <label for="cnic_no" class="form-label fw-semibold">
            CNIC No (Optional)
        </label>
        <input
            type="text"
            id="cnic_no"
            name="cnic_no"
            class="form-control form-control-lg"
            placeholder="Enter cnic_no"
            value="{{ old('cnic_no', $model->cnic_no) }}"
        />
        <span id="cnic_no_error" class="text-danger error">{{ $errors->first('cnic_no') }}</span>
    </div>
    <div class="col-12">
        <label for="phone" class="form-label fw-semibold">
            Phone <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            id="phone"
            name="phone"
            class="form-control form-control-lg"
            placeholder="Enter phone"
            value="{{ old('phone', $model->phone) }}"
        />
        <span id="phone_error" class="text-danger error">{{ $errors->first('phone') }}</span>
    </div>
    <div class="col-12">
        <label for="alter_phone" class="form-label fw-semibold">
            Alter Phone (Optional)
        </label>
        <input
            type="text"
            id="alter_phone"
            name="alter_phone"
            class="form-control form-control-lg"
            placeholder="Enter alter_phone"
            value="{{ old('alter_phone', $model->alter_phone) }}"
        />
        <span id="alter_phone_error" class="text-danger error">{{ $errors->first('alter_phone') }}</span>
    </div>
    <div class="col-12">
        <label for="email" class="form-label fw-semibold">
            Email (Optional)
        </label>
        <input
            type="text"
            id="email"
            name="email"
            class="form-control form-control-lg"
            placeholder="Enter email"
            value="{{ old('email', $model->email) }}"
        />
        <span id="email_error" class="text-danger error">{{ $errors->first('email') }}</span>
    </div>
    <div class="col-12"></div>
        <label for="address" class="form-label fw-semibold">
            Address <span class="text-danger">*</span>
        </label>
        <textarea
            id="address"
            name="address"
            class="form-control form-control-lg"
            placeholder="Enter address"
        >{{ old('address', $model->address) }}</textarea>
        <span id="address_error" class="text-danger error">{{ $errors->first('address') }}</span>
    </div>
    <div class="col-12"></div>
        <label for="note" class="form-label fw-semibold">
            Important Note (Optional)
        </label>
        <textarea
            id="note"
            name="note"
            class="form-control form-control-lg"
            placeholder="Enter note"
        >{{ old('note', $model->note) }}</textarea>
        <span id="note_error" class="text-danger error">{{ $errors->first('note') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="status_id" class="form-label fw-semibold">
          Status
        </label>
        <select id="status_id" name="status_id" class="form-select">
          <option value="">Select status</option>
          @foreach ($statuses as $status)
            <option value="{{ $status->ulid }}" {{ $model->status_id == $status->id ? 'selected' : '' }}>{{ ucfirst($status->name) }}</option>
          @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>
