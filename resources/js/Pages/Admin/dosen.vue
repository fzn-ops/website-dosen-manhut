<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormDosen from '@/Components/admin/ModalFormDosen.vue';
import ModalImportDosen from '@/Components/admin/ModalImportDosen.vue';
import TablePagination from '@/Components/admin/TablePagination.vue';

// Initial Lecturer Data matching design
const initialLecturers = [
	{ id: 1, name: 'Farhan Hakim', nip: 'J0403231075', email: 'farhanhakim123@apps.ipb.ac.id', phone: '+62 812 1234 1234' },
	{ id: 2, name: 'Fauzan Fuadiansyah', nip: 'J0403231076', email: 'fauzanfuadiansyah@apps.ipb.ac.id', phone: '+62 812 1234 1234' },
	{ id: 3, name: 'Rintan Arufafa Aji', nip: 'J0403231113', email: 'contohajakaloyangpanjang@apps.ipb.ac.id', phone: '+62 812 1234 1234' },
	{ id: 4, name: 'Muhammad Fauzan Fuadiansyah S.Kom., M.Cs.', nip: 'J0403231077', email: '-', phone: '-' },
	{ id: 5, name: 'Dakota Johnson', nip: 'J0403231078', email: '-', phone: '-' },
	{ id: 6, name: 'Dr. Ir. Budi Rahardjo M.Sc.', nip: 'J0403231080', email: 'budi.rahardjo@apps.ipb.ac.id', phone: '+62 813 9876 5432' },
	{ id: 7, name: 'Prof. Dr. Sulistyo Handoko', nip: 'J0403231091', email: 'sulistyo.h@apps.ipb.ac.id', phone: '+62 812 8899 0011' },
	{ id: 8, name: 'Siti Aminah S.Si., M.Kom.', nip: 'J0403231044', email: 'siti_aminah@apps.ipb.ac.id', phone: '+62 856 7788 9900' },
	{ id: 9, name: 'Ahmad Dahlan S.T., M.Eng.', nip: 'J0403231032', email: 'a.dahlan@apps.ipb.ac.id', phone: '-' },
	{ id: 10, name: 'Rian Hidayat S.Kom., M.T.', nip: 'J0403231021', email: 'rian.hidayat@apps.ipb.ac.id', phone: '+62 817 6543 2109' },
	{ id: 11, name: 'Dewi Lestari M.Kom.', nip: 'J0403231015', email: 'dewi.lestari@apps.ipb.ac.id', phone: '+62 812 3344 5566' },
	{ id: 12, name: 'Hendra Setiawan Ph.D.', nip: 'J0403231055', email: 'hendra.s@apps.ipb.ac.id', phone: '-' },
	{ id: 13, name: 'Nurul Hidayati S.Pd., M.Pd.', nip: 'J0403231062', email: 'nurul.h@apps.ipb.ac.id', phone: '+62 819 0123 4567' },
	{ id: 14, name: 'Prof. Bambang Subagyo', nip: 'J0403231070', email: 'bambang.subagyo@apps.ipb.ac.id', phone: '+62 811 2233 4455' },
	{ id: 15, name: 'Andi Pratama S.Kom., M.M.', nip: 'J0403231088', email: '-', phone: '+62 815 6789 0123' },
	{ id: 16, name: 'Tri Wahyuni M.Sc.', nip: 'J0403231095', email: 'tri.wahyuni@apps.ipb.ac.id', phone: '-' },
	{ id: 17, name: 'Agus Susanto S.Si., M.Si.', nip: 'J0403231102', email: 'agus.susanto@apps.ipb.ac.id', phone: '+62 818 7654 3210' },
	{ id: 18, name: 'Dian Permatasari M.Kom.', nip: 'J0403231110', email: 'dian.permatasari@apps.ipb.ac.id', phone: '+62 813 4567 8901' },
	{ id: 19, name: 'Eko Prasetyo S.T., M.Kom.', nip: 'J0403231125', email: '-', phone: '-' },
	{ id: 20, name: 'Fitri Handayani M.Pd.', nip: 'J0403231130', email: 'fitri.handayani@apps.ipb.ac.id', phone: '+62 812 9012 3456' },
];

