@extends('layouts.admin.app')
@section('title', __('letters'))
@push('style')
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
                        {{ __('search') }}
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

                    <form action="{{ route('admin.letters.search') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-5">
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
                                    @foreach ($cases as $case)
                                        <option value="{{ $case->id }}"
                                            {{ request('letter_case_id') == $case->id ? 'selected' : '' }}>
                                            {{ $case->case_name }} {{ $case->case_number }}
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
                                    @foreach ($parties as $party)
                                        <option value="{{ $party->id }}"
                                            {{ request('party_id') == $party->id ? 'selected' : '' }}>
                                            {{ $party->name }}
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
                                    @foreach ($firms as $firm)
                                        <option value="{{ $firm->id }}"
                                            {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                                            {{ $firm->name }}
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
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}"
                                            {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-end gap-2 mt-1">
                                <button type="submit" class="kt-btn kt-btn-primary">
                                    <i class="ki-outline ki-magnifier fs-2">
                                    </i>{{ __('search') }}
                                </button>
                                <a href="{{ route('admin.letters.search') }}" class="kt-btn kt-btn-primary">
                                    <i class="ki-outline ki-arrows-loop fs-2">
                                    </i> {{ __('reset') }}
                                </a>
                            </div>
                        </div>

                    </form>


                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-5">
                        @foreach ($letters as $letter)
                            <div class="bg-white shadow rounded overflow-hidden border border-gray-200 flex flex-col">
                                <div class="aspect-[4/3] overflow-hidden">
                                    <iframe src="{{ route('letters.get', $letter) }}"
                                        class="w-full h-full border-0"></iframe>
                                </div>

                                <div class="p-4 text-center flex-grow">
                                    <h6 class="text-base font-semibold text-gray-800">
                                        {!! Helper::highlightText($letter->subject, request('keyword')) !!}
                                    </h6>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {!! Helper::highlightText($letter->date, request('keyword')) !!}
                                    </p>
                                </div>

                                <div class="p-3 border-t flex justify-center gap-4">
                                    <a href="javascript:;" onclick="openPDF('{{ route('letters.get', $letter) }}')"
                                        class="kt-btn kt-btn-sm kt-btn-primary">
                                        <i class="ki-filled ki-bookmark"></i>
                                    </a>
                                    <a href="{{ route('letters.get', $letter) . '?download' }}"
                                        class="kt-btn kt-btn-sm kt-btn-mono">
                                        <i class="ki-filled ki-exit-down"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>

    <button data-kt-dropdown-dismiss="true" data-kt-modal-toggle="#pdfViewerModal" class="hidden"
        id="pdfViewerModalBtn">Open Modal</button>
    <div class="kt-modal" data-kt-modal="true" id="pdfViewerModal">
        <div class="kt-modal-content max-w-[70%] top-5 lg:top-[5%]">
            <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                    PDF
                </h3>
                <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost shrink-0" data-kt-modal-dismiss="true">
                    <i class="ki-filled ki-cross">
                    </i>
                </button>
            </div>
            <div class="kt-modal-body grid py-2" id="pdf-container" style="height: 80vh; overflow-y: auto;">

            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ url('assets/vendors//pdfjs/pdf.min.js') }}"></script>
    <script src="{{ url('assets/vendors/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ url('assets/vendors/select2/select2.min.js') }}"></script>
    <script>
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
        let pdfDoc = null;
        let keyword = $('[name="keyword"]').val();
        let firstMatchPage = null;

        function openPDF(url) {
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
    </script>
@endpush
