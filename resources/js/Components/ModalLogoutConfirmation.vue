<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
	show: {
		type: Boolean,
		default: false,
	},
	title: {
		type: String,
		default: 'Keluar dari Akun?',
	},
	message: {
		type: String,
		default: 'Apakah Anda yakin ingin keluar? Anda harus masuk kembali untuk mengakses halaman ini.',
	},
	loading: {
		type: Boolean,
		default: false,
	},
	confirmButtonText: {
		type: String,
		default: 'Ya, Keluar',
	},
	cancelButtonText: {
		type: String,
		default: 'Batal',
	},
});

const emit = defineEmits(['close', 'confirm']);

const handleClose = () => {
	if (!props.loading) {
		emit('close');
	}
};

const handleConfirm = () => {
	emit('confirm');
};

const handleKeyDown = (e) => {
	if (e.key === 'Escape' && props.show && !props.loading) {
		e.stopPropagation();
		handleClose();
	}
};

onMounted(() => {
	document.addEventListener('keydown', handleKeyDown);
});

onBeforeUnmount(() => {
	document.removeEventListener('keydown', handleKeyDown);
});

const isBackdropClick = ref(false);

const handleBackdropMouseDown = (e) => {
	isBackdropClick.value = e.target === e.currentTarget;
};

const handleBackdropMouseUp = (e) => {
	if (isBackdropClick.value && e.target === e.currentTarget) {
		handleClose();
	}
	isBackdropClick.value = false;
};
</script>

<template>
	<Teleport to="body">
		<!-- Backdrop Fade -->
		<Transition
			enter-active-class="ease-out duration-200"
			enter-from-class="opacity-0"
			enter-to-class="opacity-100"
			leave-active-class="ease-in duration-150"
			leave-from-class="opacity-100"
			leave-to-class="opacity-0"
		>
			<div
				v-if="show"
				class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4"
				@mousedown="handleBackdropMouseDown"
				@mouseup="handleBackdropMouseUp"
			>
				<!-- Dialog Card Scale & Translate Float -->
				<Transition
					enter-active-class="ease-out duration-200"
					enter-from-class="opacity-0 scale-95 translate-y-2"
					enter-to-class="opacity-100 scale-100 translate-y-0"
					leave-active-class="ease-in duration-150"
					leave-from-class="opacity-100 scale-100 translate-y-0"
					leave-to-class="opacity-0 scale-95 translate-y-2"
				>
					<div
						v-if="show"
						class="w-full max-w-[420px] transform rounded-[18px] bg-white p-6 shadow-2xl font-poppins border border-[#e2e8f0] text-center sm:p-7"
						@click.stop
					>
						<!-- Warning / Logout Icon in soft badge -->
						<div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50">
							<svg class="h-7 w-7 text-[#c93b2b]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
								<polyline points="16 17 21 12 16 7" />
								<line x1="21" y1="12" x2="9" y2="12" />
							</svg>
						</div>

						<!-- Title -->
						<h3 class="mt-4 text-[18px] font-bold text-[#183669]">
							{{ title }}
						</h3>

						<!-- Message -->
						<p class="mt-2 font-inter text-[13px] leading-relaxed text-[#5c728e]">
							{{ message }}
						</p>

						<!-- Actions Buttons -->
						<div class="mt-6 flex items-center justify-center gap-3">
							<!-- Cancel Button -->
							<button
								type="button"
								:disabled="loading"
								@click="handleClose"
								class="flex-1 rounded-[10px] border border-[#d6e0ee] bg-white px-4 py-2.5 font-poppins text-[13.5px] font-semibold text-[#435b76] shadow-xs transition hover:bg-[#f0f4f9] hover:text-[#183669] focus:outline-none focus:ring-0 active:scale-95 disabled:opacity-60"
							>
								{{ cancelButtonText }}
							</button>

							<!-- Confirm Logout Button -->
							<button
								type="button"
								:disabled="loading"
								@click="handleConfirm"
								class="flex-1 inline-flex items-center justify-center gap-2 rounded-[10px] bg-[#c93b2b] px-4 py-2.5 font-poppins text-[13.5px] font-semibold text-white shadow-sm transition hover:bg-[#a82d1f] focus:outline-none focus:ring-0 active:scale-95 disabled:opacity-60"
							>
								<svg v-if="loading" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
									<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
								</svg>
								<span>{{ confirmButtonText }}</span>
								<!-- <svg v-else class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
									<polyline points="16 17 21 12 16 7" />
									<line x1="21" y1="12" x2="9" y2="12" />
								</svg> -->
							</button>
						</div>
					</div>
				</Transition>
			</div>
		</Transition>
	</Teleport>
</template>
