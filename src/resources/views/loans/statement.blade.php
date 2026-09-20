@extends('me::master')

@section('title', __('Loan Statement'))

@push('buttons')
  <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-encodex-list">
    <i class="fas fa-list"></i> <span class="hide-mobile">@lang('Back to Loans')</span>
  </a>
@endpush

@section('content')
<div class="row mb-3">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header p-2 bg-encodex-secondary text-white">
        <h6 class="mb-0">@lang('Statement Filter')</h6>
      </div>
      <div class="card-body">
        <form method="GET" action="{{ route('admin.loans.statement') }}" class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label form-label-sm">@lang('From Date')</label>
            <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', $fromDate->format('Y-m-d')) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label form-label-sm">@lang('To Date')</label>
            <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', $toDate->format('Y-m-d')) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label form-label-sm">@lang('View Mode')</label>
            <select name="view_mode" class="form-control form-control-sm form-select">
              <option value="daily" {{ $viewMode == 'daily' ? 'selected' : '' }}>@lang('Daily')</option>
              <option value="monthly" {{ $viewMode == 'monthly' ? 'selected' : '' }}>@lang('Monthly')</option>
              <option value="user" {{ $viewMode == 'user' ? 'selected' : '' }}>@lang('User-wise')</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label form-label-sm">@lang('Loan User')</label>
            <select name="loan_user_id" class="form-control form-control-sm form-select" data-control="select2" data-placeholder="@lang('All Users')">
              <option value="">@lang('All Users')</option>
              @foreach($loanUsers as $user)
                <option value="{{ $user->id }}" {{ $loanUserId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 text-end">
            <button type="submit" class="btn btn-sm btn-encodex-search rounded">
              <i class="fas fa-search"></i> @lang('Filter')
            </button>
            <a href="{{ route('admin.loans.statement') }}" class="btn btn-sm btn-encodex-clear rounded">
              <i class="fas fa-eraser"></i> @lang('Reset')
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card shadow mb-4">
      <div class="card-header p-2 bg-encodex-secondary text-white">
        <h6 class="mb-0">@lang('Balance Summary')</h6>
      </div>
      <div class="card-body">
        <div class="row g-2 text-center">
          <div class="col-6 col-md-2">
            <div class="border rounded p-2">
              <small class="text-muted d-block">@lang('Opening Balance')</small>
              <div class="fw-bold small {{ $openingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                {{ toBanglaNumber(abs($openingBalance), 2) }}
                @if($openingBalance != 0)
                  @if($openingBalance > 0)<span class="text-success"> ({{ __('Receivable') }})</span>@else<span class="text-danger"> ({{ __('Payable') }})</span>@endif
                @endif
              </div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2">
              <small class="text-muted d-block">@lang('Given Loan')</small>
              <div class="fw-bold small text-danger">{{ toBanglaNumber($totalGivenLoan, 2) }}</div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2">
              <small class="text-muted d-block">@lang('Given Repayment')</small>
              <div class="fw-bold small text-success">{{ toBanglaNumber($totalGivenRepayment, 2) }}</div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2">
              <small class="text-muted d-block">@lang('Taken Loan')</small>
              <div class="fw-bold small text-danger">{{ toBanglaNumber($totalTakenLoan, 2) }}</div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2">
              <small class="text-muted d-block">@lang('Taken Repayment')</small>
              <div class="fw-bold small text-success">{{ toBanglaNumber($totalTakenRepayment, 2) }}</div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2">
              <small class="text-muted d-block">@lang('Closing Balance')</small>
              <div class="fw-bold small {{ $closingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                {{ toBanglaNumber(abs($closingBalance), 2) }}
                @if($closingBalance != 0)
                  @if($closingBalance > 0)<span class="text-success"> ({{ __('Receivable') }})</span>@else<span class="text-danger"> ({{ __('Payable') }})</span>@endif
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if($viewMode == 'daily')
  <div class="row">
    <div class="col-12">
      <div class="card shadow">
        <div class="card-header p-2 bg-encodex-secondary text-white">
          <h6 class="mb-0">@lang('Daily Summary')</h6>
        </div>
        <div class="card-body">
          @if(count($dailySummary) > 0)
            <div class="table-responsive">
              <table class="table table-sm table-bordered table-striped table-encodex mb-0">
                <thead class="text-center">
                  <tr>
                    <th>@lang('Date')</th>
                    <th>@lang('Given Loan')</th>
                    <th>@lang('Given Repayment')</th>
                    <th>@lang('Taken Loan')</th>
                    <th>@lang('Taken Repayment')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($dailySummary as $row)
                    <tr>
                      <td class="text-center">{{ $row['date'] }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['given_loan'], 2) }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['given_repayment'], 2) }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['taken_loan'], 2) }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['taken_repayment'], 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="alert alert-info mb-0">@lang('No transactions found in the selected date range.')</div>
          @endif
        </div>
      </div>
    </div>
  </div>
