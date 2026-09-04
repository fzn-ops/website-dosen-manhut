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
				class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4"
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
						class="relative w-full max-w-[92vw] sm:max-w-[440px] transform rounded-[18px] bg-white p-5 sm:p-7 shadow-2xl text-center font-poppins border border-[#e2e8f0]"
					>
						<!-- Warning Icon Badge (Single clean circle) -->
						<div class="mx-auto mb-3.5 flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-[#c93b2b]">
							<svg class="h-6 w-6 text-[#c93b2b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
								/>
							</svg>
						</div>

						<!-- Title -->
						<h3 class="text-[19px] font-bold leading-snug text-[#183669]">
							{{ title }}
						</h3>

						<!-- Primary Instruction Message -->
						<p class="mt-1.5 font-inter text-[13px] text-[#7188a3]">
							{{ message || 'Apakah Anda yakin ingin menghapus data ini? ' }}
						</p>

						<!-- Item Highlight Card (if itemName is provided) -->
						<div v-if="itemName" class="mt-3.5 rounded-[10px] border border-[#e2e8f0] bg-[#f8fafc] px-3.5 py-2.5 text-center">
							<p class="font-poppins text-[13px] font-semibold text-[#183669] line-clamp-2 break-words">
								"{{ itemName }}"
							</p>
						</div>

						<!-- Warning Subtext -->
						<!-- <p class="mt-3 flex items-center justify-center gap-1.5 font-inter text-[12px] leading-none text-[#7188a3]">
							<span class="leading-none">Data yang dihapus tidak dapat dikembalikan.</span>
						</p> -->

						<!-- Buttons Action -->
						<div class="mt-6 flex items-center justify-center gap-3">
							<!-- Batal Button -->
							<button
								type="button"
								@click="handleClose"
								:disabled="loading"
								class="flex-1 rounded-[10px] border border-[#d6e0ee] bg-white py-2.5 px-4 font-poppins text-[13.5px] font-semibold text-[#435b76] shadow-xs transition hover:bg-[#f0f4f9] hover:text-[#183669] focus:outline-none focus:ring-0 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60"
							>
								{{ cancelButtonText }}
							</button>

							<!-- Hapus Button -->
							<button
								type="button"
								@click="handleConfirm"
								:disabled="loading"
								class="flex-1 rounded-[10px] bg-[#c93b2b] py-2.5 px-4 font-poppins text-[13.5px] font-semibold text-white shadow-sm transition-all hover:bg-[#a82d1f] active:scale-95 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-70 flex items-center justify-center gap-2"
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
