<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
	show: {
		type: Boolean,
		default: false,
	},
	finished: {
		type: Boolean,
		default: false,
	},
});

const ESTIMATED_TOTAL_SECONDS = 20;

const elapsedSeconds = ref(0);
const progress = ref(0);
let timerInterval = null;

const formattedTime = computed(() => {
	const totalSecs = Math.floor(elapsedSeconds.value);
	const mins = Math.floor(totalSecs / 60)
		.toString()
		.padStart(2, '0');
	const secs = (totalSecs % 60).toString().padStart(2, '0');
	return `${mins}:${secs}`;
});

const currentStatusText = computed(() => {
	if (progress.value >= 100) return 'Sinkronisasi berhasil!';
	if (progress.value < 20) return 'Menghubungkan ke Google Scholar...';
	if (progress.value < 55) return 'Mengunduh artikel & sitasi dosen...';
	if (progress.value < 80) return 'Mengekstrak data publikasi...';
	return 'Menyimpan pembaruan ke database...';
});

const estimatedTimeText = computed(() => {
	if (progress.value >= 100) return 'Selesai';
	const remaining = Math.max(0, Math.ceil(ESTIMATED_TOTAL_SECONDS - elapsedSeconds.value));
	if (remaining > 0) {
		return `~${remaining} detik lagi`;
	}
	return 'Hampir selesai...';
});

const startProgress = () => {
	stopProgress();
	elapsedSeconds.value = 0;
	progress.value = 0;

	const intervalMs = 200;
	timerInterval = setInterval(() => {
		elapsedSeconds.value += intervalMs / 1000;

		if (props.finished) {
			progress.value = 100;
			return;
		}

		const t = elapsedSeconds.value;
		if (t <= 5) {
			progress.value = Math.min(35, Math.round((t / 5) * 35));
		} else if (t <= 12) {
			progress.value = Math.min(70, Math.round(35 + ((t - 5) / 7) * 35));
		} else if (t <= 18) {
			progress.value = Math.min(88, Math.round(70 + ((t - 12) / 6) * 18));
		} else {
			// Inching secara perlahan mendekati 95% sampai response API tiba
			progress.value = Math.min(95, Math.round(88 + ((t - 18) / 10) * 7));
		}
	}, intervalMs);
};

const stopProgress = () => {
	if (timerInterval) {
		clearInterval(timerInterval);
		timerInterval = null;
	}
};

watch(
	() => props.finished,
	(isDone) => {
		if (isDone) {
			progress.value = 100;
		}
	}
);

watch(
	() => props.show,
	(val) => {
		if (val) {
			document.body.style.overflow = 'hidden';
			startProgress();
		} else {
			document.body.style.overflow = '';
			stopProgress();
		}
	},
	{ immediate: true }
);

onBeforeUnmount(() => {
	document.body.style.overflow = '';
	stopProgress();
});
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
				class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/40 backdrop-blur-sm p-4 select-none"
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
						<!-- Circle Icon dengan Spinner Luar -->
						<div class="relative mx-auto mb-4 flex h-20 w-20 items-center justify-center">
							<!-- Ring Loading di Luar -->
							<div
								v-if="progress < 100"
								class="absolute inset-0 rounded-full border-[3px] border-[#e2e8f0] border-t-[#183669] animate-spin"
							></div>
							<div
								v-else
								class="absolute inset-0 rounded-full border-[3px] border-emerald-500 bg-emerald-50/50"
							></div>

							<!-- Badge Lingkaran Dalam -->
							<div
								class="flex h-14 w-14 items-center justify-center rounded-full shadow-sm transition-colors duration-300"
								:class="progress === 100 ? 'bg-emerald-600' : 'bg-[#183669]'"
							>
								<!-- Centang hijau saat 100% -->
								<svg
									v-if="progress === 100"
									class="h-6 w-6 text-white"
									fill="none"
									stroke="currentColor"
									stroke-width="2.5"
									viewBox="0 0 24 24"
								>
									<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
								</svg>
								<!-- Logo book.svg saat proses -->
								<img
									v-else
									src="/assets/icons/book.svg"
									alt="Publikasi"
									class="h-6 w-6 object-contain"
								/>
							</div>
						</div>

						<!-- Title -->
						<h3 class="text-[19px] font-bold leading-snug text-[#183669]">
							{{ progress === 100 ? 'Sinkronisasi Selesai!' : 'Sinkronisasi Publikasi' }}
						</h3>

						<!-- Subtitle -->
						<p class="mt-1 text-sm font-normal text-[#435b76]">
							{{ progress === 100 ? 'Semua data publikasi berhasil diperbarui.' : 'Sedang menarik data dari Google Scholar...' }}
						</p>

						<!-- Progress Section -->
						<div class="mt-5 mb-4">
							<!-- Status & Percentage Bar Header -->
							<div class="flex items-center justify-between text-xs font-inter mb-2">
								<span class="font-medium text-[#435b76] truncate max-w-[270px] text-left">
									{{ currentStatusText }}
								</span>
								<span class="font-bold font-mono text-[14px] text-[#183669]">
									{{ progress }}%
								</span>
							</div>

							<!-- Progress Bar Fill -->
							<div class="w-full overflow-hidden rounded-full bg-[#e8eef7] h-2.5">
								<div
									class="h-full rounded-full transition-all duration-200 ease-out"
									:class="progress === 100 ? 'bg-emerald-500' : 'bg-[#183669]'"
									:style="{ width: `${progress}%` }"
								></div>
							</div>

							<!-- Time & Estimation Info Row -->
							<div class="mt-2.5 flex items-center justify-between text-xs text-[#627d98] font-inter">
								<span>
									Waktu berjalan: <strong class="font-mono text-[#183669] font-bold">{{ formattedTime }}</strong>
								</span>
								<span>
									Estimasi: <strong class="font-medium text-[#183669]">{{ estimatedTimeText }}</strong>
								</span>
							</div>
						</div>

						<!-- Footer Notice -->
						<p class="text-xs text-[#7890a8] font-inter">
							Mohon jangan menutup atau merefresh halaman selama proses berlangsung.
						</p>
					</div>
				</Transition>
			</div>
		</Transition>
	</Teleport>
</template>
