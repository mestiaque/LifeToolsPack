@extends('me::master')

@section('title', __('Custom Payment Planner'))

@push('buttons')
  <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-encodex-list">
      <i class="fas fa-list"></i> @lang('Loan List')
  </a>
@endpush

@section('content')
<div class="clp-page">
    <div class="clp-hero mb-3">
        <div class="clp-summary-grid">
            <div class="clp-filter-wrap">
                <label for="clpUserFilter" class="clp-filter-label">@lang('Filter by User')</label>
                <select id="clpUserFilter" class="form-control form-control-sm form-select clp-filter-select " data-control="select2" data-placeholder="@lang('Select user to filter')">
                    <option value="">@lang('All Users')</option>
                    @foreach ($dueUsers as $dueUser)
                        <option value="{{ $dueUser['id'] }}">{{ $dueUser['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="clp-summary-card">
                <span>@lang('Total Due')</span>
                <strong>{{ toBanglaNumber($totalDue, 2) }}</strong>
            </div>
            <div class="clp-summary-card is-info">
                <span>@lang('Planned')</span>
                <strong>{{ toBanglaNumber($totalPlanned, 2) }}</strong>
            </div>
            <div class="clp-summary-card {{ $totalUnplanned > 0 ? 'is-warn' : 'is-ok' }}">
                <span>@lang('Unplanned')</span>
                <strong>{{ toBanglaNumber($totalUnplanned, 2) }}</strong>
            </div>
        </div>
    </div>

    <div class="clp-layout mb-4">
    <aside class="clp-side">
        <div class="card clp-side-card shadow-sm mb-3">
            <div class="card-body p-3">
                <h6 class="clp-side-title mb-2">@lang('Planning Progress')</h6>
                <div class="clp-progress-main mb-2">
                    <div class="clp-progress-track">
                        <div class="clp-progress-fill" style="width: {{ $overallProgressPercent }}%;"></div>
                    </div>
                    <div class="clp-progress-meta mt-1">
                        <strong>{{ toBanglaNumber($overallProgressPercent, 1) }}%</strong>
                        <span>{{ toBanglaNumber($totalPlanned, 2) }} / {{ toBanglaNumber($totalDue, 2) }}</span>
                    </div>
                </div>

                <div class="clp-user-progress-list">
                    @forelse ($userProgress as $progress)
                        <div class="clp-user-progress-item" data-loan-user-id="{{ $progress['id'] }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="clp-user-name">{{ $progress['name'] }}</span>
                                <span class="clp-user-pct">{{ toBanglaNumber($progress['progress_percent'], 1) }}%</span>
                            </div>
                            <div class="clp-user-track">
                                <div class="clp-user-fill" style="width: {{ $progress['progress_percent'] }}%;"></div>
                            </div>
                            <small class="clp-user-meta">@lang('Remaining'): {{ toBanglaNumber($progress['remaining'], 2) }}</small>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">@lang('No due user found.')</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card clp-side-card shadow-sm">
            <div class="card-body p-3">
                <h6 class="clp-side-title mb-2">@lang('Month-wise Payment Map')</h6>
                <div class="clp-map-list">
                    @forelse ($monthMap as $month)
                        <div class="clp-map-block">
                            <div class="clp-map-head">
                                <span>{{ $month['month_label'] }}</span>
                                <strong>{{ toBanglaNumber($month['month_total'], 2) }}</strong>
                            </div>
                            @foreach ($month['items'] as $item)
                                <div class="clp-map-item" data-loan-user-id="{{ $item['loan_user_id'] }}">
                                    <span>{{ $item['loan_user_name'] }}</span>
                                    <strong>{{ toBanglaNumber($item['planned_amount'], 2) }}</strong>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-muted small mb-0">@lang('No plan rows yet.')</p>
                    @endforelse
                </div>
            </div>
        </div>
    </aside>

    <section class="clp-main">
    <div class="card clp-card shadow-sm">
    <div class="card-body p-2 p-md-3">


        <div class="table-responsive clp-table-shell">
            <form id="add-custom-plan-form" action="{{ route('admin.loans.custom-payment-planner.store') }}" method="POST" class="d-none" novalidate>
                @csrf
            </form>

            <table class="table clp-tableX table-encodex table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 160px;">@lang('Month & Year')</th>
                        <th style="min-width: 260px;">@lang('Creditor')</th>
                        <th style="min-width: 180px;" class="text-end">@lang('Payment Amount')</th>
                        <th style="min-width: 340px;">@lang('Current Status / Comment')</th>
                        <th style="width: 130px;" class="text-center">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="clp-add-row">
                        <td>
                            <input form="add-custom-plan-form" type="month" name="planned_month" class="form-control form-control-sm" value="{{ old('planned_month', now()->format('Y-m')) }}" required>
                        </td>
                        <td>
                            <select form="add-custom-plan-form" name="loan_user_id" class="form-control form-control-sm form-select form-select-sm" required data-control="select2">
                                <option value="">@lang('Select due user')</option>
                                @foreach ($dueUsers as $dueUser)
                                    <option value="{{ $dueUser['id'] }}" {{ (string) old('loan_user_id') === (string) $dueUser['id'] ? 'selected' : '' }}>
                                        {{ $dueUser['name'] }} ({{ __('Due') }}: {{ toBanglaNumber($dueUser['due_amount'], 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input form="add-custom-plan-form" type="number" min="0.01" step="0.01" name="planned_amount" class="form-control form-control-sm text-end" value="{{ old('planned_amount') }}" required>
                        </td>
                        <td>
                            <input form="add-custom-plan-form" type="text" name="note" class="form-control form-control-sm" value="{{ old('note') }}" placeholder="@lang('Optional note')">
                        </td>
                        <td class="text-center">
                            <button form="add-custom-plan-form" type="submit" class="btn btn-sm clp-btn clp-btn-add btn-encodex-create" title="@lang('Add Row')">
                                <i class="fas fa-plus"></i>
                            </button>
                        </td>
                    </tr>

                    @forelse ($planRows as $row)
                        <form id="update-custom-plan-{{ $row['id'] }}" action="{{ route('admin.loans.custom-payment-planner.update', $row['id']) }}" method="POST" class="d-none" novalidate>
                            @csrf
                        </form>

                        <tr class="clp-plan-row" data-loan-user-id="{{ $row['loan_user_id'] }}">
                            <td>
                                <input form="update-custom-plan-{{ $row['id'] }}" type="month" name="planned_month" class="form-control form-control-sm" value="{{ $row['month_key'] }}" required>
                                {{-- <small class="text-muted">{{ $row['month_label'] }}</small> --}}
                            </td>
                            <td>
                                <select form="update-custom-plan-{{ $row['id'] }}" name="loan_user_id" class="form-control form-control-sm form-select form-select-sm" required data-control="select2" data-placeholder="@lang('Select due user')">
                                    @foreach ($dueUsers as $dueUser)
                                        <option value="{{ $dueUser['id'] }}" {{ (int) $row['loan_user_id'] === (int) $dueUser['id'] ? 'selected' : '' }}>
                                            {{ $dueUser['name'] }} ({{ __('Due') }}: {{ toBanglaNumber($dueUser['due_amount'], 2) }})
                                        </option>
                                    @endforeach
                                    @if (!$dueUsers->contains(fn ($dueUser) => (int) $dueUser['id'] === (int) $row['loan_user_id']))
                                        <option value="{{ $row['loan_user_id'] }}" selected>
                                            {{ $row['loan_user_name'] }} ({{ __('Due') }}: {{ toBanglaNumber($row['loan_user_due'], 2) }})
                                        </option>
                                    @endif
                                </select>
                            </td>
                            <td>
                                <input form="update-custom-plan-{{ $row['id'] }}" type="number" min="0.01" step="0.01" name="planned_amount" class="form-control form-control-sm text-end" value="{{ number_format($row['planned_amount'], 2, '.', '') }}" required>
                            </td>
                            <td>
                                {{-- @if ($row['is_complete'])
                                    <span class="clp-status-chip is-complete mb-1 d-inline-block">{{ $row['loan_user_name'] }} @lang('loan complete in your plan')</span>
                                @else
                                    <span class="clp-status-chip is-running mb-1 d-inline-block">{{ $row['loan_user_name'] }} @lang('running (remaining in plan:') {{ toBanglaNumber($row['remaining_after'], 2) }})</span>
                                @endif --}}
                                <input form="update-custom-plan-{{ $row['id'] }}" type="text" name="note" class="form-control form-control-sm" value="{{ $row['note'] }}" placeholder="@lang('Optional note')">
                            </td>
                            <td class="text-center clp-actions">
                                <button form="update-custom-plan-{{ $row['id'] }}" type="submit" class="btn btn-sm clp-btn clp-btn-save btn-encodex-save" title="@lang('Update')">
                                    <i class="fas fa-save"></i>
                                </button>
                                <form action="{{ route('admin.loans.custom-payment-planner.delete', $row['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm clp-btn clp-btn-delete btn-encodex-delete" onclick="return confirm('{{ __('Are you sure to delete this row?') }}')" title="@lang('Delete')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3 clp-empty">@lang('No custom plan row found. Add first row from the top.')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
    </section>
</div>
</div>
@endsection

@push('js')
<script>
    (function () {
        const filterEl = document.getElementById('clpUserFilter');
        if (!filterEl) {
            return;
        }

        const highlightByUser = (nodes, userId) => {
            nodes.forEach((node) => {
                const nodeUserId = node.getAttribute('data-loan-user-id');
                const isMatch = !!userId && nodeUserId === userId;
                node.classList.toggle('clp-highlight', isMatch);
                node.classList.toggle('clp-muted', !!userId && !isMatch);
            });
        };

        const applyFilter = () => {
            const selectedUserId = filterEl.value;

            highlightByUser(document.querySelectorAll('.clp-plan-row[data-loan-user-id]'), selectedUserId);
            highlightByUser(document.querySelectorAll('.clp-user-progress-item[data-loan-user-id]'), selectedUserId);
            highlightByUser(document.querySelectorAll('.clp-map-item[data-loan-user-id]'), selectedUserId);

            document.querySelectorAll('.clp-map-block').forEach((block) => {
                const items = block.querySelectorAll('.clp-map-item[data-loan-user-id]');
                const hasHighlighted = !!selectedUserId && Array.from(items).some((item) => item.classList.contains('clp-highlight'));
                block.classList.toggle('clp-map-focus', hasHighlighted);
            });
        };

        filterEl.addEventListener('change', applyFilter);
        applyFilter();
    })();
</script>
@endpush

@push('css')
<style>
    .clp-page {
        position: relative;
    }

    .clp-filter-wrap {
        min-width: 210px;
        max-width: 260px;
    }
    .clp-filter-label {
        display: block;
        margin-bottom: 4px;
        font-size: 11px;
        color: #334155;
        font-weight: 700;
    }
    .clp-filter-select {
        border-radius: 10px;
        border-color: #cbd5e1;
    }

    .clp-summary-grid {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .clp-summary-card {
        min-width: 130px;
        padding: 8px 10px;
        border-radius: 12px;
        border: 1px solid #bae6fd;
        background: #f0f9ff;
        text-align: right;
    }
    .clp-summary-card span {
        display: block;
        font-size: 11px;
        color: #334155;
    }
    .clp-summary-card strong {
        font-size: 13px;
        color: #0f172a;
    }
    .clp-summary-card.is-info {
        border-color: #bfdbfe;
        background: #eff6ff;
    }
    .clp-summary-card.is-warn {
        border-color: #fed7aa;
        background: #fff7ed;
    }
    .clp-summary-card.is-ok {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }
    .clp-card {
        border: 1px solid #dbeafe;
        border-radius: 16px;
        overflow: hidden;
    }
    .clp-layout {
        display: grid;
        grid-template-columns: minmax(290px, 30%) minmax(0, 70%);
        gap: 14px;
        align-items: start;
    }
    .clp-side-card {
        border: 1px solid #d9e8f8;
        border-radius: 14px;
    }
    .clp-side-title {
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
    }
    .clp-progress-track,
    .clp-user-track {
        width: 100%;
        height: 10px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }
    .clp-progress-main .clp-progress-track {
        height: 12px;
    }
    .clp-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #0ea5e9, #2563eb);
    }
    .clp-progress-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        color: #475569;
    }
    .clp-user-progress-list {
        max-height: 280px;
        overflow: auto;
        padding-right: 2px;
    }
    .clp-user-progress-item {
        padding: 8px 0;
        border-top: 1px dashed #dbeafe;
    }
    .clp-user-progress-item:first-child {
        border-top: 0;
        padding-top: 2px;
    }
    .clp-user-name {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
    }
    .clp-user-pct {
        font-size: 11px;
        font-weight: 700;
        color: #0f766e;
    }
    .clp-user-fill {
        height: 100%;
        background: linear-gradient(90deg, #14b8a6, #0ea5e9);
    }
    .clp-user-meta {
        display: inline-block;
        margin-top: 3px;
        color: #64748b;
        font-size: 10px;
    }
    .clp-map-list {
        max-height: 420px;
        overflow: auto;
        padding-right: 2px;
    }
    .clp-map-block {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px;
        margin-bottom: 8px;
        background: #ffffff;
    }
    .clp-map-head,
    .clp-map-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }
    .clp-map-head {
        font-size: 12px;
        font-weight: 800;
        color: #0f172a;
        border-bottom: 1px dashed #dbeafe;
        padding-bottom: 5px;
        margin-bottom: 5px;
    }
    .clp-map-item {
        font-size: 11px;
        color: #334155;
        padding: 2px 0;
    }
    .clp-map-item strong {
        color: #0f172a;
    }
    .clp-plan-row,
    .clp-user-progress-item,
    .clp-map-item {
        transition: opacity 0.2s ease, transform 0.2s ease, background-color 0.2s ease;
    }
    .clp-plan-row.clp-muted,
    .clp-user-progress-item.clp-muted,
    .clp-map-item.clp-muted {
        opacity: 0.42;
    }
    .clp-plan-row.clp-highlight td {
        background: #e8f4ff !important;
    }
    .clp-user-progress-item.clp-highlight {
        background: #f0f9ff;
        border-radius: 8px;
        padding-left: 8px;
        padding-right: 8px;
        transform: translateX(2px);
    }
    .clp-map-item.clp-highlight {
        background: #e0f2fe;
        border-radius: 6px;
        padding: 4px 6px;
    }
    .clp-map-block.clp-map-focus {
        border-color: #7dd3fc;
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.1);
    }
    .clp-table-shell {
        border: 1px solid #d6e4f5;
        border-radius: 14px;
        overflow: auto;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 16%);
    }
    .clp-table {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }
    .clp-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: linear-gradient(90deg, #0b2946, #17395f);
        color: #f8fafc;
        font-weight: 700;
        font-size: 12px;
        letter-spacing: 0.02em;
        border: 0;
        border-bottom: 1px solid #1e3a5f;
        text-transform: none;
        padding-top: 11px;
        padding-bottom: 11px;
    }
    .clp-table thead th:first-child {
        border-top-left-radius: 12px;
    }
    .clp-table thead th:last-child {
        border-top-right-radius: 12px;
    }
    .clp-table tbody td {
        background: #ffffff;
        border: 0;
        border-bottom: 1px solid #e5edf8;
        padding: 9px 8px;
        vertical-align: middle;
    }
    .clp-table tbody tr.clp-plan-row:nth-child(odd) td {
        background: #fbfdff;
    }
    .clp-table tbody tr.clp-plan-row:hover td {
        background: #f1f7ff;
    }
    .clp-add-row td {
        background: #eef6ff;
        border-bottom: 1px solid #cfe2ff;
    }

    .clp-hint {
        font-size: 10px;
        color: #64748b;
        margin-bottom: 3px;
    }
    .clp-status-chip {
        font-size: 11px;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 999px;
    }
    .clp-status-chip.is-running {
        background: #fef3c7;
        color: #854d0e;
    }
    .clp-status-chip.is-complete {
        background: #dcfce7;
        color: #166534;
    }


    .clp-empty {
        font-weight: 500;
    }
    .clp-actions {
        white-space: nowrap;
    }
    .clp-actions .clp-btn + form {
        margin-left: 4px;
    }
    @media (max-width: 991px) {
        .clp-hero {
            flex-direction: column;
            gap: 12px;
        }
        .clp-summary-grid {
            width: 100%;
            justify-content: flex-start;
        }
        .clp-layout {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 767px) {
        .clp-table {
            font-size: 12px;
        }
        .clp-table tbody td {
            padding: 7px 6px;
        }
    }
</style>
@endpush
