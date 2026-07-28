@extends('layouts.admin.app')
@section('title', __('letters'))

@push('style')
    <link href="{{ URL('assets/vendors/jstree/jstree.min.css') }}" rel="stylesheet">
    <style>
        #partyCasesTree {
            height: 100vh;
            overflow-y: auto;
            overflow-x: auto;
            padding: 10px;
            box-sizing: border-box;
            width: 100%;
            max-width: 100%;
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
                        {{ __('list') }}
                    </span>
                </div>
            </div>
            <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                <a href="{{ route('admin.letters.create') }}" class="kt-btn kt-btn-primary">
                    <i class="ki-outline ki-plus-circle text-lg me-1">
                    </i>
                    {{ __('Add new') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 lg:gap-7.5">
        <div class="col-span-1">
            <div class="flex flex-col gap-5 lg:gap-7.5">
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">
                            Parties
                        </h3>
                    </div>
                    <div class="kt-card-content flex flex-col gap-5">

                        <div class="relative kt-input">
                            <i class="ki-filled ki-magnifier"></i>
                            <input class="min-w-0 w-full" placeholder="{{ __('Search') }}" type="text" id="treeSearch"
                                autocomplete="off" />
                        </div>

                        <div class="flex flex-col gap-5" id="partyCasesTree">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-2">
            <div class="flex flex-col gap-5 lg:gap-7.5">
                <div class="kt-card kt-card-grid h-full min-w-full">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">{{ __('List') }} <br>
                            @if (isset($case))
                                <small><strong>{{ __('case') }}: {{ $case->name_number }}</strong></small>
                            @elseif(isset($party))
                                <small><strong>{{ __('party') }}: {{ $party->name }}</strong></small>
                            @elseif(isset($firm))
                                <small><strong>{{ __('firm') }}: {{ $firm->name }}</strong></small>
                            @endif

                        </h3>
                        <div class="kt-input max-w-48">
                            <i class="ki-filled ki-magnifier"></i>
                            <input type="text" placeholder="Search…" data-kt-datatable-search="#letters_table"
                                class="kt-input">
                        </div>
                    </div>

                    <div class="kt-card-table" data-kt-datatable="true" data-kt-datatable-page-size="5" id="letters_table">
                        <div class="kt-table-wrapper kt-scrollable-x-auto h-full ">
                            <table class="kt-table" data-kt-datatable-table="true">
                                <thead>
                                    <tr>
                                        <th data-kt-datatable-column="id">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">#</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th data-kt-datatable-column="firm">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">{{ __('firm') }}</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th data-kt-datatable-column="type">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">{{ __('type') }}</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th data-kt-datatable-column="category">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">{{ __('Category') }}</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th data-kt-datatable-column="party">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">{{ __('party') }}</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th data-kt-datatable-column="subject">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">{{ __('subject') }}</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th data-kt-datatable-column="case">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">{{ __('case') }}</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th data-kt-datatable-column="date">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">{{ __('date') }}</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th data-kt-datatable-column="description">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">{{ __('description') }}</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>

                                        <th data-kt-datatable-column="user">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">{{ __('user') }}</span>
                                                <span class="kt-table-col-sort"></span>
                                            </span>
                                        </th>
                                        <th data-kt-datatable-column="action">
                                            <span class="kt-table-col">
                                                <span class="kt-table-col-label">{{ __('action') }}</span>
                                            </span>
                                        </th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach ($letters as $letter)
                                        <tr>
                                            <td>{{ $letter->id }}</td>
                                            <td>
                                                @php
                                                    $firms = $letter->firms();
                                                @endphp
                                                @if ($firms->isNotEmpty())
                                                    @foreach ($firms as $firm)
                                                        <a href="{{ route('admin.firms.letters', $firm) }}"
                                                            class="text-primary">
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
                                                    <a href="{{ route('admin.parties.letters', $letter->party) }}"
                                                        class="text-primary">{{ $letter->party->name }}</a>
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
                                                {{ $letter->user->name }}
                                                <br>({{ $letter->is_private ? __('private') : __('public') }})
                                            </td>

                                            <td class="text-center flex justify-center space-x-2">
                                                <a href="javascript:;"
                                                    onclick="viewLetter('{{ route('letters.get', $letter) }}')"
                                                    class="kt-btn kt-btn-sm kt-btn-mono">
                                                    <i class="ki-filled ki-exit-down"></i>
                                                </a>

                                                <a href="{{ route('admin.letters.edit', $letter->id) }}"
                                                    class="kt-btn kt-btn-sm kt-btn-primary">
                                                    <i class="ki-filled ki-pencil"></i>
                                                </a>

                                                <a href="javascript:;"
                                                    data-href="{{ route('admin.letters.delete', $letter->id) }}"
                                                    class="delete-btn kt-btn kt-btn-sm kt-btn-destructive">
                                                    <i class="ki-filled ki-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="kt-datatable-toolbar flex flex-wrap justify-between mt-3">
                            <div class="kt-datatable-length">
                                Show
                                <select class="kt-select kt-select-sm w-16" data-kt-datatable-size="true"
                                    name="perpage"></select>
                                per page
                            </div>
                            <div class="kt-datatable-info">
                                <span data-kt-datatable-info="true"></span>
                                <div class="kt-datatable-pagination" data-kt-datatable-pagination="true"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script src="{{ URL('assets/vendors/jstree/jstree.min.js') }}"></script>
    <script>
        $(function() {
            const treeData = @json($partyCasesTree);
            let firstPartyId = @json($firstPartyId);
            $('#partyCasesTree').jstree({
                core: {
                    data: treeData,
                    check_callback: true,
                    themes: {
                        dots: true,
                        icons: true,
                        responsive: true
                    }
                },
                search: {
                    show_only_matches: true,
                    show_only_matches_children: true
                },
                plugins: ['wholerow', 'search', 'types']
            }).on('ready.jstree', function() {
                if (firstPartyId) {
                    $(this).jstree('select_node', firstPartyId);
                }
            });

            function debounce(fn, delay) {
                let timeout;
                return function() {
                    const args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            $('#treeSearch').on('keyup', debounce(function() {
                const query = $(this).val();
                $('#partyCasesTree').jstree(true).search(query);
            }, 250));

            $('#partyCasesTree').on("select_node.jstree", function(e, data) {
                let nodeData = data.node.data;
                let type = nodeData.type; // "party" or "case"
                let id = nodeData.id;

                $.ajax({
                    url: "{{ route('admin.letters.filter') }}", // create this route
                    type: "GET",
                    data: {
                        type: type,
                        id: id
                    },
                    success: function(response) {
                        // Replace table body with new rows
                        $('#letters_table tbody').html(response.html);
                    }
                });
            });

        });
    </script>
@endpush
