@extends('me::master')

@section('title', __('Event Expenses') . ' - ' . $event->title)

@push('buttons')
  <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-encodex-list">
    <i class="fas fa-arrow-left"></i> @lang('Events')
  </a>
  <button type="button" class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#expenseModal">@lang('Add Expense')</button>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card border-left-primary shadow h-100 w-100 ">
                <div class="card-header py-1 d-flex align-items-center ">
                    <h6 class="m-0 font-weight-bold text-primary w-100">
                        {{ $event->title }}
                        <small class="text-muted">({{ formatDateTime($event->start) }} {{ $event->end ? '- ' . formatDateTime($event->end) : '' }})</small>
                        [ <span class="text-danger"> {{ toBanglaNumber($filterAmount, 2) }} </span> ]
                    </h6>
                    <a href="{{ route('admin.events.expenses.print', array_merge( ['event' => $event->id], request()->all() )) }}" class="btn btn-sm btn-encodex-print float-end" target="_blank">
                        <i class="fas fa-print"></i> @lang('Print')
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.events.expenses.index', $event->id) }}" class="mb-3">
                        <div class="row">
                        <div class="col-md">
                            <input type="text" name="title" class="form-control form-control-sm" placeholder="@lang('Enter Title')" value="{{ request('title') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="amount_type" id="" class="form-control form-control-sm">
                                <option value="">@lang('Select Amount Type')</option>
                                <option value="amount_all" {{ request('amount_type') == 'amount_all' ? 'selected' : '' }}>@lang('All Amount')</option>
                                <option value="amount_min" {{ request('amount_type') == 'amount_min' ? 'selected' : '' }}>@lang('Min Amount')</option>
                                <option value="amount_max" {{ request('amount_type') == 'amount_max' ? 'selected' : '' }}>@lang('Max Amount')</option>
                            </select>
                        </div>
                        <div class="col-md">
                            <button type="submit" class="btn btn-sm btn-encodex-search rounded">
                            <i class="fas fa-search"></i> @lang('Search')
                            </button>
                            <a href="{{ route('admin.events.expenses.index', $event->id) }}" class="btn btn-sm btn-encodex-clear rounded">
                            <i class="fas fa-eraser"></i> @lang('Reset')
                            </a>
                        </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover table-striped table-encodex">
                            <thead class="text-center">
                                <tr class="text-center">
                                <th>{{ trans('#') }}</th>
                                <th>@lang('Title')</th>
                                <th>@lang('Amount')</th>
                                @if(request('amount_type') == 'amount_all')
                                    <th>@lang('Min Amount')</th>
                                    <th>@lang('Max Amount')</th>
                                @endif
                                <th>@lang('Description')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenses as $expense)
                                <tr>
                                    <td class="text-center">{{ toBanglaNumber($loop->iteration) }}</td>
                                    <td>{{ $expense->title }}</td>
                                    <td class="text-end">{{ toBanglaNumber($expense->show_amount, 2) }}</td>
                                    @if(request('amount_type') == 'amount_all')
                                    <td class="text-end">{{ toBanglaNumber($expense->amount_min, 2) }}</td>
                                    <td class="text-end">{{ toBanglaNumber($expense->amount_max, 2) }}</td>
                                    @endif
                                    <td>{{ $expense->description ?? '-' }}</td>
                                    <td class="text-center">{{ formatDate($expense->created_at) }}</td>
                                    <td class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-sm btn-encodex-edit me-1" data-bs-toggle="modal" data-bs-target="#expenseModal{{ $expense->id }}" title="@lang('Edit')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.events.expenses.delete', [$event->id, $expense->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button title="@lang('Delete')" onclick="return confirm('{{ __('Are you sure you want to delete this?') }}')" type="submit" class="btn btn-sm btn-encodex-delete me-1">
                                        <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">@lang('No expenses found')</td>
                                </tr>
                                @endforelse
                                <tr>
                                <td colspan="2" class="text-end"><strong>@lang('Total')</strong></td>
                                <td class="text-end"><strong>{{ toBanglaNumber($filterAmount, 2) }}</strong></td>
                                <td class="text-end"><strong>{{ toBanglaNumber($filterAmountMin, 2) }}</strong></td>
                                <td class="text-end"><strong>{{ toBanglaNumber($filterAmountMax, 2) }}</strong></td>
                                <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= Modals ================= -->
    <!-- Create Modal -->
    <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
        <div class="modal-dialog glass-card shadow">
            <div class="modal-content ">
                <form method="POST" action="{{ route('admin.events.expenses.store', $event->id) }}">
                    @csrf
                    <input type="hidden" name="filter_title" value="{{ request('title') }}">
                    <input type="hidden" name="filter_amount_type" value="{{ request('amount_type', 'amount') }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="expenseModalLabel">@lang('Add Expense')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">@lang('Title') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" list="titleSuggestions" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">@lang('Amount') <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="amount" step="0.01" min="0" value="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount_min" class="form-label">@lang('Min Amount') <span class="text-danger"></span></label>
                            <input type="number" class="form-control" name="amount_min" step="0.01" min="0" >
                        </div>
                        <div class="mb-3">
                            <label for="amount_max" class="form-label">@lang('Max Amount') <span class="text-danger"></span></label>
                            <input type="number" class="form-control" name="amount_max" step="0.01" min="0" >
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">@lang('Description')</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-encodex-clear btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-encodex-save btn-sm">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach ($expenses as $expense)
        <div class="modal fade" id="expenseModal{{ $expense->id }}" tabindex="-1" aria-labelledby="expenseModalLabel{{ $expense->id }}" aria-hidden="true">
            <div class="modal-dialog glass-card shadow">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.events.expenses.update', [$event->id, $expense->id]) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="filter_title" value="{{ request('title') }}">
                        <input type="hidden" name="filter_amount_type" value="{{ request('amount_type', 'amount') }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="expenseModalLabel{{ $expense->id }}">@lang('Edit Expense')</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                        </div>
                        <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">@lang('Title') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" value="{{ $expense->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">@lang('Amount') <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="amount" step="0.01" min="0" value="{{ $expense->amount }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount_min" class="form-label">@lang('Min Amount') <span class="text-danger"></span></label>
                            <input type="number" class="form-control" name="amount_min" step="0.01" min="0" value="{{ $expense->amount_min }}" >
                        </div>
                        <div class="mb-3">
                            <label for="amount_max" class="form-label">@lang('Max Amount') <span class="text-danger"></span></label>
                            <input type="number" class="form-control" name="amount_max" step="0.01" min="0" value="{{ $expense->amount_max }}" >
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">@lang('Description')</label>
                            <textarea class="form-control" name="description" rows="2">{{ $expense->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="created_at" class="form-label">@lang('Created At')</label>
                            <input type="date" class="form-control" name="created_at" value="{{ $expense->created_at->format('Y-m-d') }}">
                        </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-encodex-clear btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn-encodex-save btn-sm">@lang('Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <datalist id="titleSuggestions">
        <option value="Nilufa : ">
        <option value="Estiaque : ">
        <option value="Home : ">
        <option value="Wedding : ">
    </datalist>
@endsection
