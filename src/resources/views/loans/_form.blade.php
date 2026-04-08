<div class="form-group row mb-3">
    <label for="user" class="col-sm-3 col-form-label">@lang('Loan User') <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <select class="form-control form-control-sm @error('user') is-invalid @enderror" id="user" name="loan_user_id" data-control="select2" data-placeholder="@lang('Select User')" required>
            <option value="">@lang('Select User')</option>
            @foreach ($loanUsers as $user)
                <option value="{{ $user->id }}" {{ (old('loan_user_id', $loan->loan_user_id ?? '') == $user->id) ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        @error('loan_user_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <label for="amount" class="col-sm-3 col-form-label">@lang('Amount') <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <input type="number" step="0.01" class="form-control form-control-sm @error('amount') is-invalid @enderror" id="amount" name="amount"
               value="{{ old('amount', $loan->amount ?? '') }}" required>
        @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <label class="col-sm-3 col-form-label">@lang('Type') <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="type_given" value="given"
                {{ (old('type', $loan->type ?? '') == 'given') ? 'checked' : '' }}>
            <label class="form-check-label" for="type_given">@lang('Given')</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="type_taken" value="taken"
                {{ (old('type', $loan->type ?? '') == 'taken') ? 'checked' : '' }}>
            <label class="form-check-label" for="type_taken">@lang('Taken')</label>
        </div>
        @error('type')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <label for="date" class="col-sm-3 col-form-label">@lang('Date') <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <input type="date" class="form-control form-control-sm @error('date') is-invalid @enderror" id="date" name="date"
               value="{{ old('date', $loan->date ?? date('Y-m-d')) }}" required>
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <label for="installment" class="col-sm-3 col-form-label">@lang('Installment')</label>
    <div class="col-sm-9">
        <input type="number" min="1" step="1" class="form-control form-control-sm @error('installment') is-invalid @enderror" id="installment" name="installment"
               value="{{ old('installment', $loan->installment ?? 1) }}" required>
        <small class="form-text text-muted">@lang('Minimum installment is 1. No separate installment means 1 installment.')</small>
        @error('installment')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row mb-3" id="installment_schedule_wrapper">
    <label class="col-sm-3 col-form-label">@lang('Installment Schedule')</label>
    <div class="col-sm-9">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead>
                    <tr>
                        <th>@lang('Installment')</th>
                        <th>@lang('Expected Date')</th>
                        <th>@lang('Amount')</th>
                        @if (isset($loan))
                            <th>@lang('Done')</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="installment_schedule_body">
                    @php
                        $oldInstallmentDates = old('installment_expected_dates', []);
                        $oldInstallmentAmounts = old('installment_amounts', []);
                        $oldDoneInstallments = old('done_installments', []);
                    @endphp
                    @if (!empty($oldInstallmentDates) || !empty($oldInstallmentAmounts))
                        @for ($i = 0; $i < max(count($oldInstallmentDates), count($oldInstallmentAmounts)); $i++)
                            <tr>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="installment_labels[]" value="{{ ($i + 1) . ' Installment' }}">
                                </td>
                                <td>
                                    <input type="date" class="form-control form-control-sm" name="installment_expected_dates[]" value="{{ $oldInstallmentDates[$i] ?? '' }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="installment_amounts[]" value="{{ $oldInstallmentAmounts[$i] ?? '' }}">
                                </td>
                                @if (isset($loan))
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="form-check-input" name="done_installments[]" value="{{ $i + 1 }}" {{ in_array($i + 1, $oldDoneInstallments, true) ? 'checked' : '' }}>
                                    </td>
                                @endif
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="form-group row mb-3">
    <label for="note" class="col-sm-3 col-form-label">@lang('Note')</label>
    <div class="col-sm-9">
        <input type="text" class="form-control form-control-sm @error('note') is-invalid @enderror" id="note" name="note"
               value="{{ old('note', $loan->note ?? '') }}">
        @error('note')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-9 offset-sm-3 text-end">
        <a href="{{ route('admin.loans.index') }}" class="btn btn-encodex-cancel btn-sm py-2 me-2">@lang('Cancel')</a>
        <button type="submit" class="btn btn-encodex-save btn-sm">
            {{ isset($loan) ? __('Update Loan') : __('Create Loan') }}
        </button>
    </div>
</div>

<script>
    (function () {
        const amountInput = document.getElementById('amount');
        const dateInput = document.getElementById('date');
        const installmentInput = document.getElementById('installment');
        const wrapper = document.getElementById('installment_schedule_wrapper');
        const body = document.getElementById('installment_schedule_body');
        const isEditMode = @json(isset($loan));
        const defaultCompletedCount = @json((int) ($loan->completed_installments ?? 0));

        if (!amountInput || !dateInput || !installmentInput || !wrapper || !body) {
            return;
        }

        function formatDate(dateObj) {
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function getOrdinal(num) {
            const absNum = Math.abs(num);
            const mod100 = absNum % 100;
            if (mod100 >= 11 && mod100 <= 13) {
                return `${num}th`;
            }
            switch (absNum % 10) {
                case 1: return `${num}st`;
                case 2: return `${num}nd`;
                case 3: return `${num}rd`;
                default: return `${num}th`;
            }
        }

        function buildInstallmentRows() {
            const installmentCount = Math.max(parseInt(installmentInput.value, 10) || 1, 1);
            const amount = parseFloat(amountInput.value) || 0;
            const baseDateValue = dateInput.value;

            const existingRows = body.querySelectorAll('tr');
            const existingDates = [];
            const existingDoneFlags = [];

            existingRows.forEach((row) => {
                const dateEl = row.querySelector('input[name="installment_expected_dates[]"]');
                const doneEl = row.querySelector('input[name="done_installments[]"]');
                existingDates.push(dateEl ? dateEl.value : '');
                existingDoneFlags.push(doneEl ? doneEl.checked : false);
            });

            body.innerHTML = '';

            if (amount <= 0 || !baseDateValue) {
                wrapper.style.display = '';
            }

            const baseDate = new Date(`${baseDateValue}T00:00:00`);
            if (Number.isNaN(baseDate.getTime())) {
                wrapper.style.display = '';
                return;
            }

            wrapper.style.display = '';

            const perInstallment = amount / installmentCount;

            for (let i = 1; i <= installmentCount; i++) {
                const currentDate = new Date(baseDate);
                currentDate.setMonth(baseDate.getMonth() + i);

                const row = document.createElement('tr');
                const expectedDate = existingDates[i - 1] || formatDate(currentDate);
                const amountValue = perInstallment.toFixed(2);
                const isChecked = typeof existingDoneFlags[i - 1] === 'boolean'
                    ? existingDoneFlags[i - 1]
                    : (isEditMode && i <= defaultCompletedCount);
                row.innerHTML = `
                    <td>
                        <input type="text" class="form-control form-control-sm" name="installment_labels[]" value="${getOrdinal(i)} Installment">
                    </td>
                    <td>
                        <input type="date" class="form-control form-control-sm" name="installment_expected_dates[]" value="${expectedDate}">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="installment_amounts[]" value="${amountValue}">
                    </td>
                    ${isEditMode ? `<td class="text-center align-middle"><input type="checkbox" class="form-check-input" name="done_installments[]" value="${i}" ${isChecked ? 'checked' : ''}></td>` : ''}
                `;
                body.appendChild(row);
            }
        }

        amountInput.addEventListener('input', buildInstallmentRows);
        dateInput.addEventListener('change', buildInstallmentRows);
        installmentInput.addEventListener('input', buildInstallmentRows);
        buildInstallmentRows();
    })();
</script>
