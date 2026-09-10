<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Halaman Tidak Ditemukan</title>
    <!-- Pastikan Vite sudah terpanggil untuk Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative flex items-center justify-center min-h-screen bg-slate-50 antialiased overflow-hidden font-sans">
    
    <!-- Memanggil komponen background ornamen yang tadi kita buat wok! -->
    <x-ornament />
    <!-- Konten Utama 404 -->
    <div class="z-10 text-center px-6 relative">
        <!-- Angka 404 Raksasa dengan warna Navy -->
        <h1 class="text-[150px] md:text-[200px] font-extrabold text-[#1a3675] tracking-tighter leading-none opacity-90 drop-shadow-sm">
            404
        </h1>
        
        <!-- Pesan Error -->
        <div class="mt-4 md:mt-8">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800">
                Waduh, Sepertinya Nyasar!
            </h2>
            <p class="mt-3 text-slate-600 max-w-md mx-auto text-sm md:text-base">
                Halaman yang kamu cari mungkin sudah dihapus, berubah nama, atau memang tidak pernah ada dalam sistem.
            </p>
        </div>
        
        <!-- Tombol Kembali -->
        <div class="mt-10">
            <a href="/" class="inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-base font-semibold rounded-lg text-white bg-[#2563eb] hover:bg-[#1a3675] transition-all duration-300 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>