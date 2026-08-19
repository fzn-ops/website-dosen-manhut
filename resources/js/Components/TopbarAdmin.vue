<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

defineEmits(['toggle']);

const showingAccountMenu = ref(false);
</script>

<template>
	<header class="sticky top-0 z-20 flex h-[70px] items-center justify-between bg-[#f1f4f8] px-4 shadow-[0_1px_3px_rgba(0,0,0,0.18)] sm:px-8">
		<button
			type="button"
			aria-label="Buka menu navigasi"
			class="rounded-md p-1 text-[#173653] transition-colors focus:outline-none focus:ring-0 active:bg-transparent hover:bg-[#e1e7ee]"
			@click="$emit('toggle')"
		>
			<svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
				<path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
			</svg>
		</button>

		<div>
			<button
				type="button"
				class="flex items-center gap-4 rounded-md px-2 py-1 text-[#173653] transition-colors focus:outline-none focus:ring-0 active:bg-transparent hover:bg-[#e1e7ee]"
				aria-label="Buka menu akun"
				:aria-expanded="showingAccountMenu"
				@click="showingAccountMenu = !showingAccountMenu"
			>
				<img src="/assets/icons/default-profile.svg" alt="Profile icon" class="h-8 w-8 rounded-full object-contain" />
				<span class="font-poppins text-base font-semibold">{{ $page.props.auth?.user?.name || 'Admin' }}</span>
			</button>

			<button
				v-if="showingAccountMenu"
				type="button"
				class="fixed inset-0 z-30 cursor-default"
				aria-label="Tutup menu akun"
				@click="showingAccountMenu = false"a
			></button>

			<div
				v-if="showingAccountMenu"
				class="fixed right-4 top-[78px] z-40 w-44 rounded-xl bg-white p-2 shadow-lg ring-1 ring-black/10 sm:right-8"
			>
				<Link
					:href="route('logout')"
					method="post"
					as="button"
					class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left font-poppins text-sm font-semibold text-[#173653] transition-colors hover:bg-[#e8eef8]"
					@click="showingAccountMenu = false"
				>
					<span
						class="h-5 w-5 bg-[#173653]"
						style="-webkit-mask: url('/assets/icons/logout.svg') center / contain no-repeat; mask: url('/assets/icons/logout.svg') center / contain no-repeat;"
						aria-hidden="true"
					></span>
					<span>Logout</span>
				</Link>
			</div>
		</div>
	</header>
</template>
