<x-layouts.main>
    <x-slot:title>
        {{ $activity->activity_name }} | DosenManhut
    </x-slot>

    <div class="bg-[#fafafc] w-full min-h-screen py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. Breadcrumb --}}
            <nav class="text-xs sm:text-sm font-medium text-gray-500 mb-4 sm:mb-6 flex items-center gap-1.5 flex-wrap">
                <a href="{{ url('/') }}" class="hover:text-[#1a3675] transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ url('/activities') }}" class="hover:text-[#1a3675] transition-colors">Aktivitas</a>
                <span>/</span>
                <span class="text-[#1a3675] font-semibold truncate max-w-[200px] sm:max-w-md" title="{{ $activity->activity_name }}">
                    {{ $activity->activity_name }}
                </span>
            </nav>

            {{-- ==========================================
                 LAYOUT UTAMA (Grid Kolom Artikel & Sidebar)
                 ========================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:gap-x-10 gap-y-8">
                
                {{-- KONTEN UTAMA (8 KOLOM) --}}
                <div class="lg:col-span-8 flex flex-col">
                    
                    {{-- 1. Judul Aktivitas (Paling Atas) --}}
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight mb-3">
                        {{ $activity->activity_name }}
                    </h1>

                    {{-- 2. Kategori Pill Badges (Di Bawah Judul) --}}
                    @php
                        $actTypes = is_array($activity->activity_type) 
                            ? $activity->activity_type 
                            : (is_string($activity->activity_type) && str_starts_with($activity->activity_type, '[') 
                                ? (json_decode($activity->activity_type, true) ?? [$activity->activity_type]) 
                                : array_filter(array_map('trim', explode(',', (string)$activity->activity_type))));
                        $actTypes = array_values(array_filter($actTypes));
                    @endphp 

                    @if(!empty($actTypes))
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-3.5">
                            @foreach($actTypes as $cat)
                                <span class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#1a3675] border border-blue-100/90 shadow-2xs">
                                    {{ $cat }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- 3. Metadata Author Byline: Dosen, Peran & Tanggal Pelaksanaan --}}
                    @php
                        $dosenProfile = $activity->user->profileDosen ?? null;
                        $dosenImg = $dosenProfile && $dosenProfile->image ? asset('storage/' . $dosenProfile->image) : null;
                        $dosenName = $activity->user->name ?? 'Nama Dosen';
                        $dosenRole = $activity->job;
                        $isSameDate = empty($activity->activity_date_end) || 
                                      ($activity->activity_date_start && $activity->activity_date_end && \Carbon\Carbon::parse($activity->activity_date_start)->format('Y-m-d') === \Carbon\Carbon::parse($activity->activity_date_end)->format('Y-m-d'));
                    @endphp

                    <div class="flex items-center justify-between py-3.5 border-y border-gray-200/80 mb-5 gap-3">
                        {{-- Kiri: Foto / Avatar + Nama Dosen & Peran --}}
                        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                            {{-- Avatar Foto Dosen --}}
                            <a href="{{ $dosenProfile ? route('lecturer.show', $dosenProfile->id) : '#' }}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full overflow-hidden bg-gray-200 shrink-0 border border-gray-200/80 shadow-2xs group" title="{{ $dosenName }}">
                                @if($dosenImg)
                                    <img src="{{ $dosenImg }}" alt="{{ $dosenName }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-200">
                                @else
                                    <div class="w-full h-full bg-[#1a3675]/10 text-[#1a3675] flex items-center justify-center font-bold text-xs sm:text-sm">
                                        {{ strtoupper(substr($dosenName, 0, 1)) }}
                                    </div>
                                @endif
                            </a>

                            {{-- Nama Dosen & Peran pada Aktivitas --}}
                            <div class="flex flex-col min-w-0">
                                <a href="{{ $dosenProfile ? route('lecturer.show', $dosenProfile->id) : '#' }}" class="text-xs sm:text-sm md:text-base font-extrabold text-gray-900 hover:text-[#1a3675] transition-colors hover:underline truncate">
                                    {{ $dosenName }}
                                </a>
                                @if(!empty($dosenRole))
                                    <p class="text-[11px] sm:text-xs text-gray-500 font-medium truncate mt-0.5">
                                        <span>Peran:</span>
                                        <span class="text-[#1a3675] font-semibold">{{ $dosenRole }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Kanan: Tanggal Pelaksanaan (Konsisten di Mobile & Desktop) --}}
                        <div class="flex items-center gap-1.5 text-[11px] sm:text-xs md:text-sm font-semibold text-[#1a3675] shrink-0 bg-blue-50/80 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg border border-blue-100/90 shadow-2xs">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#1a3675]/80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @if(!$isSameDate && $activity->activity_date_end)
                                <span>{{ \Carbon\Carbon::parse($activity->activity_date_start)->locale('id')->translatedFormat('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($activity->activity_date_end)->locale('id')->translatedFormat('d M Y') }}</span>
                            @else
                                <span>{{ \Carbon\Carbon::parse($activity->activity_date_start)->locale('id')->translatedFormat('d F Y') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- 4. Gambar Utama Dokumentasi --}}
                    @php
                        $mainPicUrl = $activity->primary_image_url ?? $activity->pictures?->first()?->path;
                    @endphp

                    <div class="w-full aspect-[16/10] sm:aspect-[16/9] max-h-[460px] bg-gray-100 rounded-2xl mb-6 overflow-hidden shadow-xs border border-gray-200/90 relative">
                        @if($mainPicUrl)
                            <img src="{{ $mainPicUrl }}" alt="{{ $activity->activity_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-100 gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-sm font-medium">Tidak ada gambar dokumentasi</span>
                            </div>
                        @endif
                    </div>

                    {{-- Styling agar render konten deskripsi sama persis dengan RichTextEditor --}}
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

                    {{-- 5. Teks Konten Deskripsi --}}
                    <div class="activity-description text-[#1e3456] text-[14px] sm:text-[15px] font-inter leading-relaxed break-words text-left bg-white p-4 sm:p-6 md:p-8 rounded-2xl border border-gray-200/80 shadow-2xs">
                        {!! $activity->description !!}
                    </div>

                </div>

                {{-- SIDEBAR AKTIVITAS RANDOM (4 KOLOM) --}}
                <div class="lg:col-span-4 mt-6 lg:mt-0">
                    <div class="lg:sticky lg:top-24 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight">
                                Aktivitas Dosen Lainnya
                            </h3>
                        </div>

                        <div class="flex flex-col gap-3.5">
                            @foreach ($relatedActivities as $item)
                            @php
                                $relImgUrl = $item->primary_image_url ?? $item->pictures?->first()?->path;
                            @endphp
                            <a href="{{ route('activity.show', $item->id) }}" class="group bg-white border border-gray-200/90 rounded-xl p-3 flex gap-3.5 hover:shadow-md hover:border-blue-200 hover:-translate-y-0.5 transition-all duration-200">
                                
                                <div class="w-24 sm:w-28 aspect-[4/3] rounded-lg overflow-hidden bg-gray-100 shrink-0 border border-gray-100">
                                    @if($relImgUrl)
                                        <img src="{{ $relImgUrl }}" alt="{{ $item->activity_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full bg-slate-200 flex items-center justify-center text-[10px] text-gray-400 font-medium">No Image</div>
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
                                    
                                    <div class="flex items-center justify-between gap-1 text-[10px] sm:text-[11px] font-semibold text-[#1a3675] mb-1">
                                        @if(!empty($relTypes))
                                            <span class="bg-blue-50 text-[#1a3675] px-1.5 py-0.5 rounded text-[10px] font-semibold truncate max-w-[85px]" title="{{ implode(', ', $relTypes) }}">
                                                {{ $relTypes[0] }}@if(count($relTypes) > 1)<span class="text-gray-500 font-bold ml-0.5">+{{ count($relTypes) - 1 }}</span>@endif
                                            </span>
                                        @endif
                                        <span class="text-gray-400 font-medium whitespace-nowrap shrink-0 ml-auto text-[10px]">
                                            {{ \Carbon\Carbon::parse($item->activity_date_start)->locale('id')->translatedFormat('d M Y') }}
                                        </span>
                                    </div>

                                    <h4 class="text-xs sm:text-sm font-extrabold text-gray-900 leading-snug line-clamp-2 group-hover:text-[#1a3675] transition-colors">
                                        {{ $item->activity_name }}
                                    </h4>
                                    @if(!empty($item->user->name))
                                        <p class="text-[11px] text-gray-500 font-medium mt-1 truncate">
                                            {{ $item->user->name }}
                                        </p>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
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