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

const degreeOptions = ['D4', 'S1', 'S2', 'S3', 'Profesi'];

const getDegreeByIndex = (index) => {
	if (index === 0) return 'S1';
	if (index === 1) return 'S2';
	if (index === 2) return 'S3';
	return 'S1';
};

const form = ref({
	user_id: null,
	name: '',
	division: '',
	educations: [
		{ degree: 'S1', university: '', major: '', graduationYear: '' },
	],
	research: '',
	contact: '',
	image: null,
	imagePreview: null,
	scholarLink: '',
	linkedinLink: '',
});

const formError = ref('');
const errors = ref({});
const isDragging = ref(false);
const imageInputRef = ref(null);

// state dropdown
const isNameDropdownOpen = ref(false);
const isDivisionDropdownOpen = ref(false);
const openDegreeDropdownIndex = ref(null);
const openYearDropdownIndex = ref(null);
const lecturerSearchQuery = ref('');
const lecturerSearchInputRef = ref(null);

const currentYear = new Date().getFullYear();

const yearOptions = computed(() => {
	const years = [];
	for (let y = currentYear; y >= 1960; y--) {
		years.push(y.toString());
	}
	return years;
});

// status dosen (jika sudah punya profile)
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

	// Untuk mengurutkan Dosen yang available (belum punya profile) tampil di paling atas
	return list.sort((a, b) => {
		if (a.isTaken === b.isTaken) return 0;
		return a.isTaken ? 1 : -1;
	});
});

// Filtering nama dosen sesuai input search
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
	form.value.user_id = lec.id;
	form.value.name = lec.name;
	errors.value.name = '';
	if (lec.email && lec.email !== '-') {
		form.value.contact = lec.email;
		errors.value.contact = '';
	} else if (lec.phone && lec.phone !== '-') {
		form.value.contact = lec.phone;
		errors.value.contact = '';
	}
	isNameDropdownOpen.value = false;
	lecturerSearchQuery.value = '';
};

const selectDivision = (div) => {
	form.value.division = div;
	errors.value.division = '';
	isDivisionDropdownOpen.value = false;
};

const parseEducations = (rawEdu) => {
	if (!rawEdu) return [];
	if (typeof rawEdu === 'string') {
		try {
			const parsed = JSON.parse(rawEdu);
			return Array.isArray(parsed) ? parsed : [];
		} catch (e) {
			return [];
		}
	}
	return Array.isArray(rawEdu) ? rawEdu : [];
};

