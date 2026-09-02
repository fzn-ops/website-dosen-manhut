<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
	collapsed: {
		type: Boolean,
		default: false,
	},
	mobile: {
		type: Boolean,
		default: false,
	},
});

defineEmits(['navigate']);

const menuItems = [
	{ label: 'Dashboard', href: '/admin/dashboard', pattern: 'admin.dashboard*', icon: 'home' },
	{ label: 'Daftar Dosen', href: '/admin/dosen', pattern: 'admin.dosen*', icon: 'teacher' },
	{ label: 'Profile Dosen', href: '/admin/profile-dosen', pattern: 'admin.profiledosen*', icon: 'teacher-profile' },
	{ label: 'Aktivitas', href: '/admin/aktivitas', pattern: 'admin.aktivitasdosen*', icon: 'activity' },
];

const isActive = (item) => {
	try {
		return route().current(item.pattern) || window.location.pathname.startsWith(item.href);
	} catch {
		return window.location.pathname.startsWith(item.href);
	}
};
</script>

<template>
	<aside :class="[
		'flex h-screen shrink-0 flex-col overflow-hidden bg-[#1d3a7a] font-poppins text-white transition-[width] duration-200',
		mobile ? 'fixed left-0 top-0 z-40' : 'sticky top-0',
		collapsed ? 'w-[80px]' : 'w-[275px]',
	]">
		<!-- Logo Header Row (Centered Horizontally & Vertically) -->
		<div :class="[
			'flex h-[70px] shrink-0 items-center justify-center border-b border-white/10 px-4',
		]">
			<Link href="/admin/dashboard" class="flex items-center justify-center gap-3.5 focus:outline-none">
				<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-base font-black text-[#1d3a7a] shadow-sm">
				</span>
				<span v-if="!collapsed" class="whitespace-nowrap text-[24px] font-bold leading-none tracking-tight text-white">
					DoSend!
				</span>
			</Link>
		</div>

		<!-- Nav Menu Area (Indented / Menjorok ke kanan dengan rounded-l-full ke tepi kanan) -->
		<nav aria-label="Navigasi utama" :class="[
			'mt-8 sm:mt-10 flex-1 space-y-4',
			collapsed ? 'px-3' : 'px-0'
		]">
			<Link
				v-for="item in menuItems"
				:key="item.label"
				:href="item.href"
				:class="[
					'flex h-11 items-center text-[15px] font-semibold transition-colors focus:outline-none whitespace-nowrap',
					collapsed ? 'justify-center rounded-full px-0' : 'gap-5 pl-7 pr-4',
					isActive(item)
						? collapsed ? 'bg-[#3c5da1] text-white' : 'ml-6 rounded-l-full bg-[#3c5da1] text-white shadow-sm'
						: collapsed ? 'text-white/80 hover:bg-[#142e63] hover:text-white' : 'ml-6 rounded-l-full text-white/80 hover:bg-[#142e63] hover:text-white',
				]"
				:title="collapsed ? item.label : undefined"
				@click="$emit('navigate')"
			>
				<img :src="`/assets/icons/${item.icon}.svg`" :alt="`${item.label} icon`" class="h-5 w-5 shrink-0 object-contain" />
				<span v-if="!collapsed" class="whitespace-nowrap truncate">{{ item.label }}</span>
			</Link>
		</nav>

		<!-- Logout Button (Menjorok sejajar dengan menu navigasi) -->
		<div class="mt-auto pb-8">
			<button
				type="button"
				:class="[
					'flex h-11 items-center text-[15px] font-semibold text-white/80 transition-colors focus:outline-none hover:bg-[#142e63] hover:text-white whitespace-nowrap',
					collapsed ? 'w-full justify-center rounded-full px-0' : 'ml-6 w-[calc(100%-24px)] gap-5 rounded-l-full pl-7 pr-4',
				]"
				:title="collapsed ? 'Logout' : undefined"
				@click="$emit('logout')"
			>
				<img src="/assets/icons/logout.svg" alt="Logout icon" class="h-5 w-5 shrink-0 object-contain" />
				<span v-if="!collapsed" class="whitespace-nowrap">Logout</span>
			</button>
		</div>
	</aside>
</template>
