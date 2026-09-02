<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormAktivitasDosen from '@/Components/admin/ModalFormAktivitasDosen.vue';
import TablePagination from '@/Components/TablePagination.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import SearchBarTable from '@/Components/SearchBarTable.vue';
import ModalDeleteConfirmation from '@/Components/ModalDeleteConfirmation.vue';

const props = defineProps({
	activities: {
		type: Array,
		default: () => [],
	},
	availableProfiles: {
		type: Array,
		default: () => [],
	},
});

const page = usePage();

// Toast State
const toast = ref({
	show: false,
	type: 'success',
	title: '',
	message: '',
});

const showToast = (type, title, message) => {
	toast.value = {
		show: true,
		type,
		title,
		message,
	};
};

const closeToast = () => {
	toast.value.show = false;
};

// Check flash messages on page mount
watch(
	() => page.props.flash,
	(flash) => {
		if (flash?.success) {
			showToast('success', 'Berhasil', flash.success);
		} else if (flash?.error) {
			showToast('error', 'Gagal', flash.error);
		}
	},
	{ immediate: true, deep: true }
);

const activities = ref([]);

watch(
	() => props.activities,
	(val) => {
		activities.value = val && val.length > 0 ? [...val] : [];
	},
	{ immediate: true, deep: true }
);

const isLoading = ref(true);
onMounted(() => {
	setTimeout(() => {
		isLoading.value = false;
	}, 350);
});

const searchQuery = ref('');
const selectedLecturerFilter = ref('');
const selectedCategories = ref([]);
const isFilterOpen = ref(false);
const filterSearchQuery = ref('');
const filterSearchInputRef = ref(null);

const categoryOptions = ['Seminar', 'Lokakarya', 'Workshop', 'Lainnya'];

