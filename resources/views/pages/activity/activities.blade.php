<x-layouts.main>
    <x-slot:title>
        Aktivitas Terkini | DosenManhut
    </x-slot>

    {{-- Data Dummy (Pastikan ada 'date_raw' format YYYY-MM-DD) --}}
<!--     @php
        $aktivitasList = [
            ['kategori' => 'Tutor', 'dosen' => 'Prof. Fulani Fulano', 'tanggal' => '10 Juni 2029', 'date_raw' => '2029-06-10', 'judul' => 'Penguatan Kapasitas Kelompok Tani Hutan', 'desc' => 'Kegiatan pengabdian kepada masyarakat dalam rangka penguatan kapasitas kelompok tani hutan menuju pengelolaan hutan rakyat lestari.', 'slug' => 'penguatan-kapasitas-kelompok-tani-hutan'],
            ['kategori' => 'Pembicara', 'dosen' => 'Dr. Budi Santoso', 'tanggal' => '12 Juni 2029', 'date_raw' => '2029-06-12', 'judul' => 'Seminar Nasional Perencanaan Kehutanan', 'desc' => 'Seminar nasional membahas strategi perencanaan kehutanan di era perubahan iklim dan dampaknya terhadap ekosistem.', 'slug' => 'seminar-nasional-perencanaan-kehutanan'],
            ['kategori' => 'Lokakarya', 'dosen' => 'Siti Nurhaliza, M.Hut', 'tanggal' => '15 Juni 2029', 'date_raw' => '2029-06-15', 'judul' => 'Workshop Pemanfaatan Hasil Hutan Bukan Kayu', 'desc' => 'Workshop interaktif mengenai inovasi dan optimalisasi pemanfaatan hasil hutan bukan kayu (HHBK) untuk kesejahteraan masyarakat sekitar.', 'slug' => 'workshop-pemanfaatan-hasil-hutan-bukan-kayu'],
            ['kategori' => 'Tutor', 'dosen' => 'Prof. Rahmat Hidayat', 'tanggal' => '20 Juni 2029', 'date_raw' => '2029-06-20', 'judul' => 'Pelatihan Sistem Informasi Geografis (SIG)', 'desc' => 'Pelatihan dasar hingga menengah penggunaan aplikasi SIG untuk pemetaan kawasan hutan dan analisis tutupan lahan.', 'slug' => 'pelatihan-sistem-informasi-geografis-sig'],
            ['kategori' => 'Seminar', 'dosen' => 'Ir. Rudi Hermawan', 'tanggal' => '22 Juni 2029', 'date_raw' => '2029-06-22', 'judul' => 'Kebijakan Resolusi Konflik Tenurial Kehutanan', 'desc' => 'Diskusi panel mengenai kebijakan terbaru dalam penyelesaian konflik tenurial di kawasan hutan negara dan hutan adat.', 'slug' => 'kebijakan-resolusi-konflik-tenurial-kehutanan'],
            ['kategori' => 'Lomba', 'dosen' => 'Dr. Fitri Ani', 'tanggal' => '25 Juni 2029', 'date_raw' => '2029-06-25', 'judul' => 'Lomba Karya Tulis Ilmiah Kehutanan Nasional', 'desc' => 'Pendampingan mahasiswa dalam penyusunan karya tulis ilmiah tingkat nasional dengan tema inovasi pengelolaan hutan lestari.', 'slug' => 'lomba-karya-tulis-ilmiah-kehutanan-nasional']
        ];
    @endphp -->

    <div class="bg-[#fafafc] w-full min-h-screen py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. Bagian Header --}}
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2">
                    Kumpulan Aktivitas Terbaru
                </h1>
                <p class="text-sm md:text-base text-gray-600">
                    Yuk lihat aktivitas terbaru dari dosen manajemen hutan!
                </p>
            </div>

            {{-- 2. Bagian Pencarian & Tombol Filter --}}
            <div class="flex items-center gap-3 w-full mb-10">
                
                {{-- Input Pencarian --}}
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           id="searchInput"
                           placeholder="Cari Aktivitas atau Nama Dosen" 
                           class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1a3675]/50 focus:border-[#1a3675] text-sm text-gray-700 bg-white">
                </div>

                {{-- Wrapper Dropdown Filter --}}
                <div class="relative shrink-0">
                    <button id="filterBtn" class="bg-[#1a3675] hover:bg-blue-800 text-white p-3 rounded-lg shadow-sm transition-colors flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1a3675]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    </button>

                    {{-- Menu Dropdown (Diperlebar menjadi w-64 untuk menampung kalender) --}}
                    <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden origin-top-right">
                        
                        {{-- Kategori Section --}}
                        <div class="px-4 pt-4 pb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kategori</div>
                        <button class="filter-opt block w-full text-left px-4 py-2 text-sm font-bold bg-gray-50 text-[#1a3675] hover:bg-gray-100" data-kategori="semua">Semua Kategori</button>
                        <button class="filter-opt block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#1a3675]" data-kategori="tutor">Tutor</button>
                        <button class="filter-opt block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#1a3675]" data-kategori="pembicara">Pembicara</button>
                        <button class="filter-opt block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#1a3675]" data-kategori="lokakarya">Lokakarya</button>
                        <button class="filter-opt block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#1a3675]" data-kategori="seminar">Seminar</button>
                        
                        <div class="border-t border-gray-100 my-2"></div>
                        
                        {{-- Rentang Waktu Section --}}
                        <div class="px-4 pt-1 pb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Rentang Waktu</div>
                        <div class="px-4 pb-4 flex flex-col gap-3">
                            <div>
                                <label class="text-[11px] text-gray-500 mb-1 block">Mulai Tanggal</label>
                                <input type="date" id="startDate" class="w-full text-xs p-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#1a3675] focus:border-[#1a3675] text-gray-700 cursor-pointer">
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 mb-1 block">Sampai Tanggal</label>
                                <input type="date" id="endDate" class="w-full text-xs p-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#1a3675] focus:border-[#1a3675] text-gray-700 cursor-pointer">
                            </div>
                            
                            <div class="flex gap-2 mt-1">
                                <button id="resetDateBtn" class="w-1/3 bg-gray-100 text-gray-600 text-xs py-2 rounded-md font-semibold hover:bg-gray-200 transition">Reset</button>
                                <button id="applyDateBtn" class="w-2/3 bg-[#1a3675] text-white text-xs py-2 rounded-md font-bold hover:bg-blue-800 transition">Terapkan</button>
                            </div>
                        </div>

                    </div>
                </div>
                
            </div>

            {{-- 3. Grid Daftar Aktivitas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="aktivitasGrid">
                
            {{-- Ganti $aktivitasList menjadi $activities (sesuai nama variabel dari Controller) --}}
            @foreach ($activities as $item)
            @php
                $imgUrl = $item->primary_image_url ?? $item->primaryPicture?->path ?? $item->pictures?->first()?->path;
            @endphp
            <a href="{{ route('activity.show', $item->id) }}" 
               class="aktivitas-card block bg-white border border-gray-200 rounded-2xl p-4 md:p-5 shadow-sm flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group cursor-pointer text-left" 
                 data-judul="{{ strtolower($item->activity_name) }}" 
                 data-dosen="{{ strtolower($item->user->name ?? 'Nama Dosen') }}"
                 data-kategori="{{ strtolower(is_array($item->activity_type) ? implode(', ', $item->activity_type) : $item->activity_type) }}"
                 data-date="{{ $item->activity_date_start ? $item->activity_date_start->format('Y-m-d') : '' }}">
                
                <div class="w-full h-48 md:h-[220px] rounded-xl mb-4 overflow-hidden bg-gray-100 relative">
                    {{-- Tampilkan gambar utama jika ada, jika tidak pakai placeholder --}}
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" 
                             alt="{{ $item->activity_name }}" 
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-full bg-[#cbd5e1] transition-transform duration-500 group-hover:scale-105 flex items-center justify-center text-gray-400">
                            <span>No Image</span>
                        </div>
                    @endif
                </div>
                
                <div class="flex items-center justify-between text-[11px] md:text-xs font-bold mb-2.5">
                    <span class="bg-[#1a3675]/10 text-[#1a3675] px-2.5 py-0.5 rounded-md font-semibold">
                        {{ is_array($item->activity_type) ? implode(', ', $item->activity_type) : $item->activity_type }}
                    </span>
                    <span class="text-gray-500 font-medium">
                        {{ $item->activity_date_start ? $item->activity_date_start->translatedFormat('d F Y') : '-' }}
                    </span>
                </div>
                
                <h3 class="text-lg md:text-xl font-bold text-[#1a3675] mb-1.5 group-hover:text-blue-700 transition-colors line-clamp-2">
                    {{ $item->activity_name }}
                </h3>

                {{-- Nama Dosen di Bawah Judul --}}
                <div class="text-xs font-semibold text-gray-700 mb-2.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#1a3675] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="truncate">{{ $item->user->name ?? 'Nama Dosen' }}</span>
                </div>

                <p class="text-xs text-gray-500 leading-relaxed line-clamp-3">
                    {{ strip_tags($item->description) }}
                </p>
            </a>
            @endforeach

            </div>

            {{-- Pesan Kosong --}}
            <div id="noResult" class="hidden text-center py-12">
                <p class="text-gray-500 font-medium">Maaf, Aktivitas yang kamu cari tidak ditemukan pada rentang waktu ini.</p>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const cards = document.querySelectorAll('.aktivitas-card');
            const noResultMsg = document.getElementById('noResult');
            
            const filterBtn = document.getElementById('filterBtn');
            const filterDropdown = document.getElementById('filterDropdown');
            const filterOpts = document.querySelectorAll('.filter-opt');
            
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            const applyDateBtn = document.getElementById('applyDateBtn');
            const resetDateBtn = document.getElementById('resetDateBtn');

            // State Variabel
            let searchQuery = '';
            let currentKategori = 'semua';
            let filterStartDate = null;
            let filterEndDate = null;

            // Toggle Buka/Tutup Dropdown
            filterBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                filterDropdown.classList.toggle('hidden');
            });

            // Cegah dropdown tertutup saat klik di dalam area dropdown itu sendiri
            filterDropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            document.addEventListener('click', (e) => {
                if (!filterBtn.contains(e.target)) {
                    filterDropdown.classList.add('hidden');
                }
            });

            // FUNGSI UTAMA: Menyaring Berdasarkan 3 Parameter
            function saringAktivitas() {
                let hasVisibleCard = false;

                cards.forEach(card => {
                    const judul = card.getAttribute('data-judul');
                    const dosen = card.getAttribute('data-dosen');
                    const kategori = card.getAttribute('data-kategori');
                    const cardDate = new Date(card.getAttribute('data-date'));
                    
                    // 1. Cek Ketikan Search
                    const matchSearch = judul.includes(searchQuery) || dosen.includes(searchQuery);
                    
                    // 2. Cek Kategori
                    const matchKategori = (currentKategori === 'semua' || kategori === currentKategori);
                    
                    // 3. Cek Rentang Waktu (Date Range)
                    let matchDate = true;
                    if (filterStartDate) {
                        matchDate = matchDate && (cardDate >= filterStartDate);
                    }
                    if (filterEndDate) {
                        matchDate = matchDate && (cardDate <= filterEndDate);
                    }

                    // Tampilkan jika lulus ketiga filter
                    if (matchSearch && matchKategori && matchDate) {
                        card.classList.remove('hidden');
                        hasVisibleCard = true;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                if (hasVisibleCard) {
                    noResultMsg.classList.add('hidden');
                } else {
                    noResultMsg.classList.remove('hidden');
                }
            }

            // Aksi: Ketik Pencarian
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase();
                saringAktivitas();
            });

            // Aksi: Pilih Kategori
            filterOpts.forEach(opt => {
                opt.addEventListener('click', (e) => {
                    filterOpts.forEach(o => {
                        o.classList.remove('font-bold', 'bg-gray-50', 'text-[#1a3675]');
                        o.classList.add('text-gray-700');
                    });
                    
                    const clickedOpt = e.currentTarget;
                    clickedOpt.classList.remove('text-gray-700');
                    clickedOpt.classList.add('font-bold', 'bg-gray-50', 'text-[#1a3675]');

                    currentKategori = clickedOpt.getAttribute('data-kategori');
                    saringAktivitas();
                });
            });

            // Aksi: Terapkan Rentang Waktu
            applyDateBtn.addEventListener('click', () => {
                // Konversi value input ke object Date Javascript
                filterStartDate = startDateInput.value ? new Date(startDateInput.value) : null;
                filterEndDate = endDateInput.value ? new Date(endDateInput.value) : null;
                
                saringAktivitas();
                filterDropdown.classList.add('hidden'); // Tutup dropdown setelah apply
            });

            // Aksi: Reset Rentang Waktu
            resetDateBtn.addEventListener('click', () => {
                startDateInput.value = '';
                endDateInput.value = '';
                filterStartDate = null;
                filterEndDate = null;
                
                saringAktivitas();
            });
        });
    </script>
</x-layouts.main>