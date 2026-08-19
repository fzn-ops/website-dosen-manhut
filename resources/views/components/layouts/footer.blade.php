<footer class="bg-[#1a3675] text-white pt-12 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        
        {{-- Brand & Tagline --}}
        <h2 class="text-2xl font-bold tracking-wide">
            DosenManhut
        </h2>
        <p class="mt-2 text-sm text-gray-200">
            Website Profile Dosennya Manajemen Hutan
        </p>

        {{-- Navigasi Footer --}}
        <div class="mt-8 flex justify-center flex-wrap gap-x-8 gap-y-3 text-sm font-medium">
            <a href="{{ url('/') }}" class="hover:text-gray-300 transition">
                Beranda
            </a>
            <a href="{{ url('/tentang-kami') }}" class="hover:text-gray-300 transition">
                Tentang Kami
            </a>
            <a href="{{ url('/dosen') }}" class="hover:text-gray-300 transition">
                Dosen
            </a>
            <a href="{{ url('/aktivitas') }}" class="hover:text-gray-300 transition">
                Aktivitas
            </a>
        </div>

        {{-- Ikon Sosial Media (WhatsApp) --}}
        <div class="mt-6 flex justify-center">
            <a href="https://wa.me/nomor-tujuan" target="_blank" rel="noopener noreferrer" 
               class="text-white hover:text-green-400 transition" aria-label="WhatsApp">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                    <path d="M12.031 0C5.393 0 0 5.393 0 12.031c0 2.124.555 4.195 1.608 6.015L0 24l6.143-1.611a12.007 12.007 0 0 0 5.888 1.542h.005c6.638 0 12.031-5.393 12.031-12.031C24.062 5.393 18.67 0 12.031 0zm0 21.922a9.92 9.92 0 0 1-5.059-1.385l-.363-.215-3.76.986.999-3.666-.236-.376A9.914 9.914 0 0 1 2.11 12.03c0-5.47 4.451-9.921 9.921-9.921 2.651 0 5.143 1.033 7.017 2.908a9.866 9.866 0 0 1 2.908 7.017c0 5.47-4.452 9.921-9.925 9.921zm5.434-7.425c-.298-.149-1.765-.87-2.038-.97-.273-.1-.472-.149-.67.149-.199.298-.77 9.7-.944 1.168-.174.199-.348.224-.646.075-.298-.149-1.258-.464-2.397-1.48-.887-.791-1.486-1.768-1.66-2.066-.174-.298-.018-.459.13-.607.134-.134.298-.348.447-.522.149-.174.199-.298.298-.497.1-.199.05-.373-.025-.522-.075-.149-.67-1.616-.919-2.213-.242-.581-.487-.502-.67-.512l-.571-.01c-.199 0-.522.075-.795.373-.273.298-1.043 1.019-1.043 2.485s1.068 2.883 1.217 3.082c.149.199 2.102 3.21 5.093 4.502.712.308 1.268.492 1.701.629.715.227 1.365.195 1.88.118.574-.086 1.765-.721 2.013-1.417.248-.696.248-1.293.174-1.417-.075-.124-.273-.199-.571-.348z"/>
                </svg>
            </a>
        </div>

        {{-- Divider Line --}}
        <div class="mt-8 mb-6 border-t border-white/20"></div>

        {{-- Copyright Dinamis --}}
        <p class="text-xs text-gray-300">
            &copy; {{ date('Y') }} DMNH IPB University
        </p>

    </div>
</footer>