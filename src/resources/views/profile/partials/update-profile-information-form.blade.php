<section>

    <form method="post" action="{{ route('admin.profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="form-group mb-4">
            <label for="profile_image" class="font-weight-bold text-primary mb-1">
                <i class="fas fa-image me-1"></i> {{ __('Profile Image') }}
            </label>
            @if($user->profile_image)
                <div class="mb-2">
                    <img loading="lazy" src="{{ route('profile_img.show', ($user->profile_image)) }}"
                         alt="{{ $user->name }}" class="img-thumbnail" style="max-width: 150px;">
                </div>
            @endif
            <div class="custom-file">
                <input type="file" class="custom-file-input border border-primary @error('profile_image') is-invalid @enderror"
                       id="profile_image" name="profile_image" accept="image/*">
                <label class="custom-file-label" for="profile_image">
                </label>
                <small class="form-text text-muted">{{ __('Leave empty to keep current image. Recommended size: 300x300px. Max: 2MB') }}</small>
            </div>
            @error('profile_image')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="name" class="font-weight-bold text-primary mb-1">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="form-control"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @if($errors->has('name'))
                <div class="text-danger mt-2">{{ $errors->first('name') }}</div>
            @endif
        </div>

        <div class="form-group mb-4">
            <label for="email" class="font-weight-bold text-primary mb-1">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="form-control"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @if($errors->has('email'))
                <div class="text-danger mt-2">{{ $errors->first('email') }}</div>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="btn btn-link p-0 align-baseline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="form-group mb-4">
            <label for="phone" class="font-weight-bold text-primary mb-1">{{ __('Phone Number') }}</label>
            <input id="phone" name="phone" type="text" class="form-control"
                   value="{{ old('phone', $user->phone) }}" autocomplete="off">
            @if($errors->has('phone'))
                <div class="text-danger mt-2">{{ $errors->first('phone') }}</div>
            @endif
        </div>

        <div class="form-group text-end">
            <button type="submit" class="btn btn-encodex">
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</section>
