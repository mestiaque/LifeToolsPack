<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('Name') }}</label>
        <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', optional($person)->name) }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('Email') }}</label>
        <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email', optional($person)->email) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('Phone') }}</label>
        <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone', optional($person)->phone) }}">
    </div>

        <div class="col-md-6">
        <label class="form-label">{{ __('Types') }}</label>
        <input type="text" name="types" class="form-control form-control-sm" placeholder="1, 2, 3" value="{{ old('types', optional($person)->types ? implode(', ', $person->types) : '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('User Type') }}</label>
        <select name="user_type" id="user_type" class="form-control form-control-sm">
            <option value="">{{ __('Select User Type') }}</option>
            <option value="admin" {{ old('user_type', optional($person)->user_type) === '\App\Models\User::class' ? 'selected' : '' }}>{{ __('User') }}</option>
            <option value="customer" {{ old('user_type', optional($person)->user_type) === '\ME\EmCore\Models\LoanUser::class' ? 'selected' : '' }}>{{ __('Loan User') }}</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('User ID') }}</label>
        <input type="number" name="user_id" class="form-control form-control-sm" value="{{ old('user_id', optional($person)->user_id) }}">
    </div>

    <div class="col-12">
        <label class="form-label">{{ __('Address') }}</label>
        <textarea name="address" class="form-control form-control-sm">{{ old('address', optional($person)->address) }}</textarea>
    </div>
</div>
