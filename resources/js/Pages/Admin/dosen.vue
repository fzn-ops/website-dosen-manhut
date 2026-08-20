<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import * as XLSX from 'xlsx';

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
	{ key: 'name', label: 'Nama Dosen', sortable: true, align: 'left', width: 'w-[24%]' },
	{ key: 'nip', label: 'NIP', sortable: true, align: 'center', width: 'w-[16%]' },
	{ key: 'email', label: 'Email', sortable: true, align: 'center', width: 'w-[27%]' },
	{ key: 'phone', label: 'Nomor Handphone', sortable: true, align: 'center', width: 'w-[18%]' },
	{ key: 'action', label: 'Aksi', sortable: false, align: 'center', width: 'w-[10%]' },
];

const sortKey = ref('name');
const sortDirection = ref('asc');

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
const pageInput = ref(1);

const totalPages = computed(() => {
	const count = Math.ceil(filteredAndSortedLecturers.value.length / rowsPerPage.value);
	return count > 0 ? count : 1;
});

const paginatedLecturers = computed(() => {
	const start = (currentPage.value - 1) * rowsPerPage.value;
	return filteredAndSortedLecturers.value.slice(start, start + rowsPerPage.value);
});

// Sync input when page changes
watch(currentPage, (val) => {
	pageInput.value = val;
});

// Reset to page 1 on search or rowsPerPage change
watch([searchQuery, rowsPerPage], () => {
	currentPage.value = 1;
	pageInput.value = 1;
});

