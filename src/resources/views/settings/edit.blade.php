@extends('me::master')

@section('title', trans('Settings'))

@section('content')
<div class="container-fluids">
    <div class="card shadow mb-4 w-100">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">@lang('Mobile')</label>
                            <input type="text" name="mobile" class="form-control form-control-sm @error('mobile') is-invalid @enderror"
                                   value="{{ old('mobile', $settings['mobile']) }}">
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">@lang('Email')</label>
                            <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                   value="{{ old('email', $settings['email']) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">@lang('Present Address')</label>
                            <textarea name="present_address" class="form-control form-control-sm @error('present_address') is-invalid @enderror"
                                      rows="2">{{ old('present_address', $settings['present_address']) }}</textarea>
                            @error('present_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">@lang('Map Link')</label>
                            <input type="url" name="map_link" class="form-control form-control-sm @error('map_link') is-invalid @enderror"
                                   value="{{ old('map_link', $settings['map_link']) }}">
                            @error('map_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">@lang('Designation')</label>
                            <input type="text" name="designation" class="form-control form-control-sm @error('designation') is-invalid @enderror"
                                   value="{{ old('designation', $settings['designation']) }}">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">@lang('Facebook Link')</label>
                            <input type="url" name="facebook_link" class="form-control form-control-sm @error('facebook_link') is-invalid @enderror"
                                   value="{{ old('facebook_link', $settings['facebook_link']) }}">
                            @error('facebook_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">@lang('Instagram Link')</label>
                            <input type="url" name="instagram_link" class="form-control form-control-sm @error('instagram_link') is-invalid @enderror"
                                   value="{{ old('instagram_link', $settings['instagram_link']) }}">
                            @error('instagram_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">@lang('Skype Link')</label>
                            <input type="text" name="skype_link" class="form-control form-control-sm @error('skype_link') is-invalid @enderror"
                                   value="{{ old('skype_link', $settings['skype_link']) }}">
                            @error('skype_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">@lang('Twitter Link')</label>
                            <input type="url" name="twitter_link" class="form-control form-control-sm @error('twitter_link') is-invalid @enderror"
                                   value="{{ old('twitter_link', $settings['twitter_link']) }}">
                            @error('twitter_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">@lang('LinkedIn Link')</label>
                            <input type="url" name="linkedin_link" class="form-control form-control-sm @error('linkedin_link') is-invalid @enderror"
                                   value="{{ old('linkedin_link', $settings['linkedin_link']) }}">
                            @error('linkedin_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group text-center">
                            <label class="font-weight-bold text-primary d-block">@lang('Banner Image')</label>
                            <div class="logo-preview mb-3">
                                @if($settings['banner_image'])
                                    <img loading="lazy" src="{{ asset('storage/images/banner_image/' . $settings['banner_image']) }}"
                                         alt="Banner Image" class="img-thumbnail" style="max-height: 150px;">
                                @else
                                    <div class="empty-logo p-4 bg-light text-center border rounded">
                                        <i class="fas fa-image fa-2x text-gray-400"></i>
                                        <p class="mt-2 text-gray-500">@lang('No image uploaded')</p>
                                    </div>
                                @endif
                            </div>
                            <input type="file" class="border border-primary custom-file-input @error('banner_image') is-invalid @enderror"
                                   name="banner_image" accept="image/*">
                            @error('banner_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">@lang('Recommended size: 1920x1053px, Max: 4MB')</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group text-center">
                            <label class="font-weight-bold text-primary d-block">@lang('Profile Image')</label>
                            <div class="logo-preview mb-3">
                                @if($settings['profile_image'])
                                    <img loading="lazy" src="{{ asset('storage/images/profile_image/' . $settings['profile_image']) }}"
                                         alt="Profile Image" class="img-thumbnail" style="max-height: 150px;">
                                @else
                                    <div class="empty-logo p-4 bg-light text-center border rounded">
                                        <i class="fas fa-image fa-2x text-gray-400"></i>
                                        <p class="mt-2 text-gray-500">@lang('No image uploaded')</p>
                                    </div>
                                @endif
                            </div>
                            <input type="file" class="border border-primary custom-file-input @error('profile_image') is-invalid @enderror"
                                   name="profile_image" accept="image/*">
                            @error('profile_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">@lang('Recommended size: 400x400px, Max: 4MB')</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group text-center">
                            <label class="font-weight-bold text-primary d-block">@lang('Signature Image')</label>
                            <div class="logo-preview mb-3">
                                @if($settings['signature_image'])
                                    <img loading="lazy" src="{{ asset('storage/images/signature_image/' . $settings['signature_image']) }}"
                                         alt="Signature Image" class="img-thumbnail" style="max-height: 150px;">
                                @else
                                    <div class="empty-logo p-4 bg-light text-center border rounded">
                                        <i class="fas fa-image fa-2x text-gray-400"></i>
                                        <p class="mt-2 text-gray-500">@lang('No image uploaded')</p>
                                    </div>
                                @endif
                            </div>
                            <input type="file" class="border border-primary custom-file-input @error('signature_image') is-invalid @enderror"
                                   name="signature_image" accept="image/*">
                            @error('signature_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">@lang('Recommended size: 300x100px, Max: 4MB')</small>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-encodex px-4">
                        <i class="fas fa-save me-1"></i> @lang('Save Settings')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            // Show image preview
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $(this).closest('.form-group').find('.logo-preview').html('<img loading="lazy" src="' + e.target.result + '" class="img-thumbnail" style="max-height: 150px;">');
                }.bind(this);
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush

@push('css')
<style>
    .empty-logo {
        border: 2px dashed #ddd;
        border-radius: 5px;
    }
</style>
@endpush
