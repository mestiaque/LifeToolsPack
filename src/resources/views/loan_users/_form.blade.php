
<div class="form-group row mb-3">
    <label for="name" class="col-sm-3 col-form-label">@lang('Name')</label>
    <div class="col-sm-9">
        <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name"
               value="{{ old('name', $loan->user ?? '') }}">
        @error('name')
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
