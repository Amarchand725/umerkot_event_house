<form method="GET" action="{{ url()->current() }}">
    <div class="card border shadow-none mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Search records..."
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input
                        type="date"
                        name="from_date"
                        class="form-control"
                        value="{{ request('from_date') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input
                        type="date"
                        name="to_date"
                        class="form-control"
                        value="{{ request('to_date') }}"
                    >
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-filter me-1"></i> Filter
                    </button>
                </div>

                <div class="col-12">
                    <a href="{{ url()->current() }}"
                       class="btn btn-sm btn-outline-secondary">
                        Reset Filters
                    </a>
                </div>

            </div>
        </div>
    </div>
</form>
