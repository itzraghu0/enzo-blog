@php
    $isEdit = isset($staff) && $staff->exists;
    $verifiedValue = old('email_verified_at', $staff->email_verified_at ?? true);
@endphp

<form action="{{ $formAction }}" method="post">
    @csrf
    @if (($formMethod ?? 'POST') !== 'POST')
        @method($formMethod)
    @endif

    <div class="kt-card">
        <div class="kt-card-header">
            <h3 class="kt-card-title">{{ __('Staff account') }}</h3>
        </div>
        <div class="kt-card-content grid gap-5">
            <p class="text-sm text-foreground">
                {{ __('Create and maintain admin staff accounts that can manage blog content, media, categories, and members.') }}
                <br class="hidden sm:block">
                {{ __('Assign the correct role and keep access verified only for trusted staff users.') }}
            </p>

            <div class="kt-form-item">
                <label class="kt-form-label">
                    {{ __('Name') }} <span class="text-danger">*</span>
                </label>
                <div class="kt-form-control">
                    <input type="text" name="name" class="kt-input w-full" value="{{ old('name', $staff->name ?? '') }}"
                        placeholder="{{ __('Enter staff name') }}" required>
                </div>
                <div class="kt-form-description">{{ __('Enter the staff member display name.') }}</div>
                @error('name')
                    <div class="kt-form-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="kt-form-item">
                <label class="kt-form-label">
                    {{ __('Email') }} <span class="text-danger">*</span>
                </label>
                <div class="kt-form-control">
                    <input type="email" name="email" class="kt-input w-full" value="{{ old('email', $staff->email ?? '') }}"
                        placeholder="{{ __('Enter staff email') }}" required>
                </div>
                <div class="kt-form-description">{{ __('This email is used for staff login.') }}</div>
                @error('email')
                    <div class="kt-form-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="kt-form-item">
                <label class="kt-form-label">
                    {{ __('Role') }} <span class="text-danger">*</span>
                </label>
                <div class="kt-form-control">
                    <select name="role" class="kt-input w-full" required>
                        @foreach ($roleOptions as $value => $label)
                            <option value="{{ $value }}" @selected((string) old('role', $staff->role ?? \App\Models\User::ROLE_ADMIN) === (string) $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="kt-form-description">{{ __('Choose the permission level for this staff account.') }}</div>
                @error('role')
                    <div class="kt-form-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="kt-form-item">
                <label class="kt-form-label">
                    {{ __('Email verified') }}
                </label>
                <div class="kt-form-control">
                    <input type="hidden" name="email_verified_at" value="0">
                    <label class="kt-label">
                        {{ __('Allow this staff user to login') }}
                        <input type="checkbox" class="kt-switch" name="email_verified_at" value="1"
                            @checked((bool) $verifiedValue)>
                    </label>
                </div>
                <div class="kt-form-description">{{ __('Unverified staff cannot access protected admin routes.') }}</div>
                @error('email_verified_at')
                    <div class="kt-form-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="kt-form-item">
                <label class="kt-form-label">
                    {{ $isEdit ? __('New password') : __('Password') }}
                    @unless($isEdit)
                        <span class="text-danger">*</span>
                    @endunless
                </label>
                <div class="kt-form-control">
                    <input type="password" name="password" class="kt-input w-full" autocomplete="new-password"
                        placeholder="{{ $isEdit ? __('Leave blank to keep current password') : __('Enter password') }}"
                        @required(! $isEdit)>
                </div>
                <div class="kt-form-description">{{ $isEdit ? __('Only fill this when changing the password.') : __('Minimum 8 characters.') }}</div>
                @error('password')
                    <div class="kt-form-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="kt-form-item mb-2.5">
                <label class="kt-form-label">
                    {{ __('Confirm password') }}
                    @unless($isEdit)
                        <span class="text-danger">*</span>
                    @endunless
                </label>
                <div class="kt-form-control">
                    <input type="password" name="password_confirmation" class="kt-input w-full" autocomplete="new-password"
                        placeholder="{{ __('Repeat password') }}" @required(! $isEdit)>
                </div>
                <div class="kt-form-description">{{ __('Repeat the same password for validation.') }}</div>
            </div>

            <div class="flex justify-end flex-wrap gap-2.5 border-t border-border pt-5">
                <a href="{{ route('admin.staff.index') }}" class="kt-btn kt-btn-mono">
                    {{ __('Back') }}
                </a>
                <button type="submit" class="kt-btn kt-btn-primary">
                    {{ $submitLabel }}
                </button>
            </div>
        </div>
    </div>
</form>
