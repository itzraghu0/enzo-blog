@extends('layouts.admin.app')
@section('title', __('Edit Staff'))

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start flex-wrap gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Edit Staff') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Home') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Staff') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Edit') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @include('admin.staff._form', [
        'formAction' => route('admin.staff.update', $staff),
        'formMethod' => 'PUT',
        'submitLabel' => __('Update staff user'),
        'staff' => $staff,
        'roleOptions' => $roleOptions,
    ])
@endsection