const goToPage = (page) => {
	if (typeof page === 'number' && page >= 1 && page <= totalPages.value) {
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

const handlePageInput = () => {
	let p = parseInt(pageInput.value, 10);
	if (isNaN(p) || p < 1) p = 1;
	if (p > totalPages.value) p = totalPages.value;
	currentPage.value = p;
	pageInput.value = p;
};

// Visible page buttons logic
const visiblePages = computed(() => {
	const total = totalPages.value;
	const current = currentPage.value;

	if (total <= 7) {
		return Array.from({ length: total }, (_, i) => i + 1);
	}

	if (current <= 3) {
		return [1, 2, 3, '...', total - 2, total - 1, total];
	}

	if (current >= total - 2) {
		return [1, 2, 3, '...', total - 2, total - 1, total];
	}

	return [1, '...', current - 1, current, current + 1, '...', total];
});

// Create/Edit Modal State
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const formError = ref('');

const form = ref({
	nip: '',
	name: '',
	email: '',
	phone: '',
});

const openModal = (lecturer = null) => {
	formError.value = '';
	if (lecturer) {
		isEditing.value = true;
		editingId.value = lecturer.id;
		form.value = {
			nip: lecturer.nip !== '-' ? lecturer.nip : '',
			name: lecturer.name,
			email: lecturer.email !== '-' ? lecturer.email : '',
			phone: lecturer.phone !== '-' ? lecturer.phone : '',
		};
	} else {
		isEditing.value = false;
		editingId.value = null;
		form.value = {
			nip: '',
			name: '',
			email: '',
			phone: '',
		};
	}
	isModalOpen.value = true;
};

const closeModal = () => {
	isModalOpen.value = false;
	formError.value = '';
};

const submitForm = () => {
	formError.value = '';
	const inputNip = form.value.nip.trim();
	const inputName = form.value.name.trim();

	if (!inputNip || !inputName) {
		formError.value = 'NIP dan Nama Dosen wajib diisi.';
		return;
	}

	// Validate duplicate NIP
	const isNipDuplicate = lecturers.value.some((l) => {
		if (isEditing.value && l.id === editingId.value) return false;
		return l.nip.toLowerCase() === inputNip.toLowerCase();
	});

	if (isNipDuplicate) {
		formError.value = `NIP "${inputNip}" sudah terdaftar. NIP tidak boleh duplikat.`;
		return;
	}

	if (isEditing.value) {
		const index = lecturers.value.findIndex((l) => l.id === editingId.value);
		if (index !== -1) {
			lecturers.value[index] = {
				...lecturers.value[index],
				nip: inputNip,
				name: inputName,
				email: form.value.email.trim() || '-',
				phone: form.value.phone.trim() || '-',
			};
		}
	} else {
		const newId = lecturers.value.length ? Math.max(...lecturers.value.map((l) => l.id)) + 1 : 1;
		lecturers.value.unshift({
			id: newId,
			nip: inputNip,
			name: inputName,
			email: form.value.email.trim() || '-',
			phone: form.value.phone.trim() || '-',
		});
	}

	closeModal();
};

const deleteLecturer = (lecturer) => {
	if (confirm(`Apakah Anda yakin ingin menghapus data dosen ${lecturer.name}?`)) {
		lecturers.value = lecturers.value.filter((l) => l.id !== lecturer.id);
	}
};

// ==========================================
// IMPORT EXCEL / CSV BULK CREATE LOGIC
// ==========================================
const isImportModalOpen = ref(false);
const selectedFile = ref(null);
const parsedData = ref([]);
const isDragging = ref(false);
const importErrorMessage = ref('');
const fileInputRef = ref(null);

// Inline edit in preview table
const editingRowIndex = ref(null);
const editRowForm = ref({ nip: '', name: '', email: '', phone: '' });

const openImportModal = () => {
	selectedFile.value = null;
	parsedData.value = [];
	importErrorMessage.value = '';
	editingRowIndex.value = null;
	isDragging.value = false;
	isImportModalOpen.value = true;
};

const closeImportModal = () => {
	isImportModalOpen.value = false;
	selectedFile.value = null;
	parsedData.value = [];
	editingRowIndex.value = null;
	importErrorMessage.value = '';
};

// Revalidate all parsed rows for duplicates
const revalidateParsedData = () => {
	const seenNipsInBatch = new Set();
	const existingSystemNips = new Set(lecturers.value.map((l) => l.nip.toLowerCase().trim()));

	parsedData.value.forEach((item) => {
		const nipClean = (item.nip || '').trim();
		const nipLower = nipClean.toLowerCase();

		if (!nipClean || nipClean === '-' || !item.name || item.name === 'Tanpa Nama') {
			item.isDuplicate = false;
			item.duplicateReason = '';
			item.isValid = false;
			return;
		}

		if (existingSystemNips.has(nipLower)) {
			item.isDuplicate = true;
			item.duplicateReason = 'NIP sudah terdaftar di sistem';
			item.isValid = false;
		} else if (seenNipsInBatch.has(nipLower)) {
			item.isDuplicate = true;
			item.duplicateReason = 'NIP duplikat di dalam file';
			item.isValid = false;
		} else {
			item.isDuplicate = false;
			item.duplicateReason = '';
			item.isValid = true;
			seenNipsInBatch.add(nipLower);
		}
	});
};

const duplicateCount = computed(() => parsedData.value.filter((p) => p.isDuplicate).length);
const incompleteCount = computed(() => parsedData.value.filter((p) => !p.isValid && !p.isDuplicate).length);
const validCount = computed(() => parsedData.value.filter((p) => p.isValid && !p.isDuplicate).length);

// Download Real Excel (.xlsx) Template
const downloadExcelTemplate = () => {
	const sampleData = [
		{
			'NIP': 'J0403231088',
			'Nama Dosen': 'Dr. Ir. Budi Santoso M.Sc.',
			'Email': 'budi.santoso@apps.ipb.ac.id',
			'Nomor Handphone': '+62 812 3456 7890',
		},
		{
			'NIP': 'J0403231099',
			'Nama Dosen': 'Siti Aminah M.Kom.',
			'Email': 'siti.aminah@apps.ipb.ac.id',
			'Nomor Handphone': '+62 813 9876 5432',
		},
		{
			'NIP': 'J0403231105',
			'Nama Dosen': 'Hendra Setiawan Ph.D.',
			'Email': 'hendra.s@apps.ipb.ac.id',
			'Nomor Handphone': '+62 815 6789 0123',
		},
	];

	const worksheet = XLSX.utils.json_to_sheet(sampleData);

	worksheet['!cols'] = [
		{ wch: 18 }, // NIP
		{ wch: 32 }, // Nama Dosen
		{ wch: 32 }, // Email
		{ wch: 22 }, // Nomor Handphone
	];

	const workbook = XLSX.utils.book_new();
	XLSX.utils.book_append_sheet(workbook, worksheet, 'Template Data Dosen');
	XLSX.writeFile(workbook, 'template_import_dosen.xlsx');
};

// Process Excel (.xlsx, .xls) and .csv Files
const processFile = async (file) => {
	importErrorMessage.value = '';
	editingRowIndex.value = null;
	if (!file) return;

	const validExtensions = ['.xlsx', '.xls', '.csv', '.txt'];
	const fileName = file.name.toLowerCase();
	const hasValidExt = validExtensions.some((ext) => fileName.endsWith(ext));

	if (!hasValidExt) {
		importErrorMessage.value = 'Format file tidak didukung. Harap unggah file Excel (.xlsx, .xls) atau .csv';
		return;
	}

	selectedFile.value = file;

	try {
		const buffer = await file.arrayBuffer();
		const workbook = XLSX.read(buffer, { type: 'array' });
		const firstSheetName = workbook.SheetNames[0];

		if (!firstSheetName) {
			importErrorMessage.value = 'File Excel tidak memiliki lembar kerja (worksheet).';
			parsedData.value = [];
			return;
		}

		const worksheet = workbook.Sheets[firstSheetName];
		const rawRows = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: '' });

		if (!rawRows || rawRows.length < 2) {
			importErrorMessage.value = 'File kosong atau tidak memiliki baris data setelah baris judul kolom.';
			parsedData.value = [];
			return;
		}

		// Detect header indexes
		const headers = rawRows[0].map((h) => String(h).toLowerCase().trim());
		const nipIdx = headers.findIndex((h) => h.includes('nip'));
		const nameIdx = headers.findIndex((h) => h.includes('nama') || h.includes('name') || h.includes('dosen'));
		const emailIdx = headers.findIndex((h) => h.includes('email') || h.includes('surel'));
		const phoneIdx = headers.findIndex((h) => h.includes('hp') || h.includes('handphone') || h.includes('phone') || h.includes('telp') || h.includes('nomor') || h.includes('wa'));

		const rows = [];
		for (let i = 1; i < rawRows.length; i++) {
			const row = rawRows[i];
			if (!row || row.every((c) => String(c).trim() === '')) continue;

			const nip = nipIdx !== -1 ? String(row[nipIdx]).trim() : (row[0] ? String(row[0]).trim() : '');
			const name = nameIdx !== -1 ? String(row[nameIdx]).trim() : (row[1] ? String(row[1]).trim() : '');
			const email = emailIdx !== -1 ? String(row[emailIdx]).trim() : (row[2] ? String(row[2]).trim() : '-');
			const phone = phoneIdx !== -1 ? String(row[phoneIdx]).trim() : (row[3] ? String(row[3]).trim() : '-');

			if (nip || name) {
				rows.push({
					nip: nip || '-',
					name: name || 'Tanpa Nama',
					email: email || '-',
					phone: phone || '-',
					isDuplicate: false,
					duplicateReason: '',
					isValid: false,
				});
			}
		}

		if (rows.length === 0) {
			importErrorMessage.value = 'Tidak ada baris data yang terbaca dari file.';
		}
		parsedData.value = rows;
		revalidateParsedData();
	} catch (err) {
		console.error(err);
		importErrorMessage.value = 'Gagal membaca file Excel. Pastikan file tidak rusak dan sesuai format template.';
	}
};

