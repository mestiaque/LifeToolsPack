@extends('me::master')

@section('title', __('PayCycle'))

@push('buttons')
    <button class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createCycleModal">
        <i class="fas fa-plus"></i> <span class="hide-mobile">{{ __('Add Cycle') }}</span>
    </button>
@endpush

@section('content')
<div class="container-fluids">

    <div class="card shadow mb-4 w-100">
        <div class="card-body">

            @if($currentCycle && $forecast)
                <h6 class="fw-bold mb-3">
                    @lang('Current Cycle'): {{ $currentCycle->month_label }}
                    <small class="text-muted">
                        ({{ $forecast['window_start']->format('d M') }} &ndash; {{ $forecast['window_end']->format('d M') }})
                    </small>
                </h6>

                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">@lang('Salary')</small>
                            <h5 class="fw-bold mb-0">৳ {{ number_format($forecast['salary_amount'], 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">@lang('Expected Expense')</small>
                            <h5 class="fw-bold text-danger mb-0">৳ {{ number_format($forecast['expected_expense'], 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">@lang('Loan Installments Due')</small>
                            <h5 class="fw-bold text-warning mb-0">৳ {{ number_format($forecast['loan_installments_due'], 2) }}</h5>
                            @if(count($forecast['loan_installments_breakdown']))
                                <button type="button" class="btn btn-link btn-sm p-0 mt-1" data-bs-toggle="modal" data-bs-target="#loanBreakdownModal">
                                    {{ __(':count due', ['count' => count($forecast['loan_installments_breakdown'])]) }}
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">@lang('Projected Balance')</small>
                            <h5 class="fw-bold {{ $forecast['projected_balance'] < 0 ? 'text-danger' : 'text-success' }} mb-0">
                                ৳ {{ number_format($forecast['projected_balance'], 2) }}
                            </h5>
                        </div>
                    </div>
                </div>

                @if($forecast['shortfall'] > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-triangle-exclamation"></i>
                        @lang('This cycle is short by'):
                        <strong>৳ {{ number_format($forecast['shortfall'], 2) }}</strong> —
                        @lang('you may need a new loan before the next salary.')
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-circle-check"></i>
                        @lang('This cycle looks covered. No new loan expected.')
                    </div>
                @endif
            @else
                <div class="alert alert-info">@lang('Add a pay cycle to see the forecast.')</div>
            @endif

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped table-encodex text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('Month')</th>
                            <th>@lang('Salary')</th>
                            <th>@lang('Expected Date')</th>
                            <th>@lang('Received Date')</th>
                            <th>@lang('Expected Expense')</th>
                            <th>@lang('Note')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cycles as $cycle)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $cycle->month_label }}</td>
                                <td>{{ number_format($cycle->salary_amount, 2) }}</td>
                                <td>{{ $cycle->expected_date->format('d M, Y') }}</td>
                                <td>{{ $cycle->received_date?->format('d M, Y') ?? '--' }}</td>
                                <td>{{ $cycle->expected_expense !== null ? number_format($cycle->expected_expense, 2) : __('Auto') }}</td>
                                <td>{{ Str::limit($cycle->note, 30) }}</td>
                                <td>
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-sm btn-encodex-edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCycle{{ $cycle->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.paycycle.destroy', $cycle->id) }}"
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
                                <td colspan="8">@lang('No pay cycles found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

{{-- LOAN BREAKDOWN --}}
@if($forecast && count($forecast['loan_installments_breakdown']))
    <div class="modal fade" id="loanBreakdownModal">
        <div class="modal-dialog glass-card modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Loan Installments Due This Cycle') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover table-striped table-encodex text-center mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('Loan User')</th>
                                    <th>@lang('Installment')</th>
                                    <th>@lang('Due Date')</th>
                                    <th>@lang('Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forecast['loan_installments_breakdown'] as $item)
                                    <tr>
                                        <td>{{ $item['loan_user'] }}</td>
                                        <td>{{ $item['label'] }}</td>
                                        <td>{{ $item['due_date']->format('d M, Y') }}</td>
                                        <td>৳ {{ number_format($item['amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="fw-bold table-warning">
                                    <td colspan="3" class="text-end">@lang('Total')</td>
                                    <td>৳ {{ number_format($forecast['loan_installments_due'], 2) }}</td>
                                </tr>
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
@endif

{{-- CREATE MODAL --}}
<div class="modal fade" id="createCycleModal">
    <div class="modal-dialog glass-card modal-lg">
        <form method="POST" action="{{ route('admin.paycycle.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5>{{ __('Add Pay Cycle') }}</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label>{{ __('Month Label') }}</label>
                    <input type="text" name="month_label" class="form-control" placeholder="2026-08" required>
                </div>
                <div class="col-md-6">
                    <label>{{ __('Salary Amount') }}</label>
                    <input type="number" step="0.01" name="salary_amount" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>{{ __('Expected Salary Date') }}</label>
                    <input type="date" name="expected_date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>{{ __('Received Date') }}</label>
                    <input type="date" name="received_date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>{{ __('Expected Expense (optional)') }}</label>
                    <input type="number" step="0.01" name="expected_expense" class="form-control"
                        placeholder="@lang('Leave empty to auto-estimate')">
                </div>
                <div class="col-12">
                    <label>{{ __('Note') }}</label>
                    <textarea name="note" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-encodex-cancel" data-bs-dismiss="modal">@lang('Close')</button>
                <button class="btn btn-encodex-save">@lang('Save Cycle')</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODALS --}}
@foreach($cycles as $cycle)
    <div class="modal fade" id="editCycle{{ $cycle->id }}">
        <div class="modal-dialog glass-card modal-lg">
            <form method="POST" action="{{ route('admin.paycycle.update', $cycle->id) }}" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5>{{ __('Edit Pay Cycle') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>{{ __('Month Label') }}</label>
                        <input type="text" name="month_label" class="form-control" value="{{ $cycle->month_label }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Salary Amount') }}</label>
                        <input type="number" step="0.01" name="salary_amount" class="form-control" value="{{ $cycle->salary_amount }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Expected Salary Date') }}</label>
                        <input type="date" name="expected_date" class="form-control" value="{{ $cycle->expected_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Received Date') }}</label>
                        <input type="date" name="received_date" class="form-control" value="{{ $cycle->received_date?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Expected Expense (optional)') }}</label>
                        <input type="number" step="0.01" name="expected_expense" class="form-control" value="{{ $cycle->expected_expense }}">
                    </div>
                    <div class="col-12">
                        <label>{{ __('Note') }}</label>
                        <textarea name="note" class="form-control">{{ $cycle->note }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-encodex-cancel" data-bs-dismiss="modal">@lang('Close')</button>
                    <button class="btn btn-encodex-save">@lang('Update Cycle')</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

@endsection
