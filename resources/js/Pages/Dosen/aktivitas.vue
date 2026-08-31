<script setup>
import DosenLayout from '@/Layouts/DosenLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormAktivitas from '@/Components/dosen/ModalFormAktivitas.vue';
import TablePagination from '@/Components/TablePagination.vue';
import SearchBarTable from '@/Components/SearchBarTable.vue';

const page = usePage();
const currentLecturerName = computed(() => page.props.auth?.user?.name || 'Dr. John Doe, M.Si');

// Initial Activities Data for the Lecturer
const initialActivities = [
	{
		id: 5,
		title: 'Rapat Evaluasi Akademik',
		name: 'Rapat Evaluasi Akademik',
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
		lecturerQuote: 'Pelatihan ini sangat berkesan dan bermanfaat, saya merasa berkembang setelah mengikuti kegiatan ini.',
	},
	{
		id: 3,
		title: 'Seminar Kurikulum Merdeka',
		name: 'Seminar Kurikulum Merdeka',
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
const selectedCategories = ref([]);
const isFilterOpen = ref(false);

const categoryOptions = ['Seminar', 'Lokakarya', 'Workshop', 'Lainnya'];

const toggleFilterDropdown = () => {
	isFilterOpen.value = !isFilterOpen.value;
};

const closeAllDropdowns = () => {
	isFilterOpen.value = false;
};

onMounted(() => document.addEventListener('click', closeAllDropdowns));
onBeforeUnmount(() => document.removeEventListener('click', closeAllDropdowns));

const toggleCategoryFilter = (cat) => {
	const idx = selectedCategories.value.indexOf(cat);
	if (idx > -1) {
		selectedCategories.value.splice(idx, 1);
	} else {
		selectedCategories.value.push(cat);
	}
	currentPage.value = 1;
};

const resetCategoryFilter = () => {
	selectedCategories.value = [];
	currentPage.value = 1;
};

const getCategoryCount = (cat) => {
	return activities.value.filter((a) => {
		const cats = Array.isArray(a.categories) && a.categories.length > 0
			? a.categories
			: (a.category ? [a.category] : []);
		return cats.includes(cat);
	}).length;
};

// Table Columns Config
const columns = [
	{ key: 'name', label: 'Nama Aktivitas', sortable: true, align: 'left', width: 'w-[23%]' },
	{ key: 'category', label: 'Kategori', sortable: true, align: 'left', width: 'w-[14%]' },
    { key: 'role', label: 'Peran', sortable: true, align: 'left', width: 'w-[13%]' },
	{ key: 'description', label: 'Deskripsi', sortable: true, align: 'left', width: 'w-[26%]' },
	{ key: 'dateSort', label: 'Tanggal', sortable: true, align: 'left', width: 'w-[14%]' },
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

	// Filter by Selected Categories (Multi-select)
	if (selectedCategories.value.length > 0) {
		list = list.filter((a) => {
			const cats = Array.isArray(a.categories) && a.categories.length > 0
				? a.categories
				: (a.category ? [a.category] : []);
			return cats.some((cat) => selectedCategories.value.includes(cat));
		});
	}

	// Search Query (Nama Aktivitas - case-insensitive sesuai placeholder)
	if (searchQuery.value.trim()) {
		const q = searchQuery.value.toLowerCase().trim();
		list = list.filter(
			(a) =>
				(a.name && a.name.toLowerCase().includes(q)) ||
				(a.title && a.title.toLowerCase().includes(q))
		);
	}

	// Sorting
	if (sortKey.value) {
		list.sort((a, b) => {
			let valA = a[sortKey.value] ?? '';
			let valB = b[sortKey.value] ?? '';

			if (sortKey.value === 'category') {
				valA = Array.isArray(a.categories) && a.categories.length > 0 ? a.categories.join(', ') : (a.category || '');
				valB = Array.isArray(b.categories) && b.categories.length > 0 ? b.categories.join(', ') : (b.category || '');
			}

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

watch([rowsPerPage, searchQuery, selectedCategories], () => {
	currentPage.value = 1;
}, { deep: true });

// Modal Form State (Dosen Component)
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingActivityData = ref(null);

const openAddModal = () => {
	isEditing.value = false;
	editingActivityData.value = null;
	isModalOpen.value = true;
};

const openEditModal = (activity) => {
	isEditing.value = true;
	editingActivityData.value = { ...activity };
	isModalOpen.value = true;
};

const closeModal = () => {
	isModalOpen.value = false;
	editingActivityData.value = null;
};

const handleSaveActivity = (formData) => {
	if (isEditing.value && editingActivityData.value) {
		// Update existing activity
		const idx = activities.value.findIndex((a) => a.id === editingActivityData.value.id);
		if (idx !== -1) {
			activities.value[idx] = {
				...activities.value[idx],
				...formData,
				name: formData.name || formData.title,
				title: formData.name || formData.title,
				dateSort: formData.startDate || activities.value[idx].dateSort,
			};
		}
	} else {
		// Create new activity
		const newId = Date.now();
		activities.value.unshift({
			id: newId,
			...formData,
			name: formData.name || formData.title,
			title: formData.name || formData.title,
			dateSort: formData.startDate || new Date().toISOString().split('T')[0],
		});
	}
	closeModal();
};

const deleteActivity = (activity) => {
	if (confirm(`Apakah Anda yakin ingin menghapus aktivitas "${activity.name || activity.title}"?`)) {
		activities.value = activities.value.filter((a) => a.id !== activity.id);
	}
};
</script>

<template>
	<Head title="Aktivitas Saya - Dosen" />

	<DosenLayout>
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
						Lihat data aktivitas, perbarui, atau tambahkan aktivitas saya yang baru
					</p>
				</div>

				<!-- Action Bar (Search, Filter, Tambah Button - 100% Matching aktivitasdosen.vue) -->
				<div class="flex items-center gap-3">
					<!-- Search Input Component -->
					<SearchBarTable
						v-model="searchQuery"
						placeholder="Cari nama aktivitas disini"
					/>

					<!-- Filter Button with /assets/icons/filter.svg (Border-only hover) -->
					<div class="relative" @click.stop @keydown.escape="isFilterOpen = false">
						<button
							type="button"
							@click="toggleFilterDropdown"
							class="relative flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-[10px] border-2 bg-transparent text-[#183669] transition-colors focus:outline-none"
							:class="isFilterOpen
								? 'border-[#183669]'
								: 'border-[#d6e0ee] hover:border-[#8ea9cb]'"
							title="Filter Berdasarkan Kategori"
						>
							<img
								src="/assets/icons/filter.svg"
								alt="Filter Icon"
								class="h-5 w-5 object-contain"
							/>
							<!-- Red active indicator dot -->
							<span
								v-if="selectedCategories.length > 0"
								class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-[#ef4444] ring-2 ring-[#eef2f7]"
							></span>
						</button>

						<!-- Filter Dropdown Menu (Matching aktivitasdosen.vue) -->
						<div
							v-if="isFilterOpen"
							class="absolute right-0 z-30 mt-2 w-64 rounded-[10px] border border-[#d6e0ee] bg-white p-2 shadow-xl"
						>
							<div class="flex items-center justify-between border-b border-[#f0f4f9] px-3 py-1.5">
								<p class="font-poppins text-xs font-bold text-[#183669]">
									Filter Berdasarkan Kategori:
								</p>
								<button
									v-if="selectedCategories.length > 0"
									type="button"
									@click="resetCategoryFilter"
									class="font-inter text-[11px] font-semibold text-[#dc2626] hover:underline"
								>
									Reset
								</button>
							</div>

							<!-- Filter Options List with Checkboxes -->
							<div class="mt-1 space-y-1">
								<label
									class="flex w-full cursor-pointer items-center gap-2.5 rounded-[6px] px-2.5 py-1.5 text-left font-inter text-xs transition-colors select-none"
									:class="selectedCategories.length === 0
										? 'bg-[#183669]/10 font-bold text-[#183669]'
										: 'text-[#435b76] hover:bg-slate-100'"
								>
									<input
										type="checkbox"
										:checked="selectedCategories.length === 0"
										@change="resetCategoryFilter"
										class="h-4 w-4 rounded border-[#c3d1e4] text-[#183669] focus:ring-0 focus:ring-offset-0 cursor-pointer"
									/>
									<span>Semua Kategori</span>
								</label>

								<label
									v-for="cat in categoryOptions"
									:key="cat"
									class="flex w-full cursor-pointer items-center gap-2.5 rounded-[6px] px-2.5 py-1.5 text-left font-inter text-xs transition-colors select-none"
									:class="selectedCategories.includes(cat)
										? 'bg-[#183669]/10 font-bold text-[#183669]'
										: 'text-[#435b76] hover:bg-slate-100'"
								>
									<input
										type="checkbox"
										:value="cat"
										:checked="selectedCategories.includes(cat)"
										@change="toggleCategoryFilter(cat)"
										class="h-4 w-4 rounded border-[#c3d1e4] text-[#183669] focus:ring-0 focus:ring-offset-0 cursor-pointer"
									/>
									<span class="truncate">{{ cat }}</span>
								</label>
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

			<!-- Main Table Container -->
			<div class="mt-6 overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
				<table class="w-full min-w-[850px] table-fixed border-collapse text-sm">
					<thead class="bg-[#183669]">
						<tr class="h-[48px]">
							<th class="w-[60px] px-3 py-2.5 text-center font-poppins text-[13px] font-semibold text-white">No</th>
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
										'group transition-colors hover:text-white/80 focus:outline-none',
										col.align === 'center'
											? 'relative mx-auto inline-flex items-center justify-center'
											: 'inline-flex items-center gap-1.5 justify-start'
									]"
								>
									<span>{{ col.label }}</span>
									<span
										:class="[
											col.align === 'center'
												? 'absolute left-full ml-1.5 inline-flex items-center text-white/70 group-hover:text-white'
												: 'inline-flex items-center text-white/70 group-hover:text-white'
										]"
									>
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
							<td class="px-3 py-2.5 text-center font-medium">
								{{ (currentPage - 1) * rowsPerPage + idx + 1 }}
							</td>
							<td class="px-4 py-2.5 text-left font-medium text-[#2f4b6e]" :title="activity.name">
								<span class="block truncate">{{ activity.name }}</span>
							</td>
							<td class="px-3 py-2.5 text-left" :title="Array.isArray(activity.categories) && activity.categories.length > 0 ? activity.categories.join(', ') : (activity.category || '-')">
								<span class="block truncate">{{ Array.isArray(activity.categories) && activity.categories.length > 0 ? activity.categories.join(', ') : (activity.category || '-') }}</span>
							</td>
							<td class="px-3 py-2.5 text-left" :title="activity.role">
								<span class="block truncate">{{ activity.role }}</span>
							</td>
                            <td class="px-4 py-2.5 text-left" :title="activity.description ? activity.description.replace(/<[^>]*>/g, '') : ''">
								<span class="block truncate">{{ activity.description ? activity.description.replace(/<[^>]*>/g, '') : '' }}</span>
							</td>
							<td class="px-3 py-2.5 text-left" :title="activity.date">
								<span class="block truncate">{{ activity.date }}</span>
							</td>
							<td class="px-3 py-2.5 text-center">
								<div class="flex items-center justify-center gap-2">
									<EditButtonTable :label="`Edit ${activity.name}`" @click="openEditModal(activity)" />
									<DeleteButtonTable :label="`Hapus ${activity.name}`" @click="deleteActivity(activity)" />
								</div>
							</td>
						</tr>
						<tr v-if="paginatedActivities.length === 0">
							<td colspan="7" class="py-10 text-center text-[#7890a8]">
								Belum ada aktivitas yang sesuai dengan pencarian atau filter.
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Universal Table Pagination -->
			<TablePagination
				v-model:currentPage="currentPage"
				v-model:rowsPerPage="rowsPerPage"
				:total-rows="filteredAndSortedActivities.length"
				:total-pages="totalPages"
			/>
		</div>
		</section>

		<!-- Modal Form Aktivitas (Dosen Version) -->
		<ModalFormAktivitas
			:show="isModalOpen"
			:is-editing="isEditing"
			:initial-data="editingActivityData"
			:lecturer-name="currentLecturerName"
			@close="closeModal"
			@submit="handleSaveActivity"
		/>
	</DosenLayout>
</template>
