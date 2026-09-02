<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import * as XLSX from 'xlsx';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';

const props = defineProps({
	show: {
		type: Boolean,
		default: false,
	},
	existingLecturers: {
		type: Array,
		default: () => [],
	},
});

const emit = defineEmits(['close', 'import']);

const selectedFile = ref(null);
const parsedData = ref([]);
const isDragging = ref(false);
const importErrorMessage = ref('');
const fileInputRef = ref(null);

// Inline edit in preview table
const editingRowIndex = ref(null);
const editRowForm = ref({ nip: '', name: '', username: '', password: '', email: '', phone: '' });

// Reset state when modal is opened or closed
watch(
	() => props.show,
	(isOpen) => {
		if (isOpen) {
			selectedFile.value = null;
			parsedData.value = [];
			importErrorMessage.value = '';
			editingRowIndex.value = null;
			isDragging.value = false;
		}
	}
);

const handleClose = () => {
	emit('close');
};

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

// Revalidate all parsed rows for duplicates & completeness (only NIP and Username cannot be duplicated)
const revalidateParsedData = () => {
	const nipCounts = {};
	const usernameCounts = {};

	const existingSystemNips = new Set(
		props.existingLecturers
			.map((l) => (l.nip || '').toString().toLowerCase().trim())
			.filter((n) => n && n !== '-')
	);
	const existingSystemUsernames = new Set(
		props.existingLecturers
			.map((l) => (l.username || '').toString().toLowerCase().trim())
			.filter((u) => u && u !== '-')
	);

	// First pass: count occurrences within the batch so all duplicate rows get flagged
	parsedData.value.forEach((item) => {
		const nipClean = (item.nip || '').toString().trim().toLowerCase();
		const usernameClean = (item.username || '').toString().trim().toLowerCase();

		if (nipClean && nipClean !== '-') {
			nipCounts[nipClean] = (nipCounts[nipClean] || 0) + 1;
		}
		if (usernameClean && usernameClean !== '-') {
			usernameCounts[usernameClean] = (usernameCounts[usernameClean] || 0) + 1;
		}
	});

	// Second pass: mark duplicates and validity
	parsedData.value.forEach((item) => {
		const nipClean = (item.nip || '').toString().trim();
		const nameClean = (item.name || '').toString().trim();
		const usernameClean = (item.username || '').toString().trim();
		const nipLower = nipClean.toLowerCase();
		const usernameLower = usernameClean.toLowerCase();

		item.isNipDuplicate = false;
		item.isUsernameDuplicate = false;
		item.isDuplicate = false;
		item.duplicateReason = '';
		item.isValid = false;

		if (!nipClean || nipClean === '-' || !nameClean || nameClean === 'Tanpa Nama') {
			return;
		}

		// Cek NIP duplikat (ke sistem atau sesama baris tabel)
		if (existingSystemNips.has(nipLower)) {
			item.isNipDuplicate = true;
			item.nipDuplicateReason = 'NIP sudah terdaftar di sistem';
		} else if (nipCounts[nipLower] > 1) {
			item.isNipDuplicate = true;
			item.nipDuplicateReason = 'NIP duplikat dengan baris lain di tabel';
		}

		// Cek Username duplikat (ke sistem atau sesama baris tabel)
		if (usernameClean && usernameClean !== '-') {
			if (existingSystemUsernames.has(usernameLower)) {
				item.isUsernameDuplicate = true;
				item.usernameDuplicateReason = 'Username sudah terdaftar di sistem';
			} else if (usernameCounts[usernameLower] > 1) {
				item.isUsernameDuplicate = true;
				item.usernameDuplicateReason = 'Username duplikat dengan baris lain di tabel';
			}
		}

		if (item.isNipDuplicate && item.isUsernameDuplicate) {
			item.isDuplicate = true;
			item.duplicateReason = 'NIP & Username duplikat';
		} else if (item.isNipDuplicate) {
			item.isDuplicate = true;
			item.duplicateReason = item.nipDuplicateReason || 'NIP duplikat';
		} else if (item.isUsernameDuplicate) {
			item.isDuplicate = true;
			item.duplicateReason = item.usernameDuplicateReason || 'Username duplikat';
		} else {
			item.isValid = true;
		}
	});
};

