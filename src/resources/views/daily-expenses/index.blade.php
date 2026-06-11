@extends('me::master')

@section('title', __('Daily Expenses'))

@push('buttons')
    <button class="btn btn-sm btn-encodex-payment" data-bs-toggle="modal" data-bs-target="#cashEntryModal">
        <i class="fas fa-wallet"></i> <span class="hide-mobile">{{ __('Cash Entries') }}</span>
    </button>
    <button class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
        <i class="fas fa-plus"></i> <span class="hide-mobile">{{ __('Add Expense') }}</span>
    </button>
@endpush

@section('content')
<div class="container-fluids">

    <div class="card shadow mb-4 w-100">
        <div class="card-body">
            <div class="daily-cash-overview mb-3">
                <div class="daily-cash-cards row g-3">
                    <div class="col-4">
                        <div class="border rounded p-3 h-100 daily-cash-summary-card">
                            <small class="text-muted daily-cash-summary-label">@lang('Cash In')</small>
                            <h5 class="fw-bold mb-0 daily-cash-summary-amount">৳ {{ number_format($totalCash, 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-3 h-100 daily-cash-summary-card">
                            <small class="text-muted daily-cash-summary-label">@lang('Expense')</small>
                            <h5 class="fw-bold text-danger mb-0 daily-cash-summary-amount">৳ {{ number_format($monthlyExpenseTotal, 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-3 h-100 daily-cash-summary-card">
                            <small class="text-muted daily-cash-summary-label">@lang('Balance')</small>
                            <h5 class="fw-bold {{ $cashRemaining < 0 ? 'text-danger' : 'text-success' }} mb-0 daily-cash-summary-amount">
                                ৳ {{ number_format($cashRemaining, 2) }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="daily-cash-progress-panel">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">@lang('Expense against cash')</small>
                        <small class="fw-bold">{{ number_format($cashUsagePercent, 2) }}%</small>
                    </div>
                    <div class="progress daily-cash-progress">
                        <div class="progress-bar daily-cash-progress-expense"
                            role="progressbar"
                            style="width: {{ $cashUsagePercent }}%;"
                            aria-valuenow="{{ $cashUsagePercent }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                        @if($totalCash > 0 && $cashUsagePercent < 100)
                            <div class="progress-bar daily-cash-progress-cash"
                                role="progressbar"
                                style="width: {{ 100 - $cashUsagePercent }}%;"
                                aria-valuenow="{{ 100 - $cashUsagePercent }}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        @endif
                    </div>
                    @if($totalCash <= 0)
                        <small class="text-muted">@lang('Add this month cash to see spending progress.')</small>
                    @elseif($cashRemaining < 0)
                        <small class="text-danger">@lang('Expense is higher than cash.')</small>
                    @endif
                </div>
            </div>

            {{-- SEARCH & DATE FILTER --}}
            <form method="GET" action="{{ route('admin.daily-expenses.index') }}" class="mb-3 glass-search-form ">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="title"
                            class="form-control form-control-sm"
                            placeholder="@lang('Enter Title')"
                            value="{{ request('title') }}">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="from"
                            class="form-control form-control-sm"
                            value="{{ request('from') }}">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="to"
                            class="form-control form-control-sm"
                            value="{{ request('to') }}">
                    </div>

                    <div class="col-md">
                        <button type="submit" class="btn btn-sm btn-encodex-search">
                            <i class="fas fa-search"></i> @lang('Search')
                        </button>

                        <a href="{{ route('admin.daily-expenses.index') }}"
                        class="btn btn-sm btn-encodex-clear">
                            <i class="fas fa-eraser"></i> @lang('Reset')
                        </a>
                    </div>
                </div>
            </form>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped table-encodex text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('Title')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Description')</th>
                            {{-- <th>@lang('Date')</th> --}}
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $sl = 1;
                            $grandTotal = 0;
                        @endphp

                        @forelse($groupedExpenses as $date => $dayExpenses)
                            {{-- DATE HEADER --}}
                            <tr class="table-secondary">
                                <td colspan="5" class="text-start fw-bold">
                                    {{ \Carbon\Carbon::parse($date)->format('d F, Y') }}
                                </td>
                            </tr>

                            @php $dayTotal = 0; @endphp

                            @foreach($dayExpenses as $expense)
                                @php
                                    $dayTotal += $expense->amount;
                                    $grandTotal += $expense->amount;
                                @endphp

                                <tr>
                                    <td>{{ toBanglaNumber($sl++) }}</td>
                                    <td>{{ $expense->title }}</td>
                                    <td>{{ number_format($expense->amount, 2) }}</td>
                                    <td>{{ Str::limit($expense->description, 40) }}</td>
                                    {{-- <td>{{ formatDate($expense->created_at) }}</td> --}}
                                    <td>
                                        <div class="d-inline-flex gap-1">
                                            {{-- Show --}}
                                            <button class="btn btn-sm btn-encodex-show"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#showExpense{{ $expense->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            {{-- Edit --}}
                                            <button class="btn btn-sm btn-encodex-edit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editExpense{{ $expense->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Delete --}}
                                            <form action="{{ route('admin.daily-expenses.destroy', $expense->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-encodex-delete"
                                                        onclick="return confirm('@lang('Are you sure?')')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            {{-- DAILY SUM --}}
                            <tr class="fw-bold table-light">
                                <td colspan="2" class="text-end">@lang('Day Total')</td>
                                <td>{{ number_format($dayTotal, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">@lang('No expenses found')</td>
                            </tr>
                        @endforelse

                        {{-- GRAND TOTAL --}}
                        @if($grandTotal > 0)
                            <tr class="fw-bold table-success">
                                <td colspan="2" class="text-end">@lang('Grand Total')</td>
                                <td>{{ number_format($grandTotal, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            {{-- <div class="mt-3">
                {{ $expenses->links() }}
            </div> --}}

        </div>
    </div>


</div>

    <div class="modal fade" id="createExpenseModal">
        <div class=" modal-dialog  glass-card modal-lg">
            <form method="POST" action="{{ route('admin.daily-expenses.store') }}" class="modal-content">
                @csrf

                <div class="modal-header">
                    <h5>{{ __('Add Expense') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label>{{ __('Title') }}</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label>{{ __('Amount') }}</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Created Date') }}</label>
                        <input type="date" name="created_at" class="form-control"
                            value="{{ now()->format('Y-m-d') }}">
                    </div>

                    <div class="col-12">
                        <label>{{ __('Description') }}</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-encodex-cancel" data-bs-dismiss="modal">@lang('Close')</button>
                    <button class="btn btn-encodex-save">@lang('Save Expense')</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="cashEntryModal" tabindex="-1" aria-labelledby="cashEntryModalLabel" aria-hidden="true">
        <div class="modal-dialog glass-card modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cashEntryModalLabel">
                        <i class="fas fa-wallet me-1"></i> @lang('This Month Cash Entries')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.daily-expenses.cash.store') }}" class="row g-2 align-items-end mb-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">@lang('Title')</label>
                            <input type="text" name="title" class="form-control" placeholder="@lang('Cash source')" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('Amount')</label>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control" required>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button class="btn btn-encodex-save">
                                <i class="fas fa-plus"></i> @lang('Add')
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover table-striped table-encodex text-center mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cashEntries as $cashEntry)
                                    <tr>
                                        <td>{{ $cashEntry->title }}</td>
                                        <td>৳ {{ number_format($cashEntry->amount, 2) }}</td>
                                        <td>
                                            <div class="d-inline-flex gap-1">
                                                <button type="button"
                                                    class="btn btn-sm btn-encodex-edit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editCashEntry{{ $cashEntry->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <form action="{{ route('admin.daily-expenses.cash.destroy', $cashEntry->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-encodex-delete"
                                                        onclick="return confirm('@lang('Are you sure?')')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">@lang('No cash entries found')</td>
                                    </tr>
                                @endforelse

                                @if($totalCash > 0)
                                    <tr class="fw-bold table-success">
                                        <td class="text-end">@lang('Total Cash')</td>
                                        <td>৳ {{ number_format($totalCash, 2) }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-encodex-cancel" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

    @foreach($cashEntries as $cashEntry)
        <div class="modal fade" id="editCashEntry{{ $cashEntry->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog glass-card">
                <form method="POST" action="{{ route('admin.daily-expenses.cash.update', $cashEntry->id) }}" class="modal-content">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Edit Cash Entry')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <div class="col-12">
                            <label class="form-label">@lang('Title')</label>
                            <input type="text" name="title" class="form-control" value="{{ $cashEntry->title }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">@lang('Amount')</label>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ $cashEntry->amount }}" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-encodex-cancel" data-bs-dismiss="modal">@lang('Close')</button>
                        <button class="btn btn-encodex-save">@lang('Update Cash')</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @foreach($expenses as $expense)

        {{-- Edit --}}
        <div class="modal fade" id="editExpense{{ $expense->id }}">
            <div class=" modal-dialog  glass-card modal-lg">
                <form method="POST"
                    action="{{ route('admin.daily-expenses.update', $expense->id) }}"
                    class="modal-content">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5>{{ __('Edit Expense') }}</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <label>{{ __('Title') }}</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ $expense->title }}">
                        </div>

                        <div class="col-md-6">
                            <label>{{ __('Amount') }}</label>
                            <input type="number" step="0.01" name="amount" class="form-control"
                                value="{{ $expense->amount }}">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Created Date') }}</label>
                            <input type="date" name="created_at" class="form-control"
                                value="{{ $expense->created_at->format('Y-m-d') }}">
                        </div>

                        <div class="col-12">
                            <label>{{ __('Description') }}</label>
                            <textarea name="description"
                                    class="form-control">{{ $expense->description }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-encodex-cancel" data-bs-dismiss="modal">@lang('Close')</button>
                        <button class="btn btn-encodex-save">@lang('Update Expense')</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Show --}}

        <div class="modal fade" id="showExpense{{ $expense->id }}" tabindex="-1"
            aria-labelledby="showExpenseModalLabel{{ $expense->id }}" aria-hidden="true">

            <div class=" modal-dialog  glass-card modal-lg ">
                <div class="modal-content shadow-lg rounded-3">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="showExpenseModalLabel{{ $expense->id }}">
                            <i class="bi bi-receipt-cutoff me-2"></i> @lang('Expense Details')
                        </h5>

                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">

                        <div class="container-fluid">
                            <div class="row g-3">

                                <!-- Title -->
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <small class="text-muted">@lang('Title')</small>
                                        <h6 class="fw-bold mb-0">
                                            {{ $expense->title }}
                                        </h6>
                                    </div>
                                </div>

                                <!-- Amount -->
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <small class="text-muted">@lang('Amount')</small>
                                        <h6 class="fw-bold text-danger mb-0">
                                            {{ $expense->amount }}
                                        </h6>
                                    </div>
                                </div>

                                <!-- Category (Optional) -->
                                @if(isset($expense->category))
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <small class="text-muted">@lang('Category')</small>
                                        <h6 class="fw-bold mb-0">
                                            {{ $expense->category->name ?? 'N/A' }}
                                        </h6>
                                    </div>
                                </div>
                                @endif

                                <!-- Date -->
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <small class="text-muted">@lang('Date')</small>
                                        <h6 class="fw-bold mb-0">
                                            {{ formatDate($expense->created_at) }}
                                        </h6>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <small class="text-muted">@lang('Description')</small>
                                        <h6 class="fw-normal mb-0">
                                            {{-- {{ $expense->description ?? 'N/A' }} --}}
                                            {!! nl2br(e($expense->description ?? '--')) !!}
                                        </h6>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-encodex-cancel btn-sm"
                                data-bs-dismiss="modal">

                            <i class="bi bi-x-circle me-1"></i> @lang('Close')

                        </button>
                    </div>

                </div>
            </div>
        </div>


    @endforeach

    @include('me::components.calculator')
@endsection

@push('css')
<style>
    .daily-cash-progress {
        position: relative;
        height: 22px;
        overflow: hidden;
        border-radius: 999px;
        padding: 3px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.36), rgba(255, 255, 255, 0.12));
        border: 1px solid rgba(255, 255, 255, 0.48);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.65),
            inset 0 -8px 18px rgba(15, 23, 42, 0.12),
            0 10px 24px rgba(15, 23, 42, 0.14);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }

    .daily-cash-progress::before {
        content: "";
        position: absolute;
        inset: 4px 8px auto 8px;
        height: 7px;
        border-radius: inherit;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.72), rgba(255, 255, 255, 0.08));
        pointer-events: none;
        z-index: 2;
    }

    .daily-cash-progress .progress-bar {
        position: relative;
        border-radius: 999px;
        background-size: 160% 160%;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.42),
            inset 0 -10px 16px rgba(15, 23, 42, 0.16);
    }

    .daily-cash-progress-expense {
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.36), rgba(255, 255, 255, 0) 45%),
            linear-gradient(135deg, rgba(255, 93, 93, 0.92), rgba(196, 30, 58, 0.88));
    }

    .daily-cash-progress-cash {
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0) 45%),
            linear-gradient(135deg, rgba(72, 221, 154, 0.9), rgba(17, 145, 93, 0.86));
    }

    .daily-cash-summary-card {
        min-width: 0;
    }

    .daily-cash-progress-panel {
        margin-top: 1rem;
    }

    .daily-cash-summary-label {
        display: block;
       
    }

    .daily-cash-summary-amount {
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    @media (min-width: 768px) {
        .daily-cash-overview {
            display: flex;
            align-items: stretch;
            gap: 16px;
        }

        .daily-cash-cards {
            flex: 0 0 calc(50% - 8px);
            margin-left: 0;
            margin-right: 0;
        }

        .daily-cash-cards > [class*="col-"] {
            padding-left: 8px;
            padding-right: 8px;
        }

        .daily-cash-progress-panel {
            flex: 0 0 calc(50% - 8px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
            margin-top: 0;
        }

        .daily-cash-summary-card {
            padding: 14px 16px !important;
        }
    }

    @media (max-width: 575.98px) {
        .daily-cash-summary-card {
            padding: 8px 6px !important;
            border-radius: 6px !important;
        }

        .daily-cash-summary-label {
            font-size: 10px;
            /* min-height: 25px; */
        }

        .daily-cash-summary-amount {
            font-size: 14px;
        }
    }
</style>
@endpush
