@extends('me::master')

@section('title', __('Loan Statement') . ' - ' . ($loanUser->name ?? '-'))

@push('buttons')
  <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-encodex-list">
      <i class="fas fa-list"></i> <span class="hide-mobile">@lang('Back to Loans')</span>
  </a>
@endpush

@section('content')

<!-- User Summary Section -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow border-left-primary">
            <div class="card-header bg-encodex-secondary text-white p-2">
                <h5 class="mb-0">{{ $loanUser->name ?? '-' }} - @lang('Loan Statement')</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <!-- Total Given -->
                    <div class="col-md-2">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">@lang('Total Given')</small>
                            <h6 class="fw-bold mb-0">{{ toBanglaNumber($totalGivenAmount, 2) }}</h6>
                        </div>
                    </div>

                    <!-- Total Given Repayment -->
                    <div class="col-md-2">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">@lang('Given Repayment')</small>
                            <h6 class="fw-bold mb-0">{{ toBanglaNumber($totalGivenRepayment, 2) }}</h6>
                        </div>
                    </div>

                    <!-- Given Due -->
                    <div class="col-md-2">
                        <div class="border rounded p-3 bg-success-light">
                            <small class="text-muted d-block">@lang('Given Due')</small>
                            <h6 class="fw-bold text-success mb-0">{{ toBanglaNumber($totalGivenDue, 2) }}</h6>
                        </div>
                    </div>

                    <!-- Total Taken -->
                    <div class="col-md-2">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">@lang('Total Taken')</small>
                            <h6 class="fw-bold mb-0">{{ toBanglaNumber($totalTakenAmount, 2) }}</h6>
                        </div>
                    </div>

                    <!-- Total Taken Repayment -->
                    <div class="col-md-2">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">@lang('Taken Repayment')</small>
                            <h6 class="fw-bold mb-0">{{ toBanglaNumber($totalTakenRepayment, 2) }}</h6>
                        </div>
                    </div>

                    <!-- Taken Due -->
                    <div class="col-md-2">
                        <div class="border rounded p-3 bg-danger-light">
                            <small class="text-muted d-block">@lang('Taken Due')</small>
                            <h6 class="fw-bold text-danger mb-0">{{ toBanglaNumber($totalTakenDue, 2) }}</h6>
                        </div>
                    </div>
                </div>

                <!-- Net Balance -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="border-top pt-3">
                            <div class="text-center">
                                <h6 class="text-muted">@lang('Net Balance')</h6>
                                @php
                                    $isReceivable = $netBalance > 0;
                                @endphp
                                @if($netBalance == 0)
                                    <h4 class="fw-bold"><span class="badge bg-secondary">@lang('Settled')</span></h4>
                                @elseif($isReceivable)
                                    <h4 class="fw-bold"><span class="badge bg-success">@lang('Receivable'): {{ toBanglaNumber(abs($netBalance), 2) }}</span></h4>
                                @else
                                    <h4 class="fw-bold"><span class="badge bg-danger">@lang('Payable'): {{ toBanglaNumber(abs($netBalance), 2) }}</span></h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Installment Schedules -->
@php
    $loansWithSchedule = $loans->filter(fn ($loan) => max((int) ($loan->installment ?? 1), 1) > 1);
