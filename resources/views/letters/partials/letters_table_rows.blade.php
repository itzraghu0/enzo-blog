@foreach ($letters as $letter)
    <tr>
        <td>{{ $letter->id }}</td>
        <td>
            @php
                $firms = $letter->firms();
            @endphp
            @if ($firms->isNotEmpty())
                @foreach ($firms as $firm)
                    <a href="{{ route('admin.firms.letters', $firm) }}" class="text-primary" data-entity-type="firm"
                        data-entity-id="{{ $firm->id }}">
                        {{ $firm->name }}
                    </a>
                    <br>
                    <br>
                @endforeach
            @else
                -
            @endif
        </td>
        <td>{{ $letter->letter_type === \App\Models\Letter::TYPE_RECEIVED ? __('received') : __('sent') }}
        </td>
        <td>
            @if ($letter->letter_category === \App\Models\Letter::CATEGORY_LETTER)
                {{ __('Letter') }}
            @elseif($letter->letter_category === \App\Models\Letter::CATEGORY_MAIL)
                {{ __('Mail') }}
            @elseif($letter->letter_category === \App\Models\Letter::CATEGORY_RECOMMENDATION)
                {{ __('Recommendation') }}
            @endif
        </td>

        <td>
            @if (!is_null($letter->party))
                <a href="{{ route('admin.parties.letters', $letter->party) }}" class="text-primary"
                    data-entity-type="party" data-entity-id="{{ $letter->party->id }}">
                    {{ $letter->party->name }}
                </a>
            @else
                -
            @endif

        </td>
        <td>
            {{ $letter->subject }} <br>

            @if (!is_null($letter->customer_number))
                <strong>{{ __('Customer No.') }}:</strong>
                {{ $letter->customer_number }}<br>
            @endif

            @if (!is_null($letter->contract_number))
                <strong>{{ __('Contract No.') }}:</strong>
                {{ $letter->contract_number }}<br>
            @endif

            @if (!is_null($letter->file_number))
                <strong>{{ __('File No.') }}:</strong> {{ $letter->file_number }}<br>
            @endif

            @if (!is_null($letter->account_number))
                <strong>{{ __('Account No.') }}:</strong>
                {{ $letter->account_number }}<br>
            @endif

            @if (!is_null($letter->tax_number))
                <strong>{{ __('Tax No.') }}:</strong> {{ $letter->tax_number }}<br>
            @endif
        </td>

        <td>
            @if (!is_null($letter->case))
                <a href="{{ route('admin.cases.letters', $letter->case) }}"
                    class="text-primary">{{ $letter->case->case_name }}
                    {{ $letter->case->case_number }}</a>
            @else
                <span class="text-muted">{{ __('no_case') }}</span>
            @endif
        </td>

        <td>
            {{ $letter->letter_type === \App\Models\Letter::TYPE_RECEIVED ? __('received_at') : __('sent_at') }}
            <br>
            {{ $letter->date }}
        </td>
        <td>{!! Str::limit(strip_tags($letter->description), 50) !!}</td>
        <td>
            <a href="{{ route('admin.users.view', $letter->user->id) }}" class="text-primary" data-entity-type="user"
                data-entity-id="{{ $letter->user->id }}">
                {{ $letter->user->name }}
            </a>
            <br>({{ $letter->is_private ? __('private') : __('public') }})
        </td>
        <td>
            @foreach ($letter->tags as $tag)
                <span class="kt-badge kt-badge-outline kt-badge-primary">{{ $tag->name }}</span>
            @endforeach
        </td>

        <td class="text-center flex justify-center space-x-2">
            <a href="javascript:;" onclick="viewLetter('{{ route('letters.get', $letter) }}')"
                class="kt-btn kt-btn-sm kt-btn-mono">
                <i class="ki-filled ki-exit-down"></i>
            </a>

            <a href="{{ route('admin.letters.edit', $letter->id) }}" class="kt-btn kt-btn-sm kt-btn-primary">
                <i class="ki-filled ki-pencil"></i>
            </a>

            <a href="javascript:;" data-href="{{ route('admin.letters.delete', $letter->id) }}"
                class="delete-btn kt-btn kt-btn-sm kt-btn-destructive">
                <i class="ki-filled ki-trash"></i>
            </a>
        </td>
    </tr>
@endforeach
