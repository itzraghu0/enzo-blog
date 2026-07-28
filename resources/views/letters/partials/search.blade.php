<div class="grid grid-cols-1 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-4 gap-6 p-5">
    <div>
        <label for="letter_type"
            class="block text-sm font-medium text-gray-700">{{ __('Letter Type') }}</label>
        <select id="letter_type" name="letter_type"
            class="select2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/50">
            <option value="" {{ request('letter_type') === null ? 'selected' : '' }}>
                {{ __('select') }}</option>
            <option value="{{ \App\Models\Letter::TYPE_RECEIVED }}"
                {{ request('letter_type', 'null') === (string) \App\Models\Letter::TYPE_RECEIVED ? 'selected' : '' }}>
                {{ __('received') }}
            </option>
            <option value="{{ \App\Models\Letter::TYPE_SENT }}"
                {{ request('letter_type', 'null') === (string) \App\Models\Letter::TYPE_SENT ? 'selected' : '' }}>
                {{ __('sent') }}
            </option>
        </select>
    </div>

    <div>
        <label for="letter_case_id"
            class="block text-sm font-medium text-gray-700">{{ __('Case') }}</label>
        <select name="letter_case_id"
            class="select2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/50">
            <option value="">{{ __('select') }}</option>
            @foreach ($cases as $caseItem)
                <option value="{{ $caseItem->id }}"
                    {{ request('letter_case_id') == $caseItem->id ? 'selected' : '' }}>
                    {{ $caseItem->case_name ?? ($caseItem->caseItem_name ?? '') }} {{ $caseItem->case_number }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="party_id"
            class="block text-sm font-medium text-gray-700">{{ __('Party') }}</label>
        <select name="party_id"
            class="select2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/50">
            <option value="">{{ __('select') }}</option>
            @foreach ($parties as $partyItem)
                <option value="{{ $partyItem->id }}"
                    {{ request('party_id') == $partyItem->id ? 'selected' : '' }}>
                    {{ $partyItem->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="firm_id"
            class="block text-sm font-medium text-gray-700">{{ __('Firm') }}</label>
        <select name="firm_id"
            class="select2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/50">
            <option value="">{{ __('select') }}</option>
            @foreach ($firms as $firmItem)
                <option value="{{ $firmItem->id }}"
                    {{ request('firm_id') == $firmItem->id ? 'selected' : '' }}>
                    {{ $firmItem->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="start_date"
            class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
        <label for="" class="kt-input">
            <input type="text" name="start_date" value="{{ request('start_date') }}"
                class="datepicker mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/50" />
        </label>
    </div>

    <div>
        <label for="end_date"
            class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
        <label for="" class="kt-input">
            <input type="text" name="end_date" value="{{ request('end_date') }}"
                class="datepicker mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/50" />
        </label>
    </div>

    <div>
        <label for="keyword"
            class="block text-sm font-medium text-gray-700">{{ __('Keyword Search') }}</label>
        <label for="" class="kt-input">
            <input type="text" name="keyword" placeholder="{{ __('Keyword Search') }}"
                value="{{ request('keyword') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/50" />
        </label>
    </div>

    <div>
        <label for="tag_id"
            class="block text-sm font-medium text-gray-700">{{ __('tags') }}</label>
        <select name="tag_id"
            class="select2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/50">
            <option value="">{{ __('select') }}</option>
            @if (isset($tags))
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}"
                        {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                        {{ $tag->name }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>

    @if (isset($buttons))
        {!! $buttons !!}
    @else
        <div class="flex items-end gap-2 mt-1">
            <button type="submit" class="kt-btn kt-btn-primary">
                <i class="ki-outline ki-magnifier fs-2">
                </i>{{ __('search') }}
            </button>
            <a href="{{ route('admin.letters') }}" class="kt-btn kt-btn-primary">
                <i class="ki-outline ki-arrows-loop fs-2">
                </i> {{ __('reset') }}
            </a>
        </div>
    @endif
</div>
