
@php($loanUserData = $loanUser ?? null)

<div class="form-group row mb-3">
    <label for="name" class="col-sm-3 col-form-label">@lang('Name')</label>
    <div class="col-sm-9">
        <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name"
               value="{{ old('name', optional($loanUserData)->name) }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <label for="email" class="col-sm-3 col-form-label">@lang('Email')</label>
    <div class="col-sm-9">
        <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email"
               value="{{ old('email', optional($loanUserData)->email) }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <label for="phone" class="col-sm-3 col-form-label">@lang('Phone')</label>
    <div class="col-sm-9">
        <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror" id="phone" name="phone"
               value="{{ old('phone', optional($loanUserData)->phone) }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <label for="address" class="col-sm-3 col-form-label">@lang('Address')</label>
    <div class="col-sm-9">
        <textarea class="form-control form-control-sm @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', optional($loanUserData)->address) }}</textarea>
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <label for="is_active" class="col-sm-3 col-form-label">@lang('Active')</label>
    <div class="col-sm-9 align-self-center">
        <div class="form-switch">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" id="is_active" name="is_active"
                   value="1"
                   @if($loanUserData)
                       {{ $loanUserData->is_active ? 'checked' : '' }}
                   @else
                       {{ old('is_active', true) ? 'checked' : '' }}
                   @endif
            >
        </div>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-9 offset-sm-3 text-end">
        {{-- @if(isset($loan)) --}}
            <button type="submit" class="btn btn-encodex-save btn-sm update-btn d-none">
                {{__('Update User') }}
            </button>
        {{-- @else --}}
            <button type="submit" class="btn btn-encodex-save btn-sm save-btn">
                {{__('Create User') }}
            </button>
        {{-- @endif --}}
        {{-- <a href="{{ route('admin.loans.index') }}" class="btn btn-encodex-cancel btn-sm">@lang('Cancel')</a> --}}
    </div>
</div>
