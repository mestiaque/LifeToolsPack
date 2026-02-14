@extends('me::master')

@section('title', __('Loan History') . ' - ' . ($loan->loanUser->name ?? '-'))

@push('buttons')
  <a href="{{ route('admin.loans.index') }}"
     class="btn btn-sm btn-encodex-list">
      <i class="fas fa-list"></i> @lang('Loan List')
  </a>
@endpush

@section('content')
<div class="card shadow mb-4">
    <div class="card-body">
        <table class="table table-bordered table-sm mb-4 table-striped table-encodex">
            <tr>
                <th>@lang('Amount')</th>
                <td>{{ toBanglaNumber($loan->amount, 2) }}</td>
            </tr>
            <tr>
                <th>@lang('Type')</th>
                <td>{{ ucfirst($loan->type) }}</td>
            </tr>
            <tr>
                <th>@lang('Date')</th>
                <td>{{ $loan->date }}</td>
            </tr>
            <tr>
                <th>@lang('Note')</th>
                <td>{{ $loan->note }}</td>
            </tr>
            <tr>
                <th>@lang('Total Repayment')</th>
                <td>{{ toBanglaNumber($loan->totalRepayment(), 2) }}</td>
            </tr>
            <tr>
                <th>@lang('Due')</th>
                <td>{{ toBanglaNumber($loan->dueAmount(), 2) }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center p-2 bg-encodex-secondary text-white">
                <h5 class="mb-0">@lang('Repayments')</h5>
            </div>
            <div class="card-body">
                @if($repayments->count())
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0 table-striped table-encodex">
                            <thead>
                                <tr class="text-center">
                                    <th>@lang('Date')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Note')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($repayments as $repayment)
                                    <tr>
                                        <td class="text-center">{{ $repayment->date }}</td>
                                        <td class="text-end">{{ toBanglaNumber($repayment->amount, 2) }}</td>
                                        <td class="text-center">{{ $repayment->note }}</td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-encodex-edit   me-1 edit-repayment-btn"
                                                data-id="{{ $repayment->id }}"
                                                data-amount="{{ $repayment->amount }}"
                                                data-date="{{ $repayment->date }}"
                                                data-note="{{ $repayment->note }}"
                                                title="@lang('Edit')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.repayments.delete', $repayment->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button title="@lang('Delete')" onclick="return confirm('{{ __('Are you sure you want to delete this repayment?') }}')"
                                                    type="submit" class="btn btn-sm btn-encodex-delete   me-1">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-muted">@lang('No repayments yet.')</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="card shadow">
            <div class="card-header bg-primary p-2 text-white d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0" id="form-title">@lang('Add Repayment')</h5>
            </div>
            <div class="card-body">
                <form id="repayment-form" action="{{ route('admin.repayments.store') }}" method="POST">
                    @csrf
                    <div id="method-field"></div>
                    @if(isset($loan))
                        <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                    @endif
                    <div class="form-group row mb-2">
                        <label for="amount" class="col-sm-3 col-form-label">@lang('Amount')</label>
                        <div class="col-sm-9">
                            <input type="number" name="amount" id="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="date" class="col-sm-3 col-form-label">@lang('Date')</label>
                        <div class="col-sm-9">
                            <input type="date" name="date" id="date" class="form-control form-control-sm @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="note" class="col-sm-3 col-form-label">@lang('Note')</label>
                        <div class="col-sm-9">
                            <input type="text" name="note" id="note" class="form-control form-control-sm @error('note') is-invalid @enderror" value="{{ old('note') }}">
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3 text-end">
                            <a href="{{ route('admin.loans.history', $loan->id) }}" class="btn btn-encodex-cancel btn-sm  me-2">@lang('Cancel')</a>
                            <button type="submit" class="btn btn-encodex-save btn-sm" id="submit-btn">@lang('Add Repayment')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('repayment-form');
        const formTitle = document.getElementById('form-title');
        const submitBtn = document.getElementById('submit-btn');
        const cancelBtn = document.getElementById('cancel-edit');
        const methodField = document.getElementById('method-field');

        // Get form inputs
        const amountInput = document.getElementById('amount');
        const dateInput = document.getElementById('date');
        const noteInput = document.getElementById('note');

        // Original form action
        const createUrl = "{{ route('admin.repayments.store') }}";

        // Add event listeners to all edit buttons
        document.querySelectorAll('.edit-repayment-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const amount = this.getAttribute('data-amount');
                const date = this.getAttribute('data-date');
                const note = this.getAttribute('data-note');

                // Update form with method spoofing for PUT
                form.action = "{{ url('admin/repayments/update') }}/" + id;
                // methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                amountInput.value = amount;
                dateInput.value = date;
                noteInput.value = note;

                // Update UI elements to show edit mode
                formTitle.textContent = "@lang('Edit Repayment')";
                submitBtn.textContent = "@lang('Update Repayment')";
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');
                cancelBtn.style.display = 'block';

                // Scroll to form
                form.scrollIntoView({behavior: 'smooth'});
            });
        });

        // Cancel edit mode
        cancelBtn.addEventListener('click', function() {
            resetForm();
        });

        function resetForm() {
            // Reset form to create mode
            form.action = createUrl;
            methodField.innerHTML = '';
            amountInput.value = '';
            dateInput.value = "{{ date('Y-m-d') }}";
            noteInput.value = '';

            // Reset UI elements
            formTitle.textContent = "@lang('Add Repayment')";
            submitBtn.textContent = "@lang('Add Repayment')";
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-primary');
            cancelBtn.style.display = 'none';
        }
    });
</script>
@endpush
@endsection
