<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
	show: {
		type: Boolean,
		default: false,
	},
	title: {
		type: String,
		default: 'Hapus Data?',
	},
	itemName: {
		type: String,
		default: '',
	},
	message: {
		type: String,
		default: '',
	},
	loading: {
		type: Boolean,
		default: false,
	},
	confirmButtonText: {
		type: String,
		default: 'Hapus',
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
	if (isBackdropClick.value && e.target === e.currentTarget && !props.loading) {
		handleClose();
	}
	isBackdropClick.value = false;
};
</script>

<template>
	<Teleport to="body">
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
				class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/40 backdrop-blur-sm p-4"
				@mousedown="handleBackdropMouseDown"
				@mouseup="handleBackdropMouseUp"
			>
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
						class="relative w-full max-w-[440px] transform rounded-[18px] bg-white p-6 sm:p-7 shadow-2xl text-center font-poppins border border-[#e2e8f0]"
					>
						<!-- Warning Icon Badge -->
						<div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600 ring-8 ring-red-50">
							<svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
								/>
							</svg>
						</div>

						<!-- Title -->
						<h3 class="text-[20px] font-bold leading-tight text-[#173a63]">
							{{ title }}
						</h3>

						<!-- Description / Message -->
						<p class="mt-2.5 font-inter text-[14px] leading-relaxed text-[#5c738e]">
							<template v-if="itemName">
								Apakah Anda yakin ingin menghapus <span class="font-semibold text-[#173a63]">"{{ itemName }}"</span>? Tindakan ini tidak dapat dibatalkan.
							</template>
							<template v-else-if="message">
								{{ message }}
							</template>
							<template v-else>
								Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
							</template>
						</p>

						<!-- Buttons Action -->
						<div class="mt-6 flex items-center justify-center gap-3">
							<!-- Batal Button -->
							<button
								type="button"
								@click="handleClose"
								:disabled="loading"
								class="flex-1 rounded-[10px] border-2 border-[#d6e0ee] bg-transparent py-2.5 px-4 font-poppins text-[14px] font-semibold text-[#435b76] transition-colors hover:border-[#8ea9cb] hover:bg-[#f8fafc] focus:outline-none disabled:cursor-not-allowed disabled:opacity-60"
							>
								{{ cancelButtonText }}
							</button>

							<!-- Hapus Button -->
							<button
								type="button"
								@click="handleConfirm"
								:disabled="loading"
								class="flex-1 rounded-[10px] bg-[#dc2626] py-2.5 px-4 font-poppins text-[14px] font-semibold text-white shadow-sm shadow-red-500/25 transition-all hover:bg-[#b91c1c] active:scale-[0.98] focus:outline-none disabled:cursor-not-allowed disabled:opacity-70 flex items-center justify-center gap-2"
							>
								<!-- Loading Spinner -->
								<svg
									v-if="loading"
									class="h-4 w-4 animate-spin text-white"
									fill="none"
									viewBox="0 0 24 24"
								>
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
									<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
								</svg>
								<span>{{ confirmButtonText }}</span>
							</button>
						</div>
					</div>
				</Transition>
			</div>
		</Transition>
	</Teleport>
</template>
