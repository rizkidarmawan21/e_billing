@props(['action', 'search' => null, 'showReset' => false])

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ $action }}">
            @if(isset($extraFilters))
                <div class="mb-3">{{ $extraFilters }}</div>
            @endif
            <div class="row align-items-end">
                <div class="col-md-8">
                    <label for="search" class="form-label fw-semibold">Cari Nama Customer</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="text" id="search" name="search" value="{{ $search }}"
                            class="form-control" placeholder="Ketik nama customer...">
                    </div>
                </div>
                <div class="col-md-4 mt-3 mt-md-0">
                    <x-ui.form-actions class="customer-search-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1" aria-hidden="true"></i> Cari
                        </button>
                        @if($showReset || filled($search))
                            <a class="btn btn-outline-secondary" href="{{ $action }}">Reset</a>
                        @endif
                    </x-ui.form-actions>
                </div>
            </div>
            @if(isset($feedback))
                {{ $feedback }}
            @endif
        </form>
    </div>
</div>