const toggleFilterDropdown = () => {
	isFilterOpen.value = !isFilterOpen.value;
	if (isFilterOpen.value) {
		filterSearchQuery.value = '';
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
	...props.availableProfiles.map((p) => p.name),
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
	currentPage.value = 1;
};

const toggleCategoryFilter = (cat) => {
	const idx = selectedCategories.value.indexOf(cat);
	if (idx > -1) {
		selectedCategories.value.splice(idx, 1);
	} else {
		selectedCategories.value.push(cat);
	}
	currentPage.value = 1;
};

const resetAllFilters = () => {
	selectedCategories.value = [];
	selectedLecturerFilter.value = '';
	filterSearchQuery.value = '';
	currentPage.value = 1;
};

// Table Columns Config
const columns = [
	{ key: 'name', label: 'Nama Aktivitas', sortable: true, align: 'left', width: 'w-[22%]' },
	{ key: 'lecturer', label: 'Nama Dosen', sortable: true, align: 'left', width: 'w-[18%]' },
	{ key: 'category', label: 'Kategori', sortable: true, align: 'left', width: 'w-[15%]' },
	{ key: 'role', label: 'Peran', sortable: true, align: 'left', width: 'w-[14%]' },
	{ key: 'dateSort', label: 'Tanggal Publish', sortable: true, align: 'center', width: 'w-[18%]' },
	{ key: 'action', label: 'Aksi', sortable: false, align: 'center', width: 'w-[13%]' },
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

	// 1. Filter by Lecturer Name
	if (selectedLecturerFilter.value) {
		list = list.filter((a) => a.lecturer === selectedLecturerFilter.value || a.lecturerName === selectedLecturerFilter.value);
	}

	// 2. Filter by Category (Multi-select)
	if (selectedCategories.value.length > 0) {
		list = list.filter((a) => {
			const cats = Array.isArray(a.categories) && a.categories.length > 0
				? a.categories
				: (a.category ? [a.category] : []);
			return cats.some((cat) => selectedCategories.value.includes(cat));
		});
	}

	// 3. Search Query (Nama Aktivitas - case-insensitive sesuai placeholder)
	if (searchQuery.value.trim()) {
		const q = searchQuery.value.toLowerCase().trim();
		list = list.filter(
			(a) =>
				(a.name && a.name.toLowerCase().includes(q)) ||
				(a.title && a.title.toLowerCase().includes(q))
		);
	}

	// 4. Sorting
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

watch([rowsPerPage, searchQuery, selectedLecturerFilter, selectedCategories], () => {
	currentPage.value = 1;
}, { deep: true });

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
	const data = new FormData();
	if (formData.user_id) data.append('user_id', formData.user_id);
	data.append('lecturerName', formData.lecturerName || formData.lecturer);
	data.append('title', formData.title || formData.name);
	data.append('role', formData.role);
	data.append('description', formData.description);
	data.append('startDate', formData.startDate);
	if (formData.endDate) data.append('endDate', formData.endDate);
	if (formData.lecturerQuote && formData.lecturerQuote !== '-') data.append('lecturerQuote', formData.lecturerQuote);
	data.append('primaryImageIndex', formData.primaryImageIndex ?? 0);

	if (Array.isArray(formData.categories)) {
		formData.categories.forEach((cat, idx) => {
			data.append(`categories[${idx}]`, cat);
		});
	}

	if (Array.isArray(formData.images)) {
		let fileIndex = 0;
		let existingIndex = 0;
		formData.images.forEach((img) => {
			if (img instanceof File || img instanceof Blob) {
				data.append(`images[${fileIndex}]`, img);
				fileIndex++;
			} else if (typeof img === 'string') {
				data.append(`existingImages[${existingIndex}]`, img);
				existingIndex++;
			}
		});
	}

	if (isEditing.value && editingActivity.value?.id) {
		router.post(route('admin.aktivitasdosen.update', editingActivity.value.id), data, {
			forceFormData: true,
			onSuccess: () => {
				isModalOpen.value = false;
				showToast('success', 'Berhasil Diperbarui', 'Data aktivitas dosen berhasil diperbarui.');
			},
			onError: (err) => {
				showToast('error', 'Gagal Memperbarui', Object.values(err)[0] || 'Terjadi kesalahan.');
			},
		});
	} else {
		router.post(route('admin.aktivitasdosen.store'), data, {
			forceFormData: true,
			onSuccess: () => {
				isModalOpen.value = false;
				showToast('success', 'Berhasil Ditambahkan', 'Aktivitas dosen baru berhasil ditambahkan.');
			},
			onError: (err) => {
				showToast('error', 'Gagal Menambahkan', Object.values(err)[0] || 'Terjadi kesalahan.');
			},
		});
	}
};

// Delete Confirmation Modal State
const isDeleteModalOpen = ref(false);
const deletingActivity = ref(null);
const isDeleting = ref(false);

const openDeleteModal = (activity) => {
	deletingActivity.value = activity;
	isDeleteModalOpen.value = true;
};

const confirmDeleteActivity = () => {
	if (!deletingActivity.value) return;
	const activity = deletingActivity.value;
	isDeleting.value = true;
	router.delete(route('admin.aktivitasdosen.destroy', activity.id), {
		preserveScroll: true,
		onSuccess: () => {
			isDeleteModalOpen.value = false;
			deletingActivity.value = null;
			isDeleting.value = false;
			showToast('success', 'Berhasil Dihapus', 'Aktivitas dosen berhasil dihapus.');
		},
		onError: (err) => {
			isDeleting.value = false;
			showToast('error', 'Gagal Menghapus', Object.values(err)[0] || 'Terjadi kesalahan.');
		},
	});
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

				<!-- Action Bar (Search, Unified Filter, Tambah Button) -->
				<div class="flex items-center gap-3">
					<!-- Search Input Component -->
					<SearchBarTable
						v-model="searchQuery"
						placeholder="Cari nama aktivitas disini"
					/>

					<!-- Single Filter Button with Unified Dropdown -->
					<div class="relative" @click.stop @keydown.escape="isFilterOpen = false">
						<button
							type="button"
							@click="toggleFilterDropdown"
							class="relative flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-[10px] border-2 bg-transparent text-[#183669] transition-colors focus:outline-none"
							:class="isFilterOpen
								? 'border-[#183669]'
								: 'border-[#d6e0ee] hover:border-[#8ea9cb]'"
							title="Filter Aktivitas"
						>
							<img
								src="/assets/icons/filter.svg"
								alt="Filter Icon"
								class="h-5 w-5 object-contain"
							/>
							<!-- Red active indicator dot -->
							<span
								v-if="selectedCategories.length > 0 || selectedLecturerFilter !== ''"
								class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-[#ef4444] ring-2 ring-[#eef2f7]"
							></span>
						</button>

						<!-- Unified Filter Dropdown Menu (Kategori + Dosen) -->
						<div
							v-if="isFilterOpen"
							class="absolute right-0 z-30 mt-2 w-72 rounded-[10px] border border-[#d6e0ee] bg-white p-3 shadow-xl font-inter"
						>
							<!-- Header with Reset All -->
							<div class="flex items-center justify-between border-b border-[#f0f4f9] pb-2">
								<p class="font-poppins text-xs font-bold text-[#183669]">
									Filter Aktivitas
								</p>
								<button
									v-if="selectedCategories.length > 0 || selectedLecturerFilter !== ''"
									type="button"
									@click="resetAllFilters"
									class="font-inter text-[11px] font-semibold text-[#dc2626] hover:underline"
								>
									Reset Semua
								</button>
							</div>

							<!-- 1. Kategori Section (Multi-select Checkboxes) -->
							<div class="mt-2.5">
								<p class="font-poppins text-[11px] font-bold text-[#183669] mb-1.5">
									Kategori:
								</p>
								<div class="grid grid-cols-2 gap-1.5">
									<label
										v-for="cat in categoryOptions"
										:key="cat"
										class="flex cursor-pointer items-center gap-2 rounded-[6px] px-2 py-1.5 text-left font-inter text-xs transition-colors select-none"
										:class="selectedCategories.includes(cat)
											? 'bg-[#183669]/10 font-bold text-[#183669]'
											: 'text-[#435b76] hover:bg-slate-100'"
									>
										<input
											type="checkbox"
											:value="cat"
											:checked="selectedCategories.includes(cat)"
											@change="toggleCategoryFilter(cat)"
											class="h-3.5 w-3.5 rounded border-[#c3d1e4] text-[#183669] focus:ring-0 focus:ring-offset-0 cursor-pointer"
										/>
										<span class="truncate">{{ cat }}</span>
									</label>
								</div>
							</div>

							<div class="my-2.5 border-t border-[#f0f4f9]"></div>

							<!-- 2. Dosen Section (Searchable List) -->
							<div>
								<div class="flex items-center justify-between mb-1.5">
									<p class="font-poppins text-[11px] font-bold text-[#183669]">
										Dosen:
									</p>
									<span v-if="selectedLecturerFilter" class="text-[11px] text-[#183669] font-medium truncate max-w-[130px]" :title="selectedLecturerFilter">
										{{ selectedLecturerFilter }}
									</span>
								</div>

								<!-- Search Box inside Filter Dropdown -->
								<div class="relative mb-1.5">
									<div class="pointer-events-none absolute inset-y-0 left-2.5 flex items-center">
										<svg class="h-3.5 w-3.5 text-[#8ca1b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
										</svg>
									</div>
									<input
										v-model="filterSearchQuery"
										type="text"
										placeholder="Ketik untuk mencari nama dosen..."
										class="h-[32px] w-full rounded-[6px] border border-[#d6e0ee] bg-[#fafcff] pl-7 pr-2.5 text-xs text-[#1e3456] placeholder-[#8ca1b9] focus:border-[#183669] focus:outline-none focus:ring-0"
										@click.stop
									/>
								</div>

								<!-- Filter Options List -->
								<div class="max-h-36 overflow-y-auto space-y-0.5 pr-0.5">
									<button
										v-for="lec in filteredLecturerFilterList"
										:key="lec"
										type="button"
										@click="setLecturerFilter(lec)"
										:class="[
											'w-full rounded-[6px] px-2.5 py-1 text-left font-inter text-xs transition-colors truncate',
											(selectedLecturerFilter === '' && lec === 'Semua Dosen') || selectedLecturerFilter === lec
												? 'bg-[#183669] font-bold text-white'
												: 'text-[#435b76] hover:bg-slate-100'
										]"
									>
										{{ lec }}
									</button>
									<div v-if="filteredLecturerFilterList.length === 0" class="py-2 text-center text-xs text-[#8ca1b9]">
										Tidak ada dosen yang cocok
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Tambah Aktivitas Button (Icon + on Mobile, Text on Desktop) -->
					<button
						type="button"
						@click="(e) => { e.currentTarget?.blur(); openAddModal(); }"
						class="flex h-[46px] w-[46px] sm:w-auto shrink-0 items-center justify-center rounded-[10px] bg-[#183669] px-0 sm:px-7 font-poppins text-[15px] font-semibold text-white shadow-sm transition hover:bg-[#122b54] active:scale-95 focus:outline-none focus:ring-0 focus-visible:outline-none select-none"
						title="Tambah Aktivitas"
					>
						<svg class="h-5 w-5 sm:hidden" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
						</svg>
						<span class="hidden sm:inline">Tambah</span>
					</button>
				</div>

				<!-- Table Section -->
				<div class="overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
					<table class="w-full min-w-[950px] table-fixed border-collapse text-sm">
						<thead class="bg-[#183669]">
							<tr class="h-[48px]">
								<th class="w-[50px] px-3 py-2.5 text-center font-poppins text-[13px] font-semibold text-white border-r border-white/15 lg:border-r-0">No</th>
								<th
									v-for="col in columns"
									:key="col.key"
									:class="[
										col.width,
										'px-3 py-2.5 font-poppins text-[13px] font-semibold text-white select-none border-r border-white/15 last:border-r-0 lg:border-r-0',
										col.align === 'center' 
									]"
								>
									<button
										v-if="col.sortable"
										type="button"
										@click="toggleSort(col.key)"
										:class="[
											'group transition-colors hover:text-white/80 focus:outline-none max-w-full',
											col.align === 'center'
												? 'mx-auto flex items-center justify-center'
												: 'inline-flex items-center gap-1.5 justify-start'
										]"
									>
										<!-- Balanced spacer for center-aligned columns so text is optically centered and arrow never overflows cell -->
										<span
											v-if="col.align === 'center'"
											class="h-3.5 w-3.5 shrink-0 opacity-0 pointer-events-none mr-1.5"
											aria-hidden="true"
										></span>
										<span class="truncate">{{ col.label }}</span>
										<span class="inline-flex shrink-0 items-center ml-1.5 text-white/70 group-hover:text-white">
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
									<span v-else class="block truncate">{{ col.label }}</span>
								</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-[#d6e0ee] font-inter text-[14px] text-[#435b76]">
							<!-- Skeleton Loading Rows -->
							<template v-if="isLoading">
								<tr
									v-for="n in 6"
									:key="`skeleton-act-${n}`"
									class="h-[52px] animate-pulse bg-white"
								>
									<td class="px-3 py-2.5 text-center">
										<div class="mx-auto h-4 w-5 rounded-md bg-slate-200"></div>
									</td>
									<td class="px-3 py-2.5">
										<div class="h-4 w-48 rounded-md bg-slate-200"></div>
									</td>
									<td class="px-3 py-2.5">
										<div class="h-4 w-36 rounded-md bg-slate-200"></div>
									</td>
									<td class="px-3 py-2.5">
										<div class="h-5 w-20 rounded-full bg-slate-200"></div>
									</td>
									<td class="px-3 py-2.5">
										<div class="h-4 w-24 rounded-md bg-slate-200"></div>
									</td>
									<td class="px-3 py-2.5 text-center">
										<div class="mx-auto h-4 w-24 rounded-md bg-slate-200"></div>
									</td>
									<td class="px-3 py-2.5 text-center">
										<div class="flex items-center justify-center gap-2">
											<div class="h-7 w-7 rounded-lg bg-slate-200"></div>
											<div class="h-7 w-7 rounded-lg bg-slate-200"></div>
										</div>
									</td>
								</tr>
							</template>

							<!-- Real Data Rows -->
							<template v-else>
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
									<td class="px-3 py-2.5 text-left" :title="Array.isArray(activity.categories) && activity.categories.length > 0 ? activity.categories.join(', ') : (activity.category || '-')">
										<span class="block truncate">{{ Array.isArray(activity.categories) && activity.categories.length > 0 ? activity.categories.join(', ') : (activity.category || '-') }}</span>
									</td>
									<td class="px-3 py-2.5 text-left" :title="activity.role">
										<span class="block truncate">{{ activity.role }}</span>
									</td>
									<td class="px-3 py-2.5 text-center" :title="activity.date">
										<span class="block truncate">{{ activity.date }}</span>
									</td>
									<td class="px-3 py-2.5 text-center">
										<div class="flex items-center justify-center gap-2">
											<EditButtonTable :label="`Edit Aktivitas ${activity.name || activity.title}`" @click="openEditModal(activity)" />
											<DeleteButtonTable :label="`Hapus Aktivitas ${activity.name || activity.title}`" @click="openDeleteModal(activity)" />
										</div>
									</td>
								</tr>
								<tr v-if="filteredAndSortedActivities.length === 0">
									<td colspan="7" class="py-8 text-center text-[#7890a8]">
										Tidak ada data aktivitas yang sesuai filter atau pencarian.
									</td>
								</tr>
							</template>
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

		<!-- MODAL DELETE CONFIRMATION (COMPONENT)  -->
		<ModalDeleteConfirmation
			:show="isDeleteModalOpen"
			title="Hapus Aktivitas Dosen"
			:item-name="deletingActivity?.name || deletingActivity?.title"
			:loading="isDeleting"
			@close="isDeleteModalOpen = false"
			@confirm="confirmDeleteActivity"
		/>

		<!-- Toast Notification -->
		<ToastNotification
			:show="toast.show"
			:type="toast.type"
			:title="toast.title"
			:message="toast.message"
			@close="closeToast"
		/>
	</AdminLayout>
</template>
