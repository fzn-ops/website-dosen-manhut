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
	{ label: 'Dashboard', href: '/dosen/dashboard', pattern: 'dosen.dashboard*', icon: 'home' },
	{ label: 'Aktivitas', href: '/dosen/aktivitas', pattern: 'dosen.aktivitas*', icon: 'activity' },
	{ label: 'Profile', href: '/dosen/profile', pattern: 'dosen.profile*', icon: 'teacher-profile' },
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
		<!-- Logo Header Row (DosenManhut) -->
		<div :class="[
			'flex h-[70px] shrink-0 items-center justify-center border-b border-white/10 px-4',
		]">
			<Link href="/dosen/dashboard" class="flex items-center justify-center gap-3.5 focus:outline-none">
				<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-base font-black text-[#1d3a7a] shadow-sm">
				</span>
				<span v-if="!collapsed" class="text-[24px] font-bold leading-none tracking-tight text-white">
					DoSend!
				</span>
			</Link>
		</div>

		<!-- Nav Menu Area -->
		<nav aria-label="Navigasi dosen" :class="[
			'mt-8 sm:mt-10 flex-1 space-y-4',
			collapsed ? 'px-3' : 'px-0'
		]">
			<Link
				v-for="item in menuItems"
				:key="item.label"
				:href="item.href"
				:class="[
					'flex h-11 items-center text-[15px] font-semibold transition-colors focus:outline-none',
					collapsed ? 'justify-center rounded-full px-0' : 'gap-5 pl-7 pr-4',
					isActive(item)
						? collapsed ? 'bg-[#3c5da1] text-white' : 'ml-6 rounded-l-full bg-[#3c5da1] text-white shadow-sm'
						: collapsed ? 'text-white/80 hover:bg-[#142e63] hover:text-white' : 'ml-6 rounded-l-full text-white/80 hover:bg-[#142e63] hover:text-white',
				]"
				:title="collapsed ? item.label : undefined"
				@click="$emit('navigate')"
			>
				<img :src="`/assets/icons/${item.icon}.svg`" :alt="`${item.label} icon`" class="h-5 w-5 shrink-0 object-contain" />
				<span v-if="!collapsed">{{ item.label }}</span>
			</Link>
		</nav>

		<!-- Logout Button -->
		<div class="mt-auto pb-8">
			<Link
				:href="route('logout')"
				method="post"
				as="button"
				:class="[
					'flex h-11 items-center text-[15px] font-semibold text-white/80 transition-colors focus:outline-none hover:bg-[#142e63] hover:text-white',
					collapsed ? 'w-full justify-center rounded-full px-0' : 'ml-6 w-[calc(100%-24px)] gap-5 rounded-l-full pl-7 pr-4',
				]"
				:title="collapsed ? 'Logout' : undefined"
				@click="$emit('navigate')"
			>
				<img src="/assets/icons/logout.svg" alt="Logout icon" class="h-5 w-5 shrink-0 object-contain" />
				<span v-if="!collapsed">Logout</span>
			</Link>
		</div>
	</aside>
</template>
