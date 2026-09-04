<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
	show: {
		type: Boolean,
		default: false,
	},
	lecturers: {
		type: Array,
		default: () => [],
	},
	publications: {
		type: Array,
		default: () => [],
	},
});

const getPublicationCount = (lecturerId) => {
	if (!props.publications || props.publications.length === 0) return 0;
	return props.publications.filter((p) => p.user_id === lecturerId).length;
};

const emit = defineEmits(['close', 'confirm']);

const searchQuery = ref('');
const selectedIds = ref([]);

// Hanya dosen yang memiliki link scholar yang dapat disinkronkan
const eligibleLecturers = computed(() => {
	return props.lecturers.filter((l) => l.has_scholar);
});

// List dosen terfilter pencarian
const filteredLecturers = computed(() => {
	const q = searchQuery.value.toLowerCase().trim();
	if (!q) return props.lecturers;
	return props.lecturers.filter(
		(l) =>
			(l.name && l.name.toLowerCase().includes(q)) ||
			(l.email && l.email.toLowerCase().includes(q))
	);
});

// Status Select All
const isAllSelected = computed(() => {
	if (eligibleLecturers.value.length === 0) return false;
	return eligibleLecturers.value.every((l) => selectedIds.value.includes(l.id));
});

const isSomeSelected = computed(() => {
	return selectedIds.value.length > 0 && !isAllSelected.value;
});

// Toggle Select All
const toggleSelectAll = () => {
	if (isAllSelected.value) {
		selectedIds.value = [];
	} else {
		selectedIds.value = eligibleLecturers.value.map((l) => l.id);
	}
};

// Toggle individual lecturer
const toggleLecturer = (lecturer) => {
	if (!lecturer.has_scholar) return;
	const idx = selectedIds.value.indexOf(lecturer.id);
	if (idx > -1) {
		selectedIds.value.splice(idx, 1);
	} else {
		selectedIds.value.push(lecturer.id);
	}
};

// Reset state saat modal dibuka
watch(
	() => props.show,
	(isOpen) => {
		if (isOpen) {
			searchQuery.value = '';
			// Default: kosong (tidak auto select all)
			selectedIds.value = [];
		}
	}
);

const handleClose = () => {
	emit('close');
};

const handleConfirm = () => {
	if (selectedIds.value.length === 0) return;
	emit('confirm', [...selectedIds.value]);
};

// ESC to close
const handleKeyDown = (e) => {
	if (e.key === 'Escape' && props.show) {
		handleClose();
	}
};

onMounted(() => {
	document.addEventListener('keydown', handleKeyDown);
});