const lecturers = ref([...initialLecturers]);

// Search Query
const searchQuery = ref('');

// Sorting
const columns = [
	{ key: 'name', label: 'Nama Dosen', sortable: true, align: 'center', width: 'w-[20%]' },
	{ key: 'nip', label: 'NIP', sortable: true, align: 'center', width: 'w-[20%]' },
	{ key: 'email', label: 'Email', sortable: true, align: 'center', width: 'w-[27%]' },
	{ key: 'phone', label: 'Nomor Handphone', sortable: true, align: 'center', width: 'w-[18%]' },
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

	if (searchQuery.value.trim()) {
		const q = searchQuery.value.toLowerCase().trim();
		list = list.filter(
			(l) => l.name.toLowerCase().includes(q) || l.nip.toLowerCase().includes(q) || l.email.toLowerCase().includes(q)
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
	if (isEditing.value) {
		const index = lecturers.value.findIndex((l) => l.id === editingId.value);
		if (index !== -1) {
			lecturers.value[index] = {
				...lecturers.value[index],
				nip: formData.nip,
				name: formData.name,
				email: formData.email,
				phone: formData.phone,
				password: formData.password || lecturers.value[index].password || formData.nip,
			};
		}
	} else {
		const newId = lecturers.value.length ? Math.max(...lecturers.value.map((l) => l.id)) + 1 : 1;
		lecturers.value.unshift({
			id: newId,
			nip: formData.nip,
			name: formData.name,
			email: formData.email,
			phone: formData.phone,
			password: formData.password || formData.nip,
		});
	}
};

const handleImportSubmit = (newLecturers) => {
	lecturers.value.unshift(...newLecturers);
	currentPage.value = 1;
};

const deleteLecturer = (lecturer) => {
	if (confirm(`Apakah Anda yakin ingin menghapus data dosen ${lecturer.name}?`)) {
		lecturers.value = lecturers.value.filter((l) => l.id !== lecturer.id);
	}
};
</script>

<template>
	<Head title="Daftar Dosen" />

	<AuthenticatedLayout>
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
							placeholder="Cari NIP atau Nama Dosen disini"
							class="h-[46px] w-full rounded-[10px] border-2 border-[#d6e0ee] bg-transparent pl-12 pr-4 font-inter text-[14px] text-[#173a63] placeholder-[#8ca1b9] transition-colors focus:border-[#183669] focus:outline-none focus:ring-0"
						/>
					</div>

					<!-- Import Button -->
					<button
						type="button"
						@click="isImportModalOpen = true"
						class="flex h-[46px] shrink-0 items-center justify-center gap-2 rounded-[10px] border-2 border-[#d6e0ee] bg-transparent px-4 font-poppins text-[14px] font-semibold text-[#183669] transition hover:border-[#183669] hover:bg-[#183669]/5 focus:border-[#183669] focus:outline-none active:border-[#183669]"
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
								v-for="(lecturer, idx) in paginatedLecturers"
								:key="lecturer.id"
								class="h-[52px] transition-colors hover:bg-[#f7f9fd]"
							>
								<td class="px-3 py-2.5 text-center font-medium">{{ (currentPage - 1) * rowsPerPage + idx + 1 }}</td>
								<td class="px-3 py-2.5 text-left font-medium text-[#2f4b6e]" :title="lecturer.name">
									<span class="block truncate">{{ lecturer.name }}</span>
								</td>
								<td class="px-3 py-2.5 text-center" :title="lecturer.nip">
									<span class="block truncate">{{ lecturer.nip }}</span>
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
										<DeleteButtonTable :label="`Hapus ${lecturer.name}`" @click="deleteLecturer(lecturer)" />
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
	</AuthenticatedLayout>
</template>
