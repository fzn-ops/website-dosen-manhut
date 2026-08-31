<x-layouts.main>
    <x-slot:title>
        Detail Dosen | Si Fulan
    </x-slot>

<!--     {{-- Data Dummy --}}
    @php
        $educationList = [
            ['degree' => 'Bachelor', 'major' => 'Ilmu Hutan Rakyat', 'univ' => 'Universitas Indonesia', 'year' => '2001'],
            ['degree' => 'Master', 'major' => 'Manajemen Kehutanan', 'univ' => 'Universitas Gadjah Mada', 'year' => '2004'],
            ['degree' => 'Doctoral', 'major' => 'Kebijakan Hutan', 'univ' => 'IPB University', 'year' => '2010'],
        ];

        $publicationList = [
            ['title' => 'Pengaruh Kebijakan Hutan Terhadap Masyarakat Adat', 'authors' => 'Si Fulan, Budi Santoso', 'publisher' => 'Jurnal Kehutanan RI', 'cited' => '120', 'year' => '2020'],
            ['title' => 'Strategi Pemanfaatan Hasil Hutan Bukan Kayu', 'authors' => 'Si Fulan', 'publisher' => 'Forestry Science Journal', 'cited' => '85', 'year' => '2021'],
            ['title' => 'Analisis Tutupan Lahan Menggunakan SIG', 'authors' => 'Andi Wahyu, Si Fulan', 'publisher' => 'Geospatial Indonesia', 'cited' => '45', 'year' => '2022'],
            ['title' => 'Resolusi Konflik Tenurial di Kawasan Hutan Konservasi', 'authors' => 'Si Fulan, Rahmat Hidayat', 'publisher' => 'Jurnal Hukum Lingkungan', 'cited' => '210', 'year' => '2023'],
        ];

        $aktivitasDosen = [
            ['nama' => 'Nama Acara Seminar Nasional', 'role' => 'Pembicara', 'year' => '2026', 'month' => 'Agustus - September', 'desc' => 'Lorem ipsum dolor sit amet, cupidatat eiusmod duis ut. Magna dolore dolor ex elit sed non cillum do aliqua adipiscing ad. Ullamco fugiat occaecat proident dolore incididunt eu pariatur officia.'],
            ['nama' => 'Lokakarya Pemanfaatan Hutan', 'role' => 'Tutor', 'year' => '2026', 'month' => 'Oktober', 'desc' => 'Lorem ipsum dolor sit amet, cupidatat eiusmod duis ut. Magna dolore dolor ex elit sed non cillum do aliqua adipiscing ad. Ullamco fugiat occaecat proident dolore incididunt eu pariatur officia.'],
            ['nama' => 'Konferensi Perubahan Iklim', 'role' => 'Keynote Speaker', 'year' => '2025', 'month' => 'Desember', 'desc' => 'Lorem ipsum dolor sit amet, cupidatat eiusmod duis ut. Magna dolore dolor ex elit sed non cillum do aliqua adipiscing ad. Ullamco fugiat occaecat proident dolore incididunt eu pariatur officia.'],
            ['nama' => 'Konferensi Perubahan Iklim', 'role' => 'Keynote Speaker', 'year' => '2025', 'month' => 'Desember', 'desc' => 'Lorem ipsum dolor sit amet, cupidatat eiusmod duis ut. Magna dolore dolor ex elit sed non cillum do aliqua adipiscing ad. Ullamco fugiat occaecat proident dolore incididunt eu pariatur officia.'],
        ];
    @endphp -->

    <div class="bg-[#fafafc] w-full min-h-screen py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <nav class="text-sm font-medium text-gray-500 mb-6">
                <a href="{{ url('/lecturers') }}" class="hover:text-[#1a3675]">Dosen</a>
                <span class="mx-1">/</span>
                <span class="text-[#1a3675] underline decoration-[#1a3675]/30 underline-offset-4">{{ $lecturer->user->name }}</span>
            </nav>

            {{-- BAGIAN ATAS (Profil & Info Akademik) --}}
            <div class="flex flex-col md:flex-row gap-8 mb-12">
                <div class="w-full md:w-1/3 lg:w-1/4 shrink-0">
                    <div class="w-full aspect-[3/4.2] bg-gray-200 rounded-2xl overflow-hidden shadow-md">
                        <div class="w-full h-full bg-[#cbd5e1]">
                            <img src="{{ asset('storage/' . $lecturer->image) }}" 
                                alt="{{ $lecturer->user->name }}"
                                class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-2/3 lg:w-3/4 flex flex-col">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-8 gap-4">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-1">{{ $lecturer->user->name }}</h1>
                            <p class="text-sm text-gray-600 font-medium">{{ $lecturer->division }}</p>
                        </div>
                        <div class="text-left sm:text-right text-xs text-gray-600">
                            <div class="flex sm:justify-end gap-3 mb-1">
                                <a href="{{ $lecturer->scholar_link }}" class="text-[#1a3675] underline hover:text-blue-800">Scholar</a>
                                <a href="{{ $lecturer->linkedin_link }}" class="text-[#1a3675] underline hover:text-blue-800">LinkedIn</a>
                            </div>
                            <p>Contact: {{ $lecturer->user->email }}</p>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6">
                        <div class="bg-[#1a3675] px-4 py-2.5 flex items-center gap-2 text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                            <h2 class="text-sm font-bold tracking-wide">Research Interest</h2>
                        </div>
                        <div class="bg-white p-4">
                            <p class="text-sm text-gray-700"><span class="font-semibold text-gray-900 w-35 inline-block">{{ $lecturer->research}}
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-[#1a3675] px-4 py-2.5 flex items-center gap-2 text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <h2 class="text-sm font-bold tracking-wide">Education</h2>
                        </div>
                        <div class="bg-white overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50/50 text-gray-900 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">Degree</th>
                                        <th class="px-4 py-3 font-semibold">Major</th>
                                        <th class="px-4 py-3 font-semibold">University</th>
                                        <th class="px-4 py-3 font-semibold text-center">Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($lecturer['educations']) && is_array($lecturer['educations']))
                                        @foreach ($lecturer['educations'] as $edu)
                                        <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                                            {{-- Karena sudah di-cast jadi array, kita pakai gaya kurung siku --}}
                                            <td class="px-4 py-3">{{ $edu['degree'] ?? '-' }}</td>
                                            <td class="px-4 py-3">{{ $edu['major'] ?? '-' }}</td>
                                            <td class="px-4 py-3">{{ $edu['university'] ?? '-' }}</td>
                                            <td class="px-4 py-3 text-center">{{ $edu['graduationYear'] ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                                                    Belum ada riwayat pendidikan.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN BAWAH (Tabs Publikasi & Aktivitas) --}}
            <div class="flex flex-col md:flex-row justify-between items-end border-b border-gray-200 mb-6 gap-4">
                <div class="flex gap-6 overflow-x-auto w-full md:w-auto">
                    <button id="btn-tab-publikasi" class="text-[#1a3675] font-bold pb-2 border-b-2 border-[#1a3675] whitespace-nowrap px-1 transition-colors">
                        Publikasi
                    </button>
                    <button id="btn-tab-aktivitas" class="text-gray-500 font-medium pb-2 border-b-2 border-transparent hover:text-gray-700 whitespace-nowrap px-1 transition-colors">
                        Aktivitas
                    </button>
                </div>

                <div class="relative w-full md:w-64 pb-2">
                    <div class="absolute inset-y-0 left-0 pb-2 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="detailSearchInput" placeholder="Cari Judul, Penulis, Acara..." class="w-full pl-9 pr-3 py-1.5 rounded-md border border-gray-300 text-xs focus:outline-none focus:ring-1 focus:ring-[#1a3675] focus:border-[#1a3675]">
                </div>
            </div>

            {{-- TAB 1: PUBLIKASI --}}
            <div id="content-publikasi" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-700">
                        <thead class="bg-[#1a3675] text-white text-xs uppercase tracking-wide">
                            <tr>
                                <th class="px-5 py-3.5 font-semibold">Title</th>
                                <th class="px-5 py-3.5 font-semibold">Authors</th>
                                <th class="px-5 py-3.5 font-semibold">Publisher</th>
                                <th class="px-5 py-3.5 font-semibold text-center">Cited By</th>
                                <th class="px-5 py-3.5 font-semibold text-center">Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($publicationList as $pub)
                            <tr class="publikasi-row border-b border-gray-100 last:border-0 hover:bg-gray-50 transition"
                                data-search="{{ strtolower($pub['title'] . ' ' . $pub['authors'] . ' ' . $pub['publisher'] . ' ' . $pub['year']) }}">
                                <td class="px-5 py-4 max-w-xs truncate" title="{{ $pub['title'] }}">{{ $pub['title'] }}</td>
                                <td class="px-5 py-4">{{ $pub['authors'] }}</td>
                                <td class="px-5 py-4">{{ $pub['publisher'] }}</td>
                                <td class="px-5 py-4 text-center">{{ $pub['cited'] }}</td>
                                <td class="px-5 py-4 text-center">{{ $pub['year'] }}</td>
                            </tr>
                            @endforeach
                            <tr id="noResultPublikasi" class="hidden">
                                <td colspan="5" class="px-5 py-8 text-center text-gray-500 font-medium">Maaf, publikasi tidak ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: AKTIVITAS --}}
            <div id="content-aktivitas" class="hidden bg-[#fafafc] border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                
                @foreach ($activities as $activity)
                <div class="aktivitas-item flex flex-col lg:flex-row p-6 border-b border-gray-200 last:border-b-0 hover:bg-white transition-colors gap-6 lg:gap-10"
                     data-search="{{ strtolower($activity->activity_name . ' ' . $activity->job . ' ' . $activity->activity_date_start . ' ' . $activity->month) }}">
                    
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-start mb-4 gap-4">
                            <div>
                                <h3 class="text-xl font-extrabold text-[#1a3675]">{{ $activity->activity_name }}</h3>
                                <p class="text-xs font-bold text-gray-600 mt-1">{{ $activity->job }}</p>
                            </div>
                            <div class="text-left sm:text-right shrink-0">
                                <h4 class="text-xl font-extrabold text-[#1a3675] leading-none mb-1">
                                    {{ $activity->activity_date_start->translatedFormat('d F Y') }}
                                </h4>
                                @if($activity->activity_date_end)
                                    <p class="text-[11px] font-semibold text-gray-500 mt-2">
                                        s/d {{ $activity->activity_date_end->translatedFormat('d F Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <p class="text-[13px] text-gray-500 leading-relaxed text-left line-clamp-3">
                            {{ $activity->description }}
                        </p>
                    </div>
                    
                    <div class="w-full lg:w-[280px] shrink-0 flex flex-col justify-center">
                        <div class="carousel-wrapper relative flex items-center justify-center h-24 mt-2 w-full max-w-[250px] mx-auto lg:mx-0">
                            <button class="btn-prev absolute left-0 z-20 bg-white/90 p-1.5 rounded-full shadow hover:bg-white text-gray-800 transition active:scale-95 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                    @php
                        $images = $activity->pictures->pluck('path')->toArray();
                        $totalImages = count($images);
                    @endphp

                    <div class="relative flex items-center justify-center w-full h-full">

                        {{-- FOTO 1 (Kiri) --}}
                        <div class="carousel-img absolute transition-all duration-300 ease-in-out overflow-hidden w-16 h-12 opacity-50 scale-90-translate-x-12 z-0 rounded-md border-transparent cursor-pointer hover:opacity-80">
                            <img src="{{ $images[0] ?? asset('default-image.jpg') }}" alt="Aktivitas 1" class="w-full h-full object-cover pointer-events-none">
                        </div>

                        {{-- FOTO 2 (Tengah) --}}
                        <div class="carousel-img absolute transition-all duration-300 ease-in-out overflow-hidden w-24 h-16 opacity-100 scale-100 translate-x-0 z-10 shadow-lg border-2 border-white rounded-lg cursor-pointer">
                            <img src="{{ $images[1] ?? ($images[0] ?? asset('default-image.jpg')) }}" alt="Aktivitas 2" class="w-full h-full object-cover pointer-events-none">
                        </div>

                        {{-- FOTO 3 (Kanan) beserta Overlay jika gambar > 3 --}}
                        <div class="carousel-img relative absolute transition-all duration-300 ease-in-out overflow-hidden w-16 h-12 opacity-50 scale-90 translate-x-12 z-0 rounded-md border-transparent cursor-pointer hover:opacity-80">
                            <img src="{{ $images[2] ?? ($images[0] ?? asset('default-image.jpg')) }}" alt="Aktivitas 3" class="w-full h-full object-cover pointer-events-none">
                        </div>

                    </div>
                            <button class="btn-next absolute right-0 z-20 bg-white/90 p-1.5 rounded-full shadow hover:bg-white text-gray-800 transition active:scale-95 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
                <div id="noResultAktivitas" class="hidden p-8 text-center text-gray-500 font-medium">
                    Maaf, aktivitas tidak ditemukan.
                </div>
            </div>

            {{-- Pagination (Letakkan tepat di bawah penutup div content-aktivitas) --}}
            <div id="paginationContainer" class="flex justify-end items-center gap-1 mt-8 mb-4 text-sm font-medium text-gray-600">
                <button class="p-1 hover:text-[#1a3675] transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                
                <button class="w-7 h-7 flex items-center justify-center rounded-full bg-[#1a3675] text-white focus:outline-none">1</button>
                <button class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-200 transition-colors focus:outline-none">2</button>
                <button class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-200 transition-colors focus:outline-none">3</button>
                
                <span class="px-1 text-gray-400">...</span>
                
                <button class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-200 transition-colors focus:outline-none">99</button>
                
                <button class="p-1 hover:text-[#1a3675] transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

        </div>
    </div>

    {{-- ================================================= --}}
    {{-- MODAL / POPUP GAMBAR (Tersembunyi secara default) --}}
    {{-- ================================================= --}}
    <div id="imageModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/85 backdrop-blur-sm opacity-0    transition-opacity duration-300">

            {{-- Tombol Tutup (X) ditaruh absolute di pojok kanan atas --}}
            <button id="closeModalBtn" class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors   focus:outline-none z-50">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            {{-- Gambar yang Diperbesar (Sekarang akan benar-benar di tengah) --}}
            <img id="modalImage" src="" alt="Popup Image" class="max-w-[90vw] max-h-[85vh] rounded-lg shadow-2xl scale-95   transition-transform duration-300 object-contain">

            {{-- Teks Bantuan ditaruh absolute di bagian bawah tengah layar --}}
            <p class="absolute bottom-10 text-white/60 text-sm tracking-wide font-medium">Klik di mana saja untuk menutup</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- STATE GLOBAL ---
            const itemsPerPage = 3; // Tampilkan 2 item per halaman (Ubah sesuai kebutuhan)
            let currentPubPage = 1;
            let currentAktPage = 1;
            let activeTab = 'publikasi'; // Default tab yang aktif
            let searchQuery = '';

            // --- ELEMEN DOM ---
            const btnPublikasi = document.getElementById('btn-tab-publikasi');
            const btnAktivitas = document.getElementById('btn-tab-aktivitas');
            const contentPublikasi = document.getElementById('content-publikasi');
            const contentAktivitas = document.getElementById('content-aktivitas');
            const searchInput = document.getElementById('detailSearchInput');
            const pubRows = document.querySelectorAll('.publikasi-row');
            const noResultPub = document.getElementById('noResultPublikasi');
            const aktItems = document.querySelectorAll('.aktivitas-item');
            const noResultAkt = document.getElementById('noResultAktivitas');
            const paginationContainer = document.getElementById('paginationContainer');

            // --- 1. LOGIKA TAB TOGGLE ---
            const activeClasses = ['text-[#1a3675]', 'font-bold', 'border-[#1a3675]'];
            const inactiveClasses = ['text-gray-500', 'font-medium', 'border-transparent'];

            function switchTab(isPublikasi) {
                activeTab = isPublikasi ? 'publikasi' : 'aktivitas';
                
                if (isPublikasi) {
                    contentPublikasi.classList.remove('hidden');
                    contentAktivitas.classList.add('hidden');
                    btnPublikasi.classList.remove(...inactiveClasses);
                    btnPublikasi.classList.add(...activeClasses);
                    btnAktivitas.classList.remove(...activeClasses);
                    btnAktivitas.classList.add(...inactiveClasses);
                } else {
                    contentAktivitas.classList.remove('hidden');
                    contentPublikasi.classList.add('hidden');
                    btnAktivitas.classList.remove(...inactiveClasses);
                    btnAktivitas.classList.add(...activeClasses);
                    btnPublikasi.classList.remove(...activeClasses);
                    btnPublikasi.classList.add(...inactiveClasses);
                }
                
                // Render ulang pagination untuk tab yang dipilih
                applySearchAndPagination();
            }
            btnPublikasi.addEventListener('click', () => switchTab(true));
            btnAktivitas.addEventListener('click', () => switchTab(false));

            // --- 2. LOGIKA SEARCH & PAGINATION TERINTEGRASI ---
            function applySearchAndPagination() {
                // Saring Publikasi
                let pubMatched = [];
                pubRows.forEach(row => {
                    if (row.getAttribute('data-search').includes(searchQuery)) {
                        pubMatched.push(row);
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Hitung total halaman Publikasi
                const totalPubPages = Math.ceil(pubMatched.length / itemsPerPage);
                if (currentPubPage > totalPubPages) currentPubPage = totalPubPages || 1;

                // Tampilkan hanya item Publikasi pada halaman saat ini
                pubMatched.forEach((row, index) => {
                    const start = (currentPubPage - 1) * itemsPerPage;
                    const end = start + itemsPerPage;
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });
                noResultPub.classList.toggle('hidden', pubMatched.length > 0);

                // Saring Aktivitas
                let aktMatched = [];
                aktItems.forEach(item => {
                    if (item.getAttribute('data-search').includes(searchQuery)) {
                        aktMatched.push(item);
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Hitung total halaman Aktivitas
                const totalAktPages = Math.ceil(aktMatched.length / itemsPerPage);
                if (currentAktPage > totalAktPages) currentAktPage = totalAktPages || 1;

                // Tampilkan hanya item Aktivitas pada halaman saat ini
                aktMatched.forEach((item, index) => {
                    const start = (currentAktPage - 1) * itemsPerPage;
                    const end = start + itemsPerPage;
                    item.style.display = (index >= start && index < end) ? '' : 'none';
                });
                noResultAkt.classList.toggle('hidden', aktMatched.length > 0);

                // Render Tombol Pagination berdasarkan Tab Aktif
                const currentTotalPages = activeTab === 'publikasi' ? totalPubPages : totalAktPages;
                const currentPage = activeTab === 'publikasi' ? currentPubPage : currentAktPage;
                renderPagination(currentTotalPages, currentPage);
            }

            function renderPagination(totalPages, currentPage) {
                paginationContainer.innerHTML = ''; 
                if (totalPages <= 1) return; // Sembunyikan pagination jika hanya 1 halaman

                let html = '';

                // Tombol Prev
                const prevDisabled = currentPage === 1 ? 'opacity-30 cursor-not-allowed' : 'hover:text-[#1a3675] hover:bg-gray-200';
                html += `<button class="p-1 rounded-full transition-colors focus:outline-none ${prevDisabled}" onclick="if(${currentPage} > 1) window.changePage(${currentPage - 1})">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                         </button>`;

                // Nomor Halaman
                for (let i = 1; i <= totalPages; i++) {
                    if (i === currentPage) {
                        html += `<button class="w-7 h-7 flex items-center justify-center rounded-full bg-[#1a3675] text-white focus:outline-none">${i}</button>`;
                    } else {
                        html += `<button class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-200 transition-colors focus:outline-none text-gray-700" onclick="window.changePage(${i})">${i}</button>`;
                    }
                }

                // Tombol Next
                const nextDisabled = currentPage === totalPages ? 'opacity-30 cursor-not-allowed' : 'hover:text-[#1a3675] hover:bg-gray-200';
                html += `<button class="p-1 rounded-full transition-colors focus:outline-none ${nextDisabled}" onclick="if(${currentPage} < ${totalPages}) window.changePage(${currentPage + 1})">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                         </button>`;

                paginationContainer.innerHTML = html;
            }

            // Fungsi Global untuk dipanggil dari atribut onclick di HTML
            window.changePage = function(newPage) {
                if (activeTab === 'publikasi') {
                    currentPubPage = newPage;
                } else {
                    currentAktPage = newPage;
                }
                applySearchAndPagination();
            };

            // Event Ketik Search
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase();
                currentPubPage = 1; // Reset ke halaman 1 saat mencari
                currentAktPage = 1;
                applySearchAndPagination();
            });


            // --- 3. LOGIKA CAROUSEL FOTO & POPUP ---
            const carousels = document.querySelectorAll('.carousel-wrapper');
            const stateClasses = [
                "w-16 h-12 opacity-50 scale-90 -translate-x-12 z-0 rounded-md border-transparent cursor-pointer hover:opacity-80".split(" "), // Kiri
                "w-24 h-16 opacity-100 scale-100 translate-x-0 z-10 shadow-lg border-2 border-white rounded-lg cursor-pointer".split(" "), // Tengah
                "w-16 h-12 opacity-50 scale-90 translate-x-12 z-0 rounded-md border-transparent cursor-pointer hover:opacity-80".split(" ") // Kanan
            ];

            carousels.forEach(wrapper => {
                const btnPrev = wrapper.querySelector('.btn-prev');
                const btnNext = wrapper.querySelector('.btn-next');
                const images = wrapper.querySelectorAll('.carousel-img');
                let positions = [0, 1, 2];

                function updateCarousel() {
                    images.forEach((img, index) => {
                        const currentPos = positions[index];
                        stateClasses.forEach(clsArray => img.classList.remove(...clsArray));
                        img.classList.add(...stateClasses[currentPos]);
                    });
                }
                btnNext.addEventListener('click', () => { positions = positions.map(p => (p - 1 + 3) % 3); updateCarousel(); });
                btnPrev.addEventListener('click', () => { positions = positions.map(p => (p + 1) % 3); updateCarousel(); });
            });

            // Modal
            const imageModal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const carouselClickableDivs = document.querySelectorAll('.carousel-img');

            function openModal(imageSrc) {
                modalImage.src = imageSrc;
                imageModal.classList.remove('hidden');
                setTimeout(() => {
                    imageModal.classList.remove('opacity-0');
                    modalImage.classList.remove('scale-95');
                    modalImage.classList.add('scale-100');
                }, 10);
            }

            function closeModal() {
                imageModal.classList.add('opacity-0');
                modalImage.classList.remove('scale-100');
                modalImage.classList.add('scale-95');
                setTimeout(() => {
                    imageModal.classList.add('hidden');
                    modalImage.src = '';
                }, 300);
            }

            carouselClickableDivs.forEach(div => {
                div.addEventListener('click', () => {
                    const imgTag = div.querySelector('img');
                    if(imgTag) openModal(imgTag.src);
                });
            });

            closeModalBtn.addEventListener('click', closeModal);
            imageModal.addEventListener('click', (e) => { if (e.target === imageModal) closeModal(); });

            // Inisialisasi awal
            applySearchAndPagination();
        });
    </script>
</x-layouts.main>