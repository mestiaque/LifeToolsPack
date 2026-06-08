@extends('me::master')

@section('title', __('Daily Expenses'))

@push('buttons')
    <button class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
        <i class="fas fa-plus"></i> {{ __('Add Expense') }}
    </button>
@endpush

@section('content')
<div class="container-fluids">

    <div class="card shadow mb-4 w-100">
        <div class="card-body">

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
                                <td colspan="6" class="text-start fw-bold">
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
                                            {{ $expense->description ?? 'N/A' }}
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

