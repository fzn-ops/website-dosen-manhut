<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormDosen from '@/Components/admin/ModalFormDosen.vue';
import ModalImportDosen from '@/Components/admin/ModalImportDosen.vue';
import TablePagination from '@/Components/TablePagination.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import SearchBarTable from '@/Components/SearchBarTable.vue';
import ModalDeleteConfirmation from '@/Components/ModalDeleteConfirmation.vue';

const props = defineProps({
	lecturers: {
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

// Flash Session Watcher
watch(
	() => page.props.flash,
	(flash) => {
		if (flash?.success) {
			showToast('success', 'Berhasil', flash.success);
		}
		if (flash?.error) {
			showToast('error', 'Gagal', flash.error);
		}
	},
	{ immediate: true, deep: true }
);

const lecturers = ref([]);

watch(
	() => props.lecturers,
	(val) => {
		lecturers.value = val && val.length > 0 ? [...val] : [];
	},
	{ immediate: true, deep: true }
);

// Search Query
const searchQuery = ref('');

// Sorting
const columns = [
	{ key: 'nip', label: 'NIP', sortable: true, align: 'center', width: 'w-[18%]' },
	{ key: 'name', label: 'Nama Dosen', sortable: true, align: 'center', width: 'w-[20%]' },
	{ key: 'username', label: 'Username', sortable: true, align: 'center', width: 'w-[15%]' },
	{ key: 'email', label: 'Email', sortable: true, align: 'center', width: 'w-[18%]' },
	{ key: 'phone', label: 'Nomor Handphone', sortable: true, align: 'center', width: 'w-[15%]' },
	{ key: 'action', label: 'Aksi', sortable: false, align: 'center', width: 'w-[10%]' },
];

const sortKey = ref('id');
const sortDirection = ref('desc');

const toggleSort = (key) => {
	if (sortKey.value === key) {
		sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
	} else {
		sortKey.value = key;
		sortDirection.value = 'asc';
	}
};

// Filtered and Sorted Lecturers
const filteredAndSortedLecturers = computed(() => {
	let list = [...lecturers.value];

	// Search Query (NIP atau Nama Dosen - case-insensitive sesuai placeholder)
	if (searchQuery.value.trim()) {
		const q = searchQuery.value.toLowerCase().trim();
		list = list.filter(
			(l) => (l.name && l.name.toLowerCase().includes(q)) || (l.nip && l.nip.toLowerCase().includes(q))
		);
	}

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

// Pagination State & Controls
const currentPage = ref(1);
const rowsPerPage = ref(10);

const totalPages = computed(() => {
	const count = Math.ceil(filteredAndSortedLecturers.value.length / rowsPerPage.value);
	return count > 0 ? count : 1;
});

const paginatedLecturers = computed(() => {
	const start = (currentPage.value - 1) * rowsPerPage.value;
	return filteredAndSortedLecturers.value.slice(start, start + rowsPerPage.value);
});

// Reset to page 1 on search or rowsPerPage change
watch([searchQuery, rowsPerPage], () => {
	currentPage.value = 1;
});

// MODAL HANDLERS
const isFormModalOpen = ref(false);
const isEditing = ref(false);
const selectedLecturer = ref(null);
const editingId = ref(null);

const isImportModalOpen = ref(false);

const openCreateModal = () => {
	isEditing.value = false;
	selectedLecturer.value = null;
	editingId.value = null;
	isFormModalOpen.value = true;
};

const openEditModal = (lecturer) => {
	isEditing.value = true;
	selectedLecturer.value = lecturer;
	editingId.value = lecturer.id;
	isFormModalOpen.value = true;
};

const handleFormSubmit = (formData) => {
	if (isEditing.value && editingId.value) {
		router.put(`/admin/dosen/${editingId.value}`, formData, {
			preserveScroll: true,
			onSuccess: () => {
				isFormModalOpen.value = false;
				showToast('success', 'Berhasil Diperbarui', 'Data akun dosen berhasil diperbarui.');
			},
			onError: (errors) => {
				const firstError = Object.values(errors)[0] || 'Gagal memperbarui data dosen.';
				showToast('error', 'Gagal Memperbarui', firstError);
			},
		});
	} else {
		router.post('/admin/dosen', formData, {
			preserveScroll: true,
			onSuccess: () => {
				isFormModalOpen.value = false;
				showToast('success', 'Berhasil Ditambahkan', 'Akun dosen baru berhasil ditambahkan.');
			},
			onError: (errors) => {
				const firstError = Object.values(errors)[0] || 'Gagal menambahkan dosen baru.';
				showToast('error', 'Gagal Menambahkan', firstError);
			},
		});
	}
};

const handleImportSubmit = (newLecturers) => {
	router.post('/admin/dosen/import', {
		lecturers: newLecturers,
	}, {
		preserveScroll: true,
		onSuccess: () => {
			isImportModalOpen.value = false;
			showToast('success', 'Import Berhasil', `Berhasil mengimpor data akun dosen.`);
		},
		onError: (errors) => {
			const firstError = Object.values(errors)[0] || 'Gagal mengimpor data dosen.';
			showToast('error', 'Gagal Import', firstError);
		},
	});
};

// Delete Confirmation Modal State
const isDeleteModalOpen = ref(false);
const deletingLecturer = ref(null);
const isDeleting = ref(false);

const openDeleteModal = (lecturer) => {
	deletingLecturer.value = lecturer;
	isDeleteModalOpen.value = true;
};

const confirmDeleteLecturer = () => {
	if (!deletingLecturer.value) return;
	const lecturer = deletingLecturer.value;
	isDeleting.value = true;
	router.delete(`/admin/dosen/${lecturer.id}`, {
		preserveScroll: true,
		onSuccess: () => {
			isDeleteModalOpen.value = false;
			deletingLecturer.value = null;
			isDeleting.value = false;
			showToast('success', 'Berhasil Dihapus', `Data dosen "${lecturer.name}" berhasil dihapus.`);
		},
		onError: (errors) => {
			isDeleting.value = false;
			const firstError = Object.values(errors)[0] || 'Gagal menghapus data dosen.';
			showToast('error', 'Gagal Menghapus', firstError);
		},
	});
};
</script>

<template>
	<Head title="Daftar Dosen" />

	<AdminLayout>
		<section class="mx-auto w-full max-w-[1520px] px-4 py-6 font-poppins sm:px-6 sm:py-8 lg:px-8">
			<div class="space-y-6">
				<!-- Header Title & Subtitle -->
				<div class="space-y-1.5">
					<h1 class="mt-1 text-[34px] font-bold leading-[1.02] tracking-[-0.03em] text-[#173a63] sm:text-[42px] lg:text-[48px]">
						Daftar Dosen
					</h1>
					<p class="mt-1.5 font-inter text-[14px] font-medium leading-tight text-[#4d6786] sm:text-[16px]">
						Lihat data dosen, perbarui, atau tambahkan dosen baru
					</p>
				</div>

				<!-- Action Bar (Search, Import, Tambah Button) -->
				<div class="flex items-center gap-3">
					<!-- Search Input Component -->
					<SearchBarTable
						v-model="searchQuery"
						placeholder="Cari NIP atau Nama Dosen disini"
					/>

					<!-- Import Button (Border-only hover) -->
					<button
						type="button"
						@click="isImportModalOpen = true"
						class="flex h-[46px] shrink-0 items-center justify-center gap-2 rounded-[10px] border-2 border-[#d6e0ee] bg-transparent px-4 font-poppins text-[14px] font-semibold text-[#183669] transition-colors hover:border-[#8ea9cb] focus:border-[#183669] focus:outline-none"
						title="Import Data Dosen (Excel / CSV)"
					>
						<svg class="h-5 w-5 text-[#183669]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
						</svg>
						<span class="hidden sm:inline">Import</span>
					</button>

					<!-- Tambah Button -->
					<button
						type="button"
						@click="openCreateModal"
						class="flex h-[46px] shrink-0 items-center justify-center rounded-[10px] bg-[#183669] px-7 font-poppins text-[15px] font-semibold text-white shadow-sm transition hover:bg-[#122b54]"
					>
						Tambah
					</button>
				</div>

				<!-- Table Section -->
				<div class="overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
					<table class="w-full min-w-[900px] table-fixed border-collapse text-sm">
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
							<tr
								v-for="(lecturer, idx) in paginatedLecturers"
								:key="lecturer.id"
								class="h-[52px] transition-colors hover:bg-[#f7f9fd]"
							>
								<td class="px-3 py-2.5 text-center font-medium">
									{{ (currentPage - 1) * rowsPerPage + idx + 1 }}
								</td>
								<td class="px-3 py-2.5 text-left" :title="lecturer.nip">
									<span class="block truncate">{{ lecturer.nip }}</span>
								</td>
								<td class="px-3 py-2.5 text-left font-medium text-[#2f4b6e]" :title="lecturer.name">
									<span class="block truncate">{{ lecturer.name }}</span>
								</td>
								<td class="px-3 py-2.5 text-left font-medium text-[#2f4b6e]" :title="lecturer.username">
									<span class="block truncate">{{ lecturer.username }}</span>
								</td>
								<td :class="['px-3 py-2.5', lecturer.email && lecturer.email !== '-' ? 'text-left' : 'text-center']" :title="lecturer.email">
									<a
										v-if="lecturer.email && lecturer.email !== '-'"
										:href="`mailto:${lecturer.email}`"
										class="block truncate text-[#2a68c4] underline decoration-[#2a68c4] transition hover:text-[#1d4d96]"
									>
										{{ lecturer.email }}
									</a>
									<span v-else class="block truncate text-[#7890a8]">-</span>
								</td>
								<td class="px-3 py-2.5 text-center" :title="lecturer.phone">
									<span class="block truncate">{{ lecturer.phone }}</span>
								</td>
								<td class="px-3 py-2.5 text-center">
									<div class="flex items-center justify-center gap-2">
										<EditButtonTable :label="`Edit ${lecturer.name}`" @click="openEditModal(lecturer)" />
										<DeleteButtonTable :label="`Hapus ${lecturer.name}`" @click="openDeleteModal(lecturer)" />
									</div>
								</td>
							</tr>
							<tr v-if="filteredAndSortedLecturers.length === 0">
								<td colspan="6" class="py-8 text-center text-[#7890a8]">
									Tidak ada data dosen yang sesuai pencarian.
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

		<!-- MODAL FORM TAMBAH / EDIT DOSEN (COMPONENT) -->
		<ModalFormDosen
			:show="isFormModalOpen"
			:is-editing="isEditing"
			:initial-data="selectedLecturer || {}"
			:editing-id="editingId"
			:existing-lecturers="lecturers"
			@close="isFormModalOpen = false"
			@submit="handleFormSubmit"
		/>

		<!-- MODAL IMPORT DATA DOSEN (COMPONENT)        -->
		<ModalImportDosen
			:show="isImportModalOpen"
			:existing-lecturers="lecturers"
			@close="isImportModalOpen = false"
			@import="handleImportSubmit"
		/>

		<!-- MODAL DELETE CONFIRMATION (COMPONENT)     -->
		<ModalDeleteConfirmation
			:show="isDeleteModalOpen"
			title="Hapus Akun Dosen"
			:item-name="deletingLecturer?.name"
			:loading="isDeleting"
			@close="isDeleteModalOpen = false"
			@confirm="confirmDeleteLecturer"
		/>

		<!-- TOAST NOTIFICATION -->
		<ToastNotification
			:show="toast.show"
			:type="toast.type"
			:title="toast.title"
			:message="toast.message"
			@close="closeToast"
		/>
	</AdminLayout>
</template>
