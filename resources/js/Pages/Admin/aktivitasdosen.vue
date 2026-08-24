<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormAktivitasDosen from '@/Components/admin/ModalFormAktivitasDosen.vue';
import TablePagination from '@/Components/TablePagination.vue';

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

// Close filter dropdown when clicking outside
const closeAllDropdowns = () => {
	isFilterOpen.value = false;
};

onMounted(() => document.addEventListener('click', closeAllDropdowns));
onBeforeUnmount(() => document.removeEventListener('click', closeAllDropdowns));

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
			let valA = a[sortKey.value] ?? '';
			let valB = b[sortKey.value] ?? '';

			if (typeof valA === 'number' && typeof valB === 'number') {
				return sortDirection.value === 'asc' ? valA - valB : valB - valA;
			}

			valA = valA.toString().toLowerCase();
			valB = valB.toString().toLowerCase();

			if (valA === valB) return 0;
			if (sortDirection.value === 'asc') {
				return valA > valB ? 1 : -1;
			}
			return valA < valB ? 1 : -1;
		});
	}

	return list;
});

// Pagination State & Controls
const currentPage = ref(1);
const rowsPerPage = ref(10);

const totalPages = computed(() => {
	const count = Math.ceil(filteredAndSortedActivities.value.length / rowsPerPage.value);
	return count > 0 ? count : 1;
});

const paginatedActivities = computed(() => {
	const start = (currentPage.value - 1) * rowsPerPage.value;
	return filteredAndSortedActivities.value.slice(start, start + rowsPerPage.value);
});

watch([rowsPerPage, searchQuery, selectedLecturerFilter], () => {
	currentPage.value = 1;
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
		// New entry at top (nomor 1) with clean incremented numeric ID
		const maxId = activities.value.length ? Math.max(...activities.value.map((a) => Number(a.id) || 0)) : 0;
		const newId = maxId + 1;
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
	<Head title="Aktivitas Dosen" />

	<AdminLayout>
		<section
			class="mx-auto w-full max-w-[1520px] px-4 py-6 font-poppins sm:px-6 sm:py-8 lg:px-8"
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
					<div class="relative" @click.stop @keydown.escape="isFilterOpen = false">
						<button
							type="button"
							@click="toggleFilterDropdown"
							class="flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-[10px] border-2 bg-transparent text-[#183669] transition focus:outline-none"
							:class="isFilterOpen
								? 'border-[#183669]'
								: 'border-[#d6e0ee] hover:border-[#183669] hover:bg-[#183669]/5'"
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
										col.align === 'center'
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
								<td class="px-3 py-2.5 text-left" :title="activity.description ? activity.description.replace(/<[^>]*>/g, '') : ''">
									<span class="block truncate">{{ activity.description ? activity.description.replace(/<[^>]*>/g, '') : '' }}</span>
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

				<!-- Pagination -->
				<TablePagination
					:current-page="currentPage"
					:total-pages="totalPages"
					:rows-per-page="rowsPerPage"
					@update:current-page="currentPage = $event"
					@update:rows-per-page="rowsPerPage = $event; currentPage = 1"
				/>
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
	</AdminLayout>
</template>
