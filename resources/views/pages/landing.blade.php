<x-layouts.main>
    <x-slot:title>
        Beranda | DosenManhut
    </x-slot>

    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    {{-- =======================================
         1. HERO SECTION
         ======================================= --}}
    <section class="w-full h-full mb-8">
        {{-- Container Hero dengan Rounded Corners --}}
        <div class="relative w-full h-[600px] md:h-[700px] overflow-hidden shadow-2xl flex flex-col items-center justify-center text-center">
            
            {{-- Background Image (Ganti URL dengan gambar aslimu di folder public) --}}
            <div class="absolute inset-0 bg-cover bg-center" 
                 style="background-image: url('{{ asset('/images/hero_section.jpg') }}');">
            </div>
            
            {{-- Dark Overlay untuk memperjelas teks --}}
            <div class="absolute inset-0 bg-black/60"></div>

            {{-- Konten Hero --}}
            <div class="relative z-10 px-6 max-w-4xl flex flex-col items-center mt-8 animate-spin">
                <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 drop-shadow-md">
                    Selamat Datang!
                </h1>
                
                <p class="text-sm md:text-base text-gray-200 mb-10 leading-relaxed max-w-2xl drop-shadow">
                    Selamat datang di Direktori Dosen Departemen Manajemen Hutan IPB University.
                    Telusuri profil, aktivitas akademik, dan publikasi ilmiah dari seluruh jajaran staf
                    pengajar kami yang berdedikasi memajukan ilmu pengetahuan.
                </p>
                
                <a href="#kontribusi" class="bg-white text-[#1a3675] px-8 py-2.5 rounded-full font-bold text-sm hover:bg-gray-100 transition shadow-lg mb-16">
                    Kenalan Yuk!
                </a>

                {{-- Scroll Down Indicator (Dots & Arrow) --}}
                <div class="flex flex-col items-center space-y-2 animate-spin">
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                    <svg class="w-6 h-6 text-white mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    {{-- =======================================
         2. SEKILAS KONTRIBUSI KAMI SECTION
         ======================================= --}}
    <section id="kontribusi" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 bg-[#fafafa]">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-[#1a3675] mb-12">
            Sekilas Kontribusi Kami
        </h2>

        {{-- Grid 4 Kolom --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Card 1: Publikasi --}}
            <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-5xl font-extrabold text-[#1a3675] mb-4">{{ $allCategories['Seminar'] ?? 0 }}</span>
                <h3 class="text-lg font-bold text-[#1a3675] mb-3">Seminar</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed px-2">
                    Disini kamu bisa belajar banyak hal terkait dengan talenta yang telah kamu temukan pada diri kamu saat ini.
                </p>
            </div>

            {{-- Card 2: Lokakarya --}}
            <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-5xl font-extrabold text-[#1a3675] mb-4">{{ $allCategories['Lokakarya'] ?? 0 }}</span>
                <h3 class="text-lg font-bold text-[#1a3675] mb-3">Lokakarya</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed px-2">
                    Disini kamu bisa belajar banyak hal terkait dengan talenta yang telah kamu temukan pada diri kamu saat ini.
                </p>
            </div>

            {{-- Card 3: Seminar --}}
            <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-5xl font-extrabold text-[#1a3675] mb-4">{{ $allCategories['Workshop'] ?? 0 }}</span>
                <h3 class="text-lg font-bold text-[#1a3675] mb-3">Workshop</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed px-2">
                    Disini kamu bisa belajar banyak hal terkait dengan talenta yang telah kamu temukan pada diri kamu saat ini.
                </p>
            </div>

            {{-- Card 4: Workshop --}}
            <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-5xl font-extrabold text-[#1a3675] mb-4">{{ $allCategories['Lainnya'] ?? 0 }}</span>
                <h3 class="text-lg font-bold text-[#1a3675] mb-3">Lainnya</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed px-2">
                    Disini kamu bisa belajar banyak hal terkait dengan talenta yang telah kamu temukan pada diri kamu saat ini.
                </p>
            </div>

        </div>
    </section>

    {{-- =======================================
         3. JAJARAN STAFF PENGAJAR SECTION
         ======================================= --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-8">
            Jajaran Staff Pengajar
        </h2>

        {{-- Tabs Filter --}}
        <div class="flex flex-wrap justify-center gap-x-6 gap-y-3 text-sm font-semibold text-gray-600 mb-10" id="filter-container">
            <button class="filter-btn text-gray-900 border-b-2 border-gray-900 pb-1" data-filter="semua">Semua</button>
            <button class="filter-btn hover:text-gray-900 transition pb-1" data-filter="perencanaan kehutanan">Perencanaan Hutan</button>
            <button class="filter-btn hover:text-gray-900 transition pb-1" data-filter="pemanfaatan sumberdaya hutan">Pemanfaatan SDH</button>
            <button class="filter-btn hover:text-gray-900 transition pb-1" data-filter="kebijakan kehutanan">Kebijakan Kehutanan</button>
        </div>

        {{-- Container Carousel dengan Tombol --}}
        <div class="relative group">
            
            {{-- Tombol Kiri (Disembunyikan di mobile, muncul di hover desktop) --}}
            <button id="btn-prev" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 md:-translate-x-5 z-10 bg-white/90 hover:bg-white shadow-sm p-2.5 rounded-full text-[#1a3675] border border-gray-100 hidden sm:flex items-center justify-center transition-transform active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            {{-- Slider Container (Flexbox dengan overflow-x) --}}
            <div id="dosen-slider" class="flex gap-6 overflow-x-auto snap-x snap-mandatory hide-scroll pb-6 pt-2 px-2 scroll-smooth">
                
                @foreach ($lecturers as $lecturer)
                {{-- Card Item (Lebar di-setting agar responsif) --}}
                <a href="{{  route('lecturer.show', $lecturer['id']) }}" class="dosen-card shrink-0 w-[75%] sm:w-[45%] md:w-[30%] lg:w-[23%] snap-center relative rounded-xl overflow-hidden shadow-sm group aspect-[3/4] bg-gray-200 cursor-pointer transition-all duration-300 hover:-translate-y-2 hover:shadow-lg opacity-100 scale-100" data-category="{{ strtolower($lecturer['division']) }}">
                    <div class="w-full h-full bg-gray-300 transition-transform duration-500 group-hover:scale-110">
                        @if(!empty($lecturer['image']))
                                <img src="{{ $lecturer['image'] }}" 
                                     alt="{{ $lecturer['name'] }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-500 transition-transform duration-500 group-hover:scale-110">No Image</div>
                            @endif
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#284078]/95 via-[#284078]/10 to-transparent transition-opacity duration-300"></div>
                    
                    <div class="absolute bottom-0 left-0 p-5 text-white w-full transform transition-transform duration-300 group-hover:-translate-y-2">
                        <h3 class="font-bold text-lg mb-1 leading-tight">{{ $lecturer['name'] }}</h3>
                        <p class="text-xs text-gray-200">{{ $lecturer['division'] }}</p>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Tombol Kanan --}}
            <button id="btn-next" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 md:translate-x-5 z-10 bg-white/90 hover:bg-white shadow-lg p-2.5 rounded-full text-[#1a3675] border border-gray-100 hidden sm:flex items-center justify-center transition-transform active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>

        </div>

        <div class="mt-8 flex justify-center">
            <a href="{{ url('/lecturers') }}" class="inline-flex items-center gap-2 px-6 py-2 border-2 border-gray-800 rounded-full text-sm font-bold text-gray-800 hover:bg-gray-800 hover:text-white transition-all">
                Lihat Semua Dosen <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </section>


        {{-- =======================================
         4. AKTIVITAS TERKINI SECTION
         ======================================= --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10">
            Aktivitas Terkini
        </h2>

        {{-- Cek apakah ada data aktivitas yang dikirim --}}
        @if(isset($activities) && $activities->count() > 0)
            @php
                // Ambil 1 data paling atas untuk kolom Kiri (Highlight)
                $highlight = $activities->first();
                
                // Ambil 3 data berikutnya (lewati 1 data pertama) untuk kolom Kanan (List)
                $listActivities = $activities->slice(1, 3);
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {{-- ==========================================
                     KIRI: HIGHLIGHT (Data Pertama)
                     ========================================== --}}
                <a href="{{ route('activity.show', $highlight->id) }}" class="lg:col-span-7 bg-white border border-gray-200 rounded-2xl p-4 md:p-5 shadow-sm flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group cursor-pointer block text-left">
                    
                    <div class="w-full h-48 md:h-[320px] rounded-xl mb-4 overflow-hidden relative">
                        @if($highlight->primaryPicture)
                            <img src="{{ asset('storage/' . $highlight->primaryPicture->path) }}" 
                                 alt="{{ $highlight->activity_name }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full bg-[#cbd5e1] flex items-center justify-center text-gray-500 transition-transform duration-500 group-hover:scale-105">No Image</div>
                        @endif
                    </div>
                    
                    <div class="flex flex-wrap justify-between items-center text-[11px] md:text-xs text-[#1a3675] font-bold mb-2">
                        <span>{{ $highlight->job ?? 'Partisipan' }} &bull; {{ $highlight->user->name ?? 'Nama Dosen' }}</span>
                        <span>{{ \Carbon\Carbon::parse($highlight->activity_date_start)->translatedFormat('d F Y') }}</span>
                    </div>
                    
                    <h3 class="text-xl md:text-2xl font-bold text-[#1a3675] mb-2 group-hover:text-blue-700 transition-colors">
                        {{ $highlight->activity_name }}
                    </h3>
                    <p class="text-xs md:text-sm text-gray-500 leading-relaxed line-clamp-3 md:line-clamp-4">
                        {{ $highlight->description }}
                    </p>
                </a>

                {{-- ==========================================
                     KANAN: LIST (Data ke 2, 3, 4)
                     ========================================== --}}
                <div class="lg:col-span-5 flex flex-col gap-3 md:gap-4">
                    
                    @foreach ($listActivities as $item)
                    <a href="{{ route('activity.show', $item->id) }}" class="bg-white border border-gray-200 rounded-2xl p-2.5 md:p-3 shadow-sm flex flex-row gap-3 md:gap-4 transition-all duration-300 hover:-translate-x-1 hover:shadow-lg group cursor-pointer h-full items-center md:items-start text-left">
                        
                        <div class="w-24 h-24 md:w-[140px] md:h-[120px] rounded-xl shrink-0 overflow-hidden relative">
                            @if($item->primaryPicture)
                                <img src="{{ $item->primaryPicture->path }}" 
                                     alt="{{ $item->activity_name }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full bg-[#cbd5e1] flex items-center justify-center text-[10px] text-gray-500 transition-transform duration-500 group-hover:scale-110">No Image</div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col flex-grow py-1 justify-center">
                            <div class="flex flex-wrap justify-between items-center text-[9px] md:text-[10px] text-[#1a3675] font-bold mb-1">
                                <span class="truncate pr-2 max-w-[65%]">{{ $item->job ?? 'Partisipan' }} &bull; {{ $item->user->name ?? 'Dosen' }}</span>
                                <span>{{ \Carbon\Carbon::parse($item->activity_date_start)->format('d/m/y') }}</span>
                            </div>
                            <h4 class="text-sm md:text-[15px] font-bold text-[#1a3675] mb-1 leading-tight group-hover:text-blue-700 transition-colors line-clamp-2">
                                {{ $item->activity_name }}
                            </h4>
                            <p class="hidden md:block text-[10px] text-gray-500 leading-relaxed line-clamp-2">
                                {{ $item->description }}
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Fallback jika belum ada data aktivitas sama sekali di database --}}
            <div class="w-full p-10 text-center bg-gray-50 rounded-2xl border border-gray-200 text-gray-500">
                Belum ada aktivitas terkini yang ditambahkan.
            </div>
        @endif

        <div class="mt-10 flex justify-center">
            <a href="{{ url('/activities') }}" class="inline-flex items-center gap-2 px-6 py-2 border-2 border-gray-800 rounded-full text-sm font-bold text-gray-800 hover:bg-gray-800 hover:text-white transition-all">
                Semua Aktivitas <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </section>

    {{-- Script JavaScript Gabungan (Filter Kategori + Slider) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGIKA FILTER ---
            const buttons = document.querySelectorAll('.filter-btn');
            const cards = document.querySelectorAll('.dosen-card');

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    // Styling tab aktif
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
                            setTimeout(() => {
                                card.classList.remove('opacity-0', 'scale-95');
                                card.classList.add('opacity-100', 'scale-100');
                            }, 10);
                        } else {
                            card.classList.remove('opacity-100', 'scale-100');
                            card.classList.add('opacity-0', 'scale-95');
                            setTimeout(() => {
                                if(card.classList.contains('opacity-0')) card.classList.add('hidden');
                            }, 300);
                        }
                    });
                });
            });

            // --- LOGIKA SLIDER (Geser Kanan/Kiri) ---
            const slider = document.getElementById('dosen-slider');
            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');

            // Geser slider sebesar 300px per klik
            const scrollAmount = 320; 

            btnPrev.addEventListener('click', () => {
                slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });

            btnNext.addEventListener('click', () => {
                slider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        });
    </script>

{{-- =======================================
         5. CTA (CALL TO ACTION) SECTION
         ======================================= --}}
    <section class="relative w-full h-[500px] lg:h-[650px] mt-16 flex flex-col items-center justify-center text-center group overflow-hidden">
        
        {{-- Background Image --}}
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" 
             style="background-image: url('{{ asset('images/picture_cta.png') }}');">
        </div>
        
        {{-- Dark Overlay --}}
        <div class="absolute inset-0 bg-black/60 md:bg-black/50"></div>

        {{-- Konten CTA --}}
        <div class="relative z-10 px-6 flex flex-col items-center w-full">
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-8 drop-shadow-lg leading-tight">
                Yuk Berkolaborasi Dengan Kami!
            </h2>
            
            {{-- Tombol Hubungi Kami --}}
            <a href="mailto:email.dosen@apps.ipb.ac.id" class="bg-white text-[#1a3675] px-10 py-3 rounded-full font-bold text-sm md:text-base hover:bg-gray-100 hover:scale-105 transition-all shadow-xl">
                Hubungi Kami
            </a>
        </div>
        
    </section>

</x-layouts.main>