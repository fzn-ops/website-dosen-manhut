<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

defineEmits(['toggle']);

const showingAccountMenu = ref(false);
</script>

<template>
	<header class="flex h-[70px] items-center justify-between bg-[#f1f4f8] px-8 shadow-[0_2px_5px_rgba(0,0,0,0.35)]">
		<button
			type="button"
			aria-label="Buka menu navigasi"
			class="rounded-md p-1 text-[#173653] transition-colors hover:bg-[#e1e7ee] focus:outline-none focus:ring-2 focus:ring-[#3c5da1]"
			@click="$emit('toggle')"
		>
			<svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
				<path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
			</svg>
		</button>

		<div class="relative">
			<button
				type="button"
				class="flex items-center gap-4 rounded-md px-2 py-1 text-[#173653] transition-colors hover:bg-[#e1e7ee] focus:outline-none focus:ring-2 focus:ring-[#3c5da1]"
				aria-label="Buka menu akun"
				:aria-expanded="showingAccountMenu"
				@click="showingAccountMenu = !showingAccountMenu"
			>
				<span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#173653] text-[#f1f4f8]" aria-hidden="true">
					<svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
						<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm0 4a3.25 3.25 0 1 1 0 6.5A3.25 3.25 0 0 1 12 6Zm0 14a7.96 7.96 0 0 1-5.6-2.3c.45-2.05 2.27-3.45 5.6-3.45s5.15 1.4 5.6 3.45A7.96 7.96 0 0 1 12 20Z" />
					</svg>
				</span>
				<span class="text-base font-semibold">{{ $page.props.auth?.user?.name || 'Admin' }}</span>
			</button>

			<div v-if="showingAccountMenu" class="absolute right-0 z-20 mt-2 w-48 overflow-hidden rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5">
				<Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" @click="showingAccountMenu = false">Profile</Link>
				<Link :href="route('logout')" method="post" as="button" class="block w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100" @click="showingAccountMenu = false">Log Out</Link>
			</div>
		</div>
	</header>
</template>
