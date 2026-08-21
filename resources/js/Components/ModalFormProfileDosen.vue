<script setup>
import { computed, nextTick, ref, watch } from 'vue';

const props = defineProps({
	show: {
		type: Boolean,
		default: false,
	},
	isEditing: {
		type: Boolean,
		default: false,
	},
	initialData: {
		type: Object,
		default: () => null,
	},
	availableLecturers: {
		type: Array,
		default: () => [],
	},
	existingProfiles: {
		type: Array,
		default: () => [],
	},
});

const emit = defineEmits(['close', 'submit']);

const divisiOptions = [
	'Perencanaan Kehutanan',
	'Pemanfaatan Sumberdaya Hutan',
	'Kebijakan Kehutanan',
];

const form = ref({
	name: '',
	division: '',
	educations: [
		{ university: '', major: '', graduationYear: '' },
	],
	research: '',
	contact: '',
	image: null,
	imagePreview: null,
	scholarLink: '',
	linkedinLink: '',
});

const formError = ref('');
const isDragging = ref(false);
const imageInputRef = ref(null);

// Custom Dropdowns State
const isNameDropdownOpen = ref(false);
const isDivisionDropdownOpen = ref(false);
const lecturerSearchQuery = ref('');
const lecturerSearchInputRef = ref(null);

// Lecturers list with status (if already has profile)
const selectableLecturers = computed(() => {
	const currentProfileId = props.initialData?.id;
	const takenLecturerNames = new Set(
		props.existingProfiles
			.filter((p) => !props.isEditing || p.id !== currentProfileId)
			.map((p) => p.name.toLowerCase().trim())
	);

	const list = props.availableLecturers.map((lec) => ({
		...lec,
		isTaken: takenLecturerNames.has(lec.name.toLowerCase().trim()),
	}));

	// Urutkan: Dosen yang available (belum punya profil) tampil di paling atas
	return list.sort((a, b) => {
		if (a.isTaken === b.isTaken) return 0;
		return a.isTaken ? 1 : -1;
	});
});

// Filtered lecturers based on search input
const filteredSelectableLecturers = computed(() => {
	const q = lecturerSearchQuery.value.toLowerCase().trim();
	if (!q) return selectableLecturers.value;

	return selectableLecturers.value.filter(
		(l) => l.name.toLowerCase().includes(q) || (l.nip && l.nip.toLowerCase().includes(q))
	);
});

const toggleNameDropdown = () => {
	isNameDropdownOpen.value = !isNameDropdownOpen.value;
	isDivisionDropdownOpen.value = false;
	if (isNameDropdownOpen.value) {
		lecturerSearchQuery.value = '';
		nextTick(() => {
			lecturerSearchInputRef.value?.focus();
		});
	}
};

const selectLecturer = (lec) => {
	if (lec.isTaken) return;
	form.value.name = lec.name;
	if (lec.email && lec.email !== '-') {
		form.value.contact = lec.email;
	} else if (lec.phone && lec.phone !== '-') {
		form.value.contact = lec.phone;
	}
	isNameDropdownOpen.value = false;
	lecturerSearchQuery.value = '';
};

const selectDivision = (div) => {
	form.value.division = div;
	isDivisionDropdownOpen.value = false;
};

watch(
	() => props.show,
	(isOpen) => {
		if (isOpen) {
			formError.value = '';
			isNameDropdownOpen.value = false;
			isDivisionDropdownOpen.value = false;
			lecturerSearchQuery.value = '';

			if (props.isEditing && props.initialData) {
				form.value = {
					name: props.initialData.name || '',
					division: props.initialData.division || divisiOptions[0],
					educations: props.initialData.educations && props.initialData.educations.length > 0
						? JSON.parse(JSON.stringify(props.initialData.educations))
						: [{ university: '', major: '', graduationYear: '' }],
					research: props.initialData.research !== '-' ? props.initialData.research : '',
					contact: props.initialData.contact !== '-' ? props.initialData.contact : '',
					image: props.initialData.image || null,
					imagePreview: props.initialData.imagePreview || props.initialData.image || null,
					scholarLink: props.initialData.scholarLink !== '-' ? props.initialData.scholarLink : '',
					linkedinLink: props.initialData.linkedinLink !== '-' ? props.initialData.linkedinLink : '',
				};
			} else {
				form.value = {
					name: '',
					division: '',
					educations: [
						{ university: '', major: '', graduationYear: '' },
					],
					research: '',
					contact: '',
					image: null,
					imagePreview: null,
					scholarLink: '',
					linkedinLink: '',
				};
			}
		}
	},
	{ immediate: true }
);

