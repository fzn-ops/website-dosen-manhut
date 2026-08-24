<x-layouts.main>
    <x-slot:title>
        Detail Aktivitas | DosenManhut
    </x-slot>

    {{-- Data Dummy untuk Sidebar --}}
    @php
        $aktivitasLainnya = [
            ['title' => 'Pelatihan Lifeskill Survive di Hutan, Fahutan IPB', 'date' => '10 Juni 2029', 'img' => 'https://picsum.photos/seed/hutan1/400/300'],
            ['title' => 'Pelatihan Lifeskill Survive di Hutan, Fahutan IPB', 'date' => '10 Juni 2029', 'img' => 'https://picsum.photos/seed/hutan2/400/300'],
            ['title' => 'Pelatihan Lifeskill Survive di Hutan, Fahutan IPB', 'date' => '10 Juni 2029', 'img' => 'https://picsum.photos/seed/hutan3/400/300'],
        ];
    @endphp

    <div class="bg-[#fafafc] w-full min-h-screen py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. Breadcrumb --}}
            <nav class="text-sm font-medium text-gray-900 mb-6 flex items-center gap-1.5">
                <a href="{{ url('/aktivitas') }}" class="hover:text-[#1a3675] transition-colors">Aktivitas</a>
                <span>/</span>
                <span class="text-[#1a3675] underline underline-offset-4 decoration-[#1a3675]/40 truncate max-w-[200px] sm:max-w-none">
                    Lorem Ipsum Dol...
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
                        Lorem Ipsum Dolor
                    </h1>
                    
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center text-sm md:text-base gap-3">
                        {{-- Kiri: Peran & Nama --}}
                        <div class="font-bold text-[#1a3675] flex items-center gap-2">
                            <span>Pembicara</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#1a3675]"></span>
                            <span>Dr. Fulan Fulana, M.Si</span>
                        </div>
                        
                        {{-- Kanan: Tanggal (Warna Biru, Mentok sejajar ujung gambar) --}}
                        <div class="font-bold text-[#1a3675] text-left sm:text-right">
                            11 November 2029
                        </div>
                    </div>
                    
                </div>

                {{-- BARIS 1 KANAN: KOSONG --}}
                {{-- (Agar sidebar turun dan sejajar dengan gambar di baris ke-2) --}}
                <div class="hidden lg:block lg:col-span-4"></div>


                {{-- =======================================
                     BARIS 2 KIRI: GAMBAR & TEKS KONTEN
                     ======================================= --}}
                <div class="lg:col-span-8 flex flex-col">
                    
                    {{-- Banner Placeholder --}}
                    <div class="w-full h-[250px] sm:h-[350px] md:h-[450px] bg-[#d9d9d9] rounded-2xl mb-8 overflow-hidden shadow-sm">
                        {{-- <img src="{{ asset('images/banner.jpg') }}" alt="Banner" class="w-full h-full object-cover"> --}}
                    </div>

                    {{-- Teks Konten --}}
                    <div class="prose prose-sm md:prose-base max-w-none text-gray-700 text-left leading-relaxed flex flex-col gap-5">
                        <p>
                            Lorem ipsum dolor sit amet, cupidatat eiusmod duis ut. Magna dolore dolor ex elit sed non cillum do aliqua adipiscing ad. 
                            Ullamco fugiat occaecat proident dolore incididunt eu pariatur officia. Exercitation eiusmod sunt adipiscing pariatur est nulla 
                            tempor enim voluptate laborum pariatur. Dolore velit occaecat aliqua sint ullamco dolor exercitation Fauzan Cikarang.
                        </p>
                        <p>
                            Commodo in non exercitation nulla enim qui aliquip nulla. Esse adipiscing ex anim fugiat labore mollit mollit. Id dolore ea 
                            minim enim ut laborum magna. Aku Fauzan liqua ut nulla consequat sunt enim laboris voluptate quis. Non dolore elit 
                            reprehenderit dolore laboris laboris mollit incididunt eu in Fauzan Fuadiansyah. Velit quis aliqua sed dolore excepteur irure 
                            cillum labore duis. Sed fugiat officia ad reprehenderit excepteur laborum cillum laborum reprehenderit nostrud. Commodo 
                            laboris sed cupidatat cillum aute fugiat veniam ex in eiusmod. Adipiscing nisi elit exercitation ea id ullamco eu quis. Mollit 
                            reprehenderit duis ut ut deserunt magna esse excepteur pariatur nisi. Sed tempor anim dolore anim excepteur eu nisi labore.
                        </p>
                    </div>

                </div>

                {{-- =======================================
                     BARIS 2 KANAN: SIDEBAR AKTIVITAS
                     ======================================= --}}
                <div class="lg:col-span-4 mt-8 md:mt-10 lg:mt-0">
                    {{-- Sekarang judul sidebar ini dijamin 100% lurus dengan batas atas gambar abu-abu! --}}
                    <h3 class="text-xl font-extrabold text-gray-900 mb-5">
                        Aktivitas Dosen Lainnya
                    </h3>

                    <div class="flex flex-col gap-4">
                        @foreach ($aktivitasLainnya as $item)
                        <a href="#" class="h-[124px] group bg-white border border-gray-200 rounded-xl p-3 flex gap-4 hover:scale-50 hover:shadow-md transition-all duration-300">
                            
                            <div class="w-[120px] shrink-0 aspect-[4/3] rounded-lg overflow-hidden bg-gray-100">
                                <img src="{{ $item['img'] }}" alt="Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>

                            <div class="flex flex-col justify-center flex-1">
                                <span class="text-[10px] md:text-xs font-semibold text-[#1a3675] mb-1">
                                    {{ $item['date'] }}
                                </span>
                                <h4 class="text-sm md:text-[15px] font-extrabold text-[#1a3675] leading-snug line-clamp-3 group-hover:text-blue-800 transition-colors">
                                    {{ $item['title'] }}
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