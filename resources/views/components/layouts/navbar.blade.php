<nav class="bg-[#1a3675] text-white shadow-md sticky top-0 z-50">
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
                
                <a href="{{ url('/dosen') }}" 
                   class="relative py-1 transition-colors duration-300 {{ request()->is('dosen*') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300 {{ request()->is('dosen*') ? 'after:w-full' : 'after:w-0 hover:after:w-full' }}">
                    Dosen
                </a>
                
                <a href="{{ url('/aktivitas') }}" 
                   class="relative py-1 transition-colors duration-300 {{ request()->is('aktivitas*') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300 {{ request()->is('aktivitas*') ? 'after:w-full' : 'after:w-0 hover:after:w-full' }}">
                    Aktivitas
                </a>
                
                <!-- <a href="{{ url('/faq') }}" 
                   class="relative py-1 transition-colors duration-300 {{ request()->is('faq*') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} after:content-[''] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:bg-white after:transition-all after:duration-300 {{ request()->is('faq*') ? 'after:w-full' : 'after:w-0 hover:after:w-full' }}">
                    FAQ
                </a> -->
            </div>

            {{-- Mobile Menu Button (Hamburger) --}}
            <div class="flex md:hidden">
                <button type="button" 
                        onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="text-gray-300 hover:text-white focus:outline-none p-2"
                        aria-label="Toggle Navigation">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile Menu Dropdown --}}
    <div id="mobile-menu" class="hidden md:hidden bg-[#162e63] border-t border-blue-900 pt-2 pb-4 space-y-1">
        <a href="{{ url('/') }}" class="block px-4 py-2 text-base transition-colors {{ request()->is('/') ? 'bg-[#1a3675] text-white border-l-4 border-white font-semibold' : 'text-gray-300 hover:bg-[#1a3675] hover:text-white border-l-4 border-transparent' }}">
            Beranda
        </a>
        <a href="{{ url('/about') }}" class="block px-4 py-2 text-base transition-colors {{ request()->is('about*') ? 'bg-[#1a3675] text-white border-l-4 border-white font-semibold' : 'text-gray-300 hover:bg-[#1a3675] hover:text-white border-l-4 border-transparent' }}">
            Tentang Kami
        </a>
        <a href="{{ url('/dosen') }}" class="block px-4 py-2 text-base transition-colors {{ request()->is('dosen*') ? 'bg-[#1a3675] text-white border-l-4 border-white font-semibold' : 'text-gray-300 hover:bg-[#1a3675] hover:text-white border-l-4 border-transparent' }}">
            Dosen
        </a>
        <a href="{{ url('/aktivitas') }}" class="block px-4 py-2 text-base transition-colors {{ request()->is('aktivitas*') ? 'bg-[#1a3675] text-white border-l-4 border-white font-semibold' : 'text-gray-300 hover:bg-[#1a3675] hover:text-white border-l-4 border-transparent' }}">
            Aktivitas
        </a>
        <!-- <a href="{{ url('/faq') }}" class="block px-4 py-2 text-base transition-colors {{ request()->is('faq*') ? 'bg-[#1a3675] text-white border-l-4 border-white font-semibold' : 'text-gray-300 hover:bg-[#1a3675] hover:text-white border-l-4 border-transparent' }}">
            FAQ
        </a> -->
    </div>
</nav>