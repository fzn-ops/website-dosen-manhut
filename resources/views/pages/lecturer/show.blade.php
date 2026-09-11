<x-layouts.main>
    <x-slot:title>
        {{ $lecturer->user->name ?? 'Detail Dosen' }} | DosenManhut
    </x-slot>
    <x-preloader />
    <div class="bg-[#fafafc] w-full min-h-screen py-6 sm:py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <nav class="text-xs sm:text-sm font-medium text-gray-500 mb-4 sm:mb-6">
                <a href="{{ url('/lecturers') }}" class="hover:text-[#1a3675] transition-colors">Dosen</a>
                <span class="mx-1 text-gray-400">/</span>
                <span class="text-[#1a3675] font-semibold underline decoration-[#1a3675]/30 underline-offset-4">{{ $lecturer->user->name }}</span>
            </nav>

            {{-- BAGIAN ATAS (Profil & Info Akademik dalam Profile Card Elegan) --}}
            <div class="bg-white border border-gray-200/90 rounded-2xl md:rounded-3xl p-4 sm:p-6 md:p-8 shadow-xs mb-6 sm:mb-10">
                
                {{-- Container Profil: Di Mobile berdampingan (Row), Di Desktop 2 Kolom Sejajar Presisi Mengikuti Tinggi Tabel --}}
                <div class="flex flex-col md:flex-row gap-4 sm:gap-6 md:gap-8 items-start md:items-stretch">
                    
                    {{-- Bagian Mobile Header (Foto + Identitas berdampingan di layar HP) & Desktop Foto --}}
                    <div class="flex flex-row md:flex-col gap-4 sm:gap-5 md:gap-0 shrink-0 w-full md:w-48 lg:w-60 xl:w-72 items-center md:items-stretch">
                        {{-- Foto Dosen (Tinggi mengunci presisi ke tinggi tabel edukasi, lebar proporsional 3:4 tidak pipih) --}}
                        <div class="w-28 sm:w-36 md:w-full aspect-[3/4] md:aspect-auto md:h-full shrink-0 rounded-xl sm:rounded-2xl overflow-hidden shadow-md ring-2 sm:ring-4 ring-gray-50 bg-[#cbd5e1] relative">
                            @if(!empty($lecturer->image))
                                <img src="{{ asset('storage/' . $lecturer->image) }}" 
                                    alt="{{ $lecturer->user->name }}"
                                    class="w-full h-full md:absolute md:inset-0 object-cover object-top"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full md:absolute md:inset-0 bg-gradient-to-br from-slate-200 to-slate-300 items-center justify-center text-gray-400" style="display: none;">
                                    <svg class="w-12 sm:w-14 h-12 sm:h-14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                            @else
                                <div class="w-full h-full md:absolute md:inset-0 bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center text-gray-400">
                                    <svg class="w-12 sm:w-14 h-12 sm:h-14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                            @endif
                        </div>

                        {{-- Identitas Khusus Mobile (Muncul di sebelah kanan foto pada layar HP) --}}
                        <div class="flex flex-col flex-1 md:hidden text-left min-w-0">
                            <h1 class="text-base sm:text-xl font-extrabold text-gray-900 tracking-tight leading-snug mb-1">
                                {{ $lecturer->user->name }}
                            </h1>
                            <div class="mb-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] sm:text-[11px] font-semibold bg-blue-50 text-[#1a3675] border border-blue-100/80">
                                    <svg class="w-3 h-3 text-[#1a3675]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    {{ $lecturer->division }}
                                </span>
                            </div>

                            {{-- Social Buttons Mobile --}}
                            <div class="flex flex-wrap gap-1.5 mb-1.5">
                                @if(!empty($lecturer->scholar_link))
                                    <a href="{{ $lecturer->scholar_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white hover:bg-blue-50 border border-[#d6e0ee] text-[11px] font-semibold text-[#1a3675] shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                        <span>Scholar</span>
                                    </a>
                                @endif
                                @if(!empty($lecturer->linkedin_link))
                                    <a href="{{ $lecturer->linkedin_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white hover:bg-blue-50 border border-[#d6e0ee] text-[11px] font-semibold text-[#0a66c2] shadow-2xs">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.761-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                        <span>LinkedIn</span>
                                    </a>
                                @endif
                            </div>

                            {{-- Email Button Mobile (Tepat di bawah Scholar & LinkedIn) --}}
                            @if(!empty($lecturer->user->email))
                                <a href="mailto:{{ $lecturer->user->email }}" class="inline-flex w-fit max-w-full flex-wrap items-center gap-1.5 rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-700 shadow-2xs break-all hover:bg-gray-100">
                                    <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span class="truncate">{{ $lecturer->user->email }}</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Detail Info Dosen --}}
                    <div class="flex-1 flex flex-col items-start text-left w-full min-w-0">
                        
                        {{-- Header Row Khusus Layar Tablet, Split Screen & Desktop --}}
                        <div class="hidden md:flex flex-col w-full gap-2.5 mb-4 pb-3.5 border-b border-gray-100">
                            {{-- Baris 1: Nama & Tombol Scholar/LinkedIn (Fluid di Split Screen, Sejajar di Desktop Luas) --}}
                            <div class="flex flex-col xl:flex-row xl:justify-between xl:items-center w-full gap-2 xl:gap-4">
                                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-gray-900 tracking-tight leading-snug">
                                    {{ $lecturer->user->name }}
                                </h1>

                                <div class="flex flex-wrap items-center gap-2 shrink-0">
                                    @if(!empty($lecturer->scholar_link))
                                        <a href="{{ $lecturer->scholar_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg bg-white hover:bg-blue-50 border border-[#d6e0ee] text-[11px] sm:text-xs font-semibold text-[#1a3675] transition shadow-2xs">
                                            <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                            <span>Google Scholar</span>
                                            <svg class="w-2.5 sm:w-3 h-2.5 sm:h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @endif

                                    @if(!empty($lecturer->linkedin_link))
                                        <a href="{{ $lecturer->linkedin_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg bg-white hover:bg-blue-50 border border-[#d6e0ee] text-[11px] sm:text-xs font-semibold text-[#0a66c2] transition shadow-2xs">
                                            <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.761-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                            <span>LinkedIn</span>
                                            <svg class="w-2.5 sm:w-3 h-2.5 sm:h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Baris 2: Departemen di Kiri, Email di Kanan (Fluid Wrap di Layar Sempit) --}}
                            <div class="flex flex-wrap justify-between items-center w-full gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-md text-[11px] sm:text-xs font-semibold bg-blue-50 text-[#1a3675] border border-blue-100/80 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-[#1a3675]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    {{ $lecturer->division }}
                                </span>

                                @if(!empty($lecturer->user->email))
                                    <a href="mailto:{{ $lecturer->user->email }}" class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 text-[11px] sm:text-xs font-medium text-gray-700 transition shadow-2xs max-w-full">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <span class="truncate">{{ $lecturer->user->email }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Card Research Interest --}}
                        <div class="w-full border border-gray-200 rounded-xl overflow-hidden shadow-2xs mb-4 bg-white text-left">
                            <div class="bg-[#1a3675] px-4 py-2 flex items-center justify-center gap-2 text-white">
                                <h2 class="text-xs sm:text-sm font-bold tracking-wide">
                                    Ketertarikan Penelitian
                                </h2>
                            </div>
                            <div class="p-3 sm:p-3.5">
                                <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">{{ $lecturer->research ?? 'Belum ada bidang riset yang dicantumkan.' }}</p>
                            </div>
                        </div>

                        {{-- Card Education --}}
                        <div class="w-full border border-gray-200 rounded-xl overflow-hidden shadow-2xs bg-white text-left">
                            <div class="bg-[#1a3675] px-4 py-2 flex items-center justify-center gap-2 text-white">
                                <h2 class="text-xs sm:text-sm font-bold tracking-wide text-center">Edukasi</h2>
                            </div>
                            <div class="w-full overflow-x-auto">
                                <table class="w-full text-gray-600 text-left">
                                    <thead class="bg-gray-50/80 text-gray-900 border-b border-gray-200 font-semibold text-[10px] sm:text-xs uppercase tracking-wider">
                                        <tr>
                                            <th class="px-2.5 sm:px-3.5 py-2 sm:py-2.5 text-center w-[10%]">Tingkat</th>
                                            <th class="px-2.5 sm:px-3.5 py-2 sm:py-2.5 text-center w-[44%]">Jurusan</th>
                                            <th class="px-2.5 sm:px-3.5 py-2 sm:py-2.5 text-center w-[36%]">Universitas</th>
                                            <th class="px-2.5 sm:px-3.5 py-2 sm:py-2.5 text-center w-[10%]">Tahun</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($lecturer['educations']) && is_array($lecturer['educations']))
                                            @foreach ($lecturer['educations'] as $edu)
                                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/80 transition">
                                                <td class="px-2.5 sm:px-3.5 py-2 sm:py-2.5 text-center font-semibold text-gray-600 text-[11px] sm:text-xs md:text-sm">{{ $edu['degree'] ?? '-' }}</td>
                                                <td class="px-2.5 sm:px-3.5 py-2 sm:py-2.5 text-left font-semibold text-gray-900 text-[11px] sm:text-xs md:text-sm break-words">{{ $edu['major'] ?? '-' }}</td>
                                                <td class="px-2.5 sm:px-3.5 py-2 sm:py-2.5 text-left font-medium text-gray-600 text-[11px] sm:text-xs md:text-sm break-words">{{ $edu['university'] ?? '-' }}</td>
                                                <td class="px-2.5 sm:px-3.5 py-2 sm:py-2.5 text-center font-semibold text-gray-600 text-[11px] sm:text-xs md:text-sm">{{ $edu['graduationYear'] ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="px-4 py-4 text-center text-gray-500 text-xs">
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
            </div>

            {{-- BAGIAN BAWAH (Tabs Publikasi & Aktivitas) --}}
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-end border-b border-gray-200 mb-6 gap-4">
                <div class="flex gap-6 overflow-x-auto shrink-0 border-b sm:border-b-0 border-gray-200 sm:pb-0">
                    <button id="btn-tab-publikasi" class="text-[#1a3675] font-bold pb-2.5 border-b-2 border-[#1a3675] whitespace-nowrap px-1 text-sm sm:text-base transition-colors cursor-pointer">
                        Publikasi
                    </button>
                    <button id="btn-tab-aktivitas" class="text-gray-500 font-medium pb-2.5 border-b-2 border-transparent hover:text-gray-700 whitespace-nowrap px-1 text-sm sm:text-base transition-colors cursor-pointer">
                        Aktivitas
                    </button>
                </div>

                <div class="relative w-full sm:w-64 pb-2">
                    <div class="pointer-events-none absolute inset-y-0 left-0 pb-2 pl-3 flex items-center text-[#183669]">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="detailSearchInput" placeholder="Cari Judul, Acara..." class="h-[38px] w-full rounded-lg border border-[#d6e0ee] bg-white pl-9 pr-3 text-xs text-[#173a63] placeholder-[#8ca1b9] focus:outline-none focus:border-[#183669] focus:ring-1 focus:ring-[#183669] transition shadow-2xs">
                </div>
            </div>

            {{-- TAB 1: PUBLIKASI --}}
            <div id="content-publikasi" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-2xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm text-gray-700 min-w-[620px]">
                        <thead class="bg-[#1a3675] text-white text-[11px] sm:text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-4 sm:px-5 py-3 font-semibold text-center w-[38%] min-w-[200px]">Title</th>
                                <th class="px-4 sm:px-5 py-3 font-semibold text-center w-[25%] min-w-[140px]">Authors</th>
                                <th class="px-4 sm:px-5 py-3 font-semibold text-center w-[23%] min-w-[130px]">Publisher</th>
                                <th class="px-3 sm:px-4 py-3 font-semibold text-center w-[7%] whitespace-nowrap">Cited By</th>
                                <th class="px-3 sm:px-4 py-3 font-semibold text-center w-[7%] whitespace-nowrap">Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($publications as $pub)
                            <tr class="publikasi-row border-b border-gray-100 last:border-0 hover:bg-gray-50 transition"
                                data-search="{{ strtolower($pub['title'] . ' ' . $pub['authors'] . ' ' . $pub['publisher'] . ' ' . $pub['year']) }}">
                                <td class="px-4 sm:px-5 py-3.5 font-medium text-gray-900 leading-snug break-words">{{ $pub['title'] }}</td>
                                <td class="px-4 sm:px-5 py-3.5 text-gray-600 leading-snug break-words">{{ $pub['authors'] }}</td>
                                <td class="px-4 sm:px-5 py-3.5 text-gray-600 leading-snug break-words">{{ $pub['publisher'] }}</td>
                                <td class="px-3 sm:px-4 py-3.5 text-center font-semibold text-gray-700">{{ $pub['cited_by'] }}</td>
                                <td class="px-3 sm:px-4 py-3.5 text-center text-gray-600">{{ $pub['year'] }}</td>
                            </tr>
                            @endforeach
                            <tr id="noResultPublikasi" class="hidden">
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500 font-medium">Maaf, publikasi tidak ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: AKTIVITAS --}}
            <div id="content-aktivitas" class="hidden bg-[#fafafc] border border-gray-200 rounded-xl overflow-hidden shadow-2xs divide-y divide-gray-200">
                
                @foreach ($activities as $activity)
                <div class="aktivitas-item flex flex-row p-3.5 sm:p-5 lg:p-6 hover:bg-white transition-colors gap-3.5 sm:gap-5 lg:gap-8 items-center"
                     data-search="{{ strtolower($activity->activity_name . ' ' . $activity->job . ' ' . $activity->activity_date_start . ' ' . $activity->month) }}">
                    
                    {{-- Kolom Konten Teks (Kiri) --}}
                    <div class="flex-1 min-w-0 flex flex-col">
                        @php
                            $isSameDate = empty($activity->activity_date_end) || 
                                          ($activity->activity_date_start && $activity->activity_date_end && $activity->activity_date_start->format('Y-m-d') === $activity->activity_date_end->format('Y-m-d'));
                        @endphp

                        {{-- Baris Header: Judul & Peran di Kiri, Tanggal di Kanan (Khusus Desktop lg:flex) --}}
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-1 lg:gap-6">
                            <div class="flex-1 min-w-0">
                                {{-- Judul Aktivitas --}}
                                <h3 class="text-xs sm:text-base lg:text-lg font-extrabold text-[#1a3675] leading-snug line-clamp-2 lg:line-clamp-none">
                                    {{ $activity->activity_name }}
                                </h3>

                                {{-- Peran Dosen --}}
                                @if(!empty($activity->job))
                                    <p class="text-[10px] sm:text-xs lg:text-[13px] font-semibold text-gray-500 mt-0.5 line-clamp-1 lg:line-clamp-none">
                                        {{ $activity->job }}
                                    </p>
                                @endif
                            </div>

                            {{-- Tanggal Pelaksanaan di Kanan Atas (Tampil Hanya di Desktop) --}}
                            <div class="hidden lg:block text-right shrink-0">
                                @if($isSameDate)
                                    <h4 class="text-xs sm:text-sm font-bold text-[#1a3675] leading-snug whitespace-nowrap">
                                        {{ $activity->activity_date_start->locale('id')->translatedFormat('d F Y') }}
                                    </h4>
                                @else
                                    <h4 class="text-xs sm:text-sm font-bold text-[#1a3675] leading-snug whitespace-nowrap">
                                        {{ $activity->activity_date_start->locale('id')->translatedFormat('d F Y') }}
                                    </h4>
                                    <p class="text-[10px] sm:text-[11px] font-semibold text-gray-500 mt-0.5 whitespace-nowrap">
                                        s/d {{ $activity->activity_date_end->locale('id')->translatedFormat('d F Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Deskripsi / Quote Aktivitas --}}
                        @if(!empty($activity->quote) || !empty($activity->description))
                            <p class="text-[10px] sm:text-xs lg:text-[13px] text-gray-600 leading-relaxed text-left line-clamp-1 sm:line-clamp-2 lg:line-clamp-3 mt-1 sm:mt-1.5 lg:mt-2">
                                {{ $activity->quote ?? $activity->description }}
                            </p>
                        @endif

                        {{-- Baris Bawah: Link Lihat Detail Aktivitas (Kiri) & Tanggal (Kanan) di Mobile View --}}
                        <div class="mt-2.5 lg:mt-3 flex items-center justify-between gap-2">
                            <a href="{{ route('activity.show', $activity->id) }}" class="inline-flex items-center gap-1 sm:gap-1.5 text-[10px] sm:text-xs font-bold text-[#1a3675] hover:text-blue-700 transition-colors group/link shrink-0">
                                <span>Lihat Detail Aktivitas</span>
                                <svg class="w-2.5 h-2.5 sm:w-3.5 sm:h-3.5 transition-transform group-hover/link:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </a>

                            {{-- Tanggal Pelaksanaan di Bawah Kanan (Khusus Mobile & Tablet View < lg) --}}
                            <div class="flex lg:hidden items-center gap-1 text-[9px] sm:text-[11px] font-semibold text-gray-500 shrink-0 text-right">
                                <svg class="w-3 h-3 text-[#1a3675]/80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @if($isSameDate)
                                    <span class="whitespace-nowrap">{{ $activity->activity_date_start->locale('id')->translatedFormat('d M Y') }}</span>
                                @else
                                    <span class="whitespace-nowrap">{{ $activity->activity_date_start->locale('id')->translatedFormat('d M') }} - {{ $activity->activity_date_end->locale('id')->translatedFormat('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Slide Banner / Thumbnail Foto Aktivitas Modern (Kanan) --}}
                    @php
                        $pics = $activity->pictures;
                        $primaryPic = $pics->firstWhere('is_primary', true) ?? $pics->first();
                        $otherPics = $pics->filter(fn($p) => !$primaryPic || $p->id !== $primaryPic->id)->values();
                        $sortedPics = $primaryPic ? collect([$primaryPic])->concat($otherPics) : $pics;
                        $images = $sortedPics->pluck('path')->toArray();
                        $totalImages = count($images);
                        $defaultImg = asset('images/hero_section.jpg');
                        if ($totalImages === 0) {
                            $images = [$defaultImg];
                            $totalImages = 1;
                        }
                    @endphp

                    <div class="w-24 sm:w-36 md:w-44 lg:w-[250px] shrink-0 self-center">
                        <div class="slide-banner-wrapper relative w-full aspect-square sm:aspect-[16/10] sm:h-28 lg:h-32 rounded-xl overflow-hidden shadow-2xs border border-gray-200/90 bg-gray-100 group/banner touch-pan-y select-none cursor-pointer"
                             data-total="{{ $totalImages }}">
                            
                            {{-- Slides Track --}}
                            <div class="slides-track flex w-full h-full transition-transform duration-300 ease-out">
                                @foreach($images as $imgSrc)
                                    <div class="slide-item min-w-full w-full h-full shrink-0 overflow-hidden relative cursor-pointer" onclick="window.openActivityModal('{{ $imgSrc }}')">
                                        <img src="{{ $imgSrc }}" alt="{{ $activity->activity_name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover/banner:scale-105 pointer-events-none select-none">
                                    </div>
                                @endforeach
                            </div>

                            @if($totalImages > 1)
                                {{-- Gradient Shadow Bawah --}}
                                <div class="absolute inset-x-0 bottom-0 h-6 sm:h-10 bg-gradient-to-t from-black/60 via-black/20 to-transparent pointer-events-none z-10"></div>

                                {{-- Counter Top Right --}}
                                <div class="counter-badge absolute top-1 sm:top-2 right-1 sm:right-2 px-1.5 sm:px-2 py-0.5 rounded-md bg-black/50 backdrop-blur-xs text-[9px] sm:text-[10px] font-semibold text-white z-20 pointer-events-none">
                                    <span class="current-slide-num">1</span>/{{ $totalImages }}
                                </div>

                                {{-- Tombol Prev (Hanya Tampil di Layar Desktop lg:) --}}
                                <button type="button" class="hidden lg:flex btn-slide-prev absolute left-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-black/40 hover:bg-black/70 backdrop-blur-xs text-white items-center justify-center opacity-0 group-hover/banner:opacity-100 transition-all duration-200 z-20 cursor-pointer shadow-sm focus:outline-none" aria-label="Foto Sebelumnya">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                                </button>

                                {{-- Tombol Next (Hanya Tampil di Layar Desktop lg:) --}}
                                <button type="button" class="hidden lg:flex btn-slide-next absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-black/40 hover:bg-black/70 backdrop-blur-xs text-white items-center justify-center opacity-0 group-hover/banner:opacity-100 transition-all duration-200 z-20 cursor-pointer shadow-sm focus:outline-none" aria-label="Foto Berikutnya">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                </button>

                                 {{-- Indicator Dots --}}
                                <div class="dots-container flex items-center justify-center gap-1 sm:gap-1.5 absolute bottom-1.5 sm:bottom-2 inset-x-0 z-20">
                                    @for($i = 0; $i < $totalImages; $i++)
                                        <button type="button" class="slide-dot transition-all duration-300 {{ $i === 0 ? 'w-3 sm:w-4 h-1 sm:h-1.5 bg-white rounded-full shadow-sm' : 'w-1 sm:w-1.5 h-1 sm:h-1.5 bg-white/50 hover:bg-white/80 rounded-full cursor-pointer' }}" data-index="{{ $i }}" aria-label="Slide {{ $i + 1 }}"></button>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                <div id="noResultAktivitas" class="hidden p-8 text-center text-gray-500 font-medium">
                    Maaf, aktivitas tidak ditemukan.
                </div>
            </div>

            {{-- Pagination --}}
            <div id="paginationContainer" class="flex justify-center sm:justify-end items-center gap-1.5 mt-8 mb-4 text-xs sm:text-sm font-medium text-gray-600">
            </div>

        </div>
    </div>

    {{-- MODAL / POPUP GAMBAR --}}
    <div id="imageModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/85 backdrop-blur-sm opacity-0 transition-opacity duration-300">
        {{-- Tombol Tutup --}}
        <button id="closeModalBtn" class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors focus:outline-none z-50 cursor-pointer">
            <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        {{-- Gambar yang Diperbesar --}}
        <img id="modalImage" src="" alt="Popup Image" class="max-w-[90vw] max-h-[85vh] rounded-lg shadow-2xl scale-95 transition-transform duration-300 object-contain">

        {{-- Teks Bantuan --}}
        <p class="absolute bottom-6 sm:bottom-10 text-white/60 text-xs sm:text-sm tracking-wide font-medium">Klik tombol "X" atau area diluar gambar untuk menutup</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- STATE GLOBAL ---
            let currentPubPage = 1;
            let currentAktPage = 1;
            let activeTab = 'publikasi';
            let searchQuery = '';

            function getPubItemsPerPage() {
                return window.innerWidth < 640 ? 5 : 10;
            }

            function getAktItemsPerPage() {
                return 10;
            }

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
                
                applySearchAndPagination();
            }
            btnPublikasi.addEventListener('click', () => switchTab(true));
            btnAktivitas.addEventListener('click', () => switchTab(false));

            // --- 2. LOGIKA SEARCH & PAGINATION TERINTEGRASI ---
            function applySearchAndPagination() {
                const pubPerPage = getPubItemsPerPage();
                const aktPerPage = getAktItemsPerPage();

                // Saring Publikasi
                let pubMatched = [];
                pubRows.forEach(row => {
                    if (row.getAttribute('data-search').includes(searchQuery)) {
                        pubMatched.push(row);
                    } else {
                        row.style.display = 'none';
                    }
                });

                const totalPubPages = Math.ceil(pubMatched.length / pubPerPage);
                if (currentPubPage > totalPubPages) currentPubPage = totalPubPages || 1;

                pubMatched.forEach((row, index) => {
                    const start = (currentPubPage - 1) * pubPerPage;
                    const end = start + pubPerPage;
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

                const totalAktPages = Math.ceil(aktMatched.length / aktPerPage);
                if (currentAktPage > totalAktPages) currentAktPage = totalAktPages || 1;

                aktMatched.forEach((item, index) => {
                    const start = (currentAktPage - 1) * aktPerPage;
                    const end = start + aktPerPage;
                    item.style.display = (index >= start && index < end) ? '' : 'none';
                });
                noResultAkt.classList.toggle('hidden', aktMatched.length > 0);

                const currentTotalPages = activeTab === 'publikasi' ? totalPubPages : totalAktPages;
                const currentPage = activeTab === 'publikasi' ? currentPubPage : currentAktPage;
                renderPagination(currentTotalPages, currentPage);
            }

            function generatePaginationArray(currentPage, totalPages) {
                if (totalPages <= 7) {
                    return Array.from({ length: totalPages }, (_, i) => i + 1);
                }
                if (currentPage <= 3) {
                    return [1, 2, 3, 4, '...', totalPages];
                }
                if (currentPage >= totalPages - 2) {
                    return [1, '...', totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
                }
                return [1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages];
            }

            // --- FUNGSI RENDER PAGINATION ---
            function renderPagination(totalPages, currentPage) {
                paginationContainer.innerHTML = ''; 
                if (totalPages <= 1) return;

                let html = '';

                // 1. Tombol Prev
                const prevDisabled = currentPage === 1 ? 'opacity-30 cursor-not-allowed' : 'hover:text-[#1a3675] hover:bg-gray-200 cursor-pointer';
                const prevClick = currentPage > 1 ? `onclick="window.changePage(${currentPage - 1})"` : '';
                html += `<button class="p-1 rounded-full transition-colors focus:outline-none ${prevDisabled}" ${prevClick}>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                         </button>`;

                // 2. Nomor Halaman
                const paginationArray = generatePaginationArray(currentPage, totalPages);
                
                paginationArray.forEach(item => {
                    if (item === '...') {
                        html += `<span class="px-1 text-gray-400">...</span>`;
                    } else if (item === currentPage) {
                        html += `<button class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full bg-[#1a3675] text-white font-bold text-xs sm:text-sm focus:outline-none">${item}</button>`;
                    } else {
                        html += `<button class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full hover:bg-gray-200 transition-colors focus:outline-none text-gray-700 text-xs sm:text-sm cursor-pointer" onclick="window.changePage(${item})">${item}</button>`;
                    }
                });

                // 3. Tombol Next
                const nextDisabled = currentPage === totalPages ? 'opacity-30 cursor-not-allowed' : 'hover:text-[#1a3675] hover:bg-gray-200 cursor-pointer';
                const nextClick = currentPage < totalPages ? `onclick="window.changePage(${currentPage + 1})"` : '';
                html += `<button class="p-1 rounded-full transition-colors focus:outline-none ${nextDisabled}" ${nextClick}>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                         </button>`;

                paginationContainer.innerHTML = html;
            }

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
                currentPubPage = 1;
                currentAktPage = 1;
                applySearchAndPagination();
            });

            // Responsif saat resize / rotasi layar
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    applySearchAndPagination();
                }, 150);
            });

            // --- 3. LOGIKA SLIDE BANNER AKTIVITAS & GESTURE SLIDE / SCROLL ---
            const slideWrappers = document.querySelectorAll('.slide-banner-wrapper');
            window.activityJustDragged = false;

            slideWrappers.forEach(wrapper => {
                const track = wrapper.querySelector('.slides-track');
                const total = parseInt(wrapper.getAttribute('data-total') || '1', 10);
                if (total <= 1) return;

                let currentIndex = 0;
                const btnPrev = wrapper.querySelector('.btn-slide-prev');
                const btnNext = wrapper.querySelector('.btn-slide-next');
                const dots = wrapper.querySelectorAll('.slide-dot');
                const counterNum = wrapper.querySelector('.current-slide-num');

                function goToSlide(index) {
                    currentIndex = (index + total) % total;
                    track.style.transform = `translateX(-${currentIndex * 100}%)`;
                    if (counterNum) counterNum.textContent = currentIndex + 1;

                    dots.forEach((dot, idx) => {
                        if (idx === currentIndex) {
                            dot.className = 'slide-dot transition-all duration-300 w-4 h-1.5 bg-white rounded-full shadow-sm';
                        } else {
                            dot.className = 'slide-dot transition-all duration-300 w-1.5 h-1.5 bg-white/50 hover:bg-white/80 rounded-full cursor-pointer';
                        }
                    });
                }

                if (btnNext) {
                    btnNext.addEventListener('click', (e) => {
                        e.stopPropagation();
                        goToSlide(currentIndex + 1);
                    });
                }

                if (btnPrev) {
                    btnPrev.addEventListener('click', (e) => {
                        e.stopPropagation();
                        goToSlide(currentIndex - 1);
                    });
                }

                dots.forEach((dot, idx) => {
                    dot.addEventListener('click', (e) => {
                        e.stopPropagation();
                        goToSlide(idx);
                    });
                });

                // A. TOUCH SWIPE GESTURE (Mobile & Tablet)
                let touchStartX = 0;
                let touchStartY = 0;

                wrapper.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                    touchStartY = e.changedTouches[0].screenY;
                }, { passive: true });

                wrapper.addEventListener('touchend', (e) => {
                    const touchEndX = e.changedTouches[0].screenX;
                    const touchEndY = e.changedTouches[0].screenY;
                    const diffX = touchEndX - touchStartX;
                    const diffY = touchEndY - touchStartY;

                    // Deteksi swipe horizontal
                    if (Math.abs(diffX) > 30 && Math.abs(diffX) > Math.abs(diffY)) {
                        window.activityJustDragged = true;
                        setTimeout(() => { window.activityJustDragged = false; }, 150);

                        if (diffX < 0) {
                            goToSlide(currentIndex + 1); // Swipe kiri -> Next
                        } else {
                            goToSlide(currentIndex - 1); // Swipe kanan -> Prev
                        }
                    }
                }, { passive: true });

                // B. MOUSE DRAG GESTURE (Desktop)
                let isMouseDown = false;
                let dragStartX = 0;

                wrapper.addEventListener('mousedown', (e) => {
                    if (e.target.closest('button')) return;
                    isMouseDown = true;
                    dragStartX = e.clientX;
                });

                window.addEventListener('mouseup', (e) => {
                    if (!isMouseDown) return;
                    isMouseDown = false;
                    const diffX = e.clientX - dragStartX;
                    if (Math.abs(diffX) > 35) {
                        window.activityJustDragged = true;
                        setTimeout(() => { window.activityJustDragged = false; }, 150);

                        if (diffX < 0) {
                            goToSlide(currentIndex + 1);
                        } else {
                            goToSlide(currentIndex - 1);
                        }
                    }
                });

                // C. SCROLL WHEEL GESTURE (Desktop / Touchpad)
                let lastWheelTime = 0;
                wrapper.addEventListener('wheel', (e) => {
                    const now = Date.now();
                    if (now - lastWheelTime < 350) return; // Throttle 350ms

                    const delta = Math.abs(e.deltaX) > Math.abs(e.deltaY) ? e.deltaX : e.deltaY;
                    if (Math.abs(delta) > 15) {
                        e.preventDefault();
                        lastWheelTime = now;
                        if (delta > 0) {
                            goToSlide(currentIndex + 1); // Scroll down/right -> Next
                        } else {
                            goToSlide(currentIndex - 1); // Scroll up/left -> Prev
                        }
                    }
                }, { passive: false });
            });

            // Modal Lightbox Foto Aktivitas
            const imageModal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const closeModalBtn = document.getElementById('closeModalBtn');

            window.openActivityModal = function(imageSrc) {
                if (!imageSrc || window.activityJustDragged) return;
                modalImage.src = imageSrc;
                imageModal.classList.remove('hidden');
                setTimeout(() => {
                    imageModal.classList.remove('opacity-0');
                    modalImage.classList.remove('scale-95');
                    modalImage.classList.add('scale-100');
                }, 10);
            };

            function closeModal() {
                if (!imageModal) return;
                imageModal.classList.add('opacity-0');
                if (modalImage) {
                    modalImage.classList.remove('scale-100');
                    modalImage.classList.add('scale-95');
                }
                setTimeout(() => {
                    imageModal.classList.add('hidden');
                    if (modalImage) modalImage.src = '';
                }, 300);
            }

            if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
            if (imageModal) {
                imageModal.addEventListener('click', (e) => {
                    if (e.target === imageModal || e.target === closeModalBtn) {
                        closeModal();
                    }
                });
            }

            applySearchAndPagination();
        });
    </script>
</x-layouts.main>