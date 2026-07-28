@extends('layouts.admin.app')
@section('title', __('letters'))
@push('style')
    <link href="{{ url('assets/vendors/flatpickr/flatpickr.css') }}" rel="stylesheet" />
    <link href="{{ url('assets/vendors/select2/select2.min.css') }}" rel="stylesheet" />

    <style>
        .ck-editor__editable_inline {
            min-height: 200px;
        }
    </style>
@endpush

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div
            class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start flex-wrap gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">
                    {{ __('letters') }}
                </h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <a class="text-secondary-foreground hover:text-primary" href="html/demo9.html">
                        {{ __('Home') }}
                    </a>
                    <span class="text-muted-foreground text-sm">
                        /
                    </span>
                    <span class="text-secondary-foreground">
                        {{ __('letters') }}
                    </span>
                    <span class="text-muted-foreground text-sm">
                        /
                    </span>
                    <span class="text-secondary-foreground">
                        {{ __('add_new') }}
                    </span>
                </div>
            </div>
            <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                <a href="{{ route('admin.letters') }}" class="kt-btn kt-btn-primary">
                    <i class="ki-outline ki-arrow-left text-lg me-1">
                    </i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </div>
@endsection



@php

    $selectedRefs = [];

    if (!empty($letter->customer_number)) {
        $selectedRefs[] = 'customer_number';
    }
    if (!empty($letter->contract_number)) {
        $selectedRefs[] = 'contract_number';
    }
    if (!empty($letter->file_number)) {
        $selectedRefs[] = 'file_number';
    }
    if (!empty($letter->account_number)) {
        $selectedRefs[] = 'account_number';
    }
    if (!empty($letter->tax_number)) {
        $selectedRefs[] = 'tax_number';
    }

    $selectedCustomer = explode(',', $letter->customer_number ?? '');
    $selectedContract = explode(',', $letter->contract_number ?? '');
    $selectedFile = explode(',', $letter->file_number ?? '');
    $selectedAccount = explode(',', $letter->account_number ?? '');
    $selectedTax = explode(',', $letter->tax_number ?? '');

@endphp