// Education Dynamic Rows (Min 1, Max 3)
const addEducationRow = () => {
	if (form.value.educations.length < 3) {
		form.value.educations.push({ university: '', major: '', graduationYear: '' });
	}
};

const removeEducationRow = (index) => {
	if (form.value.educations.length > 1) {
		form.value.educations.splice(index, 1);
	}
};

// Image Upload Handlers
const handleImageSelect = (e) => {
	const file = e.target.files?.[0];
	if (file) {
		processImageFile(file);
	}
};

const handleImageDrop = (e) => {
	isDragging.value = false;
	const file = e.dataTransfer?.files?.[0];
	if (file) {
		processImageFile(file);
	}
};

const processImageFile = (file) => {
	if (!file.type.startsWith('image/')) {
		formError.value = 'Harap unggah file gambar yang valid (jpg, png, jpeg).';
		return;
	}
	if (file.size > 10 * 1024 * 1024) {
		formError.value = 'Ukuran gambar maksimal 10MB.';
		return;
	}

	form.value.image = file;
	const reader = new FileReader();
	reader.onload = (e) => {
		form.value.imagePreview = e.target.result;
	};
	reader.readAsDataURL(file);
};

const removeImage = () => {
	form.value.image = null;
	form.value.imagePreview = null;
	if (imageInputRef.value) {
		imageInputRef.value.value = '';
	}
};

const handleClose = () => {
	formError.value = '';
	isNameDropdownOpen.value = false;
	isDivisionDropdownOpen.value = false;
	emit('close');
};

const handleSubmit = () => {
	formError.value = '';
	const selectedName = form.value.name.trim();

	if (!selectedName) {
		formError.value = 'Nama Dosen wajib dipilih.';
		return;
	}
	if (!form.value.division.trim()) {
		formError.value = 'Divisi Dosen wajib dipilih.';
		return;
	}

	// 1 Lecturer = 1 Profile validation
	const currentProfileId = props.initialData?.id;
	const isAlreadyTaken = props.existingProfiles.some((p) => {
		if (props.isEditing && p.id === currentProfileId) return false;
		return p.name.toLowerCase().trim() === selectedName.toLowerCase().trim();
	});

	if (isAlreadyTaken) {
		formError.value = `Dosen "${selectedName}" sudah memiliki profile. Satu dosen hanya dapat memiliki 1 profile.`;
		return;
	}

	// Education Validation (Min 1 row with university & major filled)
	const filledEducations = form.value.educations.filter((e) => e.university.trim() || e.major.trim());
	if (filledEducations.length === 0) {
		formError.value = 'Pendidikan wajib diisi minimal 1 jenjang (Nama Universitas dan Program Studi).';
		return;
	}

	const hasValidEdu = filledEducations.some((e) => e.university.trim() && e.major.trim());
	if (!hasValidEdu) {
		formError.value = 'Harap lengkapi Nama Universitas dan Program Studi untuk jenjang pendidikan.';
		return;
	}

	// Format primary education text summary
	const primaryEdu = form.value.educations
		.filter((e) => e.university.trim() || e.major.trim())
		.map((e) => [e.major.trim(), e.university.trim(), e.graduationYear.trim()].filter(Boolean).join(' - '))
		.join(' | ') || '-';

	emit('submit', {
		name: selectedName,
		division: form.value.division.trim(),
		educations: form.value.educations.map((e) => ({
			university: e.university.trim(),
			major: e.major.trim(),
			graduationYear: e.graduationYear.trim(),
		})),
		educationSummary: primaryEdu,
		research: form.value.research.trim() || '-',
		contact: form.value.contact.trim() || '-',
		image: form.value.image,
		imagePreview: form.value.imagePreview,
		scholarLink: form.value.scholarLink.trim() || '-',
		linkedinLink: form.value.linkedinLink.trim() || '-',
	});

	handleClose();
};
</script>

