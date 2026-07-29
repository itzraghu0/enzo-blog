@php
    $isEdit = isset($staff) && $staff->exists;
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
        <div class="kt-card-content p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="kt-form-label">{{ __('Name') }}</label>
                    <input type="text" name="name" class="kt-input w-full" value="{{ old('name', $staff->name ?? '') }}">
                </div>
                <div>
                    <label class="kt-form-label">{{ __('Email') }}</label>
                    <input type="email" name="email" class="kt-input w-full" value="{{ old('email', $staff->email ?? '') }}">
                </div>
                <div>
                    <label class="kt-form-label">{{ __('Role') }}</label>
                    <select name="role" class="kt-input w-full">
                        @foreach ($roleOptions as $value => $label)
                            <option value="{{ $value }}" @selected((string) old('role', $staff->role ?? \App\Models\User::ROLE_ADMIN) === (string) $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="kt-form-label">{{ __('Email verified') }}</label>
                    <label class="kt-label whitespace-nowrap flex items-center gap-2">
                        <input type="checkbox" class="kt-switch kt-switch-sm" name="email_verified_at" value="1" @checked(old('email_verified_at', $staff->email_verified_at ?? false))>
                        <span>{{ __('Verified') }}</span>
                    </label>
                </div>
                <div>
                    <label class="kt-form-label">{{ $isEdit ? __('New password') : __('Password') }}</label>
                    <input type="password" name="password" class="kt-input w-full" autocomplete="new-password">
                </div>
                <div>
                    <label class="kt-form-label">{{ __('Confirm password') }}</label>
                    <input type="password" name="password_confirmation" class="kt-input w-full" autocomplete="new-password">
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 mt-5">
        <button type="submit" class="kt-btn kt-btn-primary">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.staff.index') }}" class="kt-btn kt-btn-mono">
            {{ __('Back') }}
        </a>
    </div>
</form>
