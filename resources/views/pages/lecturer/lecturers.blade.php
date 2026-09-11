<x-layouts.main>
    <x-slot:title>
        Staff Pengajar | DosenManhut
    </x-slot>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <x-preloader />
    {{-- Wrapper Halaman --}}
    <div class="bg-[#fafafc] w-full min-h-screen py-8 sm:py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. Bagian Header --}}
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-2">
                    Jajaran Staff Pengajar
                </h1>
                <p class="text-xs sm:text-sm md:text-base text-gray-600 max-w-2xl leading-relaxed">
                    Yuk cari dosen yang kamu ingin ketahui lebih lanjut. Gunakan fitur pencarian atau filter kategori untuk mempermudah pencarian.
                </p>
            </div>

            {{-- 2. Bagian Pencarian & Filter --}}
            <div class="flex flex-col gap-4 mb-8 sm:mb-10">
                
                {{-- Search Bar --}}
                <div class="group relative w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-[#183669]">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           id="searchInput"
                           placeholder="Cari Dosen disini..." 
                           class="h-[44px] sm:h-[46px] w-full rounded-[10px] border-2 border-[#d6e0ee] bg-white pl-10 sm:pl-11 pr-10 font-inter text-[13px] sm:text-[14px] text-[#173a63] placeholder-[#8ca1b9] transition-all duration-200 hover:border-[#8ea9cb] focus:border-[#183669] focus:outline-none focus:ring-0 shadow-2xs">
                    
                    <button type="button" 
                            id="clearSearchBtn"
                            class="absolute inset-y-0 right-0 hidden items-center pr-3 sm:pr-3.5 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer"
                            aria-label="Hapus pencarian">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Style khusus Filter Pill untuk mencegah teks hilang saat active/hover --}}
                <style>
                    .category-btn.is-active {
                        background-color: #1a3675 !important;
                        color: #ffffff !important;
                        border-color: #1a3675 !important;
                        font-weight: 700 !important;
                    }
                    .category-btn.is-active:hover {
                        background-color: #152c61 !important;
                        color: #ffffff !important;
                    }
                    .category-btn:not(.is-active) {
                        background-color: #ffffff !important;
                        color: #4b5563 !important;
                        border-color: #d6e0ee !important;
                        font-weight: 600 !important;
                    }
                    .category-btn:not(.is-active):hover {
                        background-color: #f0f7ff !important;
                        color: #1a3675 !important;
                        border-color: #1a3675 !important;
                    }
                </style>

                {{-- Kumpulan Pill Kategori (Horizontal scrollable di mobile, wrap di desktop) --}}
                <div class="flex items-center gap-2 sm:gap-3 overflow-x-auto pb-1 -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-wrap no-scrollbar" id="filterContainer">
                    <button data-filter="semua" class="category-btn is-active shrink-0 px-4 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm shadow-2xs border transition-all active:scale-95 cursor-pointer">
                        Semua
                    </button>
                    <button data-filter="perencanaan kehutanan" class="category-btn shrink-0 px-4 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm shadow-2xs border transition-all active:scale-95 cursor-pointer">
                        Perencanaan Hutan
                    </button>
                    <button data-filter="pemanfaatan sumberdaya hutan" class="category-btn shrink-0 px-4 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm shadow-2xs border transition-all active:scale-95 cursor-pointer">
                        Pemanfaatan SDH
                    </button>
                    <button data-filter="kebijakan kehutanan" class="category-btn shrink-0 px-4 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm shadow-2xs border transition-all active:scale-95 cursor-pointer">
                        Kebijakan Kehutanan
                    </button>
                </div>

            </div>

            @php 
                $perPage = 10;
                $totalPages = ceil(count($lecturers) / $perPage); 
            @endphp

            {{-- Grid Daftar Dosen --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 md:gap-6" id="dosenGrid">
                @foreach ($lecturers as $lecturer)
                    @php 
                        $page = floor($loop->index / $perPage) + 1; 
                        $hasImage = !empty($lecturer['image']);
                    @endphp

                    <a href="{{ route('lecturer.show', $lecturer['id']) }}"
                       class="dosen-card block relative rounded-xl sm:rounded-2xl overflow-hidden shadow-xs hover:shadow-xl group/card aspect-[3/4] bg-gray-200 cursor-pointer transition-all duration-300 hover:-translate-y-1.5 {{ $page > 1 ? 'hidden' : '' }}" 
                       data-name="{{ strtolower($lecturer['name']) }}"
                       data-category="{{ strtolower($lecturer['division']) }}"
                       data-page="{{ $page }}">

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

                        <div class="absolute bottom-0 left-0 p-3 sm:p-4 text-white w-full pointer-events-none">
                            <h3 class="font-bold text-xs sm:text-sm md:text-base mb-0.5 leading-tight line-clamp-2 drop-shadow-[0_1px_2px_rgba(0,0,0,0.6)]">{{ $lecturer['name'] }}</h3>
                            <p class="text-[9px] sm:text-[10px] md:text-[11px] text-gray-200 line-clamp-1 font-medium drop-shadow-[0_1px_1px_rgba(0,0,0,0.5)]">{{ $lecturer['division'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Container Tombol Pagination --}}
            <nav id="paginationNav" class="flex items-center justify-center sm:justify-end space-x-1.5 sm:space-x-2 mt-8 sm:mt-10 {{ $totalPages <= 1 ? 'hidden' : '' }}" aria-label="Pagination">
                {{-- Tombol Prev --}}
                <button id="btn-prev" onclick="changePage(-1)" disabled 
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-[#1a3675] bg-white border border-[#d6e0ee] rounded-lg hover:bg-gray-50 disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed transition-colors shadow-2xs cursor-pointer">
                    &laquo; Prev
                </button>
            
                {{-- Container Deretan Angka Halaman --}}
                <div id="page-numbers" class="flex space-x-1 sm:space-x-1.5"></div>
            
                {{-- Tombol Next --}}
                <button id="btn-next" onclick="changePage(1)" 
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-[#1a3675] bg-white border border-[#d6e0ee] rounded-lg hover:bg-gray-50 disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed transition-colors shadow-2xs cursor-pointer">
                    Next &raquo;    
                </button>
            </nav>
            
            {{-- Pesan Jika Tidak Ada Hasil --}}
            <div id="noResult" class="hidden text-center py-12 sm:py-16">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 text-[#1a3675] mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-gray-600 font-semibold text-sm sm:text-base">Maaf, Dosen yang kamu cari tidak ditemukan.</p>
                <p class="text-xs text-gray-400 mt-1">Coba gunakan kata kunci lain atau ubah filter kategori.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            const categoryBtns = document.querySelectorAll('.category-btn');
            const cards = Array.from(document.querySelectorAll('.dosen-card'));
            const noResultMsg = document.getElementById('noResult');
            const paginationNav = document.getElementById('paginationNav');

            let currentCategory = 'semua';
            let searchQuery = '';

            // --- PENGATURAN PAGINASI ---
            let currentPage = 1;
            const perPage = 10;

            function updateClearBtn() {
                if (clearSearchBtn) {
                    if (searchInput.value.trim().length > 0) {
                        clearSearchBtn.classList.remove('hidden');
                        clearSearchBtn.classList.add('flex');
                    } else {
                        clearSearchBtn.classList.remove('flex');
                        clearSearchBtn.classList.add('hidden');
                    }
                }
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    searchQuery = '';
                    updateClearBtn();
                    currentPage = 1;
                    updateView();
                    searchInput.focus();
                });
            }

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

                        const pageNumbersContainer = document.getElementById('page-numbers');
                        pageNumbersContainer.innerHTML = '';

                        for (let i = 1; i <= totalPages; i++) {
                            if (i === currentPage) {
                                pageNumbersContainer.innerHTML += `
                                    <button class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white bg-[#1a3675] border border-[#1a3675] rounded-lg shadow-xs cursor-default">
                                        ${i}
                                    </button>
                                `;
                            } else {
                                pageNumbersContainer.innerHTML += `
                                    <button onclick="goToPage(${i})" class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-[#1a3675] bg-white border border-[#d6e0ee] rounded-lg hover:bg-gray-50 transition-colors shadow-2xs cursor-pointer">
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

            window.goToPage = function(page) {
                currentPage = page;
                updateView();
                window.scrollTo({ top: document.getElementById('dosenGrid').offsetTop - 100, behavior: 'smooth' });
            };

            // EVENT: Saat Mengetik di Kolom Pencarian
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    searchQuery = e.target.value.toLowerCase();
                    updateClearBtn();
                    currentPage = 1;
                    updateView();
                });
            }

            // EVENT: Saat Klik Tombol Kategori
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    categoryBtns.forEach(b => b.classList.remove('is-active'));

                    const clickedBtn = e.currentTarget;
                    clickedBtn.classList.add('is-active');

                    currentCategory = clickedBtn.getAttribute('data-filter');
                    currentPage = 1;
                    updateView();
                });
            });

            // Inisialisasi awal
            updateClearBtn();
            updateView();
        });
    </script>
</x-layouts.main>