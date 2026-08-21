<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, nextTick, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormAktivitasDosen from '@/Components/admin/ModalFormAktivitasDosen.vue';

// Available Lecturer Profiles (12 Dosen yang memiliki profile - disinkronkan dengan profiledosen.vue)
const availableProfiles = [
	{ id: 1, name: 'Farhan Hakim', division: 'Perencanaan Kehutanan' },
	{ id: 2, name: 'Fauzan Fuadiansyah', division: 'Perencanaan Kehutanan' },
	{ id: 3, name: 'Rintan Arufafa Aji', division: 'Pemanfaatan Sumberdaya Hutan' },
	{ id: 4, name: 'Muhammad Fauzan Fuadiansyah S.Kom., M.Cs.', division: 'Kebijakan Kehutanan' },
	{ id: 5, name: 'Dakota Johnson', division: 'Pemanfaatan Sumberdaya Hutan' },
	{ id: 6, name: 'Dr. Ir. Budi Rahardjo M.Sc.', division: 'Perencanaan Kehutanan' },
	{ id: 7, name: 'Prof. Dr. Sulistyo Handoko', division: 'Kebijakan Kehutanan' },
	{ id: 8, name: 'Siti Aminah S.Si., M.Kom.', division: 'Pemanfaatan Sumberdaya Hutan' },
	{ id: 9, name: 'Ahmad Dahlan S.T., M.Eng.', division: 'Perencanaan Kehutanan' },
	{ id: 10, name: 'Rian Hidayat S.Kom., M.T.', division: 'Perencanaan Kehutanan' },
	{ id: 11, name: 'Dewi Lestari M.Kom.', division: 'Pemanfaatan Sumberdaya Hutan' },
	{ id: 12, name: 'Hendra Setiawan Ph.D.', division: 'Kebijakan Kehutanan' },
];

// Initial Activities Data (Sorted by newest first)
const initialActivities = [
	{
		id: 5,
		title: 'Rapat Evaluasi Akademik',
		name: 'Rapat Evaluasi Akademik',
		lecturer: 'Prof. Dr. Sulistyo Handoko',
		lecturerName: 'Prof. Dr. Sulistyo Handoko',
		description: 'Evaluasi capaian semester ganjil dan rencana perbaikan kurikulum.',
		role: 'Peserta',
		startDate: '2026-01-22',
		endDate: '2026-01-22',
		categories: ['Lainnya'],
		category: 'Lainnya',
		date: '22 Januari 2026',
		dateSort: '2026-01-22',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Diskusi yang sangat produktif untuk kemajuan departemen.',
	},
	{
		id: 4,
		title: 'Lokakarya Desa Siman',
		name: 'Lokakarya Desa Siman',
		lecturer: 'Farhan Hakim',
		lecturerName: 'Farhan Hakim',
		description: 'Pendampingan pengelolaan UMKM desa berbasis digital.',
		role: 'Narasumber',
		startDate: '2026-01-21',
		endDate: '2026-01-21',
		categories: ['Lokakarya'],
		category: 'Lokakarya',
		date: '21 Januari 2026',
		dateSort: '2026-01-21',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Pelatihan ini sangat keren dan hebat, saya merasa berkembang setelah mengikuti kegiatan ini.',
	},
	{
		id: 3,
		title: 'Seminar Kurikulum Merdeka',
		name: 'Seminar Kurikulum Merdeka',
		lecturer: 'Dr. Ir. Budi Rahardjo M.Sc.',
		lecturerName: 'Dr. Ir. Budi Rahardjo M.Sc.',
		description: 'Pemaparan strategi implementasi kurikulum adaptif di kampus.',
		role: 'Pemateri Utama',
		startDate: '2026-01-18',
		endDate: '2026-01-18',
		categories: ['Seminar'],
		category: 'Seminar',
		date: '18 Januari 2026',
		dateSort: '2026-01-18',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Antusiasme peserta sangat luar biasa dalam menyerap materi.',
	},
	{
		id: 2,
		title: 'Workshop Metodologi Riset',
		name: 'Workshop Metodologi Riset',
		lecturer: 'Siti Aminah S.Si., M.Kom.',
		lecturerName: 'Siti Aminah S.Si., M.Kom.',
		description: 'Pelatihan teknik sampling dan validasi instrumen penelitian.',
		role: 'Fasilitator',
		startDate: '2026-01-15',
		endDate: '2026-01-15',
		categories: ['Workshop'],
		category: 'Workshop',
		date: '15 Januari 2026',
		dateSort: '2026-01-15',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Semoga mahasiswa dapat mengaplikasikan metode penelitian dengan tepat.',
	},
	{
		id: 1,
		title: 'Pelatihan SPSS Dasar',
		name: 'Pelatihan SPSS Dasar',
		lecturer: 'Rian Hidayat S.Kom., M.T.',
		lecturerName: 'Rian Hidayat S.Kom., M.T.',
		description: 'Praktik olah data kuantitatif untuk tugas akhir mahasiswa.',
		role: 'Instruktur',
		startDate: '2026-01-12',
		endDate: '2026-01-12',
		categories: ['Workshop'],
		category: 'Workshop',
		date: '12 Januari 2026',
		dateSort: '2026-01-12',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Pemahaman statistik sangat penting dalam penyusunan tugas akhir.',
	},
];

