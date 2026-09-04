<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

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

defineEmits(['navigate', 'logout']);

const page = usePage();
const isLockedAccount = computed(() => {
	const user = page.props.auth?.user;
	if (!user) return false;
	return !!(user.is_locked ?? (user.is_default_password || user.is_email_empty));
});

const menuItems = [
	{ label: 'Dashboard', href: '/dosen/dashboard', pattern: 'dosen.dashboard*', icon: 'home' },
	{ label: 'Aktivitas', href: '/dosen/aktivitas', pattern: 'dosen.aktivitas*', icon: 'activity' },
	{ label: 'Profile', href: '/dosen/profile', pattern: 'dosen.profile*', icon: 'teacher' },
];

const isLocked = (item) => {
	return isLockedAccount.value && item.label !== 'Profile';
};

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
		'shrink-0 flex-col bg-[#1d3a7a] font-poppins text-white select-none',
		mobile
			? [
				'fixed left-0 top-0 bottom-0 z-40 h-screen h-[100dvh] w-[265px] max-w-[82vw] overflow-y-auto overflow-x-hidden shadow-2xl transition-transform duration-300 ease-in-out flex',
				collapsed ? '-translate-x-full pointer-events-none' : 'translate-x-0 pointer-events-auto'
			]
			: [
				'h-full self-stretch overflow-hidden transition-[width] duration-200 flex shrink-0',
				collapsed ? 'w-[80px]' : 'w-[275px]'
			]
	]">
		<!-- Logo Header Row (DosenManhut) -->
		<div class="flex h-[70px] shrink-0 items-center justify-center border-b border-white/10 px-4">
			<Link :href="isLockedAccount ? '/dosen/profile' : '/dosen/dashboard'" class="flex items-center justify-center gap-3.5 focus:outline-none">
				<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-base font-black text-[#1d3a7a] shadow-sm">
				</span>
				<span v-if="!collapsed || mobile" class="whitespace-nowrap text-[22px] sm:text-[24px] font-bold leading-none tracking-tight text-white">
					DoSend!
				</span>
			</Link>
		</div>

		<!-- Nav Menu Area -->
		<nav aria-label="Navigasi dosen" :class="[
			'mt-8 sm:mt-10 flex-1 space-y-4',
			(!mobile && collapsed) ? 'px-3' : 'px-0'
		]">
			<component
				:is="isLocked(item) ? 'div' : Link"
				v-for="item in menuItems"
				:key="item.label"
				:href="isLocked(item) ? undefined : item.href"
				:class="[
					'flex h-11 items-center text-[15px] font-semibold transition-colors focus:outline-none whitespace-nowrap select-none',
					(!mobile && collapsed) ? 'justify-center rounded-full px-0' : 'gap-5 pl-7 pr-4',
					isLocked(item)
						? (!mobile && collapsed)
							? 'opacity-35 cursor-not-allowed justify-center'
							: 'ml-6 rounded-l-full opacity-35 cursor-not-allowed text-white/60'
						: isActive(item)
							? (!mobile && collapsed) ? 'bg-[#3c5da1] text-white' : 'ml-6 rounded-l-full bg-[#3c5da1] text-white shadow-sm'
							: (!mobile && collapsed) ? 'text-white/80 hover:bg-[#142e63] hover:text-white' : 'ml-6 rounded-l-full text-white/80 hover:bg-[#142e63] hover:text-white',
				]"
				:title="isLocked(item) ? `${item.label} (Terkunci - Ubah password default Anda terlebih dahulu)` : ((!mobile && collapsed) ? item.label : undefined)"
				@click="!isLocked(item) && $emit('navigate')"
			>
				<img :src="`/assets/icons/${item.icon}.svg`" :alt="`${item.label} icon`" class="h-5 w-5 shrink-0 object-contain" />
				<span v-if="!collapsed || mobile" class="whitespace-nowrap truncate flex-1">{{ item.label }}</span>
				<!-- Lock icon when item is locked -->
				<svg v-if="(!collapsed || mobile) && isLocked(item)" class="h-4 w-4 shrink-0 text-white/60 ml-auto" fill="currentColor" viewBox="0 0 20 20">
					<path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
				</svg>
			</component>
		</nav>

		<!-- Logout Button -->
		<div class="mt-auto pb-8 pt-4">
			<button
				type="button"
				:class="[
					'flex h-11 items-center text-[15px] font-semibold text-white/80 transition-colors focus:outline-none hover:bg-[#142e63] hover:text-white whitespace-nowrap',
					(!mobile && collapsed) ? 'w-full justify-center rounded-full px-0' : 'ml-6 w-[calc(100%-24px)] gap-5 rounded-l-full pl-7 pr-4',
				]"
				:title="(!mobile && collapsed) ? 'Logout' : undefined"
				@click="$emit('logout')"
			>
				<img src="/assets/icons/logout.svg" alt="Logout icon" class="h-5 w-5 shrink-0 object-contain" />
				<span v-if="!collapsed || mobile" class="whitespace-nowrap">Logout</span>
			</button>
		</div>
	</aside>
</template>
