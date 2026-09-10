<x-layouts.main>
    <x-slot:title>
        Beranda | DosenManhut
    </x-slot>

    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <x-preloader />
    <x-ornament/>
    {{-- =======================================
         1. HERO SECTION
         ======================================= --}}
    <section class="w-full h-full mb-6 sm:mb-8">
        {{-- Container Hero --}}
        <div class="relative w-full h-[520px] sm:h-[620px] md:h-[700px] overflow-hidden shadow-xl flex flex-col items-center justify-center text-center">
            
            {{-- Background Image --}}
            <div class="absolute inset-0 bg-cover bg-center" 
                 style="background-image: url('{{ asset('/images/hero_section.jpg') }}');">
            </div>
            
            {{-- Dark Overlay --}}
            <div class="absolute inset-0 bg-black/60"></div>

            {{-- Konten Hero --}}
            <div class="relative z-10 px-4 sm:px-6 max-w-4xl flex flex-col items-center mt-6 sm:mt-8">
                <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-white mb-4 sm:mb-6 drop-shadow-md tracking-tight">
                    Selamat Datang!
                </h1>
                
                <p class="text-xs sm:text-sm md:text-base text-gray-200 mb-8 sm:mb-10 leading-relaxed max-w-2xl drop-shadow px-2">
                    Selamat datang di Direktori Dosen Departemen Manajemen Hutan IPB University.
                    Telusuri profil, aktivitas akademik, dan publikasi ilmiah dari seluruh jajaran staf
                    pengajar kami yang berdedikasi memajukan ilmu pengetahuan.
                </p>
                
                <a href="#kontribusi" class="bg-white text-[#1a3675] px-6 sm:px-8 py-2.5 sm:py-3 rounded-full font-bold text-xs sm:text-sm hover:bg-gray-100 transition shadow-lg mb-10 sm:mb-16">
                    Kenalan Yuk!
                </a>

                {{-- Scroll Down Indicator --}}
                <div class="flex flex-col items-center space-y-1.5 sm:space-y-2 animate-bounce">
                    <div class="w-1.5 sm:w-2 h-1.5 sm:h-2 bg-white rounded-full"></div>
                    <div class="w-1.5 sm:w-2 h-1.5 sm:h-2 bg-white rounded-full"></div>
                    <div class="w-1.5 sm:w-2 h-1.5 sm:h-2 bg-white rounded-full"></div>
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    {{-- =======================================
         2. SEKILAS KONTRIBUSI KAMI SECTION
         ======================================= --}}
    <section id="kontribusi" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 md:py-20">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-center text-[#1a3675] mb-8 sm:mb-12">
            Sekilas Kontribusi Kami
        </h2>

        {{-- Grid 4 Kolom (2 Kolom di Mobile) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
            
            {{-- Card 1: Seminar --}}
            <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-4 sm:p-6 md:p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-[#1a3675] mb-2 sm:mb-4">{{ $allCategories['Seminar'] ?? 0 }}</span>
                <h3 class="text-sm sm:text-lg font-bold text-[#1a3675] mb-1.5 sm:mb-3">Seminar</h3>
                <p class="text-[10px] sm:text-[11px] text-gray-500 leading-relaxed">
                    Kontribusi pemikiran dan riset dalam forum ilmiah nasional maupun internasional.
                </p>
            </div>

            {{-- Card 2: Lokakarya --}}
            <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-4 sm:p-6 md:p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-[#1a3675] mb-2 sm:mb-4">{{ $allCategories['Lokakarya'] ?? 0 }}</span>
                <h3 class="text-sm sm:text-lg font-bold text-[#1a3675] mb-1.5 sm:mb-3">Lokakarya</h3>
                <p class="text-[10px] sm:text-[11px] text-gray-500 leading-relaxed">
                    Pendampingan teknis dan penyusunan rumusan kebijakan kehutanan terapan.
                </p>
            </div>

            {{-- Card 3: Workshop --}}
            <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-4 sm:p-6 md:p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-[#1a3675] mb-2 sm:mb-4">{{ $allCategories['Workshop'] ?? 0 }}</span>
                <h3 class="text-sm sm:text-lg font-bold text-[#1a3675] mb-1.5 sm:mb-3">Workshop</h3>
                <p class="text-[10px] sm:text-[11px] text-gray-500 leading-relaxed">
                    Pelatihan peningkatan kapasitas praktisi, akademisi, dan masyarakat.
                </p>
            </div>

            {{-- Card 4: Lainnya --}}
            <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-4 sm:p-6 md:p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-[#1a3675] mb-2 sm:mb-4">{{ $allCategories['Lainnya'] ?? 0 }}</span>
                <h3 class="text-sm sm:text-lg font-bold text-[#1a3675] mb-1.5 sm:mb-3">Lainnya</h3>
                <p class="text-[10px] sm:text-[11px] text-gray-500 leading-relaxed">
                    Aktivitas pengabdian, narasumber ahli, dan kolaborasi strategis lainnya.
                </p>
            </div>

        </div>
    </section>

    {{-- =======================================
         3. JAJARAN STAFF PENGAJAR SECTION
         ======================================= --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-center text-gray-900 mb-6 sm:mb-8">
            Jajaran Staff Pengajar
        </h2>

        {{-- Tabs Filter --}}
        <div class="flex flex-wrap justify-center gap-x-4 sm:gap-x-6 gap-y-2 sm:gap-y-3 text-xs sm:text-sm font-semibold text-gray-600 mb-8 sm:mb-10" id="filter-container">
            <button class="filter-btn text-gray-900 border-b-2 border-gray-900 pb-1 cursor-pointer" data-filter="semua">Semua</button>
            <button class="filter-btn hover:text-gray-900 transition pb-1 cursor-pointer" data-filter="perencanaan kehutanan">Perencanaan Hutan</button>
            <button class="filter-btn hover:text-gray-900 transition pb-1 cursor-pointer" data-filter="pemanfaatan sumberdaya hutan">Pemanfaatan SDH</button>
            <button class="filter-btn hover:text-gray-900 transition pb-1 cursor-pointer" data-filter="kebijakan kehutanan">Kebijakan Kehutanan</button>
        </div>

        {{-- Container Carousel --}}
        <div class="relative">
            
            {{-- Tombol Kiri --}}
            <button id="btn-prev" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 md:-translate-x-5 z-10 bg-white/90 hover:bg-white shadow-md p-2.5 rounded-full text-[#1a3675] border border-gray-100 hidden sm:flex items-center justify-center transition-transform active:scale-95 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            {{-- Slider Container (Menampilkan persis 4 kartu di Desktop & 2 kartu di Mobile) --}}
            <div id="dosen-slider" class="flex gap-3.5 sm:gap-4 lg:gap-6 overflow-x-auto snap-x snap-mandatory hide-scroll pb-6 pt-2 scroll-smooth">
                
                @foreach ($lecturers as $lecturer)
                @php $hasImage = !empty($lecturer['image']); @endphp
                <a href="{{ route('lecturer.show', $lecturer['id']) }}" class="dosen-card shrink-0 w-[calc((100%-0.875rem)/2)] sm:w-[calc((100%-1rem)/2)] md:w-[calc((100%-2*1rem)/3)] lg:w-[calc((100%-3*1.5rem)/4)] snap-start relative rounded-2xl overflow-hidden shadow-xs group/card aspect-[3/4] bg-gray-200 cursor-pointer transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl" data-category="{{ strtolower($lecturer['division']) }}">
                    <div class="w-full h-full bg-[#cbd5e1] relative overflow-hidden">
                        @if($hasImage)
                            <img src="{{ $lecturer['image'] }}" 
                                 alt="{{ $lecturer['name'] }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover/card:scale-105"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 items-center justify-center text-gray-400" style="display: none;">
                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Gradient Biru Gelap yang Pas & Tidak Menutupi Wajah/Atas --}}
                    <div class="absolute inset-x-0 bottom-0 h-3/5 bg-gradient-to-t from-[#1a3675]/90 via-[#1a3675]/35 to-transparent pointer-events-none"></div>
                    
                    <div class="absolute bottom-0 left-0 p-3 sm:p-4 md:p-5 text-white w-full pointer-events-none">
                        <h3 class="font-bold text-xs sm:text-sm md:text-base mb-0.5 leading-tight line-clamp-2 drop-shadow-[0_1px_2px_rgba(0,0,0,0.6)]">{{ $lecturer['name'] }}</h3>
                        <p class="text-[9px] sm:text-[10px] md:text-xs text-gray-200 line-clamp-1 font-medium drop-shadow-[0_1px_1px_rgba(0,0,0,0.5)]">{{ $lecturer['division'] }}</p>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Tombol Kanan --}}
            <button id="btn-next" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 md:translate-x-5 z-10 bg-white/90 hover:bg-white shadow-md p-2.5 rounded-full text-[#1a3675] border border-gray-100 hidden sm:flex items-center justify-center transition-transform active:scale-95 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>

        </div>

        <div class="mt-8 flex justify-center">
            <a href="{{ url('/lecturers') }}" class="inline-flex items-center gap-2 px-6 py-2 border-2 border-[#1a3675] rounded-full text-xs sm:text-sm font-bold text-[#1a3675] hover:bg-[#1a3675] hover:text-white transition-all">
                Lihat Semua Dosen <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </section>

    {{-- =======================================
         4. AKTIVITAS TERKINI SECTION
         ======================================= --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-center text-gray-900 mb-8 sm:mb-10">
            Aktivitas Terkini
        </h2>

        @if(isset($activities) && $activities->count() > 0)
            @php
                $highlight = $activities->first();
                $listActivities = $activities->slice(1, 3);
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {{-- KIRI: HIGHLIGHT (Data Pertama) --}}
                @php
                    $highlightImgUrl = $highlight->primary_image_url ?? $highlight->primaryPicture?->path ?? $highlight->pictures?->first()?->path;
                    $highlightDesc = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags(str_replace(['<', '>'], [' <', '> '], $highlight->description)))));
                @endphp
                <a href="{{ route('activity.show', $highlight->id) }}" class="lg:col-span-7 bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group cursor-pointer block text-left">
                    
                    <div class="w-full h-48 sm:h-64 md:h-[320px] rounded-xl mb-4 overflow-hidden relative bg-slate-100">
                        @if($highlightImgUrl)
                            <img src="{{ $highlightImgUrl }}" 
                                 alt="{{ $highlight->activity_name }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full bg-[#cbd5e1] flex items-center justify-center text-gray-500 transition-transform duration-500 group-hover:scale-105">No Image</div>
                        @endif
                    </div>
                    
                    <div class="flex flex-wrap justify-between items-center text-[10px] sm:text-xs text-[#1a3675] font-bold mb-2">
                        <span>{{ $highlight->job ?? 'Partisipan' }} &bull; {{ $highlight->user->name ?? 'Nama Dosen' }}</span>
                        <span>{{ \Carbon\Carbon::parse($highlight->activity_date_start)->locale('id')->translatedFormat('d F Y') }}</span>
                    </div>
                    
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-[#1a3675] mb-2 group-hover:text-blue-700 transition-colors leading-snug">
                        {{ $highlight->activity_name }}
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed line-clamp-3 md:line-clamp-4">
                        {{ \Illuminate\Support\Str::limit($highlightDesc, 180, '...') }}
                    </p>
                </a>

                {{-- KANAN: LIST (Data ke 2, 3, 4) --}}
                <div class="lg:col-span-5 flex flex-col gap-3 md:gap-4">
                    
                    @foreach ($listActivities as $item)
                    @php
                        $itemImgUrl = $item->primary_image_url ?? $item->primaryPicture?->path ?? $item->pictures?->first()?->path;
                        $itemDesc = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags(str_replace(['<', '>'], [' <', '> '], $item->description)))));
                    @endphp
                    <a href="{{ route('activity.show', $item->id) }}" class="bg-white border border-gray-200 rounded-2xl p-3 sm:p-3.5 shadow-xs flex flex-row gap-3 sm:gap-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group cursor-pointer h-full items-center sm:items-start text-left">
                        
                        <div class="w-20 h-20 sm:w-28 sm:h-24 md:w-[130px] md:h-[110px] rounded-xl shrink-0 overflow-hidden relative bg-slate-100">
                            @if($itemImgUrl)
                                <img src="{{ $itemImgUrl }}" 
                                     alt="{{ $item->activity_name }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full bg-[#cbd5e1] flex items-center justify-center text-[10px] text-gray-500 transition-transform duration-500 group-hover:scale-105">No Image</div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col flex-grow py-0.5 justify-center">
                            <div class="flex flex-wrap justify-between items-center text-[9px] sm:text-[10px] text-[#1a3675] font-bold mb-1">
                                <span class="truncate pr-2 max-w-[65%]">{{ $item->job ?? 'Partisipan' }} &bull; {{ $item->user->name ?? 'Dosen' }}</span>
                                <span>{{ \Carbon\Carbon::parse($item->activity_date_start)->locale('id')->format('d/m/y') }}</span>
                            </div>
                            <h4 class="text-xs sm:text-sm font-bold text-[#1a3675] mb-1 leading-tight group-hover:text-blue-700 transition-colors line-clamp-2">
                                {{ $item->activity_name }}
                            </h4>
                            <p class="hidden sm:block text-[10px] sm:text-[11px] text-gray-500 leading-relaxed line-clamp-2">
                                {{ \Illuminate\Support\Str::limit($itemDesc, 110, '...') }}
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        @else
            <div class="w-full p-8 sm:p-10 text-center bg-gray-50 rounded-2xl border border-gray-200 text-gray-500 text-xs sm:text-sm">
                Belum ada aktivitas terkini yang ditambahkan.
            </div>
        @endif

        <div class="mt-8 sm:mt-10 flex justify-center">
            <a href="{{ url('/activities') }}" class="inline-flex items-center gap-2 px-6 py-2 border-2 border-[#1a3675] rounded-full text-xs sm:text-sm font-bold text-[#1a3675] hover:bg-[#1a3675] hover:text-white transition-all">
                Semua Aktivitas <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </section>

    {{-- Script JavaScript Gabungan (Filter Kategori + Slider) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGIKA FILTER (Tanpa animasi naik-turun liar) ---
            const buttons = document.querySelectorAll('.filter-btn');
            const cards = document.querySelectorAll('.dosen-card');

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    buttons.forEach(btn => {
                        btn.classList.remove('text-gray-900', 'border-b-2', 'border-gray-900');
                        btn.classList.add('text-gray-600');
                    });
                    button.classList.add('text-gray-900', 'border-b-2', 'border-gray-900');
                    button.classList.remove('text-gray-600');

                    const filter = button.getAttribute('data-filter');

                    cards.forEach(card => {
                        if (filter === 'semua' || card.getAttribute('data-category') === filter) {
                            card.classList.remove('hidden');
                        } else {
                            card.classList.add('hidden');
                        }
                    });
                });
            });

            // --- LOGIKA SLIDER (Geser Kanan/Kiri) ---
            const slider = document.getElementById('dosen-slider');
            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');

            function getScrollStep() {
                const firstCard = slider?.querySelector('.dosen-card:not(.hidden)');
                if (firstCard) {
                    const style = window.getComputedStyle(slider);
                    const gap = parseFloat(style.gap) || 24;
                    return firstCard.offsetWidth + gap;
                }
                return 300;
            }

            if (btnPrev && slider) {
                btnPrev.addEventListener('click', () => {
                    slider.scrollBy({ left: -getScrollStep(), behavior: 'smooth' });
                });
            }

            if (btnNext && slider) {
                btnNext.addEventListener('click', () => {
                    slider.scrollBy({ left: getScrollStep(), behavior: 'smooth' });
                });
            }
        });
    </script>

    {{-- =======================================
         5. CTA (CALL TO ACTION) SECTION
         ======================================= --}}
    <section class="relative w-full h-[400px] sm:h-[500px] lg:h-[600px] mt-12 sm:mt-16 flex flex-col items-center justify-center text-center group overflow-hidden">
        
        {{-- Background Image --}}
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" 
             style="background-image: url('{{ asset('images/picture_cta.png') }}');">
        </div>
        
        {{-- Dark Overlay --}}
        <div class="absolute inset-0 bg-black/60 md:bg-black/50"></div>

        {{-- Konten CTA --}}
        <div class="relative z-10 px-4 sm:px-6 flex flex-col items-center w-full">
            <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold text-white mb-6 sm:mb-8 drop-shadow-lg leading-tight">
                Yuk Berkolaborasi Dengan Kami!
            </h2>
            
            <a href="mailto:manhut@apps.ipb.ac.id" class="bg-white text-[#1a3675] px-8 sm:px-10 py-2.5 sm:py-3 rounded-full font-bold text-xs sm:text-sm md:text-base hover:bg-gray-100 hover:scale-105 transition-all shadow-xl">
                Hubungi Kami
            </a>
        </div>
        
    </section>

</x-layouts.main>