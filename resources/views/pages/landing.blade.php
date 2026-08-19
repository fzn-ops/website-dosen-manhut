<x-layouts.main>
    <x-slot:title>
        Beranda | DosenManhut
    </x-slot>

    {{-- =======================================
         1. HERO SECTION
         ======================================= --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Container Hero dengan Rounded Corners --}}
        <div class="relative w-full h-[550px] md:h-[600px] rounded-[2rem] overflow-hidden shadow-2xl flex flex-col items-center justify-center text-center">
            
            {{-- Background Image (Ganti URL dengan gambar aslimu di folder public) --}}
            <div class="absolute inset-0 bg-cover bg-center" 
                 style="background-image: url('{{ asset('images/hero-dosen.jpg') }}');">
            </div>
            
            {{-- Dark Overlay untuk memperjelas teks --}}
            <div class="absolute inset-0 bg-black/60"></div>

            {{-- Konten Hero --}}
            <div class="relative z-10 px-6 max-w-4xl flex flex-col items-center mt-8">
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
                <div class="flex flex-col items-center space-y-2 animate-bounce">
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
                <span class="text-5xl font-extrabold text-[#1a3675] mb-4">100</span>
                <h3 class="text-lg font-bold text-[#1a3675] mb-3">Publikasi</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed px-2">
                    Disini kamu bisa belajar banyak hal terkait dengan talenta yang telah kamu temukan pada diri kamu saat ini.
                </p>
            </div>

            {{-- Card 2: Lokakarya --}}
            <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-5xl font-extrabold text-[#1a3675] mb-4">100</span>
                <h3 class="text-lg font-bold text-[#1a3675] mb-3">Lokakarya</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed px-2">
                    Disini kamu bisa belajar banyak hal terkait dengan talenta yang telah kamu temukan pada diri kamu saat ini.
                </p>
            </div>

            {{-- Card 3: Seminar --}}
            <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-5xl font-extrabold text-[#1a3675] mb-4">100</span>
                <h3 class="text-lg font-bold text-[#1a3675] mb-3">Seminar</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed px-2">
                    Disini kamu bisa belajar banyak hal terkait dengan talenta yang telah kamu temukan pada diri kamu saat ini.
                </p>
            </div>

            {{-- Card 4: Workshop --}}
            <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-8 text-center flex flex-col items-center transition hover:-translate-y-1 hover:shadow-lg duration-300">
                <span class="text-5xl font-extrabold text-[#1a3675] mb-4">100</span>
                <h3 class="text-lg font-bold text-[#1a3675] mb-3">Workshop</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed px-2">
                    Disini kamu bisa belajar banyak hal terkait dengan talenta yang telah kamu temukan pada diri kamu saat ini.
                </p>
            </div>

        </div>
    </section>

</x-layouts.main>