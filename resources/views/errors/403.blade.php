<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Akses Ditolak</title>
    <!-- Pastikan Vite sudah terpanggil -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative flex items-center justify-center min-h-screen bg-slate-50 antialiased overflow-hidden font-sans">
    
    <!-- Memanggil komponen background ornamen estetik kita -->
    <x-ornament />

    <!-- Konten Utama 403 -->
    <div class="z-10 text-center px-6 relative">
        <!-- Angka 403 Raksasa -->
        <h1 class="text-[150px] md:text-[200px] font-extrabold text-[#1a3675] tracking-tighter leading-none opacity-90 drop-shadow-sm">
            403
        </h1>
        
        <!-- Pesan Error -->
        <div class="mt-4 md:mt-8">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800">
                Akses Ditolak!
            </h2>
            <p class="mt-3 text-slate-600 max-w-md mx-auto text-sm md:text-base">
                Waduh, kamu tidak memiliki izin untuk mengakses halaman ini. Halaman ini mungkin dikhususkan untuk peran atau otoritas yang berbeda.
            </p>
        </div>
        
        <!-- Tombol Kembali -->
        <div class="mt-10">
            <a href="/login" class="inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-base font-semibold rounded-lg text-white bg-[#2563eb] hover:bg-[#1a3675] transition-all duration-300 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Kembali ke Tempat Aman
            </a>
        </div>
    </div>

</body>
</html>