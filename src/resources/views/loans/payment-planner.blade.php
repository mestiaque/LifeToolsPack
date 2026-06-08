@extends('me::master')

@section('title', __('Custom Loan Planner'))

@push('buttons')
@extends('me::master')

@section('title', __('Custom Loan Planner'))

@push('buttons')
  <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-encodex-list">
      <i class="fas fa-list"></i> @lang('Loan List')
  </a>
@endpush

@section('content')
<div class="clp-wrap">
    <div class="card clp-card">
        <div class="clp-head">
            <div>
                <p class="clp-title">@lang('Custom Loan Planner')</p>
                <p class="clp-subtitle">@lang('Plan month-wise partial payments in one table. You can move any payment to earlier or later months anytime.')</p>
            </div>
            <div class="clp-stat-grid">
                <div class="clp-stat-item">
                    <span>@lang('Total Due')</span>
                    <strong>{{ toBanglaNumber($totalDue, 2) }}</strong>
                </div>
                <div class="clp-stat-item">
                    <span>@lang('Planned')</span>
                    <strong>{{ toBanglaNumber($totalPlanned, 2) }}</strong>
                </div>
                <div class="clp-stat-item {{ $totalUnplanned > 0 ? 'warn' : 'ok' }}">
                    <span>@lang('Unplanned')</span>
                    <strong>{{ toBanglaNumber($totalUnplanned, 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="table-responsive clp-table-wrap">
            <table class="table clp-table">
                <thead>
                    <tr>
                        <th style="min-width: 140px;">@lang('Month & Year')</th>
                        <th style="min-width: 240px;">@lang('Creditor')</th>
                        <th style="min-width: 170px;" class="text-end">@lang('Payment Amount')</th>
                        <th style="min-width: 270px;">@lang('Current Status / Note')</th>
                        <th style="width: 145px;" class="text-center">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="clp-add-row">
                        <form method="POST" action="{{ route('admin.loans.payment-planner.store') }}">
                            @csrf
                            <td>
                                <input type="month" name="planned_month" class="form-control form-control-sm" value="{{ old('planned_month', now()->format('Y-m')) }}" required>
                            </td>
                            <td>
                                <select name="loan_id" class="form-control form-control-sm form-select" required>
                                    <option value="">@lang('Select due loan')</option>
                                    @foreach ($dueLoans as $dueLoan)
                                        <option value="{{ $dueLoan['id'] }}" {{ (string) old('loan_id') === (string) $dueLoan['id'] ? 'selected' : '' }}>
                                            {{ $dueLoan['name'] }} ({{ __('Due') }}: {{ toBanglaNumber($dueLoan['due_amount'], 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0.01" name="planned_amount" class="form-control form-control-sm text-end" value="{{ old('planned_amount') }}" placeholder="0.00" required>
                            </td>
                            <td>
                                <input type="text" name="note" class="form-control form-control-sm" value="{{ old('note') }}" placeholder="@lang('e.g. Running loan, keep remaining visible')">
                            </td>
                            <td class="text-center">
                                <button type="submit" class="btn btn-sm clp-btn-save">
                                    <i class="fas fa-plus"></i> @lang('Add')
                                </button>
                            </td>
                        </form>
                    </tr>

                    @forelse ($planRows as $row)
                        <tr>
                            <form method="POST" action="{{ route('admin.loans.payment-planner.update', $row['id']) }}">
                                @csrf
                                <td>
                                    <input type="month" name="planned_month" class="form-control form-control-sm" value="{{ $row['month_key'] }}" required>
                                    <div class="clp-month-preview">{{ $row['month_label'] }}</div>
                                </td>
                                <td>
                                    <select name="loan_id" class="form-control form-control-sm form-select" required>
                                        @foreach ($dueLoans as $dueLoan)
                                            <option value="{{ $dueLoan['id'] }}" {{ (int) $row['loan_id'] === (int) $dueLoan['id'] ? 'selected' : '' }}>
                                                {{ $dueLoan['name'] }} ({{ __('Due') }}: {{ toBanglaNumber($dueLoan['due_amount'], 2) }})
                                            </option>
                                        @endforeach
                                        @if (!$dueLoans->contains(fn ($loan) => (int) $loan['id'] === (int) $row['loan_id']))
                                            <option value="{{ $row['loan_id'] }}" selected>
                                                {{ $row['loan_name'] }} ({{ __('Due') }}: {{ toBanglaNumber($row['loan_due'], 2) }})
                                            </option>
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0.01" name="planned_amount" class="form-control form-control-sm text-end" value="{{ number_format($row['planned_amount'], 2, '.', '') }}" required>
                                </td>
                                <td>
                                    <div class="clp-status {{ $row['remaining_after'] > 0 ? 'is-running' : 'is-complete' }}">
                                        {{ $row['status'] }}
                                    </div>
                                    <input type="text" name="note" class="form-control form-control-sm" value="{{ $row['note'] }}" placeholder="@lang('Optional note')">
                                </td>
                                <td class="text-center">
                                    <button type="submit" class="btn btn-sm clp-btn-edit" title="@lang('Update')">
                                        <i class="fas fa-save"></i>
                                    </button>
                            </form>
                            <form method="POST" action="{{ route('admin.loans.payment-planner.delete', $row['id']) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm clp-btn-delete" onclick="return confirm('{{ __('Are you sure you want to delete this plan row?') }}')" title="@lang('Delete')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                                </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="clp-empty">@lang('No custom plan added yet. Add the first row from above.')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .clp-wrap {
        position: relative;
        isolation: isolate;
    }
    .clp-wrap::before {
        content: "";
        position: absolute;
        inset: -14px -12px auto;
        height: 220px;
        background: radial-gradient(circle at 10% 20%, rgba(15, 118, 110, 0.14), transparent 55%),
                    radial-gradient(circle at 90% 0%, rgba(194, 65, 12, 0.16), transparent 48%),
                    linear-gradient(120deg, #f8fafc, #fff7ed 50%, #f0fdfa);
        border-radius: 18px;
        z-index: -1;
    }
    .clp-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 16px 34px rgba(30, 41, 59, 0.06);
        overflow: hidden;
    }
    .clp-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 18px 18px 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .clp-title {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
    }
    .clp-subtitle {
        margin: 4px 0 0;
        font-size: 12px;
        color: #475569;
    }
    .clp-stat-grid {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .clp-stat-item {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 10px;
        padding: 7px 10px;
        min-width: 116px;
        text-align: right;
    }
    .clp-stat-item span {
        display: block;
        font-size: 11px;
        color: #64748b;
    }
    .clp-stat-item strong {
        font-size: 13px;
        color: #0f172a;
        font-weight: 700;
    }
    .clp-stat-item.warn {
        border-color: #fed7aa;
        background: #fff7ed;
    }
    .clp-stat-item.ok {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }
    .clp-table-wrap {
        padding: 10px;
    }
    .clp-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
    }
    .clp-table thead th {
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 10px;
        color: #475569;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
    .clp-table tbody td {
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }
    .clp-table .form-control,
    .clp-table .form-select {
        border-color: #cbd5e1;
        border-radius: 8px;
        font-size: 12px;
    }
    .clp-table .form-control:focus,
    .clp-table .form-select:focus {
        border-color: #0f766e;
        box-shadow: 0 0 0 0.15rem rgba(15, 118, 110, 0.15);
    }
    .clp-add-row td {
        background: #f8fafc;
    }
    .clp-status {
        margin-bottom: 6px;
        padding: 4px 8px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 600;
    }
    .clp-status.is-running {
        background: #fff7ed;
        color: #9a3412;
    }
    .clp-status.is-complete {
        background: #f0fdf4;
        color: #166534;
    }
    .clp-month-preview {
        margin-top: 4px;
        font-size: 10px;
        color: #64748b;
    }
    .clp-btn-save,
    .clp-btn-edit,
    .clp-btn-delete {
        border-radius: 8px;
        min-width: 40px;
    }
    .clp-btn-save {
        background: #0f766e;
        border-color: #0f766e;
        color: #fff;
    }
    .clp-btn-save:hover {
        background: #115e59;
        border-color: #115e59;
        color: #fff;
    }
    .clp-btn-edit {
        background: #0369a1;
        border-color: #0369a1;
        color: #fff;
        margin-right: 4px;
    }
    .clp-btn-delete {
        background: #be123c;
        border-color: #be123c;
        color: #fff;
    }
    .clp-empty {
        text-align: center;
        color: #64748b;
        padding: 22px 8px;
        font-size: 12px;
    }

    @media (max-width: 992px) {
        .clp-head {
            flex-direction: column;
        }
        .clp-stat-grid {
            justify-content: flex-start;
        }
    }
</style>
@endpush
