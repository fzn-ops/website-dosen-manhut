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
	loading: {
		type: Boolean,
		default: false,
	},
});

const emit = defineEmits(['close', 'confirm']);

// Tab active: 'lecturers', 'years', 'all'
const activeTab = ref('lecturers');

// State Checklist (Default SEMUANYA KOSONG)
const selectedLecturerIds = ref([]);
const selectedYears = ref([]);
const searchQuery = ref('');

// Helper jumlah artikel per dosen
const getPublicationCountByLecturer = (lecturerId) => {
	const pubList = Array.isArray(props.publications) ? props.publications : [];
	return pubList.filter((p) => p && p.user_id === lecturerId).length;
};

// Dosen yang memiliki publikasi
const lecturersWithPubs = computed(() => {
	const lecs = Array.isArray(props.lecturers) ? props.lecturers : [];
	return lecs.filter((l) => l && getPublicationCountByLecturer(l.id) > 0);
});

// Filter pencarian dosen
const filteredLecturers = computed(() => {
	const lecs = Array.isArray(props.lecturers) ? props.lecturers : [];
	const q = searchQuery.value ? searchQuery.value.toLowerCase().trim() : '';
	if (!q) return lecs;
	return lecs.filter(
		(l) =>
			(l?.name && l.name.toLowerCase().includes(q)) ||
			(l?.email && l.email.toLowerCase().includes(q))
	);
});

// Data Tahun Unik berserta jumlah artikel
const yearSummaryList = computed(() => {
	const pubList = Array.isArray(props.publications) ? props.publications : [];
	if (pubList.length === 0) return [];
	const map = {};
	pubList.forEach((p) => {
		if (p) {
			const y = p.year ? String(p.year) : 'Tanpa Tahun';
			map[y] = (map[y] || 0) + 1;
		}
	});

	return Object.keys(map)
		.map((year) => ({
			year,
			count: map[year],
		}))
		.sort((a, b) => {
			if (a.year === 'Tanpa Tahun') return 1;
			if (b.year === 'Tanpa Tahun') return -1;
			return Number(b.year) - Number(a.year);
		});
});

// Toggle Select All Dosen
const isAllLecturersSelected = computed(() => {
	const lecs = lecturersWithPubs.value || [];
	if (lecs.length === 0) return false;
	return lecs.every((l) => selectedLecturerIds.value.includes(l.id));
});

const toggleSelectAllLecturers = () => {
	if (isAllLecturersSelected.value) {
		selectedLecturerIds.value = [];
	} else {
		selectedLecturerIds.value = (lecturersWithPubs.value || []).map((l) => l.id);
	}
};

const toggleLecturer = (lecturerId) => {
	const idx = selectedLecturerIds.value.indexOf(lecturerId);
	if (idx > -1) selectedLecturerIds.value.splice(idx, 1);
	else selectedLecturerIds.value.push(lecturerId);
};

// Toggle Select All Tahun
const isAllYearsSelected = computed(() => {
	const years = yearSummaryList.value || [];
	if (years.length === 0) return false;
	return years.every((y) => selectedYears.value.includes(y.year));
});

const toggleSelectAllYears = () => {
	if (isAllYearsSelected.value) {
		selectedYears.value = [];
	} else {
		selectedYears.value = (yearSummaryList.value || []).map((y) => y.year);
	}
};

const toggleYear = (year) => {
	const idx = selectedYears.value.indexOf(year);
	if (idx > -1) selectedYears.value.splice(idx, 1);
	else selectedYears.value.push(year);
};

// Hitung total artikel yang akan dihapus pada tab dosen
const totalArticlesToDeleteByLecturers = computed(() => {
	const pubList = Array.isArray(props.publications) ? props.publications : [];
	return pubList.filter((p) => p && selectedLecturerIds.value.includes(p.user_id)).length;
});

// Hitung total artikel yang akan dihapus pada tab tahun
const totalArticlesToDeleteByYears = computed(() => {
	const pubList = Array.isArray(props.publications) ? props.publications : [];
	return pubList.filter((p) => {
		if (!p) return false;
		const y = p.year ? String(p.year) : 'Tanpa Tahun';
		return selectedYears.value.includes(y);
	}).length;
});

// Reset saat modal buka
watch(
	() => props.show,
	(isOpen) => {
		if (isOpen) {
			activeTab.value = 'lecturers';
			selectedLecturerIds.value = [];
			selectedYears.value = [];
			searchQuery.value = '';
		}
	}
);

const handleClose = () => {
	if (!props.loading) {
		emit('close');
	}
};

