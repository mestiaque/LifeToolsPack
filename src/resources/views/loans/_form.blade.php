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