<template>
	<div
		v-if="show"
		class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/40 p-4 sm:p-6 transition-all"
		@click.self="handleClose"
	>
		<div
			class="w-full max-w-[1220px] transform rounded-[10px] bg-white p-7 shadow-2xl transition-all sm:p-10 lg:p-12 font-poppins max-h-[92vh] overflow-y-auto"
			@click="isNameDropdownOpen = false; isDivisionDropdownOpen = false"
		>
			<!-- Header Title -->
			<h2 class="text-left text-[24px] font-bold text-[#183669]">
				{{ isEditing ? 'Form Edit Profile Dosen' : 'Form Tambah Profile Dosen' }}
			</h2>

			<!-- Error Alert Message -->
			<div v-if="formError" class="mt-4 rounded-[10px] bg-red-50 p-3.5 font-inter text-[13px] text-red-600 border border-red-200">
				{{ formError }}
			</div>

			<form @submit.prevent="handleSubmit" class="mt-7 space-y-7 font-poppins">
				<!-- 2 Column Layout (Spacious & Clean with generous gap) -->
				<div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-14 xl:gap-16">
					<!-- ================= LEFT COLUMN ================= -->
					<div class="space-y-5">
						<!-- Nama Dosen & Divisi Subgrid with Custom Dropdown Matching Filter UI -->
						<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
							<!-- Nama Dosen (Searchable Custom Dropdown) -->
							<div class="relative" @click.stop>
								<label class="block text-[14px] font-bold text-[#183669]">
									Nama Dosen<span class="text-red-500">*</span>
								</label>
								<p class="font-inter text-[11px] text-[#7188a3]">Ketik, cari, dan pilih Nama Dosen</p>

								<!-- Dropdown Trigger Button -->
								<button
									type="button"
									@click="toggleNameDropdown"
									class="mt-1.5 flex h-[44px] w-full items-center justify-between rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[13px] transition focus:border-[#183669] focus:outline-none focus:ring-0"
									:class="{ 'border-[#183669] ring-1 ring-[#183669]/20': isNameDropdownOpen }"
								>
									<span :class="form.name ? 'font-medium text-[#1e3456] truncate' : 'text-[#a6b7cb]'">
										{{ form.name || 'Pilih / Cari Nama Dosen' }}
									</span>
									<svg
										:class="['h-4 w-4 shrink-0 text-[#8ca1b9] transition-transform duration-200', isNameDropdownOpen ? 'rotate-180 text-[#183669]' : '']"
										fill="none"
										stroke="currentColor"
										stroke-width="2"
										viewBox="0 0 24 24"
									>
										<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
									</svg>
								</button>

								<!-- Searchable Dropdown Menu Popover -->
								<div
									v-if="isNameDropdownOpen"
									class="absolute left-0 right-0 z-40 mt-1.5 rounded-[10px] border border-[#d6e0ee] bg-white p-2 shadow-2xl font-inter"
								>
									<!-- Search Input Field inside dropdown -->
									<div class="relative mb-2">
										<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
											<svg class="h-3.5 w-3.5 text-[#8ca1b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
											</svg>
										</div>
										<input
											v-model="lecturerSearchQuery"
											type="text"
											placeholder="Ketik untuk mencari nama dosen..."
											class="h-[36px] w-full rounded-[8px] border border-[#d6e0ee] bg-[#fafcff] pl-8 pr-3 text-xs text-[#1e3456] placeholder-[#8ca1b9] focus:border-[#183669] focus:outline-none focus:ring-0"
											ref="lecturerSearchInputRef"
											@click.stop
										/>
									</div>

									<!-- Options List -->
									<div class="max-h-52 overflow-y-auto space-y-1">
										<button
											v-for="lec in filteredSelectableLecturers"
											:key="lec.id"
											type="button"
											@click="selectLecturer(lec)"
											:disabled="lec.isTaken"
											:class="[
												'flex w-full items-center justify-between rounded-[6px] px-3 py-2 text-left text-xs transition-colors',
												form.name === lec.name
													? 'bg-[#183669] font-bold text-white'
													: lec.isTaken
														? 'cursor-not-allowed text-[#a0aec0] bg-gray-50'
														: 'text-[#435b76] hover:bg-slate-100'
											]"
										>
											<div class="min-w-0 pr-2">
												<p class="truncate font-medium">{{ lec.name }}</p>
												<p :class="form.name === lec.name ? 'text-white/80' : 'text-[#8ca1b9]'" class="text-[10px]">
													NIP: {{ lec.nip }}
												</p>
											</div>
											<span
												v-if="lec.isTaken"
												class="shrink-0 text-[10px] text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200"
											>
												Sudah punya profile
											</span>
										</button>

										<div v-if="filteredSelectableLecturers.length === 0" class="py-4 text-center text-xs text-[#8ca1b9]">
											Tidak ada nama dosen yang cocok
										</div>
									</div>
								</div>
							</div>

							<!-- Divisi (Custom Dropdown Matching Filter UI) -->
							<div class="relative" @click.stop>
								<label class="block text-[14px] font-bold text-[#183669]">
									Divisi<span class="text-red-500">*</span>
								</label>
								<p class="font-inter text-[11px] text-[#7188a3]">Pilih Divisi Dosen</p>

								<!-- Dropdown Trigger Button -->
								<button
									type="button"
									@click="isDivisionDropdownOpen = !isDivisionDropdownOpen; isNameDropdownOpen = false"
									class="mt-1.5 flex h-[44px] w-full items-center justify-between rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[13px] text-[#1e3456] transition focus:border-[#183669] focus:outline-none focus:ring-0"
									:class="{ 'border-[#183669] ring-1 ring-[#183669]/20': isDivisionDropdownOpen }"
								>
									<span :class="form.division ? 'font-medium text-[#1e3456] truncate' : 'text-[#a6b7cb] truncate'">
										{{ form.division || 'Pilih Divisi Dosen' }}
									</span>
									<svg
										:class="['h-4 w-4 shrink-0 text-[#8ca1b9] transition-transform duration-200', isDivisionDropdownOpen ? 'rotate-180 text-[#183669]' : '']"
										fill="none"
										stroke="currentColor"
										stroke-width="2"
										viewBox="0 0 24 24"
									>
										<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
									</svg>
								</button>

								<!-- Dropdown Menu Popover -->
								<div
									v-if="isDivisionDropdownOpen"
									class="absolute left-0 right-0 z-40 mt-1.5 rounded-[10px] border border-[#d6e0ee] bg-white p-2 shadow-2xl font-inter space-y-1"
								>
									<button
										v-for="div in divisiOptions"
										:key="div"
										type="button"
										@click="selectDivision(div)"
										:class="[
											'w-full rounded-[6px] px-3 py-2 text-left text-xs transition-colors',
											form.division === div
												? 'bg-[#183669] font-bold text-white'
												: 'text-[#435b76] hover:bg-slate-100'
										]"
									>
										{{ div }}
									</button>
								</div>
							</div>
						</div>

						<!-- Pendidikan (Dynamic Repeater: Min 1, Max 3) -->
						<div>
							<div class="flex items-center justify-between">
								<div>
									<label class="block text-[14px] font-bold text-[#183669]">
										Pendidikan<span class="text-red-500">*</span>
									</label>
									<p class="font-inter text-[11px] text-[#7188a3]">
										Masukkan jenjang pendidikan (Min. 1, Maks. 3 jenjang)
									</p>
								</div>
								<span class="text-[11px] font-semibold text-[#7188a3]">{{ form.educations.length }}/3 Jenjang</span>
							</div>

							<div class="mt-2.5 space-y-3">
								<div
									v-for="(edu, idx) in form.educations"
									:key="idx"
									class="flex items-center gap-2.5"
								>
									<!-- Universitas -->
									<input
										v-model="edu.university"
										type="text"
										placeholder="Nama Universitas"
										class="h-[42px] min-w-0 flex-[1.2] rounded-[10px] border border-[#d6e0ee] bg-white px-3 font-inter text-[13px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
									/>
									<!-- Program Studi -->
									<input
										v-model="edu.major"
										type="text"
										placeholder="Program Studi"
										class="h-[42px] min-w-0 flex-1 rounded-[10px] border border-[#d6e0ee] bg-white px-3 font-inter text-[13px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
									/>
									<!-- Tahun Lulus -->
									<input
										v-model="edu.graduationYear"
										type="text"
										placeholder="Tahun Lulus"
										class="h-[42px] w-24 shrink-0 rounded-[10px] border border-[#d6e0ee] bg-white px-2 text-center font-inter text-[13px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
									/>

									<!-- Plus Button for Row 0 (only if < 3), Minus Button for others -->
									<button
										v-if="idx === 0 && form.educations.length < 3"
										type="button"
										@click="addEducationRow"
										class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-[10px] border-2 border-[#183669] bg-white text-[#183669] transition hover:bg-[#183669] hover:text-white"
										title="Tambah Jenjang Pendidikan (Maks. 3)"
									>
										<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
										</svg>
									</button>
									<button
										v-else-if="form.educations.length > 1"
										type="button"
										@click="removeEducationRow(idx)"
										class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-[10px] border-2 border-[#ff7875] bg-white text-[#ff4d4f] transition hover:bg-[#ff4d4f] hover:text-white"
										title="Hapus Jenjang Pendidikan"
									>
										<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
										</svg>
									</button>
								</div>
							</div>
						</div>

						<!-- Ketertarikan / Penelitian -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Ketertarikan<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3]">Masukkan ketertarikan penelitian Dosen</p>
							<input
								v-model="form.research"
								type="text"
								placeholder="Narasumber / Kayu Jati Luhur"
								required
								class="mt-1.5 h-[44px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
							/>
						</div>

						<!-- Kontak -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Kontak<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3]">Masukkan Email atau Nomor Handphone Dosen</p>
							<input
								v-model="form.contact"
								type="text"
								placeholder="sulana@email.com"
								required
								class="mt-1.5 h-[44px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
							/>
						</div>
					</div>

					<!-- ================= RIGHT COLUMN ================= -->
					<div class="space-y-5">
						<!-- Gambar Upload Area -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Gambar<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3]">
								Masukkan gambar Dosen berupa jpg/png/jpeg! (MAX 10mb)
							</p>

							<!-- Image Drag & Drop / Preview Box -->
							<div
								v-if="!form.imagePreview"
								@dragover.prevent="isDragging = true"
								@dragleave.prevent="isDragging = false"
								@drop.prevent="handleImageDrop"
								:class="[
									'mt-1.5 flex flex-col items-center justify-center rounded-[12px] border-2 border-dashed p-8 text-center transition-colors min-h-[175px]',
									isDragging ? 'border-[#183669] bg-[#183669]/5' : 'border-[#183669]/30 bg-[#fafcff] hover:border-[#183669]/60'
								]"
							>
								<svg class="h-12 w-12 text-[#8c9eb5]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
								</svg>
								<p class="mt-2 font-inter text-[12px] text-[#7188a3]">
									Upload gambar atau seret gambar ke form ini
								</p>

								<input
									type="file"
									ref="imageInputRef"
									accept="image/png,image/jpeg,image/jpg"
									class="hidden"
									@change="handleImageSelect"
								/>
								<button
									type="button"
									@click="$refs.imageInputRef.click()"
									class="mt-3.5 rounded-[8px] border border-[#a6b7cb] bg-white px-6 py-1.5 font-inter text-[12px] font-semibold text-[#5a718d] transition hover:bg-slate-50"
								>
									Upload
								</button>
							</div>

							<!-- Image Preview State -->
							<div
								v-else
								class="mt-1.5 flex items-center justify-between rounded-[12px] border border-[#d6e0ee] bg-[#f7f9fd] p-4 min-h-[175px]"
							>
								<div class="flex items-center gap-4">
									<img
										:src="form.imagePreview"
										alt="Preview"
										class="h-20 w-20 rounded-full object-cover ring-4 ring-[#183669]/20"
									/>
									<div>
										<p class="font-poppins text-[14px] font-semibold text-[#183669]">Foto Profil Dosen</p>
										<p class="font-inter text-[12px] text-[#7188a3]">Gambar siap disimpan ke database</p>
									</div>
								</div>
								<button
									type="button"
									@click="removeImage"
									class="rounded-[8px] border border-red-200 bg-white px-3.5 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50"
								>
									Ganti Gambar
								</button>
							</div>
						</div>

						<!-- Link Google Scholar -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Link Google Scholar<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3]">Masukkan link Google Scholar dari Dosen</p>
							<input
								v-model="form.scholarLink"
								type="text"
								placeholder="www.scholar.google.com"
								class="mt-1.5 h-[44px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
							/>
						</div>

						<!-- Link LinkedIn -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Link LinkedIn
							</label>
							<p class="font-inter text-[11px] text-[#7188a3]">Masukkan link LinkedIn dari Dosen (optional)</p>
							<input
								v-model="form.linkedinLink"
								type="text"
								placeholder="www.linkedin.com"
								class="mt-1.5 h-[44px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
							/>
						</div>
					</div>
				</div>

				<!-- Modal Footer Buttons -->
				<div class="mt-8 flex items-center justify-end gap-4 pt-5 border-t border-[#f0f4f9]">
					<button
						type="button"
						@click="handleClose"
						class="h-[46px] min-w-[150px] px-6 rounded-[10px] border-2 border-[#d6e0ee] bg-white font-poppins text-[15px] font-bold text-[#183669] transition hover:border-[#183669] hover:bg-slate-50 focus:border-[#183669] focus:outline-none active:border-[#183669]"
					>
						Kembali
					</button>
					<button
						type="submit"
						class="h-[46px] min-w-[150px] px-6 rounded-[10px] bg-[#183669] font-poppins text-[15px] font-bold text-white transition hover:bg-[#122b54]"
					>
						Simpan
					</button>
				</div>
			</form>
		</div>
	</div>
</template>
