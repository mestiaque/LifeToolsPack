@extends('me::master')

@section('title', __('Payment Planner'))

@push('buttons')
  <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-encodex-list">
      <i class="fas fa-list"></i> @lang('Loan List')
  </a>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-12 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-encodex-secondary text-white p-2 fw-bold">
                @lang('Upcoming Payable/Receivable Schedule')
            </div>
            <div class="card-body">
                @if (count($schedule))
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped table-encodex mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>@lang('Date')</th>
                                    <th>@lang('Person')</th>
                                    <th>@lang('Installment')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schedule as $row)
                                    <tr>
                                        <td class="text-center">{{ $row['date'] }}</td>
                                        <td>{{ $row['party'] }}</td>
                                        <td class="text-center">{{ $row['installment_no'] }}</td>
                                        <td class="text-center">
                                            @if ($row['direction'] === 'payable')
                                                <span class="badge bg-danger">@lang('To Pay')</span>
                                            @else
                                                <span class="badge bg-success">@lang('To Receive')</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ toBanglaNumber($row['amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">@lang('No pending installment schedule found.')</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-12 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white p-2 fw-bold">
                @lang('This Month + Next 11 Months')
            </div>
            <div class="card-body">
                <div class="planner-month-list">
                    @foreach ($months as $month)
                        <div class="planner-month-item {{ $month['is_current'] ? 'planner-month-current' : '' }} {{ $month['is_next'] ? 'planner-month-next' : '' }}">
                            <span class="planner-month-inline-part planner-month-name">{{ $month['month'] }}</span>
                            <span class="planner-month-divider">|</span>
                            <span class="planner-month-inline-part planner-month-payable">@lang('Payable'): {{ toBanglaNumber($month['payable'], 2) }}</span>
                            <span class="planner-month-divider">|</span>
                            <span class="planner-month-inline-part planner-month-receivable">@lang('Receivable'): {{ toBanglaNumber($month['receivable'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .planner-month-list {
        display: grid;
        gap: 10px;
    }
    .planner-month-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 8px 10px;
        background: #f9fafb;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        overflow-x: auto;
    }
    .planner-month-current {
        background: #fee2e2;
    }
    .planner-month-next {
        background: #facc1557;
    }
    .planner-month-name {
        font-weight: 600;
        color: #1f2937;
    }
    .planner-month-payable {
        color: #b91c1c;
        font-weight: 700;
    }
    .planner-month-receivable {
        color: #166534;
        font-weight: 700;
    }
    .planner-month-divider {
        color: #6b7280;
        font-weight: 700;
    }
</style>
@endpush
