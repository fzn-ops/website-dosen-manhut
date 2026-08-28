<x-layouts.main>
    <x-slot:title>
        {{ $activity->activity_name }} | DosenManhut
    </x-slot>

    <div class="bg-[#fafafc] w-full min-h-screen py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. Breadcrumb --}}
            <nav class="text-sm font-medium text-gray-900 mb-6 flex items-center gap-1.5">
                <a href="{{ url('/activities') }}" class="hover:text-[#1a3675] transition-colors">Aktivitas</a>
                <span>/</span>
                <span class="text-[#1a3675] underline underline-offset-4 decoration-[#1a3675]/40 truncate max-w-[200px] sm:max-w-none" title="{{ $activity->activity_name }}">
                    {{ Str::limit($activity->activity_name, 25) }}
                </span>
            </nav>

            {{-- ==========================================
                 LAYOUT UTAMA (Grid Berbaris)
                 ========================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:gap-x-10 gap-y-4 lg:gap-y-6 mt-2">
                
                {{-- =======================================
                     BARIS 1 KIRI: HEADER ARTIKEL
                     ======================================= --}}
                <div class="lg:col-span-8 flex flex-col justify-end">
                    
                    <h1 class="text-3xl md:text-[40px] font-extrabold text-gray-900 tracking-tight mb-3 leading-tight">
                        {{ $activity->activity_name }}
                    </h1>
                    
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center text-sm md:text-base gap-3">
                        {{-- Kiri: Peran & Nama --}}
                        <div class="font-bold text-[#1a3675] flex items-center gap-2">
                            <span>{{ $activity->job ?? 'Partisipan' }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#1a3675]"></span>
                            <span>{{ $activity->user->name ?? 'Nama Dosen' }}</span>
                        </div>
                        
                        {{-- Kanan: Tanggal --}}
                        <div class="font-bold text-[#1a3675] text-left sm:text-right">
                            {{ \Carbon\Carbon::parse($activity->activity_date_start)->translatedFormat('d F Y') }}
                        </div>
                    </div>
                    
                </div>

                {{-- BARIS 1 KANAN: KOSONG --}}
                <div class="hidden lg:block lg:col-span-4"></div>

                {{-- =======================================
                     BARIS 2 KIRI: GAMBAR & TEKS KONTEN
                     ======================================= --}}
                <div class="lg:col-span-8 flex flex-col">
                    @php
                        $mainPicUrl = $activity->primary_image_url ?? $activity->pictures?->first()?->path;
                    @endphp

                    <div class="w-full h-[250px] sm:h-[350px] md:h-[450px] bg-[#d9d9d9] rounded-2xl mb-4 overflow-hidden shadow-sm relative">
                        @if($mainPicUrl)
                            <img src="{{ $mainPicUrl }}" alt="{{ $activity->activity_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-500 bg-gray-200">
                                <span class="text-lg font-medium">Tidak ada gambar dokumentasi</span>
                            </div>
                        @endif
                    </div>

                    <!-- {{-- Galeri Foto Tambahan jika lebih dari 1 --}}
                    @if($activity->pictures && $activity->pictures->count() > 1)
                    <div class="grid grid-cols-3 gap-3 mb-8">
                        @foreach($activity->pictures as $galleryPic)
                            <div class="h-24 sm:h-32 rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                                <img src="{{ $galleryPic->path }}" alt="Dokumentasi" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="mb-4"></div>
                    @endif -->

                    {{-- Teks Konten --}}
                    <div class="prose prose-sm md:prose-base max-w-none text-gray-700 text-left leading-relaxed flex flex-col gap-5">
                        {!! $activity->description !!}
                    </div>

                    <!-- {{-- Kutipan Dosen jika ada --}}
                    @if(!empty($activity->quote) && $activity->quote !== '-')
                    <div class="mt-8 p-5 bg-[#f0f4fa] border-l-4 border-[#1a3675] rounded-r-xl italic text-gray-800">
                        <p class="text-sm md:text-base font-medium">"{{ $activity->quote }}"</p>
                        <span class="block text-xs md:text-sm font-bold text-[#1a3675] mt-2 not-italic">&mdash; {{ $activity->user->name ?? 'Dosen' }}</span>
                    </div>
                    @endif -->

                </div>

                {{-- =======================================
                     BARIS 2 KANAN: SIDEBAR AKTIVITAS RANDOM
                     ======================================= --}}
                <div class="lg:col-span-4 mt-8 md:mt-10 lg:mt-0">
                    <h3 class="text-xl font-extrabold text-gray-900 mb-5">
                        Aktivitas Dosen Lainnya
                    </h3>

                    <div class="flex flex-col gap-4">
                        @foreach ($relatedActivities as $item)
                        @php
                            $relImgUrl = $item->primary_image_url ?? $item->pictures?->first()?->path;
                        @endphp
                        <a href="{{ route('activity.show', $item->id) }}" class="h-[124px] group bg-white border border-gray-200 rounded-xl p-3 flex gap-4 hover:scale-105 hover:shadow-md transition-all duration-300">
                            
                            <div class="w-[120px] shrink-0 aspect-[4/3] rounded-lg overflow-hidden bg-gray-100">
                                @if($relImgUrl)
                                    <img src="{{ $relImgUrl }}" alt="Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-[#cbd5e1] flex items-center justify-center text-[10px] text-gray-500 group-hover:scale-105 transition-transform duration-500">No Image</div>
                                @endif
                            </div>

                            <div class="flex flex-col justify-center flex-1">
                                <span class="text-[10px] md:text-xs font-semibold text-[#1a3675] mb-1">
                                    {{ \Carbon\Carbon::parse($item->activity_date_start)->translatedFormat('d F Y') }}
                                </span>
                                <h4 class="text-sm md:text-[15px] font-extrabold text-[#1a3675] leading-snug line-clamp-3 group-hover:text-blue-800 transition-colors">
                                    {{ $item->activity_name }}
                                </h4>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-layouts.main>