onBeforeUnmount(() => {
	document.removeEventListener('keydown', handleKeyDown);
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
				class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 font-poppins"
				@click.self="handleClose"
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
						class="relative w-full max-w-[95vw] sm:max-w-[560px] max-h-[92vh] flex flex-col transform rounded-[20px] bg-white p-4 sm:p-7 shadow-2xl border border-[#e2e8f0]"
					>
						<!-- Modal Header -->
						<div class="flex items-start justify-between border-b border-[#f0f4f9] pb-3 sm:pb-4">
							<div class="flex items-center gap-2.5 sm:gap-3">
								<div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-full bg-[#183669] shadow-sm">
									<img src="/assets/icons/book.svg" alt="Publikasi" class="h-5 w-5 object-contain" />
								</div>
								<div>
									<h3 class="text-[16px] sm:text-[18px] font-bold text-[#183669]">
										Pilih Dosen untuk Sinkronisasi
									</h3>
									<p class="font-inter text-[11px] sm:text-xs text-[#5f7895] mt-0.5">
										Pilih satu atau beberapa dosen untuk menarik artikel Google Scholar
									</p>
								</div>
							</div>
							<button
								type="button"
								@click="handleClose"
								class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
								title="Tutup"
							>
								<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
						</div>

						<!-- Search & Select All Bar -->
						<div class="mt-3 sm:mt-4 space-y-2.5 sm:space-y-3">
							<!-- Search Input with SearchBarTable Hover Style -->
							<div class="group relative">
								<div
									class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#8ca1b9] transition-colors duration-200 group-hover:text-[#183669] group-focus-within:text-[#183669]"
								>
									<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
									</svg>
								</div>
								<input
									v-model="searchQuery"
									type="text"
									placeholder="Cari nama atau email dosen..."
									class="h-[40px] sm:h-[42px] w-full rounded-[10px] border-2 border-[#d6e0ee] bg-transparent pl-10 pr-9 font-inter text-xs sm:text-sm text-[#173a63] placeholder-[#8ca1b9] transition-colors duration-200 hover:border-[#8ea9cb] focus:border-[#183669] focus:outline-none focus:ring-0"
								/>
								<button
									v-if="searchQuery"
									type="button"
									@click="searchQuery = ''"
									title="Hapus pencarian"
									class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#8ca1b9] hover:text-[#183669]"
								>
									<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</button>
							</div>

							<!-- Select All Row -->
							<div class="flex items-center justify-between px-1 text-xs font-inter">
								<label class="flex items-center gap-2 cursor-pointer select-none">
									<input
										type="checkbox"
										:checked="isAllSelected"
										:indeterminate="isSomeSelected"
										@change="toggleSelectAll"
										class="h-4 w-4 rounded border-[#c3d1e4] text-[#183669] focus:ring-0 cursor-pointer"
									/>
									<span class="font-semibold text-[#183669]">
										Pilih Semua Dosen
									</span>
								</label>

								<span class="text-[#627d98] font-medium text-[11px] sm:text-xs">
									Terpilih: <strong class="text-[#183669] font-bold">{{ selectedIds.length }}</strong> dari {{ eligibleLecturers.length }} dosen
								</span>
							</div>
						</div>

						<!-- Lecturers List -->
						<div class="mt-3 max-h-[230px] sm:max-h-[290px] flex-1 overflow-y-auto space-y-1.5 pr-1 font-inter">
							<div
								v-for="lec in filteredLecturers"
								:key="lec.id"
								@click="toggleLecturer(lec)"
								:class="[
									'flex items-center justify-between rounded-[12px] p-2.5 sm:p-3 border transition-all select-none',
									lec.has_scholar ? 'cursor-pointer' : 'opacity-60 bg-slate-50 cursor-not-allowed',
									selectedIds.includes(lec.id)
										? 'border-[#183669] bg-[#183669]/5 shadow-sm'
										: 'border-[#e8eef7] hover:border-[#cbd9ea] bg-white'
								]"
							>
								<!-- Left: Checkbox & Name Info -->
								<div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
									<input
										type="checkbox"
										:checked="selectedIds.includes(lec.id)"
										:disabled="!lec.has_scholar"
										@click.stop="toggleLecturer(lec)"
										class="h-4 w-4 shrink-0 rounded border-[#c3d1e4] text-[#183669] focus:ring-0 cursor-pointer disabled:cursor-not-allowed"
									/>
									<div class="min-w-0">
										<p class="font-poppins text-xs sm:text-[13px] font-semibold text-[#1e3456] truncate max-w-[170px] sm:max-w-none">
											{{ lec.name }}
										</p>
										<div class="flex items-center gap-1.5 sm:gap-2 mt-0.5">
											<span class="text-[10px] sm:text-[11px] text-[#6b829e] truncate max-w-[120px] sm:max-w-[200px]">
												{{ lec.email }}
											</span>
											<!-- Status Badge -->
											<span
												v-if="lec.has_scholar"
												class="inline-flex items-center rounded-full bg-emerald-50 px-1.5 py-0.2 text-[9px] sm:text-[10px] font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 shrink-0"
											>
												Scholar Aktif
											</span>
											<span
												v-else
												class="inline-flex items-center rounded-full bg-amber-50 px-1.5 py-0.2 text-[9px] sm:text-[10px] font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 shrink-0"
											>
												Belum Ada
											</span>
										</div>
									</div>
								</div>

								<!-- Right: Publication Count Badge -->
								<div class="shrink-0 text-right pl-2">
									<span class="inline-flex items-center justify-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] sm:text-[11px] font-medium text-[#435b76]">
										{{ getPublicationCount(lec.id) }} artikel
									</span>
								</div>
							</div>

							<div v-if="filteredLecturers.length === 0" class="py-6 text-center text-xs text-[#8ca1b9]">
								Tidak ada dosen yang cocok dengan pencarian.
							</div>
						</div>

						<!-- Modal Footer -->
						<div class="mt-4 sm:mt-5 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2.5 border-t border-[#f0f4f9] pt-3 sm:pt-4">
							<span class="text-[11px] sm:text-xs text-[#627d98] text-center sm:text-left">
								{{ selectedIds.length }} dosen dipilih
							</span>
							<div class="flex items-center gap-2">
								<button
									type="button"
									@click="handleClose"
									class="flex-1 sm:flex-none rounded-[10px] px-4 py-2 font-poppins text-xs sm:text-sm font-semibold text-[#546e8d] hover:bg-slate-100 transition text-center"
								>
									Batal
								</button>
								<button
									type="button"
									@click="handleConfirm"
									:disabled="selectedIds.length === 0"
									class="flex-1 sm:flex-none flex items-center justify-center gap-2 rounded-[10px] bg-[#183669] px-4 sm:px-5 py-2 font-poppins text-xs sm:text-sm font-semibold text-white shadow-sm transition hover:bg-[#122b54] active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
								>
									<span>Mulai Sinkronisasi ({{ selectedIds.length }})</span>
								</button>
							</div>
						</div>
					</div>
				</Transition>
			</div>
		</Transition>
	</Teleport>
</template>
