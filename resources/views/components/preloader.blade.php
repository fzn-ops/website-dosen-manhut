<script>
    // 1. CEK MEMORI BROWSER SEBELUM HTML DI-RENDER
    if (sessionStorage.getItem('preloader_shown')) {
        document.documentElement.classList.add('hide-preloader');
    }
</script>

<style>
    html.hide-preloader #premium-preloader {
        display: none !important;
    }

    /* Animasi Teks */
    @keyframes luxury-fade-up {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    /* Animasi Garis */
    @keyframes luxury-expand-line {
        0% { width: 0%; opacity: 0; }
        50% { width: 100%; opacity: 1; }
        100% { width: 100%; opacity: 0; }
    }

    /* Efek Tirai */
    .luxury-curtain-up {
        transform: translateY(-100%);
        transition: transform 1.2s cubic-bezier(0.77, 0, 0.175, 1); 
    }
</style>

<!-- Container Preloader -->
<div id="premium-preloader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-[#f8fafc] w-full h-full origin-top">
    
    <div class="relative flex flex-col items-center justify-center">
        <!-- Teks Brand -->
        <h1 class="text-[#1a3675] text-xl md:text-2xl font-light tracking-[0.5em] uppercase mb-6" 
            style="animation: luxury-fade-up 1.2s ease-out forwards;">
            Dosen<span class="font-bold">Manhut</span>
        </h1>
        
        <!-- Garis Indikator -->
        <div class="w-[200px] h-[1px] bg-slate-200 relative overflow-hidden">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 h-full bg-[#1a3675]" 
                 style="animation: luxury-expand-line 2s cubic-bezier(0.65, 0, 0.35, 1) infinite;">
            </div>
        </div>
        
        <!-- Teks Loading -->
        <div class="absolute -bottom-10 text-[10px] tracking-[0.3em] text-slate-400 font-mono uppercase" 
             style="animation: luxury-fade-up 1s ease-out 0.8s forwards; opacity: 0;">
            Menyiapkan Modul
        </div>
    </div>

</div>

<!-- Logika Javascript -->
<script>
    // 2. JALANKAN ANIMASI HANYA JIKA PRELOADER BELUM PERNAH MUNCUL
    if (!sessionStorage.getItem('preloader_shown')) {
        window.addEventListener('load', function() {
            const preloader = document.getElementById('premium-preloader');
            
            if (preloader) {
                setTimeout(() => {
                    preloader.classList.add('luxury-curtain-up');
                    
                    setTimeout(() => {
                        preloader.remove();
                        // 3. CATAT DI MEMORI BAHWA PRELOADER SUDAH TAYANG!
                        sessionStorage.setItem('preloader_shown', 'true');
                    }, 1200);
                }, 400); 
            }
        });
    }
</script>