const handleFileSelect = (e) => {
	const file = e.target.files?.[0];
	if (file) {
		processFile(file);
	}
	if (fileInputRef.value) {
		fileInputRef.value.value = '';
	}
};

const handleDrop = (e) => {
	isDragging.value = false;
	const file = e.dataTransfer?.files?.[0];
	if (file) {
		processFile(file);
	}
};

const removeSelectedFile = () => {
	selectedFile.value = null;
	parsedData.value = [];
	editingRowIndex.value = null;
	importErrorMessage.value = '';
};

// Preview Row Actions (Edit / Delete / Delete All Duplicates)
const startEditPreviewRow = (idx) => {
	editingRowIndex.value = idx;
	const row = parsedData.value[idx];
	editRowForm.value = {
		nip: row.nip !== '-' ? row.nip : '',
		name: row.name !== 'Tanpa Nama' ? row.name : '',
		email: row.email !== '-' ? row.email : '',
		phone: row.phone !== '-' ? row.phone : '',
	};
};

const saveEditPreviewRow = (idx) => {
	parsedData.value[idx] = {
		...parsedData.value[idx],
		nip: editRowForm.value.nip.trim() || '-',
		name: editRowForm.value.name.trim() || 'Tanpa Nama',
		email: editRowForm.value.email.trim() || '-',
		phone: editRowForm.value.phone.trim() || '-',
	};
	editingRowIndex.value = null;
	revalidateParsedData();
};