@elseif($viewMode == 'monthly')
  <div class="row">
    <div class="col-12">
      <div class="card shadow">
        <div class="card-header p-2 bg-encodex-secondary text-white">
          <h6 class="mb-0">@lang('Monthly Summary')</h6>
        </div>
        <div class="card-body">
          @if(count($monthlySummary) > 0)
            <div class="table-responsive">
              <table class="table table-sm table-bordered table-striped table-encodex mb-0">
                <thead class="text-center">
                  <tr>
                    <th>@lang('Month')</th>
                    <th>@lang('Given Loan')</th>
                    <th>@lang('Given Repayment')</th>
                    <th>@lang('Taken Loan')</th>
                    <th>@lang('Taken Repayment')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($monthlySummary as $row)
                    <tr>
                      <td>{{ $row['month_label'] }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['given_loan'], 2) }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['given_repayment'], 2) }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['taken_loan'], 2) }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['taken_repayment'], 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="alert alert-info mb-0">@lang('No transactions found in the selected date range.')</div>
          @endif
        </div>
      </div>
    </div>
  </div>
@elseif($viewMode == 'user')
  <div class="row">
    <div class="col-12">
      <div class="card shadow">
        <div class="card-header p-2 bg-encodex-secondary text-white">
          <h6 class="mb-0">@lang('User-wise Summary')</h6>
        </div>
        <div class="card-body">
          @if(count($userSummary) > 0)
            <div class="table-responsive">
              <table class="table table-sm table-bordered table-striped table-encodex mb-0">
                <thead class="text-center">
                  <tr>
                    <th>@lang('User')</th>
                    <th>@lang('Given Loan')</th>
                    <th>@lang('Given Repayment')</th>
                    <th>@lang('Taken Loan')</th>
                    <th>@lang('Taken Repayment')</th>
                    <th>@lang('Net')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($userSummary as $row)
                    @php
                      $net = ($row['given_loan'] - $row['given_repayment']) - ($row['taken_loan'] - $row['taken_repayment']);
                    @endphp
                    <tr>
                      <td>{{ $row['loan_user_name'] }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['given_loan'], 2) }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['given_repayment'], 2) }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['taken_loan'], 2) }}</td>
                      <td class="text-end">{{ toBanglaNumber($row['taken_repayment'], 2) }}</td>
                      <td class="text-end fw-bold {{ $net >= 0 ? 'text-success' : 'text-danger' }}">{{ toBanglaNumber($net, 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="alert alert-info mb-0">@lang('No transactions found in the selected date range.')</div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endif

<div class="row mt-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header p-2 bg-encodex-secondary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">@lang('Transaction List')</h6>
      </div>
      <div class="card-body">
        @if(count($rangeTransactions) > 0)
          <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped table-hover table-encodex mb-0">
              <thead class="text-center">
                <tr>
                  <th>#</th>
                  <th>@lang('Date')</th>
                  <th>@lang('Type')</th>
                  <th>@lang('Description')</th>
                  <th>@lang('User')</th>
                  <th>@lang('Amount')</th>
                  <th>@lang('Balance Effect')</th>
                  <th>@lang('Running Balance')</th>
                  <th>@lang('Note')</th>
                </tr>
              </thead>
              <tbody>
                @foreach($rangeTransactions as $transaction)
                  <tr @if($transaction['type'] == 'loan') class="table-light" @else class="table-white" @endif>
                    <td class="text-center">{{ $loop->iteration }}</td>
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
                    <td>{{ $transaction['loan_user_name'] }}</td>
                    <td class="text-end">{{ toBanglaNumber($transaction['amount'], 2) }}</td>
                    <td class="text-end fw-bold {{ $transaction['balance_effect'] >= 0 ? 'text-success' : 'text-danger' }}">
                      {{ toBanglaNumber(abs($transaction['balance_effect']), 2) }}
                    </td>
                    <td class="text-end fw-bold {{ $transaction['running_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                      {{ toBanglaNumber(abs($transaction['running_balance']), 2) }}
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
          <div class="alert alert-info mb-0">@lang('No transactions found in the selected date range.')</div>
        @endif
      </div>
    </div>
  </div>
</div>
@include('me::components.calculator')
@endsection
