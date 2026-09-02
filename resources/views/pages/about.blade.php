<x-layouts.main>
    <x-slot:title>
        Tentang Kami | DosenManhut
    </x-slot>

    {{-- Wrapper Halaman dengan background off-white dan relative untuk ornamen --}}
    <div class="relative bg-[#fafafc] w-full min-h-screen overflow-hidden pb-20">
        
        {{-- Ornamen Kiri Atas --}}
        <div class="absolute top-0 left-0 w-64 h-64 -translate-x-1/4 -translate-y-1/4 opacity-40 pointer-events-none">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-blue-200 fill-current">
                <path d="M100 0 A100 100 0 0 1 200 100 L100 100 Z" />
                <circle cx="50" cy="150" r="40" fill="none" stroke="currentColor" stroke-width="4"/>
                <circle cx="50" cy="150" r="25" fill="none" stroke="currentColor" stroke-width="2"/>
            </svg>
        </div>

        {{-- Ornamen Kanan Bawah --}}
        <div class="absolute bottom-0 right-0 w-80 h-80 translate-x-1/4 translate-y-1/4 opacity-40 pointer-events-none">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-blue-300">
                <rect x="100" y="50" width="60" height="60" transform="rotate(45 130 80)" fill="currentColor" opacity="0.5"/>
                <rect x="50" y="100" width="60" height="60" transform="rotate(45 80 130)" fill="currentColor" opacity="0.3"/>
                <rect x="150" y="150" width="60" height="60" transform="rotate(45 180 180)" fill="none" stroke="currentColor" stroke-width="4"/>
            </svg>
        </div>

        {{-- =======================================
             KONTEN UTAMA
             ======================================= --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 md:pt-24">
            
            {{-- 1. Bagian Judul dan Teks Deskripsi --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-12">
                
                {{-- Kiri: Judul --}}
                <div class="w-full md:w-1/3">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                        Tentang Kami
                    </h1>
                    {{-- Aksen Garis Bawah (Garis + 2 Titik) --}}
                    <div class="flex items-center gap-1.5 mt-3">
                        <div class="h-1.5 w-16 bg-[#1a3675] rounded-full"></div>
                        <div class="h-1.5 w-1.5 bg-[#1a3675] rounded-full"></div>
                        <div class="h-1.5 w-1.5 bg-[#1a3675] rounded-full"></div>
                    </div>
                </div>

                {{-- Kanan: Teks --}}
                <div class="w-full md:w-2/3">
                    {{-- Di gambar teksnya berwarna biru dengan garis bawah, kamu bisa hapus class 'text-blue-700 underline decoration-blue-300' jika ingin teks normal (abu-abu/hitam) --}}
                    <p class="text-[12px] md:text-base text-[#1a3675] font-light leading-relaxed decoration-[#1a3675]/30 underline-offset-4 text-left">
                        Lorem ipsum dolor sit amet, voluptate ut nostrud consequat ut nulla. Reprehenderit 
                        laborum consectetur ad laboris adipiscing nostrud in veniam anim. Et enim nostrud nisi 
                        consectetur deserunt sunt eu sunt. Ut in aliquip in tempor consectetur deserunt culpa 
                        voluptate. Ad velit elit sed dolor nisi nisi ad esse dolor. Ut et id cillum ex cillum ut est 
                        dolore. Ut consequat elit nisi.
                    </p>
                </div>

            </div>

            {{-- 2. Gambar Besar (Banner) --}}
            <div class="w-full h-[250px] sm:h-[400px] md:h-[500px] rounded-2xl md:rounded-[2rem] overflow-hidden shadow-xl mb-20 group cursor-pointer">
                {{-- Pastikan file gambar tim/pengurus ini ada di public/images/ --}}
                <img src="{{ asset('images/hero_section.jpg') }}" 
                     alt="Tim Dosen" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            </div>

            {{-- 3. Bagian Divisi --}}
            <div class="mb-8">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                    Divisi
                </h2>
                {{-- Aksen Garis Bawah (Garis + 2 Titik) --}}
                <div class="flex items-center gap-1.5 mt-3">
                    <div class="h-1.5 w-12 bg-[#1a3675] rounded-full"></div>
                    <div class="h-1.5 w-1.5 bg-[#1a3675] rounded-full"></div>
                    <div class="h-1.5 w-1.5 bg-[#1a3675] rounded-full"></div>
                </div>
            </div>

            {{-- Grid Divisi (3 Kolom) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                
                {{-- Card Divisi 1 --}}
                <div class="bg-[#fafafc] border-2 border-white rounded-2xl shadow-[5px_5px_15px_#e6e6e6,-5px_-5px_15px_#ffffff] p-8 h-48 md:h-64 flex items-center justify-center text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-[8px_8px_20px_#d9d9d9,-8px_-8px_20px_#ffffff] cursor-pointer group flex-col">
                    <h3 class="text-xl md:text-xl font-bold text-gray-800 group-hover:text-[#1a3675] transition-colors leading-snug py-6">
                        Perencanaan Hutan
                    </h3>
                    <span class="text-sm">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </span>
                </div>

                {{-- Card Divisi 2 --}}
                <div class="bg-[#fafafc] border-2 border-white rounded-2xl shadow-[5px_5px_15px_#e6e6e6,-5px_-5px_15px_#ffffff] p-8 h-48 md:h-64 flex items-center justify-center text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-[8px_8px_20px_#d9d9d9,-8px_-8px_20px_#ffffff] cursor-pointer group flex-col">
                    <h3 class="text-xl md:text-xl font-bold text-gray-800 group-hover:text-[#1a3675] transition-colors leading-snug py-3">
                        Pemanfaatan<br>Sumber Daya Hutan
                    </h3>
                    <span class="text-sm">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </span>
                </div>

                {{-- Card Divisi 3 --}}
                <div class="bg-[#fafafc] border-2 border-white rounded-2xl shadow-[5px_5px_15px_#e6e6e6,-5px_-5px_15px_#ffffff] p-8 h-48 md:h-64 flex items-center justify-center text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-[8px_8px_20px_#d9d9d9,-8px_-8px_20px_#ffffff] cursor-pointer group flex-col">
                    <h3 class="text-xl md:text-xl font-bold text-gray-800 group-hover:text-[#1a3675] transition-colors leading-snug py-4">
                        Kebijakan Kehutanan
                    </h3>
                    <span class="text-sm">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </span>
                </div>

            </div>

        </div>
    </div>
</x-layouts.main>