const cancelEditPreviewRow = () => {
	editingRowIndex.value = null;
};

const removePreviewRow = (idx) => {
	parsedData.value.splice(idx, 1);
	if (editingRowIndex.value === idx) {
		editingRowIndex.value = null;
	}
	revalidateParsedData();
};

const removeAllIssues = () => {
	parsedData.value = parsedData.value.filter((p) => p.isValid && !p.isDuplicate);
	editingRowIndex.value = null;
	revalidateParsedData();
};

// Confirm Import Valid Items
const confirmImport = () => {
	const validItems = parsedData.value.filter((p) => p.isValid && !p.isDuplicate);
	if (validItems.length === 0) return;

	const newLecturers = validItems.map((item, idx) => ({
		id: Date.now() + idx,
		nip: item.nip,
		name: item.name,
		email: item.email,
		phone: item.phone,
	}));

	lecturers.value.unshift(...newLecturers);
	currentPage.value = 1;
	closeImportModal();
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
						@click="openImportModal"
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
						@click="openModal()"
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
								<td class="px-3 py-2.5 text-center" :title="lecturer.email">
									<a
										v-if="lecturer.email && lecturer.email !== '-'"
										:href="`mailto:${lecturer.email}`"
										class="block truncate text-[#2a68c4] underline decoration-[#2a68c4] transition hover:text-[#1d4d96]"
									>
										{{ lecturer.email }}
									</a>
									<span v-else class="block truncate">-</span>
								</td>
								<td class="px-3 py-2.5 text-center" :title="lecturer.phone">
									<span class="block truncate">{{ lecturer.phone }}</span>
								</td>
								<td class="px-3 py-2.5 text-center">
									<div class="flex items-center justify-center gap-2">
										<EditButtonTable :label="`Edit ${lecturer.name}`" @click="openModal(lecturer)" />
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

				<!-- Pagination Section (Matching Image 1) -->
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
						<select
							v-model.number="rowsPerPage"
							class="h-8 rounded-[6px] border border-[#d6e0ee] bg-white py-0 pl-2.5 pr-7 font-inter text-[13px] font-medium text-[#173a63] focus:border-[#183669] focus:outline-none focus:ring-1 focus:ring-[#183669]"
						>
							<option :value="5">5</option>
							<option :value="10">10</option>
							<option :value="20">20</option>
							<option :value="50">50</option>
						</select>
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

						<!-- Page Numbers -->
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
								<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
							</svg>
						</button>
					</div>
				</div>
			</div>
		</section>

		<!-- ========================================== -->
		<!-- MODAL POPUP FORM TAMBAH / EDIT DOSEN        -->
		<!-- ========================================== -->
		<div
			v-if="isModalOpen"
			class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/40 p-4 transition-all"
			@click.self="closeModal"
		>
			<div class="w-full max-w-[500px] transform rounded-[10px] bg-white p-7 shadow-2xl transition-all sm:p-9 font-poppins">
				<h2 class="text-center text-[22px] font-bold text-[#183669]">
					{{ isEditing ? 'Form Edit Dosen' : 'Form Tambah Dosen' }}
				</h2>

				<!-- Error alert if duplicate NIP or invalid -->
				<div v-if="formError" class="mt-4 rounded-[8px] bg-red-50 p-3 font-inter text-[12px] text-red-600">
					{{ formError }}
				</div>

				<form @submit.prevent="submitForm" class="mt-4 space-y-4 font-poppins">
					<!-- NIP Input -->
					<div>
						<label class="block text-[14px] font-bold text-[#183669]">
							NIP<span class="text-red-500">*</span>
						</label>
						<p class="font-inter text-[11px] text-[#7188a3]">Masukkan Nomor Induk Pegawai (NIP)</p>
						<input
							v-model="form.nip"
							type="text"
							placeholder="E14XXXXXX"
							required
							class="mt-1.5 h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-1 focus:ring-[#183669]"
						/>
					</div>

					<!-- Nama Input -->
					<div>
						<label class="block text-[14px] font-bold text-[#183669]">
							Nama<span class="text-red-500">*</span>
						</label>
						<p class="font-inter text-[11px] text-[#7188a3]">Masukan Nama Dosen</p>
						<input
							v-model="form.name"
							type="text"
							placeholder="John Doe"
							required
							class="mt-1.5 h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-1 focus:ring-[#183669]"
						/>
					</div>

					<!-- Email Input -->
					<div>
						<label class="block text-[14px] font-bold text-[#183669]">
							Email
						</label>
						<p class="font-inter text-[11px] text-[#7188a3]">Masukkan email aktif Dosen (optional)</p>
						<input
							v-model="form.email"
							type="text"
							placeholder="example@email.com"
							class="mt-1.5 h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-1 focus:ring-[#183669]"
						/>
					</div>

					<!-- Nomor Handphone Input -->
					<div>
						<label class="block text-[14px] font-bold text-[#183669]">
							Nomor Handphone
						</label>
						<p class="font-inter text-[11px] text-[#7188a3]">Masukkan nomor aktif Dosen (optional)</p>
						<input
							v-model="form.phone"
							type="text"
							placeholder="+62 8XX - XXXX - XXXX"
							class="mt-1.5 h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-1 focus:ring-[#183669]"
						/>
					</div>

					<!-- Action Buttons -->
					<div class="mt-7 flex items-center justify-center gap-4 pt-2">
						<button
							type="button"
							@click="closeModal"
							class="h-[44px] min-w-[140px] px-6 rounded-[10px] border-2 border-[#d6e0ee] bg-white font-poppins text-[15px] font-bold text-[#183669] transition hover:border-[#183669] hover:bg-slate-50 focus:border-[#183669] focus:outline-none active:border-[#183669]"
						>
							Kembali
						</button>
						<button
							type="submit"
							class="h-[44px] min-w-[140px] px-6 rounded-[10px] bg-[#183669] font-poppins text-[15px] font-bold text-white transition hover:bg-[#122b54]"
						>
							{{ isEditing ? 'Simpan' : 'Tambah' }}
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- ========================================== -->
		<!-- MODAL POPUP IMPORT DATA DOSEN (BULK CREATE)-->
		<!-- ========================================== -->
		<div
			v-if="isImportModalOpen"
			class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/40 p-4 transition-all"
			@click.self="closeImportModal"
		>
			<div class="w-full max-w-[720px] transform rounded-[12px] bg-white p-6 shadow-2xl transition-all sm:p-8 font-poppins">
				<!-- Modal Header -->
				<div class="text-center">
					<h2 class="text-[22px] font-bold text-[#183669]">Import Data Dosen</h2>
					<p class="mt-1 font-inter text-[13px] text-[#7188a3]">
						Unggah file Excel (.xlsx, .xls) atau CSV untuk menambahkan data dosen sekaligus.
					</p>
				</div>

				<!-- Template Download Information Card (Hanya Tampil Saat File Belum Dimasukkan) -->
				<div
					v-if="!selectedFile"
					class="mt-5 flex flex-col gap-2.5 rounded-[10px] border border-[#d6e0ee] bg-[#f7f9fd] p-3.5 sm:flex-row sm:items-center sm:justify-between"
				>
					<div class="flex items-center gap-2.5">
						<div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1b8755]/15 text-[#1b8755]">
							<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
								<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm1.5 12-1.9 3h-1.6l1.9-3-1.9-3h1.6l1.9 3 1.9-3h1.6l-1.9 3 1.9 3h-1.6l-1.9-3zM13 9V3.5L18.5 9H13z"/>
							</svg>
						</div>
						<div>
							<p class="font-poppins text-[13px] font-semibold text-[#183669]">Template Format Excel</p>
							<p class="font-inter text-[11px] text-[#7188a3]">Kolom: NIP (Unik), Nama Dosen, Email, Nomor Handphone</p>
						</div>
					</div>
					<button
						type="button"
						@click="downloadExcelTemplate"
						class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-[8px] bg-[#1b8755] px-3.5 py-2 font-poppins text-[12px] font-semibold text-white shadow-sm transition hover:bg-[#156e45]"
					>
						<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
						</svg>
						<span>Download Template Excel</span>
					</button>
				</div>

				<!-- Drag and Drop Zone (Shown when no file is selected) -->
				<div
					v-if="!selectedFile"
					@dragover.prevent="isDragging = true"
					@dragleave.prevent="isDragging = false"
					@drop.prevent="handleDrop"
					:class="[
						'mt-4 flex flex-col items-center justify-center rounded-[12px] border-2 border-dashed p-8 text-center transition-colors',
						isDragging ? 'border-[#183669] bg-[#183669]/5' : 'border-[#183669]/30 bg-[#fafcff] hover:border-[#183669]/60'
					]"
				>
					<svg class="h-12 w-12 text-[#8c9eb5]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
					</svg>
					<p class="mt-2 font-inter text-[13px] font-medium text-[#7188a3]">
						Upload file Excel atau seret file ke form ini
					</p>
					<p class="mt-0.5 font-inter text-[11px] text-[#a0aec0]">
						Mendukung format file Excel (.xlsx, .xls) dan .csv
					</p>

					<input
						type="file"
						ref="fileInputRef"
						accept=".xlsx,.xls,.csv,.txt"
						class="hidden"
						@change="handleFileSelect"
					/>
					<button
						type="button"
						@click="$refs.fileInputRef.click()"
						class="mt-3.5 rounded-[8px] border border-[#a6b7cb] bg-white px-6 py-2 font-inter text-[13px] font-semibold text-[#5a718d] transition hover:bg-slate-50"
					>
						Upload
					</button>
				</div>

				<!-- Selected File Details & Data Preview -->
				<div v-else class="mt-4 space-y-3">
					<!-- File Card Banner -->
					<div class="flex items-center justify-between rounded-[10px] border border-[#d6e0ee] bg-[#f7f9fd] p-3">
						<div class="flex items-center gap-3">
							<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[8px] bg-[#1b8755]/15 text-[#1b8755]">
								<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
									<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm1.5 12-1.9 3h-1.6l1.9-3-1.9-3h1.6l1.9 3 1.9-3h1.6l-1.9 3 1.9 3h-1.6l-1.9-3zM13 9V3.5L18.5 9H13z"/>
								</svg>
							</div>
							<div class="min-w-0">
								<p class="truncate font-poppins text-[13px] font-semibold text-[#183669]">{{ selectedFile.name }}</p>
								<p class="font-inter text-[11px] text-[#7188a3]">
									{{ (selectedFile.size / 1024).toFixed(1) }} KB • {{ parsedData.length }} baris data terbaca
								</p>
							</div>
						</div>
						<button
							type="button"
							@click="removeSelectedFile"
							class="text-xs font-semibold text-red-500 transition hover:text-red-700"
						>
							Ganti File
						</button>
					</div>

					<!-- Data Issue Detection Alert Banner (Duplikat & Tidak Lengkap) -->
					<div
						v-if="duplicateCount > 0 || incompleteCount > 0"
						class="flex flex-col gap-2 rounded-[10px] border border-amber-300 bg-amber-50 p-3 sm:flex-row sm:items-center sm:justify-between font-inter text-[12px] text-amber-900"
					>
						<div class="flex items-center gap-2">
							<svg class="h-4 w-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
							</svg>
							<span>
								<template v-if="duplicateCount > 0 && incompleteCount > 0">
									Terdeteksi <strong>{{ duplicateCount }} data duplikat</strong> dan <strong>{{ incompleteCount }} data tidak lengkap</strong>.
								</template>
								<template v-else-if="duplicateCount > 0">
									Terdeteksi <strong>{{ duplicateCount }} data dengan NIP duplikat</strong>.
								</template>
								<template v-else>
									Terdeteksi <strong>{{ incompleteCount }} data tidak lengkap</strong> (NIP/Nama kosong).
								</template>
								Anda dapat mengedit atau menghapus data bermasalah.
							</span>
						</div>
						<button
							type="button"
							@click="removeAllIssues"
							class="inline-flex shrink-0 items-center justify-center rounded-[6px] bg-amber-200/80 px-2.5 py-1 font-semibold text-amber-900 transition hover:bg-amber-300"
						>
							Hapus Data Bermasalah
						</button>
					</div>

					<!-- Error Message Alert -->
					<div v-if="importErrorMessage" class="rounded-[8px] bg-red-50 p-3 font-inter text-[12px] text-red-600">
						{{ importErrorMessage }}
					</div>

					<!-- Preview Table Section -->
					<div v-if="parsedData.length > 0" class="space-y-1.5">
						<div class="flex items-center justify-between">
							<p class="font-poppins text-[13px] font-semibold text-[#183669]">Preview Data yang Akan Masuk:</p>
							<span class="font-inter text-[12px] font-semibold text-[#1b8755]">
								{{ validCount }} dari {{ parsedData.length }} data siap diimpor
							</span>
						</div>

						<div class="max-h-[260px] overflow-y-auto rounded-[8px] border border-[#d6e0ee]">
							<table class="w-full text-left text-xs">
								<thead class="sticky top-0 bg-[#183669] font-poppins text-white">
									<tr>
										<th class="w-8 px-2.5 py-2 text-center">No</th>
										<th class="px-3 py-2">Nama Dosen</th>
										<th class="px-3 py-2 text-center">NIP</th>
										<th class="px-3 py-2">Email</th>
										<th class="px-3 py-2 text-center">No. HP</th>
										<th class="w-24 px-2 py-2 text-center">Status</th>
										<th class="w-20 px-2 py-2 text-center">Aksi</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-[#d6e0ee] bg-white font-inter text-[#435b76]">
									<tr
										v-for="(item, idx) in parsedData"
										:key="idx"
										:class="[
											'transition-colors',
											item.isDuplicate ? 'bg-red-50/60 hover:bg-red-50' : 'hover:bg-slate-50'
										]"
									>
										<!-- Row In Normal Mode -->
										<template v-if="editingRowIndex !== idx">
											<td class="px-2.5 py-2 text-center font-medium">{{ idx + 1 }}</td>
											<td class="px-3 py-2 font-medium text-[#2f4b6e]" :title="item.name">
												<span class="block truncate max-w-[150px]">{{ item.name }}</span>
											</td>
											<td class="px-3 py-2 text-center font-mono font-medium" :title="item.nip">
												{{ item.nip }}
											</td>
											<td class="px-3 py-2" :title="item.email">
												<span class="block truncate max-w-[140px]">{{ item.email }}</span>
											</td>
											<td class="px-3 py-2 text-center">
												{{ item.phone }}
											</td>
											<td class="px-2 py-2 text-center">
												<span
													v-if="item.isDuplicate"
													:title="item.duplicateReason"
													class="inline-block rounded bg-red-100 px-1.5 py-0.5 text-[10px] font-bold text-red-700"
												>
													Duplikat
												</span>
												<span
													v-else-if="item.isValid"
													class="inline-block rounded bg-green-100 px-1.5 py-0.5 text-[10px] font-bold text-green-700"
												>
													Valid
												</span>
												<span
													v-else
													class="inline-block rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-bold text-gray-600"
												>
													Tidak Lengkap
												</span>
											</td>
											<td class="px-2 py-2 text-center">
												<div class="flex items-center justify-center gap-1.5">
													<!-- Edit Row Button -->
													<button
														type="button"
														@click="startEditPreviewRow(idx)"
														title="Edit baris data ini"
														class="rounded bg-[#ffd56a] p-1 text-[#b57a00] hover:bg-[#ffcc54] transition"
													>
														<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
														</svg>
													</button>
													<!-- Delete Row Button -->
													<button
														type="button"
														@click="removePreviewRow(idx)"
														title="Hapus baris data ini"
														class="rounded bg-[#ff9ca1] p-1 text-[#d61f25] hover:bg-[#ff888e] transition"
													>
														<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
														</svg>
													</button>
												</div>
											</td>
										</template>

										<!-- Row In Inline Edit Mode -->
										<template v-else>
											<td class="px-2.5 py-1 text-center font-medium">{{ idx + 1 }}</td>
											<td class="px-2 py-1">
												<input
													v-model="editRowForm.name"
													type="text"
													placeholder="Nama"
													class="h-7 w-full rounded border border-[#183669] px-2 text-xs focus:outline-none"
												/>
											</td>
											<td class="px-2 py-1">
												<input
													v-model="editRowForm.nip"
													type="text"
													placeholder="NIP"
													class="h-7 w-full rounded border border-[#183669] px-2 text-xs font-mono focus:outline-none"
												/>
											</td>
											<td class="px-2 py-1">
												<input
													v-model="editRowForm.email"
													type="text"
													placeholder="Email"
													class="h-7 w-full rounded border border-[#d6e0ee] px-2 text-xs focus:outline-none"
												/>
											</td>
											<td class="px-2 py-1">
												<input
													v-model="editRowForm.phone"
													type="text"
													placeholder="No. HP"
													class="h-7 w-full rounded border border-[#d6e0ee] px-2 text-xs focus:outline-none"
												/>
											</td>
											<td class="px-1 py-1 text-center text-[10px] text-[#7188a3]">
												Mengedit
											</td>
											<td class="px-1 py-1 text-center">
												<div class="flex items-center justify-center gap-1">
													<!-- Save Button -->
													<button
														type="button"
														@click="saveEditPreviewRow(idx)"
														title="Simpan perubahan"
														class="rounded bg-green-500 p-1 text-white hover:bg-green-600 transition"
													>
														<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
														</svg>
													</button>
													<!-- Cancel Button -->
													<button
														type="button"
														@click="cancelEditPreviewRow"
														title="Batal edit"
														class="rounded bg-gray-300 p-1 text-gray-700 hover:bg-gray-400 transition"
													>
														<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
														</svg>
													</button>
												</div>
											</td>
										</template>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- Modal Footer Buttons with Generous Padding -->
				<div class="mt-7 flex flex-wrap items-center justify-center gap-4 pt-2">
					<button
						type="button"
						@click="closeImportModal"
						class="h-[44px] min-w-[140px] px-6 rounded-[10px] border-2 border-[#d6e0ee] bg-white font-poppins text-[14px] font-bold text-[#183669] transition hover:border-[#183669] hover:bg-slate-50 focus:border-[#183669] focus:outline-none active:border-[#183669]"
					>
						Kembali
					</button>
					<button
						type="button"
						@click="confirmImport"
						:disabled="validCount === 0"
						class="h-[44px] min-w-[180px] px-6 rounded-[10px] bg-[#183669] font-poppins text-[14px] font-bold text-white transition hover:bg-[#122b54] disabled:cursor-not-allowed disabled:opacity-50"
					>
						Tambahkan ({{ validCount }})
					</button>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