const handleConfirm = () => {
	if (props.loading) return;

	if (activeTab.value === 'lecturers') {
		if (selectedLecturerIds.value.length === 0) return;
		emit('confirm', {
			type: 'lecturers',
			dosen_ids: [...selectedLecturerIds.value],
			years: [],
		});
	} else if (activeTab.value === 'years') {
		if (selectedYears.value.length === 0) return;
		emit('confirm', {
			type: 'years',
			dosen_ids: [],
			years: [...selectedYears.value],
		});
	} else {
		// Hapus Semua
		emit('confirm', {
			type: 'all',
			dosen_ids: [],
			years: [],
		});
	}
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
								<div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600 shadow-sm">
									<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
									</svg>
								</div>
								<div>
									<h3 class="text-[16px] sm:text-[18px] font-bold text-[#183669]">
										Hapus Data Publikasi
									</h3>
									<p class="font-inter text-[11px] sm:text-xs text-[#5f7895] mt-0.5">
										Pilih opsi penghapusan data publikasi sesuai kebutuhan
									</p>
								</div>
							</div>
							<button
								type="button"
								@click="handleClose"
								:disabled="loading"
								class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition disabled:opacity-50"
								title="Tutup"
							>
								<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
						</div>

						<!-- Tab Navigation Pill -->
						<div class="mt-3 sm:mt-4 flex rounded-[12px] bg-slate-100 p-1 font-inter text-xs font-semibold">
							<button
								type="button"
								@click="activeTab = 'lecturers'"
								:class="[
									'flex-1 rounded-[10px] py-2 transition-all text-center',
									activeTab === 'lecturers' ? 'bg-white text-[#183669] shadow-sm' : 'text-[#627d98] hover:text-[#183669]'
								]"
							>
								Per Dosen
							</button>
							<button
								type="button"
								@click="activeTab = 'years'"
								:class="[
									'flex-1 rounded-[10px] py-2 transition-all text-center',
									activeTab === 'years' ? 'bg-white text-[#183669] shadow-sm' : 'text-[#627d98] hover:text-[#183669]'
								]"
							>
								Per Tahun
							</button>
							<button
								type="button"
								@click="activeTab = 'all'"
								:class="[
									'flex-1 rounded-[10px] py-2 transition-all text-center',
									activeTab === 'all' ? 'bg-red-600 text-white shadow-sm' : 'text-[#627d98] hover:text-red-600'
								]"
							>
								Hapus Semua
							</button>
						</div>

						<!-- TAB 1: BERDASARKAN DOSEN -->
						<div v-if="activeTab === 'lecturers'" class="mt-3 sm:mt-4 space-y-2.5 sm:space-y-3 flex-1 overflow-y-auto">
							<!-- Search Input with SearchBarTable Hover Style -->
							<div class="group relative">
								<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#8ca1b9] transition-colors duration-200 group-hover:text-[#183669] group-focus-within:text-[#183669]">
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
										:checked="isAllLecturersSelected"
										@change="toggleSelectAllLecturers"
										class="h-4 w-4 rounded border-[#c3d1e4] text-[#183669] focus:ring-0 cursor-pointer"
									/>
									<span class="font-semibold text-[#183669]">
										Pilih Semua Dosen
									</span>
								</label>

								<span class="text-[#627d98] font-medium text-[11px] sm:text-xs">
									Terpilih: <strong class="text-red-600 font-bold">{{ selectedLecturerIds.length }}</strong> dosen ({{ totalArticlesToDeleteByLecturers }} artikel)
								</span>
							</div>

							<!-- Lecturers List -->
							<div class="max-h-[220px] sm:max-h-[260px] overflow-y-auto space-y-1.5 pr-1 font-inter">
								<div
									v-for="lec in filteredLecturers"
									:key="`delete-lec-${lec.id}`"
									@click="toggleLecturer(lec.id)"
									:class="[
										'flex items-center justify-between rounded-[12px] p-2.5 sm:p-3 border transition-all cursor-pointer select-none',
										selectedLecturerIds.includes(lec.id)
											? 'border-red-400 bg-red-50/50 shadow-sm'
											: 'border-[#e8eef7] hover:border-[#cbd9ea] bg-white'
									]"
								>
									<div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
										<input
											type="checkbox"
											:checked="selectedLecturerIds.includes(lec.id)"
											@click.stop="toggleLecturer(lec.id)"
											class="h-4 w-4 shrink-0 rounded border-[#c3d1e4] text-red-600 focus:ring-0 cursor-pointer"
										/>
										<div class="min-w-0">
											<p class="font-poppins text-xs sm:text-[13px] font-semibold text-[#1e3456] truncate max-w-[170px] sm:max-w-none">
												{{ lec.name }}
											</p>
											<p class="text-[10px] sm:text-[11px] text-[#6b829e] truncate max-w-[170px] sm:max-w-none">
												{{ lec.email }}
											</p>
										</div>
									</div>

									<div class="shrink-0 text-right pl-2">
										<span
											class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] sm:text-[11px] font-medium"
											:class="getPublicationCountByLecturer(lec.id) > 0 ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-400'"
										>
											{{ getPublicationCountByLecturer(lec.id) }} artikel
										</span>
									</div>
								</div>

								<div v-if="filteredLecturers.length === 0" class="py-6 text-center text-xs text-[#8ca1b9]">
									Tidak ada dosen yang cocok.
								</div>
							</div>
						</div>

						<!-- TAB 2: BERDASARKAN TAHUN -->
						<div v-if="activeTab === 'years'" class="mt-3 sm:mt-4 space-y-2.5 sm:space-y-3 flex-1 overflow-y-auto">
							<!-- Select All Row -->
							<div class="flex items-center justify-between px-1 text-xs font-inter">
								<label class="flex items-center gap-2 cursor-pointer select-none">
									<input
										type="checkbox"
										:checked="isAllYearsSelected"
										@change="toggleSelectAllYears"
										class="h-4 w-4 rounded border-[#c3d1e4] text-[#183669] focus:ring-0 cursor-pointer"
									/>
									<span class="font-semibold text-[#183669]">
										Pilih Semua Tahun
									</span>
								</label>

								<span class="text-[#627d98] font-medium text-[11px] sm:text-xs">
									Terpilih: <strong class="text-red-600 font-bold">{{ selectedYears.length }}</strong> tahun ({{ totalArticlesToDeleteByYears }} artikel)
								</span>
							</div>

							<!-- Years Grid List -->
							<div class="max-h-[220px] sm:max-h-[280px] overflow-y-auto pr-1 grid grid-cols-1 sm:grid-cols-2 gap-2 font-inter">
								<div
									v-for="item in yearSummaryList"
									:key="`delete-year-${item.year}`"
									@click="toggleYear(item.year)"
									:class="[
										'flex items-center justify-between rounded-[12px] p-2.5 sm:p-3 border transition-all cursor-pointer select-none',
										selectedYears.includes(item.year)
											? 'border-red-400 bg-red-50/50 shadow-sm'
											: 'border-[#e8eef7] hover:border-[#cbd9ea] bg-white'
									]"
								>
									<div class="flex items-center gap-2 sm:gap-2.5">
										<input
											type="checkbox"
											:checked="selectedYears.includes(item.year)"
											@click.stop="toggleYear(item.year)"
											class="h-4 w-4 shrink-0 rounded border-[#c3d1e4] text-red-600 focus:ring-0 cursor-pointer"
										/>
										<span class="font-poppins text-xs sm:text-[13px] font-bold text-[#1e3456]">
											{{ item.year }}
										</span>
									</div>

									<span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] sm:text-[11px] font-medium text-[#435b76]">
										{{ item.count }} artikel
									</span>
								</div>

								<div v-if="yearSummaryList.length === 0" class="col-span-1 sm:col-span-2 py-6 text-center text-xs text-[#8ca1b9]">
									Tidak ada data tahun publikasi yang tersedia.
								</div>
							</div>
						</div>

						<!-- TAB 3: HAPUS SEMUA (TRUNCATE ALL) -->
						<div v-if="activeTab === 'all'" class="mt-3 sm:mt-4 space-y-3 font-inter flex-1 overflow-y-auto">
							<div class="rounded-[14px] bg-red-50 p-3.5 sm:p-4 border border-red-200/80 text-center">
								<div class="mx-auto mb-2 flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
									<svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
									</svg>
								</div>
								<h4 class="font-poppins text-sm sm:text-[15px] font-bold text-red-700">
									Peringatan Tindakan Permanen!
								</h4>
								<p class="mt-1 text-[11px] sm:text-xs text-red-600/90 leading-relaxed">
									Tindakan ini akan mengosongkan <strong>SELURUH data publikasi ({{ props.publications?.length || 0 }} artikel)</strong> dari database. Data yang sudah dihapus tidak dapat dipulihkan kembali kecuali disinkronkan ulang.
								</p>
							</div>
						</div>

						<!-- Modal Footer -->
						<div class="mt-4 sm:mt-5 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2.5 border-t border-[#f0f4f9] pt-3 sm:pt-4">
							<button
								type="button"
								@click="handleClose"
								:disabled="loading"
								class="rounded-[10px] px-4 py-2 font-poppins text-xs sm:text-sm font-semibold text-[#546e8d] hover:bg-slate-100 transition disabled:opacity-50 text-center"
							>
								Batal
							</button>

							<button
								type="button"
								@click="handleConfirm"
								:disabled="
									loading ||
									(activeTab === 'lecturers' && selectedLecturerIds.length === 0) ||
									(activeTab === 'years' && selectedYears.length === 0) ||
									(activeTab === 'all' && (!props.publications || props.publications.length === 0))
								"
								class="flex items-center justify-center gap-2 rounded-[10px] bg-red-600 px-4 sm:px-5 py-2.5 font-poppins text-xs sm:text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
							>
								<svg v-if="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
									<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
								</svg>
								<span v-if="activeTab === 'lecturers'">
									Hapus {{ selectedLecturerIds.length }} Dosen ({{ totalArticlesToDeleteByLecturers }} Artikel)
								</span>
								<span v-else-if="activeTab === 'years'">
									Hapus {{ selectedYears.length }} Tahun ({{ totalArticlesToDeleteByYears }} Artikel)
								</span>
								<span v-else>
									Kosongkan Semua Data
								</span>
							</button>
						</div>
					</div>
				</Transition>
			</div>
		</Transition>
	</Teleport>
</template>
