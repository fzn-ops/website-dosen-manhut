<x-layouts.main>
    <x-slot:title>
        {{ $activity->activity_name }} | DosenManhut
    </x-slot>

    <div class="bg-[#fafafc] w-full min-h-screen py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. Breadcrumb --}}
            <nav class="text-sm font-medium text-gray-900 mb-6 flex items-center gap-1.5">
                <a href="{{ url('/activities') }}" class="hover:text-[#1a3675] transition-colors">Aktivitas</a>
                <span>/</span>
                <span class="text-[#1a3675] underline underline-offset-4 decoration-[#1a3675]/40 truncate max-w-[200px] sm:max-w-none" title="{{ $activity->activity_name }}">
                    {{ Str::limit($activity->activity_name, 25) }}
                </span>
            </nav>

            {{-- ==========================================
                 LAYOUT UTAMA (Grid Berbaris)
                 ========================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:gap-x-10 gap-y-4 lg:gap-y-6 mt-2">
                
                {{-- =======================================
                     BARIS 1 KIRI: HEADER ARTIKEL
                     ======================================= --}}
                <div class="lg:col-span-8 flex flex-col justify-end">
                    @php
                        $actTypes = is_array($activity->activity_type) 
                            ? $activity->activity_type 
                            : (is_string($activity->activity_type) && str_starts_with($activity->activity_type, '[') 
                                ? (json_decode($activity->activity_type, true) ?? [$activity->activity_type]) 
                                : array_filter(array_map('trim', explode(',', (string)$activity->activity_type))));
                        $actTypes = array_values(array_filter($actTypes));
                    @endphp

                    @if(!empty($actTypes))
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            @foreach($actTypes as $cat)
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-[#1a3675]/10 text-[#1a3675] border border-[#1a3675]/15">
                                    {{ $cat }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <h1 class="text-3xl md:text-[40px] font-extrabold text-gray-900 tracking-tight mb-3 leading-tight">
                        {{ $activity->activity_name }}
                    </h1>
                    
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center text-sm md:text-base gap-3">
                        {{-- Kiri: Peran & Nama --}}
                        <div class="font-bold text-[#1a3675] flex items-center gap-2">
                            <span>{{ $activity->job ?? 'Partisipan' }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#1a3675]"></span>
                            <a href="{{ isset($activity->user->profileDosen) ? route('lecturer.show', $activity->user->profileDosen->id) : '#' }}" class="hover:text-[#1a3675] transition-colors hover:underline">
                                {{ $activity->user->name ?? 'Nama Dosen' }}
                            </a>
                        </div>
                        
                        {{-- Kanan: Tanggal --}}
                        <div class="font-bold text-[#1a3675] text-left sm:text-right">
                            @if($activity->activity_date_end && $activity->activity_date_end != $activity->activity_date_start)
                                {{ \Carbon\Carbon::parse($activity->activity_date_start)->locale('id')->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($activity->activity_date_end)->locale('id')->translatedFormat('d F Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($activity->activity_date_start)->locale('id')->translatedFormat('d F Y') }}
                            @endif
                        </div>
                    </div>
                    
                </div>

                {{-- BARIS 1 KANAN: KOSONG --}}
                <div class="hidden lg:block lg:col-span-4"></div>

                {{-- =======================================
                     BARIS 2 KIRI: GAMBAR & TEKS KONTEN
                     ======================================= --}}
                <div class="lg:col-span-8 flex flex-col">
                    @php
                        $mainPicUrl = $activity->primary_image_url ?? $activity->pictures?->first()?->path;
                    @endphp

                    <div class="w-full h-[250px] sm:h-[350px] md:h-[450px] bg-[#d9d9d9] rounded-2xl mb-4 overflow-hidden shadow-sm relative">
                        @if($mainPicUrl)
                            <img src="{{ $mainPicUrl }}" alt="{{ $activity->activity_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-500 bg-gray-200">
                                <span class="text-lg font-medium">Tidak ada gambar dokumentasi</span>
                            </div>
                        @endif
                    </div>

                    <!-- {{-- Galeri Foto Tambahan jika lebih dari 1 --}}
                    @if($activity->pictures && $activity->pictures->count() > 1)
                    <div class="grid grid-cols-3 gap-3 mb-8">
                        @foreach($activity->pictures as $galleryPic)
                            <div class="h-24 sm:h-32 rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                                <img src="{{ $galleryPic->path }}" alt="Dokumentasi" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="mb-4"></div>
                    @endif -->

                    {{-- Styling agar render konten deskripsi sama persis dengan RichTextEditor di ModalFormAktivitas --}}
                    <style>
                        .activity-description h1 {
                            font-size: 1.35rem;
                            font-weight: 700;
                            color: #183669;
                            margin: 0.75rem 0 0.35rem 0;
                            line-height: 1.25;
                        }
                        .activity-description h2 {
                            font-size: 1.2rem;
                            font-weight: 600;
                            color: #183669;
                            margin: 0.65rem 0 0.3rem 0;
                            line-height: 1.3;
                        }
                        .activity-description h3 {
                            font-size: 1.05rem;
                            font-weight: 600;
                            color: #183669;
                            margin: 0.5rem 0 0.25rem 0;
                            line-height: 1.35;
                        }
                        .activity-description p {
                            margin: 0.4rem 0;
                            line-height: 1.7;
                        }
                        .activity-description ul {
                            list-style-type: disc !important;
                            padding-left: 1.5rem !important;
                            margin: 0.5rem 0 !important;
                        }
                        .activity-description ol {
                            list-style-type: decimal !important;
                            padding-left: 1.5rem !important;
                            margin: 0.5rem 0 !important;
                        }
                        .activity-description li {
                            margin: 0.2rem 0;
                            display: list-item !important;
                        }
                        .activity-description b,
                        .activity-description strong {
                            font-weight: 700;
                            color: #173653;
                        }
                        .activity-description s,
                        .activity-description strike,
                        .activity-description del {
                            text-decoration: line-through;
                        }
                        .activity-description u {
                            text-decoration: underline;
                        }
                        .activity-description i,
                        .activity-description em {
                            font-style: italic;
                        }
                        .activity-description a {
                            color: #2563eb !important;
                            text-decoration: underline !important;
                            text-underline-offset: 2px;
                            font-weight: 500;
                            cursor: pointer;
                            transition: color 0.15s ease-in-out;
                        }
                        .activity-description a:hover {
                            color: #1d4ed8 !important;
                        }
                        .activity-description blockquote {
                            border-left: 3.5px solid #183669 !important;
                            background: #f8fafc !important;
                            padding: 0.5rem 0.85rem !important;
                            margin: 0.65rem 0 !important;
                            color: #475569 !important;
                            font-style: italic !important;
                            border-radius: 0 6px 6px 0;
                        }
                        .activity-description code {
                            background: #eef2f6 !important;
                            color: #c2410c !important;
                            padding: 0.15rem 0.4rem !important;
                            border-radius: 4px;
                            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
                            font-size: 0.875em !important;
                        }
                        .activity-description hr {
                            border: 0 !important;
                            height: 1px !important;
                            background: #d6e0ee !important;
                            margin: 1rem 0 !important;
                        }
                        .activity-description img {
                            max-width: 100%;
                            height: auto;
                            border-radius: 8px;
                            margin: 0.5rem 0;
                        }
                        /* Alignment support */
                        .activity-description [align="left"],
                        .activity-description [style*="text-align: left"],
                        .activity-description [style*="text-align:left"] {
                            text-align: left !important;
                        }
                        .activity-description [align="center"],
                        .activity-description [style*="text-align: center"],
                        .activity-description [style*="text-align:center"] {
                            text-align: center !important;
                        }
                        .activity-description [align="right"],
                        .activity-description [style*="text-align: right"],
                        .activity-description [style*="text-align:right"] {
                            text-align: right !important;
                        }
                        .activity-description [align="justify"],
                        .activity-description [style*="text-align: justify"],
                        .activity-description [style*="text-align:justify"] {
                            text-align: justify !important;
                        }
                    </style>

                    {{-- Teks Konten Deskripsi --}}
                    <div class="activity-description text-[#1e3456] text-[14px] md:text-[15px] font-inter leading-relaxed break-words text-left">
                        {!! $activity->description !!}
                    </div>

                    <!-- {{-- Kutipan Dosen jika ada --}}
                    @if(!empty($activity->quote) && $activity->quote !== '-')
                    <div class="mt-8 p-5 bg-[#f0f4fa] border-l-4 border-[#1a3675] rounded-r-xl italic text-gray-800">
                        <p class="text-sm md:text-base font-medium">"{{ $activity->quote }}"</p>
                        <span class="block text-xs md:text-sm font-bold text-[#1a3675] mt-2 not-italic">&mdash; {{ $activity->user->name ?? 'Dosen' }}</span>
                    </div>
                    @endif -->

                </div>

                {{-- =======================================
                     BARIS 2 KANAN: SIDEBAR AKTIVITAS RANDOM
                     ======================================= --}}
                <div class="lg:col-span-4 mt-8 md:mt-10 lg:mt-0">
                    <h3 class="text-xl font-extrabold text-gray-900 mb-5">
                        Aktivitas Dosen Lainnya
                    </h3>

                    <div class="flex flex-col gap-4">
                        @foreach ($relatedActivities as $item)
                        @php
                            $relImgUrl = $item->primary_image_url ?? $item->pictures?->first()?->path;
                        @endphp
                        <a href="{{ route('activity.show', $item->id) }}" class="h-[124px] group bg-white border border-gray-200 rounded-xl p-3 flex gap-4 hover:scale-105 hover:shadow-md transition-all duration-300">
                            
                            <div class="w-[120px] shrink-0 aspect-[4/3] rounded-lg overflow-hidden bg-gray-100">
                                @if($relImgUrl)
                                    <img src="{{ $relImgUrl }}" alt="Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-[#cbd5e1] flex items-center justify-center text-[10px] text-gray-500 group-hover:scale-105 transition-transform duration-500">No Image</div>
                                @endif
                            </div>

                            <div class="flex flex-col justify-center flex-1 min-w-0">
                                @php
                                    $relTypes = is_array($item->activity_type) 
                                        ? $item->activity_type 
                                        : (is_string($item->activity_type) && str_starts_with($item->activity_type, '[') 
                                            ? (json_decode($item->activity_type, true) ?? [$item->activity_type]) 
                                            : array_filter(array_map('trim', explode(',', (string)$item->activity_type))));
                                    $relTypes = array_values(array_filter($relTypes));
                                @endphp
                                <div class="flex items-center justify-between gap-1 text-[10px] md:text-xs font-semibold text-[#1a3675] mb-1">
                                    @if(!empty($relTypes))
                                        <span class="bg-[#1a3675]/10 text-[#1a3675] px-1.5 py-0.5 rounded text-[10px] font-semibold truncate max-w-[90px]" title="{{ implode(', ', $relTypes) }}">
                                            {{ $relTypes[0] }}@if(count($relTypes) > 1)<span class="text-gray-500 font-bold ml-0.5">+{{ count($relTypes) - 1 }}</span>@endif
                                        </span>
                                    @endif
                                    <span class="text-gray-500 font-medium whitespace-nowrap shrink-0 ml-auto">
                                        {{ \Carbon\Carbon::parse($item->activity_date_start)->locale('id')->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                                <h4 class="text-sm md:text-[15px] font-extrabold text-[#1a3675] leading-snug line-clamp-2 group-hover:text-blue-800 transition-colors">
                                    {{ $item->activity_name }}
                                </h4>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const descContainer = document.querySelector('.activity-description');
            if (descContainer) {
                // Pastikan link dalam deskripsi terbuka di tab baru dan aman
                const links = descContainer.querySelectorAll('a');
                links.forEach(function (link) {
                    if (!link.getAttribute('target')) {
                        link.setAttribute('target', '_blank');
                    }
                    link.setAttribute('rel', 'noopener noreferrer');
                });

                // Render formula LaTeX dengan MathJax jika ada
                if (window.MathJax && window.MathJax.typesetPromise) {
                    window.MathJax.typesetPromise([descContainer]).catch(function (err) {
                        console.warn('MathJax typesetting error:', err);
                    });
                }
            }
        });
    </script>
</x-layouts.main>