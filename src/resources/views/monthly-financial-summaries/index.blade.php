@extends('me::master')

@section('title', trans('Monthly Financial Summary'))

@push('buttons')
    <button class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createEntryModal">
        <i class="fas fa-plus"></i> <span class="hide-mobile">{{ __('Add Entry') }}</span>
    </button>
@endpush

@section('content')
<div class="container-fluids">
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-2">
                <form method="GET" action="{{ route('admin.monthly-financial-summaries.index') }}" class="p-1 glass-search-form" id="mfs-filter-form">
                    <div class="row">
                      <div class="col-md-2">
                        <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month') }}" onchange="this.form.submit()">
                      </div>
                      <div class="col-md-2">
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="@lang('Enter Title')" value="{{ request('title') }}" onchange="this.form.submit()">
                      </div>

                      <div class="col-md">
                        <a href="{{ route('admin.monthly-financial-summaries.index') }}" class="btn btn-sm btn-encodex-clear rounded">
                          <i class="fas fa-eraser"></i> @lang('Reset')
                        </a>
                      </div>
                    </div>
                </form>
            </div>
        </div>
        @forelse ($groupedEntries as $group)
            <div class="col-lg-3 mb-4">
                <div class="card shadow h-100 w-100">

                    <div class="card-body p-0" style="background: {{ $group['type'] == 'expense' ? 'greenx' : 'redx' }}">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover table-striped table-encodex mb-0 text-center">
                                <thead class="text-center">
                                    <tr>
                                        <th>@lang('Title')</th>
                                        <th>@lang('Amount')</th>
                                        <th><span class="badge {{ $group['type'] == 'expense' ? 'bg-info' : 'bg-danger' }} text-white badge-encodex">{{ $group['card_title'] }}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['items'] as $item)
                                        <tr>
                                            <td class="text-start">{{ $item->title }}</td>
                                            <td class="text-end">{{ toBanglaNumber($item->amount, 2) }}</td>
                                            <td class="text-center">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-encodex-edit js-edit-entry"
                                                        data-id="{{ $item->id }}"
                                                        data-month="{{ $item->month_label }}"
                                                        data-type="{{ $item->type }}"
                                                        data-title="{{ $item->title }}"
                                                        data-amount="{{ $item->amount }}"
                                                        data-date="{{ optional($item->date)->format('Y-m-d') }}"
                                                        data-note="{{ $item->note }}"
                                                        data-action="{{ route('admin.monthly-financial-summaries.update', $item->id) }}"
                                                        data-bs-toggle="modal" data-bs-target="#editEntryModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <form action="{{ route('admin.monthly-financial-summaries.destroy', $item->id) }}" method="POST" class="d-inline m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-encodex-delete"
                                                            onclick="return confirm('{{ __('Are you sure you want to delete this?') }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @for ($i = $group['items']->count(); $i < 10; $i++)
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                    @endfor
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>Total</td>
                                        <td class="text-end p-1"><strong>{{ toBanglaNumber($group['total'], 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow mb-4 w-100">
                    <div class="card-body text-center text-muted">
                        @lang('No entries found. Click "Add Entry" to create one.')
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>
@include('me::components.calculator')
{{-- --------------------------------------------------------- --}}
{{-- CREATE MODAL --}}
{{-- --------------------------------------------------------- --}}
<div class="modal fade" id="createEntryModal" tabindex="-1">
    <div class="modal-dialog glass-card modal-lg">
        <form action="{{ route('admin.monthly-financial-summaries.store') }}" method="POST" class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Entry') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Start Month') }}</label>
                        <input type="month" name="month_label" class="form-control form-control-sm"
                            value="{{ old('month_label', now()->format('Y-m')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Type') }}</label>
                        <select name="type" class="form-select form-select-sm" required>
                            <option value="loan_payment">{{ __('Loan Payment') }}</option>
                            <option value="expense">{{ __('Expense') }}</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label mb-1">{{ __('Items') }}</label>
                        <small class="form-text text-muted d-block mb-1">@lang('Each item can repeat for its own number of consecutive months, starting from Start Month.')</small>
                        <div id="createItemsWrapper">
                            <div class="row g-2 mb-2 create-item-row">
                                <div class="col-5">
                                    <input type="text" name="titles[]" class="form-control form-control-sm" placeholder="@lang('Title')" required>
                                </div>
                                <div class="col-3">
                                    <input type="number" step="0.01" name="amounts[]" class="form-control form-control-sm" placeholder="@lang('Amount')" required>
                                </div>
                                <div class="col-2">
                                    <input type="number" min="1" step="1" name="months_counts[]" class="form-control form-control-sm" placeholder="@lang('Months')" value="1" required>
                                </div>
                                <div class="col-2 d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-encodex-save js-add-item"><i class="fas fa-plus"></i></button>
                                    <button type="button" class="btn btn-sm btn-encodex-delete js-remove-item"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Date') }}</label>
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Note') }}</label>
                        <textarea name="note" class="form-control form-control-sm" rows="1"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-encodex-cancel" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-encodex-save">{{ __('Save Entry') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- --------------------------------------------------------- --}}
{{-- EDIT MODAL (shared, populated via JS) --}}
{{-- --------------------------------------------------------- --}}
<div class="modal fade" id="editEntryModal" tabindex="-1">
    <div class="modal-dialog glass-card modal-lg">
        <form action="" method="POST" class="modal-content" id="editEntryForm">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Entry') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Month') }}</label>
                        <input type="month" name="month_label" id="edit_month_label" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Type') }}</label>
                        <select name="type" id="edit_type" class="form-select form-select-sm" required>
                            <option value="loan_payment">{{ __('Loan Payment') }}</option>
                            <option value="expense">{{ __('Expense') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Title') }}</label>
                        <input type="text" name="title" id="edit_title" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Amount') }}</label>
                        <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Date') }}</label>
                        <input type="date" name="date" id="edit_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ __('Note') }}</label>
                        <textarea name="note" id="edit_note" class="form-control form-control-sm" rows="1"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-encodex-cancel" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-encodex-save">{{ __('Update Entry') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('js')
<script>
    document.querySelectorAll('.js-edit-entry').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('editEntryForm').action = btn.dataset.action;
            document.getElementById('edit_month_label').value = btn.dataset.month;
            document.getElementById('edit_type').value = btn.dataset.type;
            document.getElementById('edit_title').value = btn.dataset.title;
            document.getElementById('edit_amount').value = btn.dataset.amount;
            document.getElementById('edit_date').value = btn.dataset.date;
            document.getElementById('edit_note').value = btn.dataset.note;
        });
    });

    (function () {
        const wrapper = document.getElementById('createItemsWrapper');
        if (!wrapper) {
            return;
        }

        function rowTemplate() {
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 create-item-row';
            row.innerHTML = `
                <div class="col-5">
                    <input type="text" name="titles[]" class="form-control form-control-sm" placeholder="@lang('Title')" required>
                </div>
                <div class="col-3">
                    <input type="number" step="0.01" name="amounts[]" class="form-control form-control-sm" placeholder="@lang('Amount')" required>
                </div>
                <div class="col-2">
                    <input type="number" min="1" step="1" name="months_counts[]" class="form-control form-control-sm" placeholder="@lang('Months')" value="1" required>
                </div>
                <div class="col-2 d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-encodex-save js-add-item"><i class="fas fa-plus"></i></button>
                    <button type="button" class="btn btn-sm btn-encodex-delete js-remove-item"><i class="fas fa-minus"></i></button>
                </div>
            `;
            return row;
        }

        wrapper.addEventListener('click', function (e) {
            const addBtn = e.target.closest('.js-add-item');
            const removeBtn = e.target.closest('.js-remove-item');

            if (addBtn) {
                wrapper.appendChild(rowTemplate());
                return;
            }

            if (removeBtn) {
                const rows = wrapper.querySelectorAll('.create-item-row');
                if (rows.length > 1) {
                    removeBtn.closest('.create-item-row').remove();
                }
            }
        });
    })();
</script>
@endpush
