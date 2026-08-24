<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

defineEmits(['toggle']);

const showingAccountMenu = ref(false);

const closeAccountMenu = () => {
	showingAccountMenu.value = false;
};

onMounted(() => document.addEventListener('click', closeAccountMenu));
onBeforeUnmount(() => document.removeEventListener('click', closeAccountMenu));
</script>

<template>
	<header class="sticky top-0 z-20 flex h-[70px] items-center justify-between border-b border-[#e2e8f0] bg-[#f8fafc] px-4 sm:px-8">
		<!-- Hamburger Toggle Button -->
		<button
			type="button"
			aria-label="Buka menu navigasi"
			class="flex h-9 w-9 items-center justify-center rounded-lg text-[#173653] transition-colors hover:bg-[#e2e8f0] focus:outline-none"
			@click="$emit('toggle')"
		>
			<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
				<path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
			</svg>
		</button>

		<!-- Dosen Profile Button with Dropdown (Profile & Logout) -->
		<div class="relative" @click.stop>
			<button
				type="button"
				class="flex items-center gap-2.5 rounded-lg px-2.5 py-1.5 text-[#173653] transition-colors hover:bg-[#e2e8f0] focus:outline-none"
				aria-label="Buka menu akun"
				:aria-expanded="showingAccountMenu"
				@click="showingAccountMenu = !showingAccountMenu"
			>
				<img src="/assets/icons/default-profile.svg" alt="Profile icon" class="h-7 w-7 rounded-full object-contain" />
				<span class="font-poppins text-sm font-semibold text-[#173653]">
					{{ $page.props.auth?.user?.name || 'Dr. John Doe, M.Si' }}
				</span>
				<svg
					:class="['h-4 w-4 text-[#8ca1b9] transition-transform duration-200', showingAccountMenu ? 'rotate-180 text-[#173653]' : '']"
					fill="none"
					stroke="currentColor"
					stroke-width="2"
					viewBox="0 0 24 24"
				>
					<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
				</svg>
			</button>

			<!-- Dropdown Menu (2 Options: Profile & Logout) -->
			<div
				v-if="showingAccountMenu"
				class="absolute right-0 top-[48px] z-40 w-48 rounded-xl border border-[#d6e0ee] bg-white p-1.5 shadow-xl font-poppins space-y-1"
			>
				<!-- Profile Option -->
				<Link
					href="/dosen/profile"
					class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium text-[#173653] transition-colors hover:bg-[#e8eef8]"
					@click="showingAccountMenu = false"
				>
					<img src="/assets/icons/teacher-profile.svg" alt="Profile icon" class="h-4 w-4 shrink-0 object-contain text-[#173653]" />
					<span>Profile</span>
				</Link>

				<div class="h-px bg-[#eef2f8] my-1"></div>

				<!-- Logout Option -->
				<Link
					:href="route('logout')"
					method="post"
					as="button"
					class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium text-[#c93b2b] transition-colors hover:bg-[#feeceb]"
					@click="showingAccountMenu = false"
				>
					<img src="/assets/icons/logout.svg" alt="Logout icon" class="h-4 w-4 shrink-0 object-contain" />
					<span>Logout</span>
				</Link>
			</div>
		</div>
	</header>
</template>
