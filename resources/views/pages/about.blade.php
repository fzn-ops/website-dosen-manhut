<x-layouts.main>
    <x-slot:title>
        Tentang Kami | DosenManhut
    </x-slot>
    <x-preloader />
    {{-- Wrapper Halaman --}}
    <div class="relative bg-[#fafafc] w-full min-h-screen overflow-hidden pb-16 sm:pb-20">
        
        {{-- Ornamen Kiri Atas --}}
        <div class="absolute top-0 left-0 w-48 sm:w-64 h-48 sm:h-64 -translate-x-1/4 -translate-y-1/4 opacity-40 pointer-events-none">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-blue-200 fill-current">
                <path d="M100 0 A100 100 0 0 1 200 100 L100 100 Z" />
                <circle cx="50" cy="150" r="40" fill="none" stroke="currentColor" stroke-width="4"/>
                <circle cx="50" cy="150" r="25" fill="none" stroke="currentColor" stroke-width="2"/>
            </svg>
        </div>

        {{-- Ornamen Kanan Bawah --}}
        <div class="absolute bottom-0 right-0 w-56 sm:w-80 h-56 sm:h-80 translate-x-1/4 translate-y-1/4 opacity-40 pointer-events-none">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-blue-300">
                <rect x="100" y="50" width="60" height="60" transform="rotate(45 130 80)" fill="currentColor" opacity="0.5"/>
                <rect x="50" y="100" width="60" height="60" transform="rotate(45 80 130)" fill="currentColor" opacity="0.3"/>
                <rect x="150" y="150" width="60" height="60" transform="rotate(45 180 180)" fill="none" stroke="currentColor" stroke-width="4"/>
            </svg>
        </div>

        {{-- =======================================
             KONTEN UTAMA
             ======================================= --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 sm:pt-16 md:pt-20">
            
            {{-- 1. Bagian Judul dan Teks Deskripsi --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 sm:gap-8 mb-10 sm:mb-12">
                
                {{-- Kiri: Judul --}}
                <div class="w-full md:w-1/3">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                        Tentang Kami
                    </h1>
                    {{-- Aksen Garis Bawah --}}
                    <div class="flex items-center gap-1.5 mt-2.5 sm:mt-3">
                        <div class="h-1.5 w-14 sm:w-16 bg-[#1a3675] rounded-full"></div>
                        <div class="h-1.5 w-1.5 bg-[#1a3675] rounded-full"></div>
                        <div class="h-1.5 w-1.5 bg-[#1a3675] rounded-full"></div>
                    </div>
                </div>

                {{-- Kanan: Teks --}}
                <div class="w-full md:w-2/3">
                    <p class="text-xs sm:text-sm md:text-base text-[#1a3675] font-normal leading-relaxed text-left">
                        Departemen Manajemen Hutan Fakultas Kehutanan dan Lingkungan IPB University senantiasa berkomitmen untuk menyelenggarakan pendidikan, riset, dan pengabdian masyarakat guna mewujudkan pengelolaan hutan tropika yang berkelanjutan, adaptif terhadap perubahan iklim, dan berkeadilan bagi kesejahteraan bangsa.
                    </p>
                </div>

            </div>

            {{-- 2. Gambar Besar (Banner) --}}
            <div class="w-full h-[220px] sm:h-[360px] md:h-[480px] rounded-xl sm:rounded-2xl md:rounded-[2rem] overflow-hidden shadow-lg mb-12 sm:mb-16 md:mb-20 group cursor-pointer">
                <img src="{{ asset('images/hero_section.jpg') }}" 
                     alt="Tim Dosen Manajemen Hutan" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            </div>

            {{-- 3. Bagian Divisi --}}
            <div class="mb-6 sm:mb-8">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                    Divisi
                </h2>
                {{-- Aksen Garis Bawah --}}
                <div class="flex items-center gap-1.5 mt-2.5 sm:mt-3">
                    <div class="h-1.5 w-10 sm:w-12 bg-[#1a3675] rounded-full"></div>
                    <div class="h-1.5 w-1.5 bg-[#1a3675] rounded-full"></div>
                    <div class="h-1.5 w-1.5 bg-[#1a3675] rounded-full"></div>
                </div>
            </div>

            {{-- Grid Divisi (3 Kolom) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                
                {{-- Card Divisi 1 --}}
                <div class="bg-white border border-gray-200/80 rounded-2xl shadow-xs p-6 sm:p-8 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl cursor-pointer group">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1a3675] flex items-center justify-center mb-4 group-hover:bg-[#1a3675] group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-[#1a3675] transition-colors leading-snug mb-2">
                        Perencanaan Hutan
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                        Fokus pada inventarisasi sumberdaya hutan, perencanaan tata ruang dan tutupan lahan, sistem informasi geospasial, serta pemodelan hutan masa depan.
                    </p>
                </div>

                {{-- Card Divisi 2 --}}
                <div class="bg-white border border-gray-200/80 rounded-2xl shadow-xs p-6 sm:p-8 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl cursor-pointer group">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1a3675] flex items-center justify-center mb-4 group-hover:bg-[#1a3675] group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 0l8 5 8-5"/></svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-[#1a3675] transition-colors leading-snug mb-2">
                        Pemanfaatan<br class="hidden sm:inline"> Sumber Daya Hutan
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                        Pengembangan teknologi pemanfaatan hasil hutan kayu & bukan kayu, ergonomi kehutanan, pemanenan ramah lingkungan (RIL), dan efisiensi rantai pasok.
                    </p>
                </div>

                {{-- Card Divisi 3 --}}
                <div class="bg-white border border-gray-200/80 rounded-2xl shadow-xs p-6 sm:p-8 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl cursor-pointer group">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1a3675] flex items-center justify-center mb-4 group-hover:bg-[#1a3675] group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-[#1a3675] transition-colors leading-snug mb-2">
                        Kebijakan Kehutanan
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                        Kajian tata kelola kehutanan, perhutanan sosial, resolusi konflik tenurial, ekonomi lingkungan, dan formulasi kebijakan konservasi sumberdaya alam.
                    </p>
                </div>

            </div>

        </div>
    </div>
</x-layouts.main>