@extends('layouts.admin.app')
@section('title', __('letters'))
@push('style')
    <link href="{{ URL('assets/vendors/sweetalert/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendors/flatpickr/flatpickr.css') }}" rel="stylesheet" />
    <link href="{{ url('assets/vendors/select2/select2.min.css') }}" rel="stylesheet" />
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
    <div class="grid lg:grid-cols-3 gap-5 lg:gap-7.5 items-stretch">
        <div class="lg:col-span-3">
            <div class="kt-card kt-card-grid h-full min-w-full">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">{{ __('Search') }} {{ __('letters') }}</h3>
                </div>

                <div class="kt-card-content">
                    <form action="{{ route('admin.letters') }}" method="GET" class="space-y-6">
                        @include('letters.partials.search')
                    </form>
                </div>
            </div>

        </div>

        <div class="lg:col-span-3">
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
                    <div class="kt-table-wrapper kt-scrollable-x-auto">
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
                                    <th data-kt-datatable-column="tags">
                                        <span class="kt-table-col">
                                            <span class="kt-table-col-label">{{ __('tags') }}</span>
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
                                                        class="text-primary" data-entity-type="firm"
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
                                                <a href="{{ route('admin.parties.letters', $letter->party) }}"
                                                    class="text-primary" data-entity-type="party"
                                                    data-entity-id="{{ $letter->party->id }}">{{ $letter->party->name }}</a>
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
                                        <td>
                                            @foreach ($letter->tags as $tag)
                                                <span
                                                    class="kt-badge kt-badge-outline kt-badge-primary">{{ $tag->name }}</span>
                                            @endforeach
                                        </td>

                                        <td class="text-center flex justify-center space-x-2">
                                            <a href="{{ route('letters.get', [$letter, 'download']) }}"
                                                class="kt-btn kt-btn-sm kt-btn-destructive">
                                                <i class="ki-filled ki-exit-down"></i>
                                            </a>

                                            <a href="javascript:;"
                                                onclick="viewLetter('{{ route('letters.get', $letter) }}')"
                                                class="kt-btn kt-btn-sm kt-btn-mono">
                                                <i class="ki-filled ki-exit-up"></i>
                                            </a>

                                            @if ($letter->meetings()->exists())
                                                <a href="{{ route('admin.meetings', ['letter_id' => $letter->id]) }}"
                                                    class="kt-btn kt-btn-sm bg-blue-500 text-white hover:bg-blue-600"
                                                    title="{{ __('View Meetings') }}">
                                                    <i class="ki-filled ki-people"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('admin.meetings', ['create_for_letter' => $letter->id]) }}"
                                                    class="kt-btn kt-btn-sm bg-green-500 text-white hover:bg-green-600"
                                                    title="{{ __('Create Meeting') }}">
                                                    <i class="ki-outline ki-message-add"></i>
                                                </a>
                                            @endif

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

    <button data-kt-dropdown-dismiss="true" data-kt-modal-toggle="#pdfViewerModal" class="hidden"
        id="pdfViewerModalBtn">Open Modal</button>
    <div class="kt-modal" data-kt-modal="true" data-kt-modal="true" data-kt-modal-backdrop-static="true"
        id="pdfViewerModal">
        <div class="kt-modal-content max-w-[70%] top-5 lg:top-[5%]">
            <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                    PDF
                </h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="kt-modal-body pe-2">
                <div class="kt-modal-body grid py-2" id="pdf-container" style="height: 80vh; overflow-y: auto;"></div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ url('assets/vendors//pdfjs/pdf.min.js') }}"></script>
    <script src="{{ URL('assets/vendors/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ url('assets/vendors/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ url('assets/vendors/select2/select2.min.js') }}"></script>

    <script>
        let pdfDoc = null;
        let keyword = $('[name="keyword"]').val();
        let firstMatchPage = null;

        function viewLetter(url) {
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                "{{ url('assets/vendors/pdfjs/pdf.worker.min.js') }}";

            pdfjsLib.getDocument(url).promise.then(pdf => {
                pdfDoc = pdf;
                document.getElementById("pdf-container").innerHTML = "";
                firstMatchPage = null;
                loadAllPages();
            });

            $('#pdfViewerModalBtn').click();
        }

        function loadAllPages() {
            let pagePromises = [];

            for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                let promise = pdfDoc.getPage(pageNum).then(page => {
                    let scale = 1.5;
                    let viewport = page.getViewport({
                        scale
                    });

                    let canvas = document.createElement("canvas");
                    canvas.classList.add("pdf-page");
                    canvas.dataset.pageNumber = pageNum;
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    document.getElementById("pdf-container").appendChild(canvas);

                    let ctx = canvas.getContext("2d");
                    let renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };

                    return page.render(renderContext).promise.then(() => {
                        if (keyword) {
                            return highlightKeyword(keyword, page, viewport, ctx, pageNum);
                        }
                    });
                });

                pagePromises.push(promise);
            }

            Promise.all(pagePromises).then(() => {
                if (firstMatchPage !== null) {
                    scrollToPage(firstMatchPage);
                }
            });
        }

        function highlightKeyword(keyword, page, viewport, ctx, pageNum) {
            return page.getTextContent().then(textContent => {
                ctx.font = "16px Arial";
                ctx.fillStyle = "rgba(255, 255, 0, 0.5)";

                let found = false;

                textContent.items.forEach(textItem => {
                    let text = textItem.str;
                    let lowerText = text.toLowerCase();
                    let lowerKeyword = keyword.toLowerCase();

                    let index = lowerText.indexOf(lowerKeyword);
                    if (index !== -1) {
                        let tx = pdfjsLib.Util.transform(viewport.transform, textItem.transform);
                        let x = tx[4];
                        let y = tx[5] - 10;
                        let wordWidth = ctx.measureText(text.substring(0, index + keyword.length)).width -
                            ctx.measureText(text.substring(0, index)).width;
                        let height = 10;

                        ctx.fillRect(x + ctx.measureText(text.substring(0, index)).width, y, wordWidth,
                            height);
                        found = true;
                    }
                });

                if (found && firstMatchPage === null) {
                    firstMatchPage = pageNum;
                }
            });
        }


        function scrollToPage(pageNum) {
            let pageElement = document.querySelector(`canvas[data-page-number="${pageNum}"]`);
            if (pageElement) {
                pageElement.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        }

        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                theme: 'default'
            });

            flatpickr(".datepicker", {
                enableTime: true,
                dateFormat: "Y-m-d"
            });
        });
    </script>

    <script>
        $(document).on('click', '.delete-btn', function() {
            const url = $(this).data('href');
            swal({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('You will not be able to recover this!') }}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{ __('Yes, delete it!') }}",
                cancelButtonText: "{{ __('Cancel') }}",
                closeOnConfirm: false
            }, function() {
                window.location.href = url;
            });
        });
    </script>
@endpush