@endphp
@if($loansWithSchedule->count() > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-encodex-secondary text-white p-2">
                    <h5 class="mb-0">@lang('Installment Schedules')</h5>
                </div>
                <div class="card-body">
                    @foreach($loansWithSchedule as $loan)
                        @php
                            $installmentCount = max((int) ($loan->installment ?? 1), 1);
                            $completedInstallments = min(max((int) ($loan->completed_installments ?? 0), 0), $installmentCount);
                            $baseDate = \Carbon\Carbon::parse($loan->date);
                            $installmentAmount = $loan->amount / $installmentCount;
                            $savedDates = is_array($loan->installment_expected_dates) ? $loan->installment_expected_dates : [];
                            $savedAmounts = is_array($loan->installment_amounts) ? $loan->installment_amounts : [];
                            $today = \Carbon\Carbon::today();
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">
                                {{ $loan->date }} &middot;
                                @if($loan->type == 'given')
                                    <span class="badge bg-success">@lang('Given')</span>
                                @else
                                    <span class="badge bg-danger">@lang('Taken')</span>
                                @endif
                                &middot; {{ toBanglaNumber($loan->amount, 2) }}
                                @if($loan->note) &middot; {{ $loan->note }} @endif
                            </small>
                            <a href="{{ route('admin.loans.history', $loan->id) }}" class="btn btn-sm btn-encodex-show">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        <div class="border rounded p-3 mb-4">
                            <div class="installment-line-wrap">
                                <div class="installment-line-track"></div>
                                <div class="installment-line-points" style="grid-template-columns: repeat({{ $installmentCount }}, minmax(70px, 1fr));">
                                    @for ($i = 1; $i <= $installmentCount; $i++)
                                        @php
                                            $expectedDate = !empty($savedDates[$i - 1])
                                                ? \Carbon\Carbon::parse($savedDates[$i - 1])
                                                : $baseDate->copy()->addMonths($i);
                                            $amountForItem = is_numeric($savedAmounts[$i - 1] ?? null)
                                                ? (float) $savedAmounts[$i - 1]
                                                : $installmentAmount;
                                            if ($i <= $completedInstallments) {
                                                $statusClass = 'done';
                                            } elseif ($expectedDate->lt($today)) {
                                                $statusClass = 'expired';
                                            } else {
                                                $statusClass = 'pending';
                                            }
                                        @endphp
                                        <div class="installment-line-item text-center">
                                            <div class="installment-line-amount">{{ toBanglaNumber($amountForItem, 2) }}</div>
                                            <div class="installment-line-dot installment-line-dot-{{ $statusClass }}" title="{{ ucfirst($statusClass) }}"></div>
                                            <div class="installment-line-date">{{ $expectedDate->format('Y-m-d') }}</div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Transaction Statement -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-encodex-secondary text-white p-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">@lang('Transaction History')</h5>
            </div>
            <div class="card-body">
                @if(count($transactions) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-striped table-hover table-encodex">
                            <thead class="text-center">
                                <tr>
                                    <th>{{ trans('#') }}</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Note')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $index => $transaction)
                                    <tr @if($transaction['type'] == 'loan') class="table-light" @else class="table-white" @endif>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $transaction['date'] }}</td>
                                        <td class="text-center">
                                            @if($transaction['type'] == 'loan')
                                                @if($transaction['loan_type'] == 'given')
                                                    <span class="badge bg-success">@lang('Loan Given')</span>
                                                @else
                                                    <span class="badge bg-danger">@lang('Loan Taken')</span>
                                                @endif
                                            @else
                                                <span class="badge bg-info">@lang('Repayment')</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction['description'] }}</td>
                                        <td class="text-end fw-bold">
                                            @if($transaction['amount'] > 0)
                                                <span class="text-success">+ {{ toBanglaNumber($transaction['amount'], 2) }}</span>
                                            @else
                                                <span class="text-danger">{{ toBanglaNumber($transaction['amount'], 2) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($transaction['note'])
                                                <small>{{ $transaction['note'] }}</small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        @lang('No transactions found for this user.')
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Loans Details by Type -->
<div class="row mt-4">
    <!-- Given Loans -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-success p-2 text-white">
                <h6 class="mb-0">@lang('Loans Given')</h6>
            </div>
            <div class="card-body">
                @php
                    $givenLoans = $loans->where('type', 'given');
                @endphp

                @if($givenLoans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>@lang('Date')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Repaid')</th>
                                    <th>@lang('Due')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($givenLoans as $loan)
                                    <tr>
                                        <td class="text-center">{{ $loan->date }}</td>
                                        <td class="text-end">{{ toBanglaNumber($loan->amount, 2) }}</td>
                                        <td class="text-end">{{ toBanglaNumber($loan->totalRepayment(), 2) }}</td>
                                        <td class="text-end @if($loan->dueAmount() > 0) text-danger fw-bold @else text-success @endif">
                                            {{ toBanglaNumber($loan->dueAmount(), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr class="bg-success-light">
                                    <td class="text-end">@lang('TOTAL')</td>
                                    <td class="text-end">{{ toBanglaNumber($totalGivenAmount, 2) }}</td>
                                    <td class="text-end">{{ toBanglaNumber($totalGivenRepayment, 2) }}</td>
                                    <td class="text-end">{{ toBanglaNumber($totalGivenDue, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-muted text-center py-3">@lang('No loans given.')</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Taken Loans -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-danger p-2 text-white">
                <h6 class="mb-0">@lang('Loans Taken')</h6>
            </div>
            <div class="card-body">
                @php
                    $takenLoans = $loans->where('type', 'taken');
                @endphp

                @if($takenLoans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>@lang('Date')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Repaid')</th>
                                    <th>@lang('Due')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($takenLoans as $loan)
                                    <tr>
                                        <td class="text-center">{{ $loan->date }}</td>
                                        <td class="text-end">{{ toBanglaNumber($loan->amount, 2) }}</td>
                                        <td class="text-end">{{ toBanglaNumber($loan->totalRepayment(), 2) }}</td>
                                        <td class="text-end @if($loan->dueAmount() > 0) text-danger fw-bold @else text-success @endif">
                                            {{ toBanglaNumber($loan->dueAmount(), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr class="bg-danger-light">
                                    <td class="text-end">@lang('TOTAL')</td>
                                    <td class="text-end">{{ toBanglaNumber($totalTakenAmount, 2) }}</td>
                                    <td class="text-end">{{ toBanglaNumber($totalTakenRepayment, 2) }}</td>
                                    <td class="text-end">{{ toBanglaNumber($totalTakenDue, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-muted text-center py-3">@lang('No loans taken.')</div>
                @endif
            </div>
        </div>
    </div>
</div>



@push('css')
  <style>
    .bg-success-light {
      background: rgba(76, 175, 80, 0.1) !important;
    }
    .bg-danger-light {
      background: rgba(244, 67, 54, 0.1) !important;
    }

    .installment-line-wrap {
        --dot-size: 16px;
        --amount-space: 22px;
        position: relative;
        padding: 8px;
    }
    .installment-line-track {
        position: absolute;
        top: calc(8px + var(--amount-space) + (var(--dot-size) / 2));
        left: 14px;
        right: 14px;
        height: 2px;
        background: #d1d5db;
        z-index: 1;
    }
    .installment-line-points {
        display: grid;
        gap: 0;
        position: relative;
        z-index: 2;
    }
    .installment-line-item {
        min-width: 0;
        position: relative;
        padding-top: var(--amount-space);
    }
    .installment-line-amount {
        font-size: 12px;
        font-weight: 600;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
    }
    .installment-line-dot {
        width: var(--dot-size);
        height: var(--dot-size);
        border-radius: 50%;
        margin: 0 auto;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px rgba(31, 41, 55, 0.45), 0 0 0 4px rgba(255, 255, 255, 0.85);
    }
    .installment-line-dot-done {
        background: #22c55e;
    }
    .installment-line-dot-pending {
        background: #eab308;
    }
    .installment-line-dot-expired {
        background: #ef4444;
    }
    .installment-line-date {
        font-size: 11px;
        margin-top: 8px;
        white-space: nowrap;
    }
  </style>
@endpush

@endsection