@section('content')
    <div class="grid lg:grid-cols-3 gap-5 lg:gap-7.5 items-stretch">
        <div class="lg:col-span-3">
            <div class="flex flex-col gap-5 lg:gap-7.5">
                <form action="{{ route('admin.letters.update', $letter) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="kt-card mt-3">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">{{ __('Letter input') }}</h3>
                        </div>

                        <div class="kt-card-content ">
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5 p-3">
                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('letter') }}
                                        {{ __('type') }}</span>
                                    <div class="grow min-w-48">
                                        <select class="select2 kt-input w-full" name="letter_type" id="letter_type">
                                            <option value="{{ \App\Models\Letter::TYPE_RECEIVED }}"
                                                {{ \App\Models\Letter::TYPE_RECEIVED == $letter->letter_type ? 'selected' : '' }}>
                                                {{ __('received') }}
                                            </option>
                                            <option value="{{ \App\Models\Letter::TYPE_SENT }}"
                                                {{ \App\Models\Letter::TYPE_SENT == $letter->letter_type ? 'selected' : '' }}>
                                                {{ __('sent') }}
                                            </option>
                                        </select>
                                        @error('letter_type')
                                            <span
                                                class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5 p-3">
                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('letter') }}
                                        {{ __('date') }}</span>
                                    <div class="grow min-w-48">
                                        <input type="text" name="date" class="kt-input w-full datepicker"
                                            value="{{ $letter->date }}">
                                        @error('date')
                                            <span
                                                class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('letter') }}
                                        {{ __('Category') }}</span>
                                    <div class="grow min-w-48">
                                        <select class="select2 kt-input w-full" name="letter_category" id="letter_category">
                                            <option value="{{ \App\Models\Letter::CATEGORY_LETTER }}"
                                                {{ \App\Models\Letter::CATEGORY_LETTER == $letter->letter_category ? 'selected' : '' }}>
                                                {{ __('Letter') }}
                                            </option>
                                            <option value="{{ \App\Models\Letter::CATEGORY_MAIL }}"
                                                {{ \App\Models\Letter::CATEGORY_MAIL == $letter->letter_category ? 'selected' : '' }}>
                                                {{ __('Mail') }}
                                            </option>
                                            <option value="{{ \App\Models\Letter::CATEGORY_RECOMMENDATION }}"
                                                {{ \App\Models\Letter::CATEGORY_RECOMMENDATION == $letter->letter_category ? 'selected' : '' }}>
                                                {{ __('Recommendation') }}
                                            </option>
                                        </select>

                                        @error('letter_category')
                                            <span
                                                class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                @php
                                    $selectedFirmIds = explode(',', $letter->firm_id);
                                @endphp
                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('firm') }}</span>
                                    <div class="grow min-w-48">
                                        <select class="select2 kt-input w-full" name="firm_id[]" id="firm_id" multiple>
                                            @foreach ($firms as $firm)
                                                <option value="{{ $firm->id }}"
                                                    {{ in_array($firm->id, $selectedFirmIds) ? 'selected' : '' }}>
                                                    {{ $firm->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('firm_id')
                                            <span
                                                class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('party') }}</span>
                                    <div class="grow min-w-48">
                                        <select class="select2 kt-input w-full" name="party_id" id="party_id">
                                            @foreach ($parties as $party)
                                                <option value="{{ $party->id }}"
                                                    {{ $letter->party_id == $party->id ? 'selected' : '' }}>
                                                    {{ $party->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('party_id')
                                            <span
                                                class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="kt-card mt-3">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">{{ __('Letter Subject') }}</h3>
                        </div>
                        <div class="kt-card-content ">
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5 p-3">
                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('subject') }}</span>
                                    <div class="grow min-w-48">
                                        <select class="select2 kt-input w-full" name="subject" id="subject_id">
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->subject }}"
                                                    {{ $letter->subject == $subject->subject ? 'selected' : '' }}>
                                                    {{ $subject->subject }}</option>
                                            @endforeach
                                        </select>
                                        @error('subject')
                                            <span
                                                class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5 p-3">
                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('reference_number') }}</span>
                                    <div class="grow min-w-48">
                                        <select name="reference_number[]" id="reference_number" multiple
                                            class="select2 kt-input w-full">
                                            @php $refOld = collect(old('reference_number', [])); @endphp
                                            @foreach (['customer_number', 'contract_number', 'file_number', 'account_number', 'tax_number'] as $ref)
                                                <option value="{{ $ref }}"
                                                    {{ in_array($ref, $selectedRefs) ? 'selected' : '' }}>
                                                    {{ __($ref) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('reference_number')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('tags') }}</span>
                                    <div class="grow min-w-48">
                                        <select name="tags[]" id="tags" multiple class="select2 kt-input w-full">
                                            @php
                                                $selectedTags = $letter->tags->pluck('name')->toArray();
                                            @endphp
                                            @foreach ($tags as $tag)
                                                <option value="{{ $tag->name }}"
                                                    {{ in_array($tag->name, old('tags', $selectedTags)) ? 'selected' : '' }}>
                                                    {{ $tag->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tags')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5 p-3">
                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5" id="customer_number_row">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('customer_number') }}</span>
                                    <div class="grow min-w-48">
                                        <select name="customer_number[]" id="customer_number" multiple
                                            class="kt-input w-full">
                                            @foreach ($customer_numbers as $cn)
                                                <option value="{{ $cn->number }}"
                                                    {{ in_array($cn->number, $selectedCustomer) ? 'selected' : '' }}>
                                                    {{ $cn->number }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('customer_number')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5" id="contract_number_row">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('contract_number') }}</span>
                                    <div class="grow min-w-48">
                                        <select name="contract_number[]" id="contract_number" multiple
                                            class="kt-input w-full">
                                            @foreach ($contract_numbers as $item)
                                                <option value="{{ $item->number }}"
                                                    {{ in_array($item->number, $selectedContract) ? 'selected' : '' }}>
                                                    {{ $item->number }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('contract_number')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5" id="file_number_row">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('file_number') }}</span>
                                    <div class="grow min-w-48">
                                        <select name="file_number[]" id="file_number" multiple class="kt-input w-full">
                                            @foreach ($file_numbers as $item)
                                                <option value="{{ $item->number }}"
                                                    {{ in_array($item->number, $selectedFile) ? 'selected' : '' }}>
                                                    {{ $item->number }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('file_number')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5" id="account_number_row">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('account_number') }}</span>
                                    <div class="grow min-w-48">
                                        <select name="account_number[]" id="account_number" multiple
                                            class="kt-input w-full">
                                            @foreach ($account_numbers as $item)
                                                <option value="{{ $item->number }}"
                                                    {{ in_array($item->number, $selectedAccount) ? 'selected' : '' }}>
                                                    {{ $item->number }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('account_number')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5" id="tax_number_row">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('tax_number') }}</span>
                                    <div class="grow min-w-48">
                                        <select name="tax_number[]" id="tax_number" multiple class="kt-input w-full">
                                            @foreach ($tax_numbers as $item)
                                                <option value="{{ $item->number }}"
                                                    {{ in_array($item->number, $selectedTax) ? 'selected' : '' }}>
                                                    {{ $item->number }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tax_number')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div data-kt-accordion="true" data-kt-accordion-expand-all="true"
                        class="kt-accordion kt-accordion-outline mt-3">
                        <div data-kt-accordion-item="true" class="kt-accordion-item">
                            <button id="accordion_toggle_2" data-kt-accordion-toggle="true"
                                aria-controls="accordion_case_content" class="kt-accordion-toggle">
                                <span class="kt-accordion-title">{{ __('Case') }}</span><span aria-hidden="true"
                                    class="kt-accordion-indicator"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-plus kt-accordion-indicator-on" aria-hidden="true">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5v14"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-minus kt-accordion-indicator-off" aria-hidden="true">
                                        <path d="M5 12h14"></path>
                                    </svg>
                                </span>
                            </button>

                            <div class="kt-accordion-content hidden" aria-labelledby="accordion_toggle_2"
                                id="accordion_case_content">
                                <div class="kt-accordion-wrapper">
                                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5 p-3">
                                        <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                            <span class="kt-form-label max-w-32 w-full">{{ __('case') }}</span>
                                            <div class="grow min-w-48">
                                                @php
                                                    function renderCaseOptions($cases, $prefix = '', $selectedId = null)
                                                    {
                                                        foreach ($cases as $case) {
                                                            $isSelected = $selectedId == $case->id ? 'selected' : '';
                                                            echo '<option value="' .
                                                                $case->id .
                                                                '" ' .
                                                                $isSelected .
                                                                '>' .
                                                                $prefix .
                                                                $case->case_name .
                                                                ' ' .
                                                                $case->case_number .
                                                                '</option>';
                                                            if ($case->children->isNotEmpty()) {
                                                                renderCaseOptions(
                                                                    $case->children,
                                                                    $prefix . '-- ',
                                                                    $selectedId,
                                                                );
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                <select class="select2 kt-input w-full" name="letter_case_id"
                                                    id="letter_case_id">
                                                    <option value="">{{ __('Select') }}</option>
                                                    @php renderCaseOptions($cases, '', $letter->letter_case_id) @endphp
                                                </select>

                                                @error('letter_case_id')
                                                    <span
                                                        class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                                @enderror

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="kt-card mt-3">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Document</h3>
                        </div>
                        <div class="kt-card-content ">
                            <div class="grid grid-cols-1 md:grid-cols-1 xl:grid-cols-1 gap-5 p-3">
                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('letter') }}</span>
                                    <div class="grow min-w-48">
                                        <input type="file" name="letter" class="kt-input w-full">
                                        <a href="{{ route('letters.get', $letter) }}"
                                            class="kt-btn kt-btn-sm kt-btn-mono">
                                            <i class="ki-filled ki-exit-down"></i>
                                        </a>
                                        @error('letter')
                                            <span
                                                class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-1 xl:grid-cols-1 gap-5 p-3">
                                <div class="flex items-start flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full pt-2">{{ __('description') }}</span>
                                    <div class="grow min-w-48">
                                        <textarea name="description" id="description" class="ckeditor">{{ $letter->description }}</textarea>
                                        @error('description')
                                            <span
                                                class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="kt-card mt-3">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Reply </h3>
                        </div>

                        <div class="kt-card-content ">
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5 p-3">
                                @if (auth()->user()->role === \App\Models\User::ROLE_ADMIN)
                                    <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                        <span class="kt-form-label max-w-32 w-full">{{ __('private') }}</span>
                                        <div class="grow min-w-48">
                                            <select class="kt-input w-full" name="is_private" id="is_private">
                                                <option value="0" {{ !$letter->is_private ? 'selected' : '' }}>
                                                    {{ __('No') }} </option>
                                                <option value="1" {{ !$letter->is_private ? 'selected' : '' }}>
                                                    {{ __('Yes') }} </option>
                                            </select>
                                            @error('is_private')
                                                <span
                                                    class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif


                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('Reply required') }}</span>
                                    <div class="grow min-w-48">
                                        <select class="kt-input w-full" name="reply_required" id="reply_required">
                                            <option value="0" {{ is_null($letter->reply_date) ? 'selected' : '' }}>
                                                {{ __('No') }} </option>
                                            <option value="1" {{ !is_null($letter->reply_date) ? 'selected' : '' }}>
                                                {{ __('Yes') }} </option>
                                        </select>
                                        @error('reply_required')
                                            <span
                                                class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                                    <span class="kt-form-label max-w-32 w-full">{{ __('reply_date') }}</span>
                                    <div class="grow min-w-48">
                                        <input type="text" name="reply_date" class="kt-input w-full datepicker"
                                            value="{{ $letter->reply_date }}">
                                        @error('reply_date')
                                            <span
                                                class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="kt-card-footer justify-center">
                            <button class="kt-btn kt-btn-primary" type="submit"><i
                                    class="ki-filled ki-check-circle fs-5"></i>
                                {{ __('Save') }}</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ url('assets/vendors/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ url('assets/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ url('assets/vendors/ckeditor/ckeditor.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('.select2').select2({
                placeholder: "{{ __('select') }}",
                width: '100%',
                theme: 'default'
            });

            flatpickr(".datepicker", {
                enableTime: true,
                dateFormat: "Y-m-d"
            });

            $('#subject_id, #customer_number, #contract_number, #file_number, #account_number, #tax_number, #tags')
                .select2({
                    tags: true,
                    placeholder: "{{ __('select') }}",
                    allowClear: true,
                    width: 'resolve'
                });

            toggleRefFields();
            $('#reference_number').on('change', toggleRefFields);

        });

        function toggleRefFields() {
            const selected = $('#reference_number').val() || [];
            const map = ['customer_number', 'contract_number', 'file_number', 'account_number', 'tax_number'];
            map.forEach(function(key) {
                const row = $('#' + key + '_row');
                if (selected.includes(key)) {
                    row.show();
                } else {
                    row.hide();
                    $('#' + key).val(null).trigger('change');
                }
            });
        }
    </script>
@endpush
