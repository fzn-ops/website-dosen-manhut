<x-layouts.main>
    <x-slot:title>
        {{ $lecturer->user->name ?? 'Detail Dosen' }} | DosenManhut
    </x-slot>

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
                
                {{-- Container Profil: Di Mobile berdampingan (Row), Di Desktop 2 Kolom Proporsional & Rapi --}}
                <div class="flex flex-col md:flex-row gap-4 sm:gap-6 md:gap-8 items-start">
                    
                    {{-- Bagian Mobile Header (Foto + Identitas berdampingan di layar HP) & Desktop Foto --}}
                    <div class="flex flex-row md:flex-col gap-4 sm:gap-5 md:gap-0 shrink-0 w-full md:w-64 lg:w-72 xl:w-80 items-center md:items-start">
                        {{-- Foto Dosen (Selalu proporsional 3:4, membesar proporsional mengisi kartu secara penuh) --}}
                        <div class="w-28 sm:w-36 md:w-full aspect-[3/4] shrink-0 rounded-xl sm:rounded-2xl overflow-hidden shadow-md ring-2 sm:ring-4 ring-gray-50 bg-[#cbd5e1] relative">
                            @if(!empty($lecturer->image))
                                <img src="{{ asset('storage/' . $lecturer->image) }}" 
                                    alt="{{ $lecturer->user->name }}"
                                    class="w-full h-full object-cover object-top"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 items-center justify-center text-gray-400" style="display: none;">
                                    <svg class="w-12 sm:w-14 h-12 sm:h-14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center text-gray-400">
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
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-blue-50 text-[#1a3675] border border-blue-100/80">
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
                                <a href="mailto:{{ $lecturer->user->email }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-50 hover:bg-gray-100 border border-gray-200 text-[11px] font-medium text-gray-700 shadow-2xs max-w-full">
                                    <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span class="truncate">{{ $lecturer->user->email }}</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Detail Info Dosen --}}
                    <div class="flex-1 flex flex-col items-start text-left w-full min-w-0">
                        
                        {{-- Header Row Khusus Layar Tablet & Desktop (2 Baris Sejajar Presisi) --}}
                        <div class="hidden md:flex flex-col w-full gap-3 mb-5 pb-4 border-b border-gray-100">
                            {{-- Baris 1: Nama di Kiri, Scholar & LinkedIn di Kanan (Sejajar Sempurna) --}}
                            <div class="flex flex-row justify-between items-center w-full gap-4">
                                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                                    {{ $lecturer->user->name }}
                                </h1>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if(!empty($lecturer->scholar_link))
                                        <a href="{{ $lecturer->scholar_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-blue-50 border border-[#d6e0ee] text-xs font-semibold text-[#1a3675] transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                            <span>Google Scholar</span>
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @endif

                                    @if(!empty($lecturer->linkedin_link))
                                        <a href="{{ $lecturer->linkedin_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-blue-50 border border-[#d6e0ee] text-xs font-semibold text-[#0a66c2] transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.761-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                            <span>LinkedIn</span>
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Baris 2: Departemen di Kiri, Email di Kanan (Sejajar Sempurna) --}}
                            <div class="flex flex-row justify-between items-center w-full gap-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-[#1a3675] border border-blue-100/80 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-[#1a3675]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    {{ $lecturer->division }}
                                </span>

                                @if(!empty($lecturer->user->email))
                                    <a href="mailto:{{ $lecturer->user->email }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 text-xs font-medium text-gray-700 transition shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <span>{{ $lecturer->user->email }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Card Research Interest --}}
                        <div class="w-full border border-gray-200 rounded-xl overflow-hidden shadow-2xs mb-4 bg-white text-left">
                            <div class="bg-[#1a3675] px-4 py-2 flex items-center gap-2 text-white">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                <h2 class="text-xs sm:text-sm font-bold tracking-wide">Ketertarikan Penelitian</h2>
                            </div>
                            <div class="p-3 sm:p-3.5">
                                <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">{{ $lecturer->research ?? 'Belum ada bidang riset yang dicantumkan.' }}</p>
                            </div>
                        </div>

                        {{-- Card Education --}}
                        <div class="w-full border border-gray-200 rounded-xl overflow-hidden shadow-2xs bg-white text-left">
                            <div class="bg-[#1a3675] px-4 py-2 flex items-center gap-2 text-white">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <h2 class="text-xs sm:text-sm font-bold tracking-wide">Edukasi</h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs sm:text-sm text-gray-600 min-w-[480px]">
                                    <thead class="bg-gray-50 text-gray-900 border-b border-gray-200 font-semibold text-[11px] sm:text-xs uppercase tracking-wider">
                                        <tr>
                                            <th class="px-3 py-2.5 sm:py-3 text-center w-[15%]">Tingkat</th>
                                            <th class="px-3 py-2.5 sm:py-3 text-center w-[40%]">Jurusan</th>
                                            <th class="px-3 py-2.5 sm:py-3 text-center w-[30%]">Universitas</th>
                                            <th class="px-3 py-2.5 sm:py-3 text-center w-[15%]">Tahun</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($lecturer['educations']) && is_array($lecturer['educations']))
                                            @foreach ($lecturer['educations'] as $edu)
                                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/80 transition">
                                                <td class="px-3 py-2.5 sm:py-3 text-center font-medium text-gray-900">{{ $edu['degree'] ?? '-' }}</td>
                                                <td class="px-3 py-2.5 sm:py-3 text-left">{{ $edu['major'] ?? '-' }}</td>
                                                <td class="px-3 py-2.5 sm:py-3 text-left">{{ $edu['university'] ?? '-' }}</td>
                                                <td class="px-3 py-2.5 sm:py-3 text-center">{{ $edu['graduationYear'] ?? '-' }}</td>
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
                                <th class="px-4 sm:px-5 py-3 font-semibold">Title</th>
                                <th class="px-4 sm:px-5 py-3 font-semibold">Authors</th>
                                <th class="px-4 sm:px-5 py-3 font-semibold">Publisher</th>
                                <th class="px-4 sm:px-5 py-3 font-semibold text-center">Cited By</th>
                                <th class="px-4 sm:px-5 py-3 font-semibold text-center">Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($publications as $pub)
                            <tr class="publikasi-row border-b border-gray-100 last:border-0 hover:bg-gray-50 transition"
                                data-search="{{ strtolower($pub['title'] . ' ' . $pub['authors'] . ' ' . $pub['publisher'] . ' ' . $pub['year']) }}">
                                <td class="px-4 sm:px-5 py-3.5 max-w-xs truncate font-medium text-gray-900" title="{{ $pub['title'] }}">{{ $pub['title'] }}</td>
                                <td class="px-4 sm:px-5 py-3.5 text-gray-600">{{ $pub['authors'] }}</td>
                                <td class="px-4 sm:px-5 py-3.5 text-gray-600">{{ $pub['publisher'] }}</td>
                                <td class="px-4 sm:px-5 py-3.5 text-center font-semibold text-gray-700">{{ $pub['cited_by'] }}</td>
                                <td class="px-4 sm:px-5 py-3.5 text-center text-gray-600">{{ $pub['year'] }}</td>
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
                <div class="aktivitas-item flex flex-col lg:flex-row p-4 sm:p-6 hover:bg-white transition-colors gap-4 sm:gap-6 lg:gap-10"
                     data-search="{{ strtolower($activity->activity_name . ' ' . $activity->job . ' ' . $activity->activity_date_start . ' ' . $activity->month) }}">
                    
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-start mb-3 sm:mb-4 gap-2 sm:gap-4">
                            <div>
                                <h3 class="text-lg sm:text-xl font-extrabold text-[#1a3675] leading-snug">{{ $activity->activity_name }}</h3>
                                <p class="text-xs font-bold text-gray-600 mt-0.5">{{ $activity->job }}</p>
                            </div>
                            <div class="text-left sm:text-right shrink-0">
                                <h4 class="text-base sm:text-lg font-bold text-[#1a3675] leading-none mb-0.5">
                                    {{ $activity->activity_date_start->locale('id')->translatedFormat('d F Y') }}
                                </h4>
                                @if($activity->activity_date_end)
                                    <p class="text-[10px] sm:text-[11px] font-semibold text-gray-500 mt-1">
                                        s/d {{ $activity->activity_date_end->locale('id')->translatedFormat('d F Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs sm:text-[13px] text-gray-500 leading-relaxed text-left line-clamp-3">
                            {{ $activity->quote ?? $activity->description }}
                        </p>
                    </div>
                    
                    <div class="w-full lg:w-[280px] shrink-0 flex flex-col justify-center">
                        <div class="carousel-wrapper relative flex items-center justify-center h-24 sm:h-28 w-full max-w-[250px] mx-auto lg:mx-0">
                            <button class="btn-prev absolute left-0 z-20 bg-white/90 p-1.5 rounded-full shadow-md hover:bg-white text-gray-800 transition active:scale-95 focus:outline-none cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                    @php
                        $pics = $activity->pictures;
                        $primaryPic = $pics->firstWhere('is_primary', true) ?? $pics->first();
                        $otherPics = $pics->filter(fn($p) => !$primaryPic || $p->id !== $primaryPic->id)->values();
                        $sortedPics = $primaryPic ? collect([$primaryPic])->concat($otherPics) : $pics;
                        $images = $sortedPics->pluck('path')->toArray();
                        $totalImages = count($images);

                        $defaultImg = asset('images/hero_section.jpg');
                        $imgCenter = $images[0] ?? $defaultImg;
                        $imgLeft = $totalImages > 2 ? $images[$totalImages - 1] : ($images[1] ?? $imgCenter);
                        $imgRight = $images[1] ?? ($images[2] ?? $imgCenter);
                    @endphp

                    <div class="relative flex items-center justify-center w-full h-full">

                        {{-- FOTO 1 (Kiri) --}}
                        <div class="carousel-img absolute transition-all duration-300 ease-in-out overflow-hidden w-16 h-12 opacity-50 scale-90 -translate-x-12 z-0 rounded-md border-transparent cursor-pointer hover:opacity-80">
                            <img src="{{ $imgLeft }}" alt="Aktivitas Kiri" class="w-full h-full object-cover pointer-events-none">
                        </div>

                        {{-- FOTO 2 (Tengah / Thumbnail Utama) --}}
                        <div class="carousel-img absolute transition-all duration-300 ease-in-out overflow-hidden w-24 h-16 opacity-100 scale-100 translate-x-0 z-10 shadow-lg border-2 border-white rounded-lg cursor-pointer">
                            <img src="{{ $imgCenter }}" alt="Aktivitas Utama" class="w-full h-full object-cover pointer-events-none">
                        </div>

                        {{-- FOTO 3 (Kanan) --}}
                        <div class="carousel-img absolute transition-all duration-300 ease-in-out overflow-hidden w-16 h-12 opacity-50 scale-90 translate-x-12 z-0 rounded-md border-transparent cursor-pointer hover:opacity-80">
                            <img src="{{ $imgRight }}" alt="Aktivitas Kanan" class="w-full h-full object-cover pointer-events-none">
                        </div>

                    </div>
                            <button class="btn-next absolute right-0 z-20 bg-white/90 p-1.5 rounded-full shadow-md hover:bg-white text-gray-800 transition active:scale-95 focus:outline-none cursor-pointer">
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
        <p class="absolute bottom-6 sm:bottom-10 text-white/60 text-xs sm:text-sm tracking-wide font-medium">Klik di mana saja untuk menutup</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- STATE GLOBAL ---
            const itemsPerPage = 10;
            let currentPubPage = 1;
            let currentAktPage = 1;
            let activeTab = 'publikasi';
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

                const totalPubPages = Math.ceil(pubMatched.length / itemsPerPage);
                if (currentPubPage > totalPubPages) currentPubPage = totalPubPages || 1;

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

                const totalAktPages = Math.ceil(aktMatched.length / itemsPerPage);
                if (currentAktPage > totalAktPages) currentAktPage = totalAktPages || 1;

                aktMatched.forEach((item, index) => {
                    const start = (currentAktPage - 1) * itemsPerPage;
                    const end = start + itemsPerPage;
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

            // --- 3. LOGIKA CAROUSEL FOTO & POPUP ---
            const carousels = document.querySelectorAll('.carousel-wrapper');
            const stateClasses = [
                "w-16 h-12 opacity-50 scale-90 -translate-x-12 z-0 rounded-md border-transparent cursor-pointer hover:opacity-80".split(" "),
                "w-24 h-16 opacity-100 scale-100 translate-x-0 z-10 shadow-lg border-2 border-white rounded-lg cursor-pointer".split(" "),
                "w-16 h-12 opacity-50 scale-90 translate-x-12 z-0 rounded-md border-transparent cursor-pointer hover:opacity-80".split(" ")
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

            applySearchAndPagination();
        });
    </script>
</x-layouts.main>