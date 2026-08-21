<x-layouts.main>
    <x-slot:title>
        Staff Pengajar | DosenManhut
    </x-slot>

    {{-- Data Dummy untuk Simulasi Filter --}}
    @php
        $dosenList = [
            ['nama' => 'Dr. Budi Santoso', 'divisi' => 'Perencanaan Hutan', 'kategori' => 'perencanaan'],
            ['nama' => 'Prof. Andi Wahyu', 'divisi' => 'Pemanfaatan SDH', 'kategori' => 'pemanfaatan'],
            ['nama' => 'Siti Nurhaliza, M.Hut', 'divisi' => 'Kebijakan Kehutanan', 'kategori' => 'kebijakan'],
            ['nama' => 'Ir. Rudi Hermawan', 'divisi' => 'Perencanaan Hutan', 'kategori' => 'perencanaan'],
            ['nama' => 'Dr. Lestari Alam', 'divisi' => 'Pemanfaatan SDH', 'kategori' => 'pemanfaatan'],
            ['nama' => 'Bambang Pamungkas', 'divisi' => 'Kebijakan Kehutanan', 'kategori' => 'kebijakan'],
            ['nama' => 'Dr. Fitri Ani', 'divisi' => 'Perencanaan Hutan', 'kategori' => 'perencanaan'],
            ['nama' => 'Prof. Rahmat Hidayat', 'divisi' => 'Pemanfaatan SDH', 'kategori' => 'pemanfaatan'],
            ['nama' => 'Agus Yudhoyono, M.Si', 'divisi' => 'Kebijakan Kehutanan', 'kategori' => 'kebijakan'],
            ['nama' => 'Dr. Maya Sari', 'divisi' => 'Perencanaan Hutan', 'kategori' => 'perencanaan'],
        ];
    @endphp

    {{-- Wrapper Halaman --}}
    <div class="bg-[#fafafc] w-full min-h-screen py-12 md:py-16">
        {{-- Diubah menjadi max-w-7xl agar SEJAJAR persis dengan Navbar --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. Bagian Header --}}
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2">
                    Jajaran Staff Pengajar
                </h1>
                <p class="text-sm md:text-base text-gray-600">
                    Yuk cari dosen yang kamu ingin ketahui lebih lanjut. Gunakan fitur pencarian atau filter kategori untuk mempermudah pencarian.
                </p>
            </div>

            {{-- 2. Bagian Pencarian & Filter --}}
            <div class="flex flex-col gap-4 mb-10">
                
                {{-- Search Bar & Tombol Filter --}}
                <div class="flex items-center gap-3 w-full">
                    {{-- Input Pencarian --}}
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               id="searchInput"
                               placeholder="Cari Dosen disini" 
                               class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1a3675]/50 focus:border-[#1a3675] text-sm text-gray-700 bg-white">
                    </div>

                    {{-- Tombol Ikon Filter Biru --}}
                    <!-- <button class="bg-[#1a3675] hover:bg-blue-800 text-white p-3 rounded-lg shadow-sm transition-colors shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    </button> -->
                </div>

                {{-- Kumpulan Pill Kategori --}}
                <div class="flex flex-wrap items-center gap-3" id="filterContainer">
                    <button data-filter="semua" class="category-btn active px-6 py-1.5 rounded-full text-sm font-semibold shadow-md transition-all active:scale-95 bg-[#1a3675] text-white border border-transparent">
                        Semua
                    </button>
                    <button data-filter="perencanaan" class="category-btn px-6 py-1.5 rounded-full text-sm font-semibold shadow-sm transition-all active:scale-95 bg-white border border-gray-200 text-gray-600 hover:border-[#1a3675] hover:shadow-md">
                        Perencanaan Hutan
                    </button>
                    <button data-filter="pemanfaatan" class="category-btn px-6 py-1.5 rounded-full text-sm font-semibold shadow-sm transition-all active:scale-95 bg-white border border-gray-200 text-gray-600 hover:border-[#1a3675] hover:shadow-md">
                        Pemanfaatan SDH
                    </button>
                    <button data-filter="kebijakan" class="category-btn px-6 py-1.5 rounded-full text-sm font-semibold shadow-sm transition-all active:scale-95 bg-white border border-gray-200 text-gray-600 hover:border-[#1a3675] hover:shadow-md">
                        Kebijakan Kehutanan
                    </button>
                </div>

            </div>

            {{-- 3. Grid Daftar Dosen --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6" id="dosenGrid">
                
                @foreach ($dosenList as $dosen)
                {{-- Menyimpan data nama dan kategori di dalam atribut HTML untuk dibaca Javascript --}}
                <div class="dosen-card relative rounded-xl overflow-hidden shadow-[0_4px_15px_-3px_rgba(0,0,0,0.1)] group aspect-[3/4] bg-gray-200 cursor-pointer transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl" 
                     data-name="{{ strtolower($dosen['nama']) }}" 
                     data-category="{{ $dosen['kategori'] }}">
                    
                    <div class="w-full h-full bg-[#cbd5e1] transition-transform duration-500 group-hover:scale-110"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#1a3675]/95 via-[#1a3675]/40 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 p-3 md:p-4 text-white w-full transform transition-transform duration-300 group-hover:-translate-y-1">
                        <h3 class="font-bold text-sm md:text-base mb-1 leading-tight line-clamp-2">{{ $dosen['nama'] }}</h3>
                        <p class="text-[9px] md:text-[10px] text-gray-200 line-clamp-1">{{ $dosen['divisi'] }}</p>
                    </div>

                </div>
                @endforeach

            </div>
            
            {{-- Pesan Jika Tidak Ada Hasil (Disembunyikan default) --}}
            <div id="noResult" class="hidden text-center py-10">
                <p class="text-gray-500 font-medium">Maaf, Dosen yang kamu cari tidak ditemukan.</p>
            </div>

        </div>
    </div>

    {{-- Script Logika Filter & Search --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categoryBtns = document.querySelectorAll('.category-btn');
            const cards = document.querySelectorAll('.dosen-card');
            const noResultMsg = document.getElementById('noResult');
            
            let currentCategory = 'semua';
            let searchQuery = '';

            // Fungsi utama untuk memfilter
            function filterDosen() {
                let hasVisibleCard = false;

                cards.forEach(card => {
                    const name = card.getAttribute('data-name');
                    const category = card.getAttribute('data-category');
                    
                    // Cek kecocokan ketikan (search) dan tombol kategori
                    const matchSearch = name.includes(searchQuery);
                    const matchCategory = (currentCategory === 'semua' || category === currentCategory);

                    if (matchSearch && matchCategory) {
                        card.classList.remove('hidden');
                        hasVisibleCard = true;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Tampilkan pesan "Tidak ditemukan" jika semua kartu hidden
                if (hasVisibleCard) {
                    noResultMsg.classList.add('hidden');
                } else {
                    noResultMsg.classList.remove('hidden');
                }
            }

            // Event Listener Ketikan Pencarian
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase();
                filterDosen();
            });

            // Event Listener Klik Kategori
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    
                    // 1. Reset semua warna tombol ke Abu-abu/Putih
                    categoryBtns.forEach(b => {
                        b.classList.remove('bg-[#1a3675]', 'text-white', 'border-transparent');
                        b.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
                    });

                    // 2. Warnai tombol yang sedang diklik jadi Biru
                    const clickedBtn = e.currentTarget;
                    clickedBtn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
                    clickedBtn.classList.add('bg-[#1a3675]', 'text-white', 'border-transparent');

                    // 3. Update kategori dan jalankan filter
                    currentCategory = clickedBtn.getAttribute('data-filter');
                    filterDosen();
                });
            });
        });
    </script>
</x-layouts.main>