@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-end space-x-2">
        
        {{-- Tombol Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                &laquo; Prev
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#1a3675] transition-colors shadow-sm">
                &laquo; Prev
            </a>
        @endif

        {{-- Nomor Halaman --}}
        <div class="hidden sm:flex space-x-1">
            @foreach ($elements as $element)
                {{-- Tanda Tiga Titik (...) --}}
                @if (is_string($element))
                    <span class="px-4 py-2 text-sm font-semibold text-gray-500">{{ $element }}</span>
                @endif

                {{-- Array Nomor Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- Halaman Aktif (Warna Biru Tema) --}}
                            <span class="px-4 py-2 text-sm font-bold text-white bg-[#1a3675] rounded-lg shadow-md cursor-default">
                                {{ $page }}
                            </span>
                        @else
                            {{-- Halaman Lain --}}
                            <a href="{{ $url }}" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#1a3675] transition-colors shadow-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Tombol Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#1a3675] transition-colors shadow-sm">
                Next &raquo;
            </a>
        @else
            <span class="px-3 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                Next &raquo;
            </span>
        @endif
        
    </nav>
@endif