watch(
	[() => props.show, () => props.initialData],
	([isOpen, initialData]) => {
		if (isOpen) {
			formError.value = '';
			errors.value = {};
			isNameDropdownOpen.value = false;
			isDivisionDropdownOpen.value = false;
			openDegreeDropdownIndex.value = null;
			openYearDropdownIndex.value = null;
			lecturerSearchQuery.value = '';

			if (props.isEditing && initialData) {
				const eduList = parseEducations(initialData.educations);
				form.value = {
					user_id: initialData.user_id || null,
					name: initialData.name || '',
					division: initialData.division || divisiOptions[0],
					educations: eduList.length > 0
						? eduList.map((e, idx) => ({
							degree: e.degree || e.jenjang || e.tingkat || getDegreeByIndex(idx),
							university: e.university || e.universitas || '',
							major: e.major || e.jurusan || e.prodi || '',
							graduationYear: e.graduationYear || e.tahunLulus || e.year || '',
						}))
						: [{ degree: 'S1', university: '', major: '', graduationYear: '' }],
					research: initialData.research && initialData.research !== '-' ? initialData.research : '',
					contact: initialData.contact && initialData.contact !== '-' ? initialData.contact : '',
					image: initialData.image || null,
					imagePreview: initialData.imagePreview || initialData.image || null,
					scholarLink: initialData.scholarLink && initialData.scholarLink !== '-' ? initialData.scholarLink : (initialData.scholar_link || ''),
					linkedinLink: initialData.linkedinLink && initialData.linkedinLink !== '-' ? initialData.linkedinLink : (initialData.linkedin_link || ''),
				};
			} else {
				form.value = {
					user_id: null,
					name: '',
					division: '',
					educations: [
						{ degree: 'S1', university: '', major: '', graduationYear: '' },
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
	{ immediate: true, deep: true }
);

// Field Pendidikan dinamis (Min 1, Max 3)
const addEducationRow = () => {
	if (form.value.educations.length < 3) {
		const nextDegree = getDegreeByIndex(form.value.educations.length);
		form.value.educations.push({ degree: nextDegree, university: '', major: '', graduationYear: '' });
		errors.value.educations = '';
	}
};

const removeEducationRow = (index) => {
	if (form.value.educations.length > 1) {
		form.value.educations.splice(index, 1);
		errors.value.educations = '';
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
		errors.value.image = 'Harap unggah file gambar yang valid (jpg, png, jpeg).';
		return;
	}
	if (file.size > 10 * 1024 * 1024) {
		errors.value.image = 'Ukuran gambar maksimal 5MB.';
		return;
	}

	errors.value.image = '';
	form.value.image = file;
	const reader = new FileReader();
	reader.onload = (e) => {
		form.value.imagePreview = e.target.result;
	};
	reader.readAsDataURL(file);
};

const previewingImage = ref(null);

const openImagePreview = (url) => {
	previewingImage.value = url;
};

const closeImagePreview = () => {
	previewingImage.value = null;
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
	errors.value = {};
	isNameDropdownOpen.value = false;
	isDivisionDropdownOpen.value = false;
	openDegreeDropdownIndex.value = null;
	openYearDropdownIndex.value = null;
	previewingImage.value = null;
	emit('close');
};

const handleSubmit = () => {
	formError.value = '';
	errors.value = {};
	const selectedName = form.value.name.trim();

	if (!selectedName) {
		errors.value.name = 'Nama Dosen wajib dipilih.';
	}
	if (!form.value.division.trim()) {
		errors.value.division = 'Divisi Dosen wajib dipilih.';
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

	// Education Validation: All added education rows must be completely filled
	const hasIncompleteEdu = form.value.educations.some(
		(e) => !e.degree || !e.degree.trim() || !e.university || !e.university.trim() || !e.major || !e.major.trim() || !e.graduationYear || !e.graduationYear.trim()
	);

	if (form.value.educations.length === 0) {
		errors.value.educations = 'Pendidikan wajib diisi minimal 1 jenjang.';
	} else if (hasIncompleteEdu) {
		errors.value.educations = 'Semua baris jenjang pendidikan yang ditambahkan wajib diisi lengkap (Jenjang, Universitas, Prodi, dan Tahun).';
	}

	if (!form.value.research.trim()) {
		errors.value.research = 'Ketertarikan penelitian Dosen wajib diisi.';
	}
	if (!form.value.contact.trim()) {
		errors.value.contact = 'Kontak Dosen wajib diisi.';
	}
	if (!form.value.imagePreview && !form.value.image) {
		errors.value.image = 'Foto Dosen wajib diunggah.';
	}
	if (!form.value.scholarLink.trim()) {
		errors.value.scholarLink = 'Link Google Scholar wajib diisi.';
	}

	// Validation: Max Graduation Year is currentYear
	const invalidYear = form.value.educations.find((e) => {
		if (!e.graduationYear || !e.graduationYear.trim()) return false;
		const y = parseInt(e.graduationYear.trim(), 10);
		return isNaN(y) || y > currentYear || y < 1950;
	});
	if (invalidYear) {
		errors.value.educations = `Tahun kelulusan tidak valid atau tidak boleh melebihi tahun saat ini (${currentYear}).`;
	}

	if (Object.keys(errors.value).length > 0) {
		return;
	}

	// Format primary education text summary
	const primaryEdu = form.value.educations
		.filter((e) => (e.university && e.university.trim()) || (e.major && e.major.trim()))
		.map((e) => {
			const deg = e.degree ? `${e.degree}: ` : '';
			const parts = [(e.major || '').trim(), (e.university || '').trim(), (e.graduationYear || '').trim()].filter(Boolean).join(' - ');
			return `${deg}${parts}`;
		})
		.join('; ') || '-';

	emit('submit', {
		user_id: form.value.user_id,
		name: selectedName,
		lecturerName: selectedName,
		nip: form.value.nip,
		division: form.value.division.trim(),
		educations: form.value.educations.map((e) => ({
			degree: e.degree || 'S1',
			university: (e.university || '').trim(),
			major: (e.major || '').trim(),
			graduationYear: (e.graduationYear || '').trim(),
		})),
		educationSummary: primaryEdu,
		research: form.value.research.trim() || '-',
		contact: form.value.contact.trim() || '-',
		image: form.value.image,
		imagePreview: form.value.imagePreview,
		scholarLink: form.value.scholarLink.trim() || '-',
		linkedinLink: form.value.linkedinLink.trim() || '-',
		scholar_link: form.value.scholarLink.trim() || null,
		linkedin_link: form.value.linkedinLink.trim() || null,
	});

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
		class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/40 p-4 sm:p-6 transition-all"
		@mousedown="handleBackdropMouseDown"
		@mouseup="handleBackdropMouseUp"
	>
		<div
			class="w-full max-w-[1220px] transform rounded-[10px] bg-white p-7 shadow-2xl transition-all sm:p-10 lg:p-12 font-poppins max-h-[92vh] overflow-y-auto"
			@click="isNameDropdownOpen = false; isDivisionDropdownOpen = false; openDegreeDropdownIndex = null; openYearDropdownIndex = null"
		>
			<!-- Header Title -->
			<h2 class="text-left text-[24px] font-bold text-[#183669]">
				{{ isEditing ? 'Form Edit Profile Dosen' : 'Form Tambah Profile Dosen' }}
			</h2>

			<!-- Error Alert Message -->
			<div v-if="formError" class="mt-4 rounded-[10px] bg-red-50 p-3.5 font-inter text-[13px] text-red-600 border border-red-200">
				{{ formError }}
			</div>

			<form @submit.prevent="handleSubmit" novalidate class="mt-7 space-y-7 font-poppins">
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
									class="mt-1.5 flex h-[44px] w-full items-center justify-between rounded-[10px] border bg-white px-3.5 font-inter text-[13px] text-[#1e3456] transition-colors duration-150 focus:outline-none"
									:class="errors.name ? 'border-red-400 focus:border-red-500 bg-red-50/20' : (isNameDropdownOpen ? 'border-[#183669] bg-white' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff]')"
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
								<p v-if="errors.name" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
									<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
									</svg>
									<span>{{ errors.name }}</span>
								</p>

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
											class="h-[36px] w-full rounded-[8px] border border-[#d6e0ee] bg-[#fafcff] pl-8 pr-3 text-xs text-[#1e3456] placeholder-[#8ca1b9] transition-colors duration-150 hover:border-[#a6b7cb] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0"
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
												'flex w-full items-center justify-between rounded-[7px] p-2 text-left text-xs transition-colors',
												form.name === lec.name
													? 'bg-[#183669] text-white font-medium'
													: lec.isTaken
														? 'cursor-not-allowed opacity-50 bg-slate-50 text-[#8ca1b9]'
														: 'text-[#1e3456] hover:bg-[#f0f4f9]'
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
									class="mt-1.5 flex h-[44px] w-full items-center justify-between rounded-[10px] border bg-white px-3.5 font-inter text-[13px] text-[#1e3456] transition-colors duration-150 focus:outline-none"
									:class="errors.division ? 'border-red-400 focus:border-red-500 bg-red-50/20' : (isDivisionDropdownOpen ? 'border-[#183669] bg-white' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff]')"
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
								<p v-if="errors.division" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
									<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
									</svg>
									<span>{{ errors.division }}</span>
								</p>

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
									class="flex items-center gap-2"
								>
									<!-- Custom Jenjang / Tingkat Dropdown (D4, S1, S2, S3, Profesi) -->
									<div class="relative w-[105px] shrink-0" @click.stop>
										<button
											type="button"
											@click="openDegreeDropdownIndex = openDegreeDropdownIndex === idx ? null : idx; isNameDropdownOpen = false; isDivisionDropdownOpen = false"
											class="flex h-[42px] w-full items-center justify-between rounded-[10px] border bg-white px-2.5 font-poppins text-[13px] font-bold text-[#183669] transition-colors duration-150 focus:outline-none"
											:class="errors.educations && !edu.degree ? 'border-red-400 focus:border-red-500 bg-red-50/20' : (openDegreeDropdownIndex === idx ? 'border-[#183669] bg-white' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff]')"
										>
											<span class="truncate">{{ edu.degree }}</span>
											<svg
												:class="['h-3.5 w-3.5 shrink-0 text-[#8ca1b9] transition-transform duration-200', openDegreeDropdownIndex === idx ? 'rotate-180 text-[#183669]' : '']"
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
											v-if="openDegreeDropdownIndex === idx"
											class="absolute left-0 z-40 mt-1.5 w-full rounded-[10px] border border-[#d6e0ee] bg-white p-1.5 shadow-2xl font-inter space-y-1"
										>
											<button
												v-for="deg in degreeOptions"
												:key="deg"
												type="button"
												@click="edu.degree = deg; openDegreeDropdownIndex = null; errors.educations = ''"
												:class="[
													'flex h-7 w-full items-center justify-between rounded-[7px] px-2 text-[12px] transition-colors',
													edu.degree === deg
														? 'bg-[#183669] text-white font-medium'
														: 'text-[#1e3456] hover:bg-[#f0f4f9]'
												]"
											>
												<span>{{ deg }}</span>
												<svg v-if="edu.degree === deg" class="h-3 w-3 shrink-0 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
												</svg>
											</button>
										</div>
									</div>

									<!-- Universitas -->
									<input
										v-model="edu.university"
										type="text"
										placeholder="Nama Universitas"
										@input="errors.educations = ''"
										class="h-[42px] min-w-0 flex-[1.2] rounded-[10px] border bg-white px-3 font-inter text-[13px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
										:class="errors.educations && !edu.university ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
									/>
									<!-- Program Studi -->
									<input
										v-model="edu.major"
										type="text"
										placeholder="Program Studi"
										@input="errors.educations = ''"
										class="h-[42px] min-w-0 flex-1 rounded-[10px] border bg-white px-3 font-inter text-[13px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
										:class="errors.educations && !edu.major ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
									/>
									<!-- Custom Tahun Lulus Dropdown (Kalender Tahun max = currentYear) -->
									<div class="relative w-[95px] shrink-0" @click.stop>
										<button
											type="button"
											@click="openYearDropdownIndex = openYearDropdownIndex === idx ? null : idx; openDegreeDropdownIndex = null; isNameDropdownOpen = false; isDivisionDropdownOpen = false"
											class="flex h-[42px] w-full items-center justify-between rounded-[10px] border bg-white px-2.5 font-inter text-[13px] transition-colors duration-150 focus:outline-none"
											:class="[
												errors.educations && !edu.graduationYear ? 'border-red-400 focus:border-red-500 bg-red-50/20' : (openYearDropdownIndex === idx ? 'border-[#183669] bg-white' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff]'),
												edu.graduationYear ? 'font-regular text-[#183669]' : 'text-[#a6b7cb]'
											]"
										>
											<span class="truncate">{{ edu.graduationYear || 'Tahun' }}</span>
											<svg
												:class="['h-3.5 w-3.5 shrink-0 text-[#8ca1b9] transition-transform duration-200', openYearDropdownIndex === idx ? 'rotate-180 text-[#183669]' : '']"
												fill="none"
												stroke="currentColor"
												stroke-width="2"
												viewBox="0 0 24 24"
											>
												<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
											</svg>
										</button>

										<!-- Dropdown Menu Popover (Scrollable Years max = currentYear) -->
										<div
											v-if="openYearDropdownIndex === idx"
											class="absolute left-0 z-40 mt-1.5 max-h-48 w-full overflow-y-auto rounded-[10px] border border-[#d6e0ee] bg-white p-1.5 shadow-2xl font-inter space-y-0.5"
										>
											<button
												v-for="yr in yearOptions"
												:key="yr"
												type="button"
												@click="edu.graduationYear = yr; openYearDropdownIndex = null; errors.educations = ''"
												:class="[
													'flex h-7 w-full items-center justify-center rounded-[6px] text-[12px] transition-colors',
													edu.graduationYear === yr
														? 'bg-[#183669] font-bold text-white'
														: 'text-[#1e3456] hover:bg-[#f0f4f9]'
												]"
											>
												{{ yr }}
											</button>
										</div>
									</div>

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
							<p v-if="errors.educations" class="mt-1.5 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
								<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
								</svg>
								<span>{{ errors.educations }}</span>
							</p>
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
								placeholder="Keanekaragaman Hayati"
								@input="errors.research = ''"
								class="mt-1.5 h-[44px] w-full rounded-[10px] border bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
								:class="errors.research ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
							/>
							<p v-if="errors.research" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
								<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
								</svg>
								<span>{{ errors.research }}</span>
							</p>
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
								@input="errors.contact = ''"
								class="mt-1.5 h-[44px] w-full rounded-[10px] border bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
								:class="errors.contact ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
							/>
							<p v-if="errors.contact" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
								<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
								</svg>
								<span>{{ errors.contact }}</span>
							</p>
						</div>
					</div>

					<!-- ================= RIGHT COLUMN ================= -->
					<div class="space-y-5">
						<!-- Gambar Upload Area (Matching profile.vue) -->
						<div>
							<div class="flex items-center justify-between">
								<label class="block text-[14px] font-bold text-[#183669]">
									Gambar<span class="text-red-500">*</span>
								</label>
								<span class="text-[11px] font-semibold text-[#7188a3]">
									{{ form.imagePreview ? '1/1' : '0/1' }} Foto
								</span>
							</div>
							<p class="font-inter text-[11px] text-[#7188a3]">
								Masukan foto terbaik berupa jpg/png/jpeg (MAX 5MB)
							</p>

							<!-- Dropzone Box Container (Fixed 250px Height, Contained Preview matching profile.vue) -->
							<div
								@dragover.prevent="isDragging = true"
								@dragleave.prevent="isDragging = false"
								@drop.prevent="handleImageDrop"
								:class="[
									'mt-2 flex h-[205px] max-h-[205px] w-full flex-col items-center justify-center overflow-hidden rounded-[12px] border-2 border-dashed p-3 text-center transition-colors',
									isDragging
										? 'border-[#183669] bg-[#183669]/5'
										: errors.image
											? 'border-red-400 bg-red-50/20'
											: 'border-[#183669]/30 bg-[#fafcff] hover:border-[#183669]/60'
								]"
							>
								<!-- State 1: No Photo Uploaded -->
								<div v-if="!form.imagePreview" class="flex flex-col items-center justify-center py-2.5">
									<svg class="h-9 w-9 text-[#8c9eb5]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
									</svg>
									<p class="mt-1.5 font-inter text-[12px] text-[#7188a3]">
										Upload gambar atau seret gambar ke form ini
									</p>
									<button
										type="button"
										@click="imageInputRef?.click()"
										class="mt-2 rounded-[8px] border border-[#a6b7cb] bg-white px-5 py-1 font-inter text-[12px] font-semibold text-[#5a718d] transition hover:bg-slate-50 shadow-xs"
									>
										Upload
									</button>
								</div>

								<!-- State 2: 1 Single Image Preview with Contained Size & Hover Actions (Matching profile.vue) -->
								<div v-else class="flex h-full w-full items-center justify-center p-1">
									<div class="group relative flex max-h-[220px] max-w-[90%] items-center justify-center overflow-hidden rounded-[10px] border border-[#d6e0ee] bg-slate-100 shadow-sm">
										<img
											:src="form.imagePreview"
											alt="Preview Foto Dosen"
											class="max-h-[180px] w-auto max-w-full rounded-[9px] object-contain cursor-pointer transition hover:opacity-95"
											@click="openImagePreview(form.imagePreview)"
										/>

										<!-- Action Overlays on Hover (Matching profile.vue & ModalFormAktivitas.vue) -->
										<div class="absolute inset-0 flex items-center justify-center gap-2 bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
											<!-- Preview / Zoom Button -->
											<button
												type="button"
												@click.stop="openImagePreview(form.imagePreview)"
												class="flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-[#183669] transition hover:bg-white hover:scale-110"
												title="Lihat Foto Penuh"
											>
												<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
													<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
												</svg>
											</button>
											<!-- Change / Re-upload Button -->
											<button
												type="button"
												@click.stop="imageInputRef?.click()"
												class="flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-[#183669] transition hover:bg-white hover:scale-110"
												title="Ganti Foto"
											>
												<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
												</svg>
											</button>
											<!-- Delete Button -->
											<button
												type="button"
												@click.stop="removeImage"
												class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500/90 text-white transition hover:bg-red-600 hover:scale-110"
												title="Hapus Foto"
											>
												<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
												</svg>
											</button>
										</div>
									</div>
								</div>

								<input
									type="file"
									ref="imageInputRef"
									accept="image/png,image/jpeg,image/jpg"
									class="hidden"
									@change="handleImageSelect"
								/>
							</div>
							<p v-if="errors.image" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
								<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
								</svg>
								<span>{{ errors.image }}</span>
							</p>
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
								placeholder="https://www.scholar.google.com"
								@input="errors.scholarLink = ''"
								class="mt-1.5 h-[44px] w-full rounded-[10px] border bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
								:class="errors.scholarLink ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
							/>
							<p v-if="errors.scholarLink" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
								<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
								</svg>
								<span>{{ errors.scholarLink }}</span>
							</p>
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
								placeholder="https://www.linkedin.com"
								class="mt-1.5 h-[44px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0"
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
						Batal
					</button>
					<button
						type="submit"
						class="h-[46px] min-w-[150px] px-6 rounded-[10px] bg-[#183669] font-poppins text-[15px] font-bold text-white transition hover:bg-[#122b54]"
					>
						{{ isEditing ? 'Simpan Perubahan' : 'Tambah Profile Dosen' }}
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Lightbox Image Modal Preview (Matching profile.vue & ModalFormAktivitas.vue) -->
	<Teleport to="body">
		<div
			v-if="previewingImage"
			class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4 transition-all"
			@click="closeImagePreview"
		>
			<div class="relative max-h-[90vh] max-w-[90vw] overflow-hidden rounded-xl bg-transparent" @click.stop>
				<button
					type="button"
					@click="closeImagePreview"
					class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/90 focus:outline-none"
					title="Tutup Preview"
				>
					<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
				<img :src="previewingImage" alt="Zoomed Preview" class="max-h-[85vh] max-w-[85vw] rounded-lg object-contain shadow-2xl" />
			</div>
		</div>
	</Teleport>
</template>
