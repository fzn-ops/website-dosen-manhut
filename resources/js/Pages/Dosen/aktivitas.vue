<script setup>
import DosenLayout from '@/Layouts/DosenLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormAktivitas from '@/Components/dosen/ModalFormAktivitas.vue';
import TablePagination from '@/Components/TablePagination.vue';
import SearchBarTable from '@/Components/SearchBarTable.vue';
import ModalDeleteConfirmation from '@/Components/ModalDeleteConfirmation.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

const props = defineProps({
	activities: {
		type: Array,
		default: () => [],
	},
	hasProfile: {
		type: Boolean,
		default: true,
	},
});

const page = usePage();
const currentLecturerName = computed(() => page.props.auth?.user?.name || 'Dosen');

const activities = ref([...(props.activities || [])]);

watch(
	() => props.activities,
	(val) => {
		activities.value = [...(val || [])];
	},
	{ deep: true }
);

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

const isLoading = ref(true);

onMounted(() => {
	document.addEventListener('click', closeAllDropdowns);
	setTimeout(() => {
		isLoading.value = false;
	}, 350);
});
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
	{ key: 'dateSort', label: 'Tanggal Publish', sortable: true, align: 'center', width: 'w-[14%]' },
	{ key: 'action', label: 'Aksi', sortable: false, align: 'center', width: 'w-[10%]' },
];

const sortKey = ref('id');
const sortDirection = ref('desc'); // Default: Data terbaru di nomor 1

