<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
	collapsed: {
		type: Boolean,
		default: false,
	},
});

const menuItems = [
	{ label: 'Dashboard', href: route('dashboard'), pattern: 'dashboard', icon: 'home' },
	{ label: 'Daftar Dosen', href: '/dosen', pattern: 'dosen*', icon: 'teacher' },
	{ label: 'Profile Dosen', href: route('profile.edit'), pattern: 'profile*', icon: 'teacher-profile' },
	{ label: 'Aktivitas', href: '/aktivitas', pattern: 'aktivitas*', icon: 'activity' },
];
</script>

<template>
	<aside :class="[
		'sticky top-0 flex h-screen shrink-0 flex-col overflow-hidden bg-[#1d3a7a] py-10 font-poppins text-white transition-[width] duration-200',
		collapsed ? 'w-[80px] px-3' : 'w-[275px] px-5',
	]">
		<Link :href="route('dashboard')" :class="[
			'mb-12 flex items-center gap-3',
			collapsed ? 'justify-center px-0' : 'px-4',
		]">
			<span class="h-11 w-11 rounded-full bg-black" aria-hidden="true"></span>
			<span v-if="!collapsed" class="text-[32px] font-bold leading-none tracking-tight">Talenta</span>
		</Link>

		<nav aria-label="Navigasi utama" :class="[collapsed ? 'space-y-4' : '-mx-5 space-y-4']">
			<Link
				v-for="item in menuItems"
				:key="item.label"
				:href="item.href"
				:class="[
					'flex h-11 items-center text-[15px] font-semibold transition-colors focus:outline-none',
					collapsed ? 'justify-center rounded-full px-0' : 'gap-5 pl-5 pr-5',
					route().current(item.pattern)
						? collapsed ? 'bg-[#3c5da1] text-white' : 'ml-5 mr-0 rounded-l-full bg-[#3c5da1] text-white'
						: collapsed ? 'text-white hover:bg-[#142e63]' : 'ml-5 rounded-l-full text-white hover:bg-[#142e63]',
				]"
				:title="collapsed ? item.label : undefined"
			>
				<img :src="`/assets/icons/${item.icon}.svg`" :alt="`${item.label} icon`" class="h-5 w-5 shrink-0 object-contain" />
				<span v-if="!collapsed">{{ item.label }}</span>
			</Link>
		</nav>

		<Link
			:href="route('logout')"
			method="post"
			as="button"
			:class="[
				'mt-auto flex h-11 items-center text-[15px] font-semibold text-white transition-colors focus:outline-none hover:bg-[#142e63]',
				collapsed ? 'w-full justify-center rounded-full px-0' : 'w-[calc(100%+20px)] gap-5 rounded-l-full px-5',
			]"
			:title="collapsed ? 'Logout' : undefined"
		>
			<img src="/assets/icons/logout.svg" alt="Logout icon" class="h-5 w-5 shrink-0 object-contain" />
			<span v-if="!collapsed">Logout</span>
		</Link>
	</aside>
</template>