const activities = ref([...initialActivities]);
const searchQuery = ref('');
const selectedLecturerFilter = ref('');
const isFilterOpen = ref(false);
const filterSearchQuery = ref('');
const filterSearchInputRef = ref(null);

const toggleFilterDropdown = () => {
	isFilterOpen.value = !isFilterOpen.value;
	if (isFilterOpen.value) {
		filterSearchQuery.value = '';
		nextTick(() => {
			filterSearchInputRef.value?.focus();
		});
	}
};

const lecturerFilterList = computed(() => [
	'Semua Dosen',
	...availableProfiles.map((p) => p.name),
]);

const filteredLecturerFilterList = computed(() => {
	const q = filterSearchQuery.value.toLowerCase().trim();
	if (!q) return lecturerFilterList.value;

	return lecturerFilterList.value.filter((lec) =>
		lec.toLowerCase().includes(q)
	);
});

const setLecturerFilter = (lec) => {
	selectedLecturerFilter.value = lec === 'Semua Dosen' ? '' : lec;
	isFilterOpen.value = false;
	filterSearchQuery.value = '';
	currentPage.value = 1;
};

// Table Columns Config
const columns = [
	{ key: 'name', label: 'Nama Aktivitas', sortable: true, align: 'left', width: 'w-[24%]' },
	{ key: 'lecturer', label: 'Nama Dosen', sortable: true, align: 'left', width: 'w-[18%]' },
	{ key: 'description', label: 'Deskripsi', sortable: true, align: 'left', width: 'w-[24%]' },
	{ key: 'role', label: 'Peran', sortable: true, align: 'left', width: 'w-[12%]' },
	{ key: 'dateSort', label: 'Tanggal', sortable: true, align: 'left', width: 'w-[12%]' },
	{ key: 'action', label: 'Aksi', sortable: false, align: 'center', width: 'w-[10%]' },
];

const isRowsDropdownOpen = ref(false);
const sortKey = ref('id');
const sortDirection = ref('desc'); // Default: Data terbaru di nomor 1

const toggleSort = (key) => {
	if (sortKey.value === key) {
		sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
	} else {
		sortKey.value = key;
		sortDirection.value = 'asc';
	}
};

// Filtered and Sorted Activities
const filteredAndSortedActivities = computed(() => {
	let list = [...activities.value];

	// Filter by Lecturer Name
	if (selectedLecturerFilter.value) {
		list = list.filter((a) => a.lecturer === selectedLecturerFilter.value || a.lecturerName === selectedLecturerFilter.value);
	}

	// Search Query
	if (searchQuery.value.trim()) {
		const q = searchQuery.value.toLowerCase().trim();
		list = list.filter(
			(a) =>
				(a.name && a.name.toLowerCase().includes(q)) ||
				(a.title && a.title.toLowerCase().includes(q)) ||
				(a.lecturer && a.lecturer.toLowerCase().includes(q)) ||
				(a.description && a.description.toLowerCase().includes(q)) ||
				(a.role && a.role.toLowerCase().includes(q)) ||
				(a.date && a.date.toLowerCase().includes(q))
		);
	}

	// Sorting
	if (sortKey.value) {
		list.sort((a, b) => {
			const valA = (a[sortKey.value] ?? '').toString().toLowerCase();
			const valB = (b[sortKey.value] ?? '').toString().toLowerCase();

			if (valA === valB) return 0;
			if (sortDirection.value === 'asc') {
				return valA > valB ? 1 : -1;
			}
			return valA < valB ? 1 : -1;
		});
	}

	return list;
});

