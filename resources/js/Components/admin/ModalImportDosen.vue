<script setup>
import { computed, ref, watch } from 'vue';
import * as XLSX from 'xlsx';

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
const editRowForm = ref({ nip: '', name: '', email: '', phone: '' });

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

// Revalidate all parsed rows for duplicates & completeness
const revalidateParsedData = () => {
	const seenNipsInBatch = new Set();
	const existingSystemNips = new Set(props.existingLecturers.map((l) => l.nip.toLowerCase().trim()));

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

// Preview Row Actions (Edit / Delete / Clean Issues)
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

	const formattedLecturers = validItems.map((item, idx) => ({
		id: Date.now() + idx,
		nip: item.nip,
		name: item.name,
		email: item.email,
		phone: item.phone,
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
	<div
		v-if="show"
		class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/40 p-4 transition-all"
		@mousedown="handleBackdropMouseDown"
		@mouseup="handleBackdropMouseUp"
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
	</div>
</template>
