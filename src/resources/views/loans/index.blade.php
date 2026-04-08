@extends('me::master')

@section('title', __('Loans'))

@push('buttons')
  <a href="{{ route('admin.loans.create') }}" class="btn btn-sm btn-encodex-create">@lang('Add Loan')</a>
  <a href="{{ route('admin.loans.payment-planner') }}" class="btn btn-sm btn-encodex-list">@lang('Payment Planner')</a>
@endpush

@section('content')
  <div class="row">
    <!-- Loans Table -->
    <div class="col-lg-8 mb-4">
      <div class="card border-left-primary shadow h-100 w-100 py-2">
        <div class="card-body">
          <form method="GET" action="{{ route('admin.loans.index') }}" class="mb-3">
            <div class="row">
              <div class="col-md-2">
                <input type="text" name="name" class="form-control form-control-sm" placeholder="@lang('Enter Name')" value="{{ request('name') }}">
              </div>
              <div class="col-md-2">
                <input type="text" name="amount" class="form-control form-control-sm" placeholder="@lang('Enter Amount')" value="{{ request('amount') }}">
              </div>
              <div class="col-md-2">
                <select name="type" class="form-control form-control-sm form-select form-select-sm" data-control="select2" data-placeholder="@lang('Select Type')">
                  <option value=""></option>
                  <option value="given" {{ (request('type') == 'given') ? 'selected' : '' }}>@lang('Given')</option>
                  <option value="taken" {{ (request('type') == 'taken') ? 'selected' : '' }}>@lang('Taken')</option>
                </select>
              </div>
              <div class="col-md-auto d-flex align-items-center" >
                    <div class="form-control form-control-sm">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="show_settled"
                            value="1"
                            id="showSettled"
                            {{ request()->boolean('show_settled') ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="showSettled">
                            @lang('Show Settled')
                        </label>
                    </div>
                </div>

              <div class="col-md">
                <button type="submit" class="btn btn-sm btn-encodex-search rounded">
                  <i class="fas fa-search"></i> @lang('Search')
                </button>
                <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-encodex-clear rounded">
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
                  <th>@lang('Person')</th>
                  <th>@lang('Type')</th>
                  <th>@lang('Amount')</th>
                  <th>@lang('Total Repayment')</th>
                  <th>@lang('Due')</th>
                  <th>@lang('Date')</th>
                  <th>@lang('Action')</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($loans as $loan)
                  <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $loan->loanUser->name ?? '-' }}</td>
                    <td class="text-center">
                        @if($loan->type == 'given')
                          <span class="badge bg-success">{{ ucfirst($loan->type) }}</span>
                        @else
                          <span class="badge bg-danger">{{ ucfirst($loan->type) }}</span>
                        @endif
                    </td>
                    <td class="text-end">{{ toBanglaNumber($loan->amount, 2) }}</td>
                    <td class="text-end">{{ toBanglaNumber($loan->totalRepayment(), 2) }}</td>
                    <td class="text-end">{{ toBanglaNumber($loan->dueAmount(), 2) }}</td>
                    <td class="text-center">{{ $loan->date }}</td>
                    <td class="d-flex justify-content-center">
                      <!-- Repayment Button -->
                      <a href="{{ route('admin.loans.history', $loan->id) }}" class="btn btn-sm btn-encodex-payment   me-1" title="@lang('Repayment')">
                        <i class="fas fa-money-bill  "></i>
                      </a>
                      <!-- Show Button -->
                      <button type="button"
                          class="btn btn-sm btn-encodex-show   me-1"
                          data-bs-toggle="modal"
                          data-bs-target="#showLoanModal{{ $loan->id }}"
                          title="@lang('Show')">
                        <i class="fas fa-eye  "></i>
                      </button>
                      <!-- Edit Button -->
                      <a title="@lang('Edit')" class="btn btn-sm btn-encodex-edit   me-1"
                          href="{{ route('admin.loans.edit', $loan->id) }}">
                          <i class="fas fa-edit  "></i>
                      </a>
                      <!-- Delete Button -->
                      <form action="{{ route('admin.loans.delete', $loan->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button title="@lang('Delete')" onclick="return confirm('{{ __('Are you sure you want to delete this?') }}')"
                            type="submit" class="btn btn-sm btn-encodex-delete   me-1">
                            <i class="fas fa-trash  "></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr class="fw-bold text-end">
                  <td class="bg-success-light" colspan="3">@lang('Given')</td>
                  <td class="bg-success-light">{{ toBanglaNumber($totalGivenLoan, 2) }}</td>
                  <td class="bg-success-light">{{ toBanglaNumber($totalGivenRepayment, 2) }}</td>
                  <td class="bg-success-light">{{ toBanglaNumber($totalGivenDue, 2) }}</td>
                  <td rowspan="2" colspan="2" class="align-middle text-center" style="font-size: 20px">
                    @php
                      $netBalance = $totalGivenDue - $totalTakenDue;
                      $isReceivable = $netBalance > 0;
                    @endphp
                    @if($netBalance == 0)
                      <span class="badge bg-secondary">@lang('Settled')</span>
                    @elseif($isReceivable)
                      <span class="badge bg-success">@lang('Receivable'): {{ toBanglaNumber(abs($netBalance), 2) }}</span>
                    @else
                      <span class="badge bg-danger">@lang('Payable'): {{ toBanglaNumber(abs($netBalance), 2) }}</span>
                    @endif
                  </td>
                </tr>
                <tr class="fw-bold text-end">
                  <td class="bg-danger-light" colspan="3">@lang('Taken')</td>
                  <td class="bg-danger-light">{{ toBanglaNumber($totalTakenLoan, 2) }}</td>
                  <td class="bg-danger-light">{{ toBanglaNumber($totalTakenRepayment, 2) }}</td>
                  <td class="bg-danger-light">{{ toBanglaNumber($totalTakenDue, 2) }}</td>
                </tr>
              </tfoot>
            </table>
            {{-- If using pagination --}}
            {{-- {{ $loans->links('pagination::bootstrap-5') }} --}}
          </div>
        </div>
      </div>
    </div>

    <!-- User-wise summary card -->
    <div class="col-lg-4 mb-4">
      <div class="card shadow mb-4 w-100">
        <div class="card-header p-2 bg-encodex-secondary text-white fw-bold">@lang('User-wise Loan Summary')</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0 table-striped">
                  <thead class="text-center">
                    <tr class="bg-encodex-light text-white">
                      <th class="bg-encodex-light text-white">@lang('User')</th>
                      <th class="bg-encodex-light text-white">@lang('Type')</th>
                      <th class="bg-encodex-light text-white">@lang('Loan')</th>
                      <th class="bg-encodex-light text-white">@lang('Repayment')</th>
                      <th class="bg-encodex-light text-white">@lang('Due')</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($userSummary as $summary)
                      @if($summary['given_loan'] > 0)
                      <tr>
                        <td>{{ ($summary['user']->name ?? '-') }}</td>
                        <td class="text-center"><span class="badge bg-success">@lang('Given')</span></td>
                        <td class="text-end">{{ toBanglaNumber($summary['given_loan'], 2) }}</td>
                        <td class="text-end">{{ toBanglaNumber($summary['given_repayment'], 2) }}</td>
                        <td class="text-end">{{ toBanglaNumber($summary['given_due'], 2) }}</td>
                      </tr>
                      @endif
                      @if($summary['taken_loan'] > 0)
                      <tr>
                        <td>{{ ($summary['user']->name ?? '-') }}</td>
                        <td class="text-center"><span class="badge bg-danger">@lang('Taken')</span></td>
                        <td class="text-end">{{ toBanglaNumber($summary['taken_loan'], 2) }}</td>
                        <td class="text-end">{{ toBanglaNumber($summary['taken_repayment'], 2) }}</td>
                        <td class="text-end">{{ toBanglaNumber($summary['taken_due'], 2) }}</td>
                      </tr>
                      @endif
                    @endforeach
                  </tbody>
                </table>
            </div>

          <!-- Net Balance Table (Receivable/Payable) -->
          <table class="table table-bordered table-sm mb-0 table-striped mt-3">
            <thead>
              <tr class="bg-encodex-light text-white">
                <th class="bg-encodex-light text-white text-center">@lang('User')</th>
                <th class="bg-encodex-light text-white text-center">@lang('Net Balance')</th>
                <th class="bg-encodex-light text-white text-center">@lang('Status')</th>
              </tr>
            </thead>
            <tbody>
              @foreach($userSummary as $summary)
                @php
                  $netBalance = $summary['given_due'] - $summary['taken_due'];
                  $isReceivable = $netBalance > 0;
                @endphp
                <tr>
                  <td><a href="{{ route('admin.loans.user-history', $summary['user']->id ?? 0) }}"><i class="fas fa-info-circle"></i></a> {{ ($summary['user']->name ?? '-') }}  </td>
                  <td class="text-end">{{ toBanglaNumber(abs($netBalance), 2) }}</td>
                  <td class="text-center">
                    @if($netBalance == 0)
                      <span class="badge bg-secondary">@lang('Settled')</span>
                    @elseif($isReceivable)
                      <span class="badge bg-success">@lang('Receivable')</span>
                    @else
                      <span class="badge bg-danger">@lang('Payable')</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ================= Modals (Outside Table) ================= -->
  @foreach ($loans as $loan)
    <div class="modal fade" id="showLoanModal{{ $loan->id }}" tabindex="-1"
        aria-labelledby="showLoanModalLabel{{ $loan->id }}" aria-hidden="true">
        <div class=" modal-dialog  glass-card modal-lg">
            <div class="modal-content shadow-lg rounded-3">

                <!-- Header -->
                <div class="modal-header ">
                    <h5 class="modal-title" id="showLoanModalLabel{{ $loan->id }}">
                        <i class="bi bi-cash-coin me-2"></i> @lang('Loan Details')
                    </h5>
                    <button type="button" class="btn-close "
                            data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <div class="container-fluid">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <small class="text-muted">@lang('Amount')</small>
                                    <h6 class="fw-bold mb-0">{{ $loan->amount }}</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <small class="text-muted">@lang('Total Repayment')</small>
                                    <h6 class="fw-bold mb-0">{{ $loan->totalRepayment() }}</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <small class="text-muted">@lang('Due Amount')</small>
                                    <h6 class="fw-bold text-danger mb-0">{{ $loan->dueAmount() }}</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <small class="text-muted">@lang('Loan Type')</small>
                                    <h6 class="fw-bold mb-0">{{ ucfirst($loan->type) }}</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <small class="text-muted">@lang('Date')</small>
                                    <h6 class="fw-bold mb-0">{{ $loan->date }}</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                              <div class="border rounded p-3">
                                <small class="text-muted">@lang('Installment')</small>
                                <h6 class="fw-bold mb-0">{{ max((int) ($loan->installment ?? 1), 1) }}</h6>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="border rounded p-3">
                                <small class="text-muted">@lang('Completed Installments')</small>
                                <h6 class="fw-bold mb-0">{{ min(max((int) ($loan->completed_installments ?? 0), 0), max((int) ($loan->installment ?? 1), 1)) }}</h6>
                              </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <small class="text-muted">@lang('Note')</small>
                                    <h6 class="fw-normal mb-0">{{ $loan->note ?? 'N/A' }}</h6>
                                </div>
                            </div>

                            <div class="col-12">
                              @php
                                $installmentCount = max((int) ($loan->installment ?? 1), 1);
                                $completedInstallments = min(max((int) ($loan->completed_installments ?? 0), 0), $installmentCount);
                                $baseDate = \Carbon\Carbon::parse($loan->date);
                                $installmentAmount = $loan->amount / $installmentCount;
                                $today = \Carbon\Carbon::today();
                              @endphp
                              <div class="border rounded p-3">
                                <small class="text-muted d-block mb-2">@lang('Installment Schedule')</small>
                                <div class="installment-line-wrap">
                                  <div class="installment-line-track"></div>
                                  <div class="installment-line-points" style="grid-template-columns: repeat({{ $installmentCount }}, minmax(70px, 1fr));">
                                    @for ($i = 1; $i <= $installmentCount; $i++)
                                      @php
                                        $expectedDate = $baseDate->copy()->addMonths($i);
                                        if ($i <= $completedInstallments) {
                                            $statusClass = 'done';
                                        } elseif ($expectedDate->lt($today)) {
                                            $statusClass = 'expired';
                                        } else {
                                            $statusClass = 'pending';
                                        }
                                      @endphp
                                      <div class="installment-line-item text-center">
                                        <div class="installment-line-amount">{{ toBanglaNumber($installmentAmount, 2) }}</div>
                                        <div class="installment-line-dot installment-line-dot-{{ $statusClass }}" title="{{ ucfirst($statusClass) }}"></div>
                                        <div class="installment-line-date">{{ $expectedDate->format('Y-m-d') }}</div>
                                      </div>
                                    @endfor
                                  </div>
                                </div>
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
@endsection


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