const duplicateCount = computed(() => parsedData.value.filter((p) => p.isDuplicate).length);
const incompleteCount = computed(() => parsedData.value.filter((p) => !p.isValid && !p.isDuplicate).length);
const validCount = computed(() => parsedData.value.filter((p) => p.isValid && !p.isDuplicate).length);

const hasNipDuplicate = computed(() => parsedData.value.some((p) => p.isNipDuplicate));
const hasUsernameDuplicate = computed(() => parsedData.value.some((p) => p.isUsernameDuplicate));

// Download Real Excel (.xlsx) Template
const downloadExcelTemplate = () => {
	const sampleData = [
		{
			'NIP': 'J0403231088',
			'Nama Dosen': 'Dr. Ir. Budi Santoso M.Sc.',
			'Username': 'budisantoso',
			'Password': 'J0403231088',
			'Email': 'budi.santoso@apps.ipb.ac.id',
			'Nomor Handphone': '+62 812-3456-7890',
		},
		{
			'NIP': 'J0403231099',
			'Nama Dosen': 'Siti Aminah M.Kom.',
			'Username': 'sitiaminah',
			'Password': 'J0403231099',
			'Email': 'siti.aminah@apps.ipb.ac.id',
			'Nomor Handphone': '+62 813-9876-5432',
		},
		{
			'NIP': 'J0403231105',
			'Nama Dosen': 'Hendra Setiawan Ph.D.',
			'Username': 'hendrasetiawan',
			'Password': 'J0403231105',
			'Email': 'hendra.s@apps.ipb.ac.id',
			'Nomor Handphone': '+62 815-6789-0123',
		},
	];

	const worksheet = XLSX.utils.json_to_sheet(sampleData);

	worksheet['!cols'] = [
		{ wch: 18 }, // NIP
		{ wch: 32 }, // Nama Dosen
		{ wch: 20 }, // Username
		{ wch: 20 }, // Password
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
		const usernameIdx = headers.findIndex((h) => h.includes('username') || h.includes('user'));
		const passwordIdx = headers.findIndex((h) => h.includes('password') || h.includes('sandi') || h.includes('pass'));
		const emailIdx = headers.findIndex((h) => h.includes('email') || h.includes('surel'));
		const phoneIdx = headers.findIndex((h) => h.includes('hp') || h.includes('handphone') || h.includes('phone') || h.includes('telp') || h.includes('nomor') || h.includes('wa'));

		const rows = [];
		for (let i = 1; i < rawRows.length; i++) {
			const row = rawRows[i];
			if (!row || row.every((c) => String(c).trim() === '')) continue;

			const nip = nipIdx !== -1 ? String(row[nipIdx]).trim() : (row[0] ? String(row[0]).trim() : '');
			const name = nameIdx !== -1 ? String(row[nameIdx]).trim() : (row[1] ? String(row[1]).trim() : '');
			const username = usernameIdx !== -1 ? String(row[usernameIdx]).trim() : '';
			const password = passwordIdx !== -1 ? String(row[passwordIdx]).trim() : '';
			const email = emailIdx !== -1 ? String(row[emailIdx]).trim() : (row[4] ? String(row[4]).trim() : '');
			const phone = phoneIdx !== -1 ? String(row[phoneIdx]).trim() : (row[5] ? String(row[5]).trim() : '');

			if (nip || name) {
				rows.push({
					nip: nip || '-',
					name: name || 'Tanpa Nama',
					username: username || '-',
					password: password || '',
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

// Preview Row Actions (Edit / Delete / Clean Issues)
const startEditPreviewRow = (idx) => {
	editingRowIndex.value = idx;
	const row = parsedData.value[idx];
	editRowForm.value = {
		nip: row.nip !== '-' ? row.nip : '',
		name: row.name !== 'Tanpa Nama' ? row.name : '',
		username: row.username !== '-' ? row.username : '',
		password: row.password || '',
		email: row.email !== '-' ? row.email : '',
		phone: row.phone !== '-' ? row.phone : '',
	};
};

const saveEditPreviewRow = (idx) => {
	parsedData.value[idx] = {
		...parsedData.value[idx],
		nip: editRowForm.value.nip.trim() || '-',
		name: editRowForm.value.name.trim() || 'Tanpa Nama',
		username: editRowForm.value.username.trim() || '-',
		password: editRowForm.value.password.trim() || '',
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

	const formattedLecturers = validItems.map((item, idx) => ({
		id: Date.now() + idx,
		nip: item.nip,
		name: item.name,
		username: item.username && item.username !== '-' ? item.username : null,
		password: item.password && item.password !== '-' ? item.password : item.nip,
		email: item.email && item.email !== '-' ? item.email : null,
		phone: item.phone && item.phone !== '-' ? item.phone : null,
	}));

	emit('import', formattedLecturers);
	handleClose();
};

const isBackdropClick = ref(false);

const handleBackdropMouseDown = (e) => {
	isBackdropClick.value = e.target === e.currentTarget;
};

const handleBackdropMouseUp = (e) => {
	if (isBackdropClick.value && e.target === e.currentTarget) {
		handleClose();
	}
	isBackdropClick.value = false;
};
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
				class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/40 backdrop-blur-sm p-4"
				@mousedown="handleBackdropMouseDown"
				@mouseup="handleBackdropMouseUp"
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
						class="w-full max-w-[880px] transform rounded-[18px] bg-white p-6 shadow-2xl font-poppins border border-[#e2e8f0] sm:p-8"
					>
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
						<p class="font-poppins text-[13px] font-semibold text-[#183669]">Template Format Excel (Dengan Contoh)</p>
						<p class="font-inter text-[11px] text-[#7188a3]">Kolom: NIP (Wajib), Nama Dosen (Wajib), Username, Password, Email, No. HP</p>
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

				<!-- Validation Summary Banner (If any invalid/duplicate items) -->
				<div
					v-if="duplicateCount > 0 || incompleteCount > 0"
					class="flex items-center justify-between rounded-[10px] border border-amber-300 bg-amber-50/80 p-3 font-inter text-[12px] text-amber-900"
				>
					<div class="flex items-center gap-2">
						<svg class="h-5 w-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
						</svg>
						<span>
							<template v-if="duplicateCount > 0">
								Terdeteksi
								<strong v-if="hasNipDuplicate && hasUsernameDuplicate"> {{ duplicateCount }} data dengan NIP & Username duplikat</strong>
								<strong v-else-if="hasNipDuplicate"> {{ duplicateCount }} data dengan NIP duplikat</strong>
								<strong v-else-if="hasUsernameDuplicate"> {{ duplicateCount }} data dengan Username duplikat</strong>
								<strong v-else> {{ duplicateCount }} data duplikat</strong><template v-if="incompleteCount > 0"> dan <strong>{{ incompleteCount }} data tidak lengkap</strong></template>.
							</template>
							<template v-else>
								Terdeteksi <strong>{{ incompleteCount }} data tidak lengkap</strong> (NIP/Nama kosong).
							</template>
							Anda dapat mengedit atau membersihkan data bermasalah.
						</span>
					</div>
					<button
						type="button"
						@click="removeAllIssues"
						class="inline-flex shrink-0 items-center justify-center rounded-[6px] bg-amber-200/80 px-2.5 py-1 font-semibold text-amber-900 transition hover:bg-amber-300"
					>
						Bersihkan Data Bermasalah
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

					<div class="max-h-[290px] overflow-auto rounded-[10px] border border-[#d6e0ee] shadow-sm">
						<table class="w-full min-w-[880px] border-separate border-spacing-0 text-left text-xs">
							<thead class="sticky top-0 z-20 bg-[#183669] font-poppins text-white shadow-sm">
								<tr>
									<th class="w-10 px-3 py-2.5 text-center">No</th>
									<th class="min-w-[150px] px-3.5 py-2.5">Nama Dosen</th>
									<th class="min-w-[120px] px-3 py-2.5 text-center">NIP</th>
									<th class="min-w-[110px] px-3 py-2.5">Username</th>
									<th class="min-w-[120px] px-3 py-2.5">Password</th>
									<th class="min-w-[160px] px-3.5 py-2.5">Email</th>
									<th class="min-w-[145px] px-3.5 py-2.5 text-center whitespace-nowrap">No. HP</th>
									<th class="min-w-[100px] px-2.5 py-2.5 text-center">Status</th>
									<th class="sticky right-0 z-30 w-28 bg-[#183669] px-3 py-2.5 text-center shadow-[-4px_0_8px_-2px_rgba(0,0,0,0.18)]">Aksi</th>
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
										<td class="px-3 py-2 text-center font-medium border-b border-[#d6e0ee]">{{ idx + 1 }}</td>
										<td class="px-3.5 py-2 font-medium text-[#1e3456] border-b border-[#d6e0ee]" :title="item.name">
											<span class="block truncate max-w-[170px]">{{ item.name }}</span>
										</td>
										<td class="px-3 py-2 text-center font-medium text-[#2f4b6e] border-b border-[#d6e0ee]" :title="item.nip">
											{{ item.nip }}
										</td>
										<td class="px-3 py-2 text-[#5a718d] border-b border-[#d6e0ee]" :title="item.username">
											<span class="block truncate max-w-[110px]">{{ item.username }}</span>
										</td>
										<td class="px-3 py-2 text-[#5a718d] border-b border-[#d6e0ee]" :title="item.password || item.nip">
											<span class="block truncate max-w-[110px]">{{ item.password || item.nip }}</span>
										</td>
										<td class="px-3.5 py-2 text-[#5a718d] border-b border-[#d6e0ee]" :title="item.email">
											<span class="block truncate max-w-[160px]">{{ item.email }}</span>
										</td>
										<td class="px-3.5 py-2 text-center text-[#5a718d] whitespace-nowrap border-b border-[#d6e0ee]">
											{{ item.phone }}
										</td>
										<td class="px-2.5 py-2 text-center border-b border-[#d6e0ee]">
											<span
												v-if="item.isDuplicate"
												:title="item.duplicateReason"
												class="inline-block rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700 cursor-help"
											>
												{{ item.isNipDuplicate && item.isUsernameDuplicate ? 'NIP & User Duplikat' : item.isNipDuplicate ? 'NIP Duplikat' : item.isUsernameDuplicate ? 'Username Duplikat' : 'Duplikat' }}
											</span>
											<span
												v-else-if="item.isValid"
												class="inline-block rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-bold text-green-700"
											>
												Valid
											</span>
											<span
												v-else
												class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600"
											>
												Tidak Lengkap
											</span>
										</td>
										<!-- Sticky Aksi Column -->
										<td
											class="sticky right-0 z-10 w-28 px-3 py-2 text-center border-b border-[#d6e0ee] transition-colors shadow-[-4px_0_8px_-2px_rgba(0,0,0,0.06)]"
											:class="item.isDuplicate ? 'bg-[#fff5f5]' : 'bg-white'"
										>
											<div class="flex items-center justify-center gap-1.5">
												<!-- Edit Row Button -->
												<EditButtonTable
													label="Edit baris data ini"
													@click="startEditPreviewRow(idx)"
												/>
												<!-- Delete Row Button -->
												<DeleteButtonTable
													label="Hapus baris data ini"
													@click="removePreviewRow(idx)"
												/>
											</div>
										</td>
									</template>

									<!-- Row In Inline Edit Mode -->
									<template v-else>
										<td class="px-3 py-1.5 text-center font-medium border-b border-[#d6e0ee] bg-amber-50/40">{{ idx + 1 }}</td>
										<td class="px-2 py-1.5 border-b border-[#d6e0ee] bg-amber-50/40">
											<input
												v-model="editRowForm.name"
												type="text"
												placeholder="Nama"
												class="h-8 w-full rounded-[6px] border border-[#d6e0ee] px-2.5 text-xs transition-colors hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0"
											/>
										</td>
										<td class="px-2 py-1.5 border-b border-[#d6e0ee] bg-amber-50/40">
											<input
												v-model="editRowForm.nip"
												type="text"
												placeholder="NIP"
												class="h-8 w-full rounded-[6px] border border-[#d6e0ee] px-2.5 text-xs transition-colors hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0"
											/>
										</td>
										<td class="px-2 py-1.5 border-b border-[#d6e0ee] bg-amber-50/40">
											<input
												v-model="editRowForm.username"
												type="text"
												placeholder="Username"
												class="h-8 w-full rounded-[6px] border border-[#d6e0ee] px-2.5 text-xs transition-colors hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0"
											/>
										</td>
										<td class="px-2 py-1.5 border-b border-[#d6e0ee] bg-amber-50/40">
											<input
												v-model="editRowForm.password"
												type="text"
												placeholder="Password (default NIP)"
												class="h-8 w-full min-w-[110px] rounded-[6px] border border-[#d6e0ee] px-2.5 text-xs transition-colors hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0"
											/>
										</td>
										<td class="px-2 py-1.5 border-b border-[#d6e0ee] bg-amber-50/40">
											<input
												v-model="editRowForm.email"
												type="text"
												placeholder="Email"
												class="h-8 w-full rounded-[6px] border border-[#d6e0ee] px-2.5 text-xs transition-colors hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0"
											/>
										</td>
										<td class="px-2 py-1.5 border-b border-[#d6e0ee] bg-amber-50/40">
											<input
												v-model="editRowForm.phone"
												type="text"
												placeholder="No. HP"
												class="h-8 w-full min-w-[130px] rounded-[6px] border border-[#d6e0ee] px-2.5 text-xs whitespace-nowrap transition-colors hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0"
											/>
										</td>
										<td class="px-2.5 py-1.5 text-center text-[10px] font-semibold text-[#b57a00] border-b border-[#d6e0ee] bg-amber-50/40">
											Mengedit...
										</td>
										<!-- Sticky Aksi Column Edit Mode -->
										<td class="sticky right-0 z-10 w-28 px-2 py-1.5 text-center border-b border-[#d6e0ee] bg-amber-50/90 shadow-[-4px_0_8px_-2px_rgba(0,0,0,0.06)]">
											<div class="flex items-center justify-center gap-1.5">
												<!-- Save Button -->
												<button
													type="button"
													@click="saveEditPreviewRow(idx)"
													title="Simpan perubahan"
													class="inline-flex h-[32px] w-[32px] shrink-0 items-center justify-center rounded-[8px] bg-green-500 text-white hover:bg-green-600 transition shadow-sm"
												>
													<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
													</svg>
												</button>
												<!-- Cancel Button -->
												<button
													type="button"
													@click="cancelEditPreviewRow"
													title="Batal edit"
													class="inline-flex h-[32px] w-[32px] shrink-0 items-center justify-center rounded-[8px] bg-gray-200 text-gray-700 hover:bg-gray-300 transition"
												>
													<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
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
					@click="handleClose"
					class="h-[44px] min-w-[140px] px-6 rounded-[10px] border-2 border-[#d6e0ee] bg-white font-poppins text-[14px] font-bold text-[#183669] transition hover:border-[#183669] hover:bg-slate-50 focus:border-[#183669] focus:outline-none active:border-[#183669]"
				>
					Batal
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
				</Transition>
			</div>
		</Transition>
	</Teleport>
</template>
