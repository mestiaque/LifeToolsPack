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
                @lang('Payable: This Month + Next 11 Months')
            </div>
            <div class="card-body">
                <div class="planner-month-list">
                    @foreach ($months as $month)
                        <div class="planner-month-item">
                            <div class="planner-month-name">{{ $month['month'] }}</div>
                            <div class="planner-month-amount text-danger">{{ toBanglaNumber($month['payable'], 2) }}</div>
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
        padding: 10px 12px;
        background: #f9fafb;
    }
    .planner-month-name {
        font-weight: 600;
        font-size: 13px;
        line-height: 1.2;
    }
    .planner-month-amount {
        margin-top: 4px;
        font-weight: 700;
        font-size: 15px;
        line-height: 1.2;
    }
</style>
@endpush
