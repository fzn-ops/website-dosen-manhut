<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

defineEmits(['toggle', 'logout']);

const page = usePage();
const showingAccountMenu = ref(false);
const imageLoadError = ref(false);

const userName = computed(() => page.props.auth?.user?.name || 'Dosen');
const userProfilePicture = computed(() => {
	if (imageLoadError.value) return null;
	return page.props.auth?.user?.profile_picture || null;
});

const onImageError = () => {
	imageLoadError.value = true;
};

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
				class="flex items-center gap-2 sm:gap-2.5 rounded-full md:rounded-lg py-1 px-1.5 md:px-2.5 md:py-1.5 text-[#173653] transition-colors hover:bg-[#e2e8f0] focus:outline-none"
				aria-label="Buka menu akun"
				:aria-expanded="showingAccountMenu"
				@click="showingAccountMenu = !showingAccountMenu"
			>
				<!-- Profile Image (Circular, object-cover object-top to focus on face) -->
				<div class="relative flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full border border-[#d6e0ee] bg-[#eef3f8] shadow-2xs">
					<img
						v-if="userProfilePicture"
						:src="userProfilePicture"
						:alt="userName"
						class="h-full w-full object-cover object-top"
						@error="onImageError"
					/>
					<img
						v-else
						src="/assets/icons/default-profile.svg"
						alt="Profile icon"
						class="h-full w-full rounded-full object-contain p-0.5"
					/>
				</div>

				<!-- Name (Hidden on Mobile/Responsive view, visible on desktop) -->
				<span class="hidden md:inline-block font-poppins text-sm font-semibold text-[#173653] max-w-[200px] lg:max-w-[320px] truncate" :title="userName">
					{{ userName }}
				</span>

				<!-- Dropdown Chevron Arrow (Always visible on all screen sizes) -->
				<svg
					:class="['h-4 w-4 text-[#8ca1b9] transition-transform duration-200 shrink-0', showingAccountMenu ? 'rotate-180 text-[#173653]' : '']"
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
				class="absolute right-0 top-[52px] z-40 min-w-[200px] max-w-[calc(100vw-2rem)] w-max rounded-xl border border-[#d6e0ee] bg-white p-1.5 shadow-xl font-poppins space-y-0.5"
			>
				<!-- User Info Header (Mobile Only) -->
				<div class="px-3 py-2 border-b border-[#eef2f8] md:hidden">
					<p class="font-poppins text-xs font-semibold text-[#173653] whitespace-nowrap truncate" :title="userName">
						{{ userName }}
					</p>
					<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">
						Dosen
					</p>
				</div>

				<!-- Profile Option -->
				<Link
					href="/dosen/profile"
					class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium text-[#173653] transition-colors hover:bg-[#e8eef8]"
					@click="showingAccountMenu = false"
				>
					<svg class="h-4 w-4 shrink-0 text-[#173653]" viewBox="0 0 17 20" fill="currentColor">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M15.105 0H0.355V1.5H3.855V3.5H12.855V1.5H14.855V7H16.355V1.25C16.355 0.56 15.795 0 15.105 0ZM3.855 5V6.5C3.855 8.98 5.875 11 8.355 11C10.835 11 12.855 8.98 12.855 6.5V5H3.855ZM15.435 15.86L16.7 20H0L1.265 15.86C1.55923 14.8849 2.16068 14.031 2.9797 13.4255C3.79873 12.82 4.79148 12.4954 5.81 12.5H10.89C12.995 12.5 14.82 13.85 15.435 15.86Z" />
					</svg>
					<span>Profile</span>
				</Link>

				<div class="h-px bg-[#eef2f8] my-1"></div>

				<!-- Logout Option -->
				<button
					type="button"
					class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium text-[#c93b2b] transition-colors hover:bg-[#feeceb] focus:outline-none"
					@click="showingAccountMenu = false; $emit('logout')"
				>
					<svg class="h-4 w-4 shrink-0 text-[#c93b2b]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
						<polyline points="16 17 21 12 16 7" />
						<line x1="21" y1="12" x2="9" y2="12" />
					</svg>
					<span>Logout</span>
				</button>
			</div>
		</div>
	</header>
</template>
