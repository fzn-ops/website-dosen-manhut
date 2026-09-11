<header class="sticky top-0 z-50 h-16">
    <nav id="main-navbar" class="absolute top-0 left-0 right-0 w-full bg-[#1a3675] text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                {{-- Brand / Logo --}}
                <div class="flex items-center space-x-3">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                        <div class="w-9 h-9 rounded-full bg-gray-300 flex items-center justify-center overflow-hidden">
                            {{-- <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover"> --}}
                        </div>
                        <span class="font-bold text-lg sm:text-xl tracking-tight text-white group-hover:text-gray-200 transition">
                            DosenManhut
                        </span>
                    </a>
                </div>

                {{-- Desktop Navigation Menu --}}
                <div class="hidden md:flex items-center space-x-8 text-sm font-medium">
                    <a href="{{ url('/') }}" 
                       class="relative py-1 transition-colors duration-300 {{ request()->is('/') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300 {{ request()->is('/') ? 'after:w-full' : 'after:w-0 hover:after:w-full' }}">
                        Beranda
                    </a>
                    
                    <a href="{{ url('/about') }}" 
                       class="relative py-1 transition-colors duration-300 {{ request()->is('about*') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300 {{ request()->is('about*') ? 'after:w-full' : 'after:w-0 hover:after:w-full' }}">
                        Tentang Kami
                    </a>
                    
                    <a href="{{ url('/lecturers') }}" 
                       class="relative py-1 transition-colors duration-300 {{ request()->is('lecturers*') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300 {{ request()->is('lecturers*') ? 'after:w-full' : 'after:w-0 hover:after:w-full' }}">
                        Dosen
                    </a>
                    
                    <a href="{{ url('/activities') }}" 
                       class="relative py-1 transition-colors duration-300 {{ request()->is('activities*') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300 {{ request()->is('activities*') ? 'after:w-full' : 'after:w-0 hover:after:w-full' }}">
                        Aktivitas
                    </a>
                </div>

                {{-- Mobile Menu Button (Animated Hamburger to X) --}}
                <div class="flex md:hidden">
                    <button type="button" 
                            id="mobile-menu-btn"
                            aria-expanded="false"
                            aria-controls="mobile-menu"
                            class="w-9 h-9 flex flex-col justify-center items-center text-white hover:text-gray-200 focus:outline-none transition-colors"
                            aria-label="Toggle Navigation">
                        <span id="bar-top" class="block w-5 h-0.5 bg-white rounded-full transition-all duration-300 ease-in-out -translate-y-1.5 origin-center"></span>
                        <span id="bar-mid" class="block w-5 h-0.5 bg-white rounded-full transition-all duration-200 ease-in-out origin-center"></span>
                        <span id="bar-bot" class="block w-5 h-0.5 bg-white rounded-full transition-all duration-300 ease-in-out translate-y-1.5 origin-center"></span>
                    </button>
                </div>

            </div>
        </div>

        {{-- Mobile Menu (Menyatu murni di dalam 1 kartu navbar dengan transisi seamless) --}}
        <div id="mobile-menu" class="grid grid-rows-[0fr] opacity-0 transition-all duration-300 ease-out md:hidden pointer-events-none">
            <div class="overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 pt-1 pb-6 flex flex-col space-y-4">
                    <a href="{{ url('/') }}" 
                       class="w-fit text-base py-1 relative transition-colors duration-300 {{ request()->is('/') ? 'text-white font-semibold after:w-full' : 'text-white/75 hover:text-white after:w-0 hover:after:w-full' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300">
                        Beranda
                    </a>
                    <a href="{{ url('/about') }}" 
                       class="w-fit text-base py-1 relative transition-colors duration-300 {{ request()->is('about*') ? 'text-white font-semibold after:w-full' : 'text-white/75 hover:text-white after:w-0 hover:after:w-full' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300">
                        Tentang Kami
                    </a>
                    <a href="{{ url('/lecturers') }}" 
                       class="w-fit text-base py-1 relative transition-colors duration-300 {{ request()->is('lecturers*') ? 'text-white font-semibold after:w-full' : 'text-white/75 hover:text-white after:w-0 hover:after:w-full' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300">
                        Dosen
                    </a>
                    <a href="{{ url('/activities') }}" 
                       class="w-fit text-base py-1 relative transition-colors duration-300 {{ request()->is('activities*') ? 'text-white font-semibold after:w-full' : 'text-white/75 hover:text-white after:w-0 hover:after:w-full' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300">
                        Aktivitas
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>

{{-- Backdrop for outside clicks --}}
<div id="mobile-menu-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-[2px] transition-opacity duration-300 opacity-0 pointer-events-none md:hidden z-40"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const backdrop = document.getElementById('mobile-menu-backdrop');
        const barTop = document.getElementById('bar-top');
        const barMid = document.getElementById('bar-mid');
        const barBot = document.getElementById('bar-bot');

        if (!btn || !menu) return;

        function openMenu() {
            // Expand Menu
            menu.classList.remove('grid-rows-[0fr]', 'opacity-0', 'pointer-events-none');
            menu.classList.add('grid-rows-[1fr]', 'opacity-100');

            // Backdrop
            if (backdrop) {
                backdrop.classList.remove('pointer-events-none', 'opacity-0');
                backdrop.classList.add('opacity-100');
            }

            // Lock scroll
            document.body.classList.add('overflow-hidden');
            btn.setAttribute('aria-expanded', 'true');

            // Animate Hamburger to X
            barTop?.classList.remove('-translate-y-1.5');
            barTop?.classList.add('translate-y-0.5', 'rotate-45');
            barMid?.classList.add('opacity-0', 'scale-x-0');
            barBot?.classList.remove('translate-y-1.5');
            barBot?.classList.add('-translate-y-0.5', '-rotate-45');
        }

        function closeMenu() {
            // Collapse Menu
            menu.classList.remove('grid-rows-[1fr]', 'opacity-100');
            menu.classList.add('grid-rows-[0fr]', 'opacity-0', 'pointer-events-none');

            // Backdrop
            if (backdrop) {
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
            }

            // Unlock scroll
            document.body.classList.remove('overflow-hidden');
            btn.setAttribute('aria-expanded', 'false');

            // Animate X back to Hamburger
            barTop?.classList.remove('translate-y-0.5', 'rotate-45');
            barTop?.classList.add('-translate-y-1.5');
            barMid?.classList.remove('opacity-0', 'scale-x-0');
            barBot?.classList.remove('-translate-y-0.5', '-rotate-45');
            barBot?.classList.add('translate-y-1.5');
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = btn.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        // Click outside on backdrop
        if (backdrop) {
            backdrop.addEventListener('click', closeMenu);
        }

        // Click outside on document
        document.addEventListener('click', function (e) {
            if (btn.getAttribute('aria-expanded') === 'true') {
                const nav = document.getElementById('main-navbar');
                if (nav && !nav.contains(e.target)) {
                    closeMenu();
                }
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && btn.getAttribute('aria-expanded') === 'true') {
                closeMenu();
            }
        });

        // Auto close when resized to desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768 && btn.getAttribute('aria-expanded') === 'true') {
                closeMenu();
            }
        });
    });
</script>