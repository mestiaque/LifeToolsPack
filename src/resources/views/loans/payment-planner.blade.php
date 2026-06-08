@extends('me::master')

@section('title', __('Payment Planner'))

@push('buttons')
  <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-encodex-list">
      <i class="fas fa-list"></i> @lang('Loan List')
  </a>
@endpush

@section('content')
@php
    $monthsWithKeys = [];
    foreach ($months as $monthItem) {
        $monthKey = '';
        try {
            $monthKey = \Carbon\Carbon::createFromFormat('M Y', $monthItem['month'])->format('Y-m');
        } catch (\Throwable $e) {
            $monthKey = '';
        }
        $monthItem['key'] = $monthKey;
        $monthsWithKeys[] = $monthItem;
    }

    $scheduleByMonth = [];
    foreach ($schedule as $row) {
        $monthKey = substr((string) ($row['date'] ?? ''), 0, 7);
        if (!isset($scheduleByMonth[$monthKey])) {
            $scheduleByMonth[$monthKey] = [];
        }
        $scheduleByMonth[$monthKey][] = $row;
    }
@endphp
<div class="pm-grid">
    <div class="card glass-card">
        <p class="pm-section-title">
            <i class="ti ti-calendar-due" aria-hidden="true"></i>
            @lang('Upcoming Payable/Receivable Schedule')
        </p>
        <div class="pm-filter-bar" id="pmFilterBar" style="display:none;">
            <span class="pm-filter-label">@lang('Filtered month')</span>
            <button type="button" class="pm-reset-filter" id="pmResetFilter">@lang('Show all months')</button>
        </div>

        <div class="pm-table-wrap pm-table-wrap-main">
            @if (count($schedule))
                <div class="pm-groups" id="pmGroups">
                    @foreach ($monthsWithKeys as $month)
                        @php
                            $monthRows = $scheduleByMonth[$month['key']] ?? [];
                        @endphp
                        <div class="pm-month-group" data-month-group="{{ $month['key'] }}">
                            <p class="pm-group-title">
                                <span>{{ $month['month'] }}</span>
                                <span class="pm-group-count">{{ toBanglaNumber(count($monthRows)) }}</span>
                            </p>

                            @if (count($monthRows))
                                <table class="pm-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('Date')</th>
                                            <th>@lang('Person')</th>
                                            <th>#</th>
                                            <th>@lang('Type')</th>
                                            <th style="text-align:right">@lang('Amount')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($monthRows as $row)
                                            <tr>
                                                <td class="text-muted-sm">{{ $row['date'] }}</td>
                                                <td>{{ $row['party'] }}</td>
                                                <td class="text-center text-muted-sm">{{ $row['installment_no'] }}</td>
                                                <td>
                                                    @if ($row['direction'] === 'payable')
                                                        <span class="pm-badge-pay">
                                                            <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                                            @lang('Pay')
                                                        </span>
                                                    @else
                                                        <span class="pm-badge-recv">
                                                            <i class="fa fa-arrow-down" aria-hidden="true"></i>
                                                            @lang('Receive')
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="{{ $row['direction'] === 'payable' ? 'pm-amount-pay' : 'pm-amount-recv' }}">
                                                    {{ toBanglaNumber($row['amount'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="pm-empty pm-empty-in-group">@lang('No pending installment in this month.')</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="pm-empty">@lang('No pending installment schedule found.')</p>
            @endif
        </div>
    </div>

    <div class="card glass-card">
        <p class="pm-section-title">
            <i class="ti ti-chart-bar" aria-hidden="true"></i>
            @lang('This Month + Next 11 Months')
        </p>

        @foreach ($monthsWithKeys as $month)
            <div class="pm-month-card {{ $month['is_current'] ? 'is-current' : '' }} {{ $month['is_next'] ? 'is-next' : '' }}"
                 data-month-key="{{ $month['key'] }}"
                 role="button"
                 tabindex="0"
                 aria-label="{{ __('Filter schedule for') }} {{ $month['month'] }}">
                <span class="pm-month-name">
                    {{ $month['month'] }}
                    @if ($month['is_current'])
                        <span class="pm-tag-current">@lang('This month')</span>
                    @elseif ($month['is_next'])
                        <span class="pm-tag-next">@lang('Next')</span>
                    @endif
                </span>
                <div class="pm-badges">
                    <span class="pm-badge pay">
                        <span class="lbl">@lang('Pay')</span>
                        {{ toBanglaNumber($month['payable'], 2) }}
                    </span>
                    <span class="pm-badge recv">
                        <span class="lbl">@lang('Recv')</span>
                        {{ toBanglaNumber($month['receivable'], 2) }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('css')
<style>
    .pm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 768px) {
        .pm-grid { grid-template-columns: 1fr; }
    }

    .pm-section-title {
        font-size: 11px;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0 0 8px;
    }
    .pm-section-title i {
        font-size: 13px;
        vertical-align: -1px;
        margin-right: 4px;
    }

    /* ── Table ── */
    .pm-table-wrap {
        border: 0.5px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .pm-table-wrap-main {
        padding: 8px;
    }
    .pm-groups {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .pm-month-group {
        border: 0.5px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }
    .pm-group-title {
        margin: 0;
        padding: 8px 10px;
        background: gainsboro;
        border-bottom: 0.5px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #111827;
        font-weight: 500;
        font-size: 12px;
    }
    .pm-group-count {
        font-size: 11px;
        color: #6b7280;
    }
    .pm-empty-in-group {
        padding: 10px;
        font-size: 12px;
    }
    .pm-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .pm-table thead tr {
        background: #f9fafb;
    }
    .pm-table th {
        padding: 8px 10px;
        text-align: left;
        color: #6b7280;
        font-weight: 500;
        font-size: 11px;
        border-bottom: 0.5px solid #e5e7eb;
    }
    .pm-table td {
        padding: 7px 10px;
        border-bottom: 0.5px solid #e5e7eb;
        color: #111827;
        vertical-align: middle;
    }
    .pm-table tr:last-child td {
        border-bottom: none;
    }
    .pm-table tr:hover td {
        background: #f9fafb;
    }
    .text-muted-sm {
        color: #6b7280;
    }
    .text-center {
        text-align: center;
    }
    .pm-badge-pay {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #FCEBEB;
        color: #A32D2D;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
    }
    .pm-badge-recv {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #EAF3DE;
        color: #3B6D11;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
    }
    .pm-badge-pay i,
    .pm-badge-recv i {
        font-size: 11px;
    }
    .pm-amount-pay {
        color: #A32D2D;
        font-weight: 500;
        text-align: right;
    }
    .pm-amount-recv {
        color: #3B6D11;
        font-weight: 500;
        text-align: right;
    }
    .pm-empty {
        padding: 1rem;
        color: #6b7280;
        margin: 0;
        font-size: 13px;
    }
    .pm-filter-bar {
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .pm-filter-label {
        font-size: 11px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .pm-reset-filter {
        border: 0.5px solid #d1d5db;
        background: #fff;
        color: #374151;
        border-radius: 8px;
        padding: 4px 9px;
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
    }
    .pm-reset-filter:hover {
        background: #f9fafb;
    }

    /* ── Monthly cards ── */
    .pm-month-card {
        background: #ffffff;
        border: 0.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px 14px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .pm-month-card:hover {
        box-shadow: 0 3px 10px rgba(17, 24, 39, 0.08);
        transform: translateY(-1px);
    }
    .pm-month-card.is-active-filter {
        outline: 1px solid #b91c1c;
        box-shadow: 0 0 0 2px rgba(185, 28, 28, 0.08);
    }
    .pm-month-card.is-current {
        border-left: 3px solid #E24B4A;
        border-radius: 0 12px 12px 0;
        background: #FCEBEB;
    }
    .pm-month-card.is-next {
        border-left: 3px solid #BA7517;
        border-radius: 0 12px 12px 0;
        background: #FAEEDA;
    }
    .pm-month-name {
        font-weight: 500;
        font-size: 13px;
        color: #111827;
        min-width: 110px;
    }
    .pm-tag-current {
        font-size: 10px;
        background: #FCEBEB;
        color: #A32D2D;
        border-radius: 6px;
        padding: 1px 6px;
        margin-left: 6px;
        font-weight: 500;
        border: 0.5px solid #f0c0c0;
    }
    .pm-tag-next {
        font-size: 10px;
        background: #FAEEDA;
        color: #854F0B;
        border-radius: 6px;
        padding: 1px 6px;
        margin-left: 6px;
        font-weight: 500;
        border: 0.5px solid #f0d0a0;
    }
    .pm-badges {
        display: flex;
        gap: 6px;
        flex: 1;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    .pm-badge {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
    }
    .pm-badge.pay {
        background: #FCEBEB;
        color: #A32D2D;
    }
    .pm-badge.recv {
        background: #EAF3DE;
        color: #3B6D11;
    }
    .pm-badge .lbl {
        font-weight: 400;
        opacity: 0.75;
        margin-right: 2px;
    }
</style>
@endpush

@push('js')
<script>
    (function () {
        var monthCards = document.querySelectorAll('.pm-month-card[data-month-key]');
        var groups = document.querySelectorAll('.pm-month-group[data-month-group]');
        var filterBar = document.getElementById('pmFilterBar');
        var filterLabel = filterBar ? filterBar.querySelector('.pm-filter-label') : null;
        var resetBtn = document.getElementById('pmResetFilter');

        if (!monthCards.length || !groups.length) {
            return;
        }

        function showAllGroups() {
            groups.forEach(function (group) {
                group.style.display = '';
            });

            monthCards.forEach(function (card) {
                card.classList.remove('is-active-filter');
            });

            if (filterBar) {
                filterBar.style.display = 'none';
            }
        }

        function filterByMonth(card) {
            var selectedKey = card.getAttribute('data-month-key');
            var selectedMonthNameNode = card.querySelector('.pm-month-name');
            var selectedMonthName = selectedMonthNameNode ? selectedMonthNameNode.childNodes[0].nodeValue.trim() : '';

            groups.forEach(function (group) {
                group.style.display = group.getAttribute('data-month-group') === selectedKey ? '' : 'none';
            });

            monthCards.forEach(function (item) {
                item.classList.remove('is-active-filter');
            });
            card.classList.add('is-active-filter');

            if (filterBar) {
                filterBar.style.display = '';
            }
            if (filterLabel) {
                filterLabel.textContent = "{{ __('Filtered month') }}: " + selectedMonthName;
            }
        }

        monthCards.forEach(function (card) {
            card.addEventListener('click', function () {
                if (card.classList.contains('is-active-filter')) {
                    showAllGroups();
                    return;
                }
                filterByMonth(card);
            });

            card.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    card.click();
                }
            });
        });

        if (resetBtn) {
            resetBtn.addEventListener('click', showAllGroups);
        }
    })();
</script>
@endpush