const toggleSort = (key) => {
	if (sortKey.value === key) {
		sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
	} else {
		sortKey.value = key;
		sortDirection.value = key === 'id' ? 'desc' : 'asc';
	}
	currentPage.value = 1;
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
	if (sortKey.value === 'id') {
		list.sort((a, b) => {
			const idA = Number(a.id) || 0;
			const idB = Number(b.id) || 0;
			return sortDirection.value === 'asc' ? idA - idB : idB - idA;
		});
	} else if (sortKey.value) {
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
	if (!props.hasProfile) return;
	isEditing.value = false;
	editingActivityData.value = null;
	isModalOpen.value = true;
};

const openEditModal = (activity) => {
	if (!props.hasProfile) return;
	isEditing.value = true;
	editingActivityData.value = { ...activity };
	isModalOpen.value = true;
};

const closeModal = () => {
	isModalOpen.value = false;
	editingActivityData.value = null;
};

const isSaving = ref(false);

const handleSaveActivity = (formData) => {
	if (!props.hasProfile) {
		showToast('error', 'Profil Belum Tersedia', 'Profil publik belum dibuat oleh Administrator. Anda belum dapat mengelola aktivitas.');
		return;
	}
	isSaving.value = true;
	const data = new FormData();
	data.append('title', formData.title || formData.name);
	data.append('role', formData.role);
	data.append('description', formData.description);
	data.append('startDate', formData.startDate);
	if (formData.endDate) {
		data.append('endDate', formData.endDate);
	}
	if (formData.lecturerQuote) {
		data.append('lecturerQuote', formData.lecturerQuote);
	}
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

	if (isEditing.value && editingActivityData.value?.id) {
		router.post(route('dosen.aktivitas.update', editingActivityData.value.id), data, {
			forceFormData: true,
			onSuccess: () => {
				isSaving.value = false;
				isModalOpen.value = false;
				editingActivityData.value = null;
				showToast('success', 'Berhasil Diperbarui', 'Data aktivitas berhasil diperbarui.');
			},
			onError: (err) => {
				isSaving.value = false;
				showToast('error', 'Gagal Memperbarui', Object.values(err)[0] || 'Terjadi kesalahan.');
			},
		});
	} else {
		router.post(route('dosen.aktivitas.store'), data, {
			forceFormData: true,
			onSuccess: () => {
				isSaving.value = false;
				isModalOpen.value = false;
				editingActivityData.value = null;
				showToast('success', 'Berhasil Ditambahkan', 'Aktivitas baru berhasil ditambahkan.');
			},
			onError: (err) => {
				isSaving.value = false;
				showToast('error', 'Gagal Menambahkan', Object.values(err)[0] || 'Terjadi kesalahan.');
			},
		});
	}
};

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

// Delete Confirmation Modal State
const isDeleteModalOpen = ref(false);
const deletingActivity = ref(null);
const isDeleting = ref(false);

const openDeleteModal = (activity) => {
	if (!props.hasProfile) return;
	deletingActivity.value = activity;
	isDeleteModalOpen.value = true;
};

const confirmDeleteActivity = () => {
	if (!props.hasProfile || !deletingActivity.value) return;
	const activity = deletingActivity.value;
	isDeleting.value = true;
	router.delete(route('dosen.aktivitas.destroy', activity.id), {
		preserveScroll: true,
		onSuccess: () => {
			isDeleteModalOpen.value = false;
			deletingActivity.value = null;
			isDeleting.value = false;
			showToast('success', 'Berhasil Dihapus', `Aktivitas "${activity.name || activity.title}" berhasil dihapus.`);
		},
		onError: (errors) => {
			isDeleting.value = false;
			const firstError = Object.values(errors)[0] || 'Gagal menghapus aktivitas.';
			showToast('error', 'Gagal Menghapus', firstError);
		},
	});
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

				<!-- Alert Banner Jika Dosen Belum Memiliki Profil Publik -->
				<div
					v-if="!hasProfile"
					class="flex items-start gap-3.5 rounded-[14px] border border-blue-200 bg-blue-50/90 p-4 sm:p-5 shadow-xs"
				>
					<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
						</svg>
					</div>
					<div class="space-y-1">
						<h3 class="font-poppins text-[15px] font-bold text-[#173a63]">
							Profil Publik Belum Tersedia
						</h3>
						<p class="font-inter text-[13px] leading-relaxed text-[#4d6786]">
							Profil Publik Anda dikelola oleh Administrator. Saat ini data profile Anda belum tersedia secara publik. Silakan <strong>hubungi Administrator</strong> untuk membuat profile publik agar dapat mengelola aktivitas anda!
						</p>
					</div>
				</div>

				<!-- Action Bar (Search, Filter, Tambah Button - 100% Matching aktivitasdosen.vue) -->
				<div class="flex items-center gap-3">
					<!-- Search Input Component -->
					<SearchBarTable
						v-model="searchQuery"
						:disabled="!hasProfile"
						placeholder="Cari nama aktivitas disini"
					/>

					<!-- Filter Button with /assets/icons/filter.svg (Border-only hover) -->
					<div class="relative" @click.stop @keydown.escape="isFilterOpen = false">
						<button
							type="button"
							:disabled="!hasProfile"
							@click="hasProfile && toggleFilterDropdown()"
							class="relative flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-[10px] border-2 bg-transparent transition-colors focus:outline-none"
							:class="[
								!hasProfile
									? 'cursor-not-allowed border-[#d6e0ee] bg-[#f0f4f9] text-[#8c9eb5] opacity-60'
									: isFilterOpen
										? 'border-[#183669] text-[#183669]'
										: 'border-[#d6e0ee] text-[#183669] hover:border-[#8ea9cb]'
							]"
							:title="!hasProfile ? 'Profil publik belum dibuat oleh Administrator.' : 'Filter Berdasarkan Kategori'"
						>
							<img
								src="/assets/icons/filter.svg"
								alt="Filter Icon"
								class="h-5 w-5 object-contain"
								:class="{ 'opacity-40 grayscale': !hasProfile }"
							/>
							<!-- Red active indicator dot -->
							<span
								v-if="hasProfile && selectedCategories.length > 0"
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

					<!-- Tambah Aktivitas Button (Icon + on Mobile, Text on Desktop) -->
					<button
						type="button"
						:disabled="!hasProfile"
						@click="(e) => { e.currentTarget?.blur(); if (hasProfile) openAddModal(); }"
						:class="[
							'flex h-[46px] w-[46px] sm:w-auto shrink-0 items-center justify-center rounded-[10px] px-0 sm:px-7 font-poppins text-[15px] font-semibold transition select-none',
							!hasProfile
								? 'cursor-not-allowed bg-[#f0f4f9] text-[#8c9eb5] border-[1.5px] border-[#d6e0ee] shadow-none'
								: 'cursor-pointer bg-[#183669] text-white shadow-sm hover:bg-[#122b54] active:scale-95 focus:outline-none'
						]"
						:title="!hasProfile ? 'Profil publik belum dibuat oleh Administrator. Silakan hubungi Administrator terlebih dahulu.' : 'Tambah Aktivitas'"
					>
						<svg class="h-5 w-5 sm:hidden" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
						</svg>
						<span class="hidden sm:inline">Tambah</span>
					</button>
				</div>

			<!-- Main Table Container -->
			<div class="mt-6 overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
				<table class="w-full min-w-[850px] table-fixed border-collapse text-sm">
					<thead class="bg-[#183669]">
						<tr class="h-[48px]">
							<th
								:class="[
									'w-[60px] px-2 py-2.5 text-center font-poppins text-[13px] font-semibold text-white border-r border-white/15 lg:border-r-0 select-none',
									!hasProfile ? 'cursor-not-allowed opacity-60' : ''
								]"
							>
								<button
									type="button"
									:disabled="!hasProfile"
									@click="hasProfile && toggleSort('id')"
									:class="[
										'group relative inline-flex items-center justify-center mx-auto transition-colors focus:outline-none',
										hasProfile ? 'hover:text-white/80' : 'cursor-not-allowed opacity-60'
									]"
									title="Urutkan No"
								>
									<span>No</span>
									<span class="absolute left-full ml-1 top-1/2 -translate-y-1/2 inline-flex items-center">
										<svg
											v-if="sortKey === 'id'"
											:class="[
												'h-3.5 w-3.5 text-white transition-transform duration-200',
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
							</th>
							<th
								v-for="col in columns"
								:key="col.key"
								:class="[
									col.width,
									'px-3 py-2.5 font-poppins text-[13px] font-semibold text-white select-none border-r border-white/15 last:border-r-0 lg:border-r-0',
									col.align === 'center',
									!hasProfile ? 'cursor-not-allowed' : ''
								]"
							>
								<button
									v-if="col.sortable"
									type="button"
									:disabled="!hasProfile"
									@click="hasProfile && toggleSort(col.key)"
									:class="[
										'group transition-colors focus:outline-none max-w-full',
										hasProfile ? 'hover:text-white/80' : 'cursor-not-allowed opacity-60',
										col.align === 'center'
											? 'mx-auto flex items-center justify-center'
											: 'inline-flex items-center gap-1.5 justify-start'
									]"
									:title="`Urutkan ${col.label}`"
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
												'h-3.5 w-3.5 text-white transition-transform duration-200',
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
								<span
									v-else
									:class="[
										'block truncate',
										!hasProfile ? 'cursor-not-allowed opacity-60' : ''
									]"
								>
									{{ col.label }}
								</span>
							</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-[#d6e0ee] font-inter text-[14px] text-[#435b76]">
						<!-- Skeleton Loading Rows -->
						<template v-if="isLoading">
							<tr
								v-for="n in 5"
								:key="`skeleton-dosen-act-${n}`"
								class="h-[52px] animate-pulse bg-white"
							>
								<td class="px-3 py-2.5 text-center">
									<div class="mx-auto h-4 w-5 rounded-md bg-slate-200"></div>
								</td>
								<td class="px-4 py-2.5">
									<div class="h-4 w-44 rounded-md bg-slate-200"></div>
								</td>
								<td class="px-3 py-2.5">
									<div class="h-5 w-20 rounded-full bg-slate-200"></div>
								</td>
								<td class="px-3 py-2.5">
									<div class="h-4 w-24 rounded-md bg-slate-200"></div>
								</td>
								<td class="px-4 py-2.5">
									<div class="h-4 w-56 rounded-md bg-slate-200"></div>
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
								<td class="px-3 py-2.5 text-center" :title="activity.publishDate || activity.date">
									<span class="block truncate">{{ activity.publishDate || activity.date }}</span>
								</td>
								<td class="px-3 py-2.5 text-center">
									<div class="flex items-center justify-center gap-2">
										<EditButtonTable :label="`Edit ${activity.name}`" :disabled="!hasProfile" @click="openEditModal(activity)" />
										<DeleteButtonTable :label="`Hapus ${activity.name}`" :disabled="!hasProfile" @click="openDeleteModal(activity)" />
									</div>
								</td>
							</tr>
							<tr v-if="paginatedActivities.length === 0">
								<td colspan="7" class="py-10 text-center text-[#7890a8]">
									Belum ada aktivitas yang sesuai dengan pencarian atau filter.
								</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>

			<!-- Universal Table Pagination -->
			<TablePagination
				v-model:currentPage="currentPage"
				v-model:rowsPerPage="rowsPerPage"
				:total-pages="totalPages"
				:total-items="filteredAndSortedActivities.length"
				:disabled="!hasProfile"
			/>
		</div>
	</section>

		<!-- Modal Form Aktivitas (Dosen Version) -->
		<ModalFormAktivitas
			:show="isModalOpen"
			:is-editing="isEditing"
			:initial-data="editingActivityData"
			:lecturer-name="currentLecturerName"
			:loading="isSaving"
			@close="closeModal"
			@submit="handleSaveActivity"
		/>

		<!-- Modal Delete Confirmation -->
		<ModalDeleteConfirmation
			:show="isDeleteModalOpen"
			title="Hapus Aktivitas"
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
	</DosenLayout>
</template>
