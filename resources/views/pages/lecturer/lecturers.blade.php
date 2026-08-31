<x-layouts.main>
    <x-slot:title>
        Staff Pengajar | DosenManhut
    </x-slot>

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
                    <button data-filter="perencanaan kehutanan" class="category-btn px-6 py-1.5 rounded-full text-sm font-semibold shadow-sm transition-all active:scale-95 bg-white border border-gray-200 text-gray-600 hover:border-[#1a3675] hover:shadow-md">
                        Perencanaan Hutan
                    </button>
                    <button data-filter="pemanfaatan sumberdaya hutan" class="category-btn px-6 py-1.5 rounded-full text-sm font-semibold shadow-sm transition-all active:scale-95 bg-white border border-gray-200 text-gray-600 hover:border-[#1a3675] hover:shadow-md">
                        Pemanfaatan SDH
                    </button>
                    <button data-filter="kebijakan kehutanan" class="category-btn px-6 py-1.5 rounded-full text-sm font-semibold shadow-sm transition-all active:scale-95 bg-white border border-gray-200 text-gray-600 hover:border-[#1a3675] hover:shadow-md">
                        Kebijakan Kehutanan
                    </button>
                </div>

            </div>

            @php 
                $perPage = 10; // Mau tampilkan berapa dosen per halaman?
                $totalPages = ceil(count($lecturers) / $perPage); 
            @endphp

            {{-- Grid Daftar Dosen --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6" id="dosenGrid">
                @foreach ($lecturers as $lecturer)
                    {{-- Hitung otomatis dosen ini masuk halaman berapa --}}
                    @php $page = floor($loop->index / $perPage) + 1; @endphp

                    <a href="{{ route('lecturer.show', $lecturer['id']) }}"
                       class="dosen-card block relative rounded-xl overflow-hidden shadow-[0_4px_15px_-3px_rgba(0,0,0,0.1)] group aspect-[3/4] bg-gray-200 cursor-pointer transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl {{ $page > 1 ? 'hidden' : '' }}" 
                       data-name="{{ strtolower($lecturer['name']) }}"
                       data-category="{{ strtolower($lecturer['division']) }}"
                       data-page="{{ $page }}"> {{-- Atribut penting untuk JS --}}

                        <div class="w-full h-full bg-[#cbd5e1] transition-transform duration-500 group-hover:scale-110">
                            <img src="{{ $lecturer['image'] ?? asset('images/default-avatar.png') }}" alt="{{ $lecturer['name'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1a3675]/95 via-[#1a3675]/5 to-transparent"></div>

                        <div class="absolute bottom-0 left-0 p-3 md:p-4 text-white w-full transform transition-transform duration-300 group-hover:-translate-y-1">
                            <h3 class="font-bold text-sm md:text-base mb-1 leading-tight line-clamp-2">{{ $lecturer['name'] }}</h3>
                            <p class="text-[9px] md:text-[10px] text-gray-200 line-clamp-1">{{ $lecturer['division'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Container Tombol Pagination (Muncul kalau halamannya > 1) --}}
            @if($totalPages > 1)
                <nav id="paginationNav" class="flex items-center justify-end space-x-2 mt-10 hidden" aria-label="Pagination">
                    {{-- Tombol Prev --}}
                    <button id="btn-prev" onclick="changePage(-1)" disabled 
                            class="px-4 py-2 text-sm font-semibold text-[#1a3675] bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed transition-colors shadow-sm">
                        &laquo; Prev
                    </button>
                
                    {{-- Container untuk Deretan Angka Halaman --}}
                    <div id="page-numbers" class="flex space-x-2"></div>
                
                    {{-- Tombol Next --}}
                    <button id="btn-next" onclick="changePage(1)" 
                            class="px-4 py-2 text-sm font-semibold text-[#1a3675] bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed transition-colors shadow-sm">
                        Next &raquo;    
                    </button>
                </nav>
            @endif
            
            {{-- Pesan Jika Tidak Ada Hasil (Disembunyikan default) --}}
            <div id="noResult" class="hidden text-center py-10">
                <p class="text-gray-500 font-medium">Maaf, Dosen yang kamu cari tidak ditemukan.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categoryBtns = document.querySelectorAll('.category-btn');
            const cards = Array.from(document.querySelectorAll('.dosen-card'));
            const noResultMsg = document.getElementById('noResult');
            const paginationNav = document.getElementById('paginationNav');

            let currentCategory = 'semua';
            let searchQuery = '';

            // --- PENGATURAN PAGINASI ---
            let currentPage = 1;
            const perPage = 10;

            // FUNGSI UTAMA: Update Tampilan
            function updateView() {
                let matchedCards = [];

                // 1. Saring dosen sesuai Search dan Kategori
                cards.forEach(card => {
                    const name = card.getAttribute('data-name');
                    const category = card.getAttribute('data-category');

                    const matchSearch = name.includes(searchQuery);
                    const matchCategory = (currentCategory === 'semua' || category === currentCategory);

                    if (matchSearch && matchCategory) {
                        matchedCards.push(card);
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // 2. Jika Tidak Ada Dosen yang Cocok
                if (matchedCards.length === 0) {
                    noResultMsg.classList.remove('hidden');
                    if (paginationNav) paginationNav.classList.add('hidden');
                    return;
                } else {
                    noResultMsg.classList.add('hidden');
                }

                // 3. Hitung Paginasi Baru Berdasarkan Hasil Filter
                const totalPages = Math.ceil(matchedCards.length / perPage);
                if (currentPage > totalPages) currentPage = 1;

                // Tampilkan dosen hanya untuk halaman saat ini
                matchedCards.forEach((card, index) => {
                    const cardPage = Math.floor(index / perPage) + 1;

                    if (cardPage === currentPage) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // 4. Update UI Tombol Paginasi
                if (paginationNav) {
                    if (totalPages > 1) {
                        paginationNav.classList.remove('hidden');

                        // Render deretan angka halaman
                        const pageNumbersContainer = document.getElementById('page-numbers');
                        pageNumbersContainer.innerHTML = ''; // Bersihkan isi sebelumnya

                        for (let i = 1; i <= totalPages; i++) {
                            if (i === currentPage) {
                                // Desain tombol aktif (Biru)
                                pageNumbersContainer.innerHTML += `
                                    <button class="px-4 py-2 text-sm font-bold text-white bg-[#1a3675] border border-[#1a3675] rounded-lg shadow-md cursor-default">
                                        ${i}
                                    </button>
                                `;
                            } else {
                                // Desain tombol tidak aktif (Putih)
                                pageNumbersContainer.innerHTML += `
                                    <button onclick="goToPage(${i})" class="px-4 py-2 text-sm font-semibold text-[#1a3675] bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                                        ${i}
                                    </button>
                                `;
                            }
                        }

                        document.getElementById('btn-prev').disabled = (currentPage === 1);
                        document.getElementById('btn-next').disabled = (currentPage === totalPages);
                    } else {
                        paginationNav.classList.add('hidden');
                    }
                }
            }

            // Ekspos fungsi ganti halaman ke HTML (tombol onClick)
            window.changePage = function(direction) {
                currentPage += direction;
                updateView();
                window.scrollTo({ top: document.getElementById('dosenGrid').offsetTop - 100, behavior: 'smooth' });
            };

            // Fungsi untuk loncat ke angka halaman tertentu
            window.goToPage = function(page) {
                currentPage = page;
                updateView();
                window.scrollTo({ top: document.getElementById('dosenGrid').offsetTop - 100, behavior: 'smooth' });
            };

            // EVENT: Saat Mengetik di Kolom Pencarian
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    searchQuery = e.target.value.toLowerCase();
                    currentPage = 1;
                    updateView();
                });
            }

            // EVENT: Saat Klik Tombol Kategori
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    // Reset Warna Tombol
                    categoryBtns.forEach(b => {
                        b.classList.remove('bg-[#1a3675]', 'text-white', 'border-transparent');
                        b.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
                    });

                    // Warnai Tombol Aktif
                    const clickedBtn = e.currentTarget;
                    clickedBtn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
                    clickedBtn.classList.add('bg-[#1a3675]', 'text-white', 'border-transparent');

                    currentCategory = clickedBtn.getAttribute('data-filter');
                    currentPage = 1; // Balik ke halaman 1 tiap kali ganti kategori
                    updateView();
                });
            });

            // Jalankan saat web pertama kali dibuka
            updateView();
        });
    </script>
</x-layouts.main>