// Pagination State & Controls (100% Matching profiledosen.vue)
const currentPage = ref(1);
const rowsPerPage = ref(10);
const pageInput = ref(1);

const totalPages = computed(() => {
	const count = Math.ceil(filteredAndSortedActivities.value.length / rowsPerPage.value);
	return count > 0 ? count : 1;
});

const paginatedActivities = computed(() => {
	const start = (currentPage.value - 1) * rowsPerPage.value;
	return filteredAndSortedActivities.value.slice(start, start + rowsPerPage.value);
});

watch(currentPage, (val) => {
	pageInput.value = val;
});

watch([rowsPerPage, searchQuery, selectedLecturerFilter], () => {
	currentPage.value = 1;
	pageInput.value = 1;
});

const handlePageInput = () => {
	let page = parseInt(pageInput.value, 10);
	if (isNaN(page) || page < 1) page = 1;
	if (page > totalPages.value) page = totalPages.value;
	currentPage.value = page;
	pageInput.value = page;
};

const goToPage = (page) => {
	if (page >= 1 && page <= totalPages.value) {
		currentPage.value = page;
	}
};

const prevPage = () => {
	if (currentPage.value > 1) {
		currentPage.value--;
	}
};

const nextPage = () => {
	if (currentPage.value < totalPages.value) {
		currentPage.value++;
	}
};

// Pagination window items matching profiledosen.vue exactly
const visiblePages = computed(() => {
	const total = totalPages.value;
	const current = currentPage.value;

	if (total <= 7) {
		return Array.from({ length: total }, (_, i) => i + 1);
	}

	if (current <= 4) {
		return [1, 2, 3, 4, 5, '...', total];
	}

	if (current >= total - 3) {
		return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
	}

	return [1, '...', current - 1, current, current + 1, '...', total];
});

// Modal State & Handlers
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingActivity = ref(null);

const openAddModal = () => {
	isEditing.value = false;
	editingActivity.value = null;
	isModalOpen.value = true;
};

const openEditModal = (activity) => {
	isEditing.value = true;
	editingActivity.value = JSON.parse(JSON.stringify(activity));
	isModalOpen.value = true;
};

const handleModalSubmit = (formData) => {
	if (isEditing.value && editingActivity.value) {
		const index = activities.value.findIndex((a) => a.id === editingActivity.value.id);
		if (index !== -1) {
			activities.value[index] = {
				...activities.value[index],
				...formData,
			};
		}
	} else {
		// New entry at top (nomor 1)
		const newId = activities.value.length ? Math.max(...activities.value.map((a) => a.id)) + 1 : 1;
		activities.value.unshift({
			id: newId,
			...formData,
			dateSort: new Date().toISOString().split('T')[0],
		});
		currentPage.value = 1;
	}
};

const deleteActivity = (activity) => {
	if (confirm(`Apakah Anda yakin ingin menghapus aktivitas "${activity.name || activity.title}"?`)) {
		activities.value = activities.value.filter((a) => a.id !== activity.id);
	}
};
</script>

<template>
	<Head title="Daftar Aktivitas" />

	<AuthenticatedLayout>
		<section
			class="mx-auto w-full max-w-[1520px] px-4 py-6 font-poppins sm:px-6 sm:py-8 lg:px-8"
			@click="isFilterOpen = false; isRowsDropdownOpen = false"
		>
			<div class="space-y-6">
				<!-- Header Title & Subtitle -->
				<div class="space-y-1.5">
					<h1 class="mt-1 text-[34px] font-bold leading-[1.02] tracking-[-0.03em] text-[#173a63] sm:text-[42px] lg:text-[48px]">
						Daftar Aktivitas
					</h1>
					<p class="mt-1.5 font-inter text-[14px] font-medium leading-tight text-[#4d6786] sm:text-[16px]">
						Lihat data aktivitas, perbarui, atau tambahkan aktivitas dosen baru
					</p>
				</div>

				<!-- Action Bar (Search, Filter, Tambah Button - 100% Matching profiledosen.vue) -->
				<div class="flex items-center gap-3">
					<!-- Search Input -->
					<div class="relative flex-1">
						<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
							<svg class="h-5 w-5 text-[#aeaeae]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
							</svg>
						</div>
						<input
							v-model="searchQuery"
							type="text"
							placeholder="Cari nama aktivitas disini"
							class="h-[46px] w-full rounded-[10px] border-2 border-[#d6e0ee] bg-transparent pl-12 pr-4 font-inter text-[14px] text-[#173a63] placeholder-[#8ca1b9] transition-colors focus:border-[#183669] focus:outline-none focus:ring-0"
						/>
					</div>

					<!-- Filter Button with /assets/icons/filter.svg -->
					<div class="relative">
						<button
							type="button"
							@click="toggleFilterDropdown"
							class="flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-[10px] border-2 border-[#d6e0ee] bg-transparent text-[#183669] transition hover:border-[#183669] hover:bg-[#183669]/5 focus:border-[#183669] focus:outline-none active:border-[#183669]"
							title="Filter Berdasarkan Nama Dosen"
						>
							<img src="/assets/icons/filter.svg" alt="Filter Icon" class="h-5 w-5 object-contain" />
						</button>

						<!-- Filter Dropdown Menu (Searchable) -->
						<div
							v-if="isFilterOpen"
							class="absolute right-0 z-30 mt-2 w-72 rounded-[10px] border border-[#d6e0ee] bg-white p-2 shadow-xl"
						>
							<p class="px-3 py-1.5 font-poppins text-xs font-bold text-[#183669] border-b border-[#f0f4f9]">
								Filter Berdasarkan Dosen:
							</p>

							<!-- Search Box inside Filter Dropdown -->
							<div class="relative my-1.5 px-1">
								<div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
									<svg class="h-3.5 w-3.5 text-[#8ca1b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
									</svg>
								</div>
								<input
									v-model="filterSearchQuery"
									type="text"
									placeholder="Ketik untuk mencari nama dosen..."
									class="h-[34px] w-full rounded-[6px] border border-[#d6e0ee] bg-[#fafcff] pl-8 pr-2.5 text-xs text-[#1e3456] placeholder-[#8ca1b9] focus:border-[#183669] focus:outline-none focus:ring-0"
									ref="filterSearchInputRef"
									@click.stop
								/>
							</div>

							<!-- Filter Options List -->
							<div class="mt-1 max-h-52 overflow-y-auto space-y-1">
								<button
									v-for="lec in filteredLecturerFilterList"
									:key="lec"
									type="button"
									@click="setLecturerFilter(lec)"
									:class="[
										'w-full rounded-[6px] px-3 py-1.5 text-left font-inter text-xs transition-colors truncate',
										(selectedLecturerFilter === '' && lec === 'Semua Dosen') || selectedLecturerFilter === lec
											? 'bg-[#183669] font-bold text-white'
											: 'text-[#435b76] hover:bg-slate-100'
									]"
								>
									{{ lec }}
								</button>
								<div v-if="filteredLecturerFilterList.length === 0" class="py-3 text-center text-xs text-[#8ca1b9]">
									Tidak ada dosen yang cocok
								</div>
							</div>
						</div>
					</div>

					<!-- Tambah Aktivitas Button -->
					<button
						type="button"
						@click="openAddModal"
						class="flex h-[46px] shrink-0 items-center justify-center rounded-[10px] bg-[#183669] px-7 font-poppins text-[15px] font-semibold text-white shadow-sm transition hover:bg-[#122b54]"
					>
						Tambah
					</button>
				</div>

				<!-- Table Section -->
				<div class="overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
					<table class="w-full min-w-[950px] table-fixed border-collapse text-sm">
						<thead class="bg-[#183669]">
							<tr class="h-[48px]">
								<th class="w-[50px] px-3 py-2.5 text-center font-poppins text-[13px] font-semibold text-white">No</th>
								<th
									v-for="col in columns"
									:key="col.key"
									:class="[
										col.width,
										'px-3 py-2.5 font-poppins text-[13px] font-semibold text-white select-none',
										col.align === 'center' ? 'text-center' : 'text-left'
									]"
								>
									<button
										v-if="col.sortable"
										type="button"
										@click="toggleSort(col.key)"
										:class="[
											'group inline-flex items-center gap-1.5 transition-colors hover:text-white/80 focus:outline-none',
											col.align === 'center' ? 'mx-auto justify-center' : 'justify-start'
										]"
									>
										<span>{{ col.label }}</span>
										<span class="inline-flex items-center text-white/70 group-hover:text-white">
											<svg
												v-if="sortKey === col.key"
												:class="[
													'h-3.5 w-3.5 transition-transform duration-200',
													sortDirection === 'asc' ? 'rotate-180' : ''
												]"
												viewBox="0 0 20 20"
												fill="currentColor"
											>
												<path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.69l3.72-3.72a.75.75 0 111.06 1.06l-5 5a.75.75 0 01-1.06 0l-5-5a.75.75 0 111.06-1.06l3.72 3.72V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
											</svg>
											<svg
												v-else
												class="h-3.5 w-3.5 opacity-50 transition-opacity group-hover:opacity-100"
												viewBox="0 0 20 20"
												fill="currentColor"
											>
												<path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.69l3.72-3.72a.75.75 0 111.06 1.06l-5 5a.75.75 0 01-1.06 0l-5-5a.75.75 0 111.06-1.06l3.72 3.72V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
											</svg>
										</span>
									</button>
									<span v-else>{{ col.label }}</span>
								</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-[#d6e0ee] font-inter text-[14px] text-[#435b76]">
							<tr
								v-for="(activity, idx) in paginatedActivities"
								:key="activity.id"
								class="h-[52px] transition-colors hover:bg-[#f7f9fd]"
							>
								<td class="px-3 py-2.5 text-center font-medium">{{ (currentPage - 1) * rowsPerPage + idx + 1 }}</td>
								<td class="px-3 py-2.5 text-left font-medium text-[#2f4b6e]" :title="activity.name || activity.title">
									<span class="block truncate">{{ activity.name || activity.title }}</span>
								</td>
								<td class="px-3 py-2.5 text-left" :title="activity.lecturer || activity.lecturerName">
									<span class="block truncate">{{ activity.lecturer || activity.lecturerName }}</span>
								</td>
								<td class="px-3 py-2.5 text-left" :title="activity.description">
									<span class="block truncate">{{ activity.description }}</span>
								</td>
								<td class="px-3 py-2.5 text-left" :title="activity.role">
									<span class="block truncate">{{ activity.role }}</span>
								</td>
								<td class="px-3 py-2.5 text-left" :title="activity.date">
									<span class="block truncate">{{ activity.date }}</span>
								</td>
								<td class="px-3 py-2.5 text-center">
									<div class="flex items-center justify-center gap-2">
										<EditButtonTable :label="`Edit Aktivitas ${activity.name || activity.title}`" @click="openEditModal(activity)" />
										<DeleteButtonTable :label="`Hapus Aktivitas ${activity.name || activity.title}`" @click="deleteActivity(activity)" />
									</div>
								</td>
							</tr>
							<tr v-if="filteredAndSortedActivities.length === 0">
								<td colspan="7" class="py-8 text-center text-[#7890a8]">
									Tidak ada data aktivitas yang sesuai filter atau pencarian.
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Pagination Section (Matching profiledosen.vue & dosen.vue 100% exactly) -->
				<div class="flex flex-col gap-4 py-2 font-inter text-[13px] text-[#4d6786] sm:flex-row sm:items-center sm:justify-between">
					<!-- Left: Page [ 1 ] of 10 | Rows per page [ 10 v ] -->
					<div class="flex items-center gap-2">
						<span>Page</span>
						<input
							type="number"
							min="1"
							:max="totalPages"
							v-model.number="pageInput"
							@keydown.enter="handlePageInput"
							@blur="handlePageInput"
							class="h-8 w-11 rounded-[6px] border border-[#d6e0ee] bg-white p-0 text-center font-inter text-[13px] font-medium text-[#173a63] focus:border-[#183669] focus:outline-none focus:ring-1 focus:ring-[#183669]"
						/>
						<span>of {{ totalPages }}</span>

						<span class="mx-1.5 text-[#cbd6e2]">|</span>

						<span>Rows per page</span>
						
						<!-- Custom Rows Per Page Dropup matching filter dropdown style -->
						<div class="relative" @click.stop>
							<button
								type="button"
								@click="isRowsDropdownOpen = !isRowsDropdownOpen"
								class="flex h-8 min-w-[56px] items-center justify-between gap-2 rounded-[6px] border border-[#d6e0ee] bg-white px-2.5 font-inter text-[13px] font-medium text-[#173a63] transition hover:border-[#183669] focus:border-[#183669] focus:outline-none"
								:class="{ 'border-[#183669] ring-1 ring-[#183669]/20': isRowsDropdownOpen }"
							>
								<span>{{ rowsPerPage }}</span>
								<svg
									:class="['h-3.5 w-3.5 text-[#8ca1b9] transition-transform duration-200', isRowsDropdownOpen ? 'rotate-180 text-[#183669]' : '']"
									fill="none"
									stroke="currentColor"
									stroke-width="2"
									viewBox="0 0 24 24"
								>
									<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
								</svg>
							</button>

							<!-- Dropup Popover -->
							<div
								v-if="isRowsDropdownOpen"
								class="absolute bottom-full left-0 z-30 mb-1.5 w-20 rounded-[8px] border border-[#d6e0ee] bg-white p-1 shadow-xl font-inter space-y-0.5"
							>
								<button
									v-for="r in [5, 10, 20, 50]"
									:key="r"
									type="button"
									@click="rowsPerPage = r; isRowsDropdownOpen = false"
									:class="[
										'w-full rounded-[4px] px-2 py-1 text-center text-xs transition-colors',
										rowsPerPage === r
											? 'bg-[#183669] font-bold text-white'
											: 'text-[#435b76] hover:bg-slate-100'
									]"
								>
									{{ r }}
								</button>
							</div>
						</div>
					</div>

					<!-- Right: << < 1 2 3 ... 8 9 10 > >> -->
					<div class="flex items-center gap-1">
						<!-- First Page (<<) -->
						<button
							type="button"
							@click="goToPage(1)"
							:disabled="currentPage === 1"
							class="flex h-8 w-8 items-center justify-center rounded-[6px] border border-[#d6e0ee] bg-white text-[#4d6786] transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
							aria-label="First Page"
						>
							<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
							</svg>
						</button>

						<!-- Prev Page (<) -->
						<button
							type="button"
							@click="prevPage"
							:disabled="currentPage === 1"
							class="flex h-8 w-8 items-center justify-center rounded-[6px] border border-[#d6e0ee] bg-white text-[#4d6786] transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
							aria-label="Previous Page"
						>
							<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
							</svg>
						</button>

						<!-- Page Numbers (Matching profiledosen.vue exactly) -->
						<template v-for="(p, index) in visiblePages" :key="index">
							<button
								v-if="p !== '...'"
								type="button"
								@click="goToPage(p)"
								:class="[
									'flex h-8 min-w-[32px] items-center justify-center rounded-[6px] px-1 text-[13px] transition-colors',
									p === currentPage
										? 'border border-[#cbd8e8] bg-[#f0f4f9] font-bold text-[#183669]'
										: 'border border-transparent text-[#4d6786] hover:bg-slate-100'
								]"
							>
								{{ p }}
							</button>
							<span v-else class="flex h-8 w-7 items-center justify-center text-[#8ca1b9]">...</span>
						</template>

						<!-- Next Page (>) -->
						<button
							type="button"
							@click="nextPage"
							:disabled="currentPage === totalPages"
							class="flex h-8 w-8 items-center justify-center rounded-[6px] border border-[#d6e0ee] bg-white text-[#4d6786] transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
							aria-label="Next Page"
						>
							<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
							</svg>
						</button>

						<!-- Last Page (>>) -->
						<button
							type="button"
							@click="goToPage(totalPages)"
							:disabled="currentPage === totalPages"
							class="flex h-8 w-8 items-center justify-center rounded-[6px] border border-[#d6e0ee] bg-white text-[#4d6786] transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
							aria-label="Last Page"
						>
							<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M5.25 4.5l7.5 7.5-7.5 7.5m6-15l7.5 7.5-7.5 7.5" />
							</svg>
						</button>
					</div>
				</div>
			</div>
		</section>

		<!-- MODAL FORM AKTIVITAS DOSEN (COMPONENT) -->
		<ModalFormAktivitasDosen
			:show="isModalOpen"
			:is-editing="isEditing"
			:initial-data="editingActivity"
			:available-profiles="availableProfiles"
			@close="isModalOpen = false"
			@submit="handleModalSubmit"
		/>
	</AuthenticatedLayout>
</template>
