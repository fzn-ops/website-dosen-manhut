<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import DatePicker from '@/Components/DatePicker.vue';
import ModalDeleteConfirmation from '@/Components/ModalDeleteConfirmation.vue';

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
	availableProfiles: {
		type: Array,
		default: () => [],
	},
});

const emit = defineEmits(['close', 'submit']);

const categoryOptions = ['Lokakarya', 'Seminar', 'Workshop', 'Lainnya'];

const form = ref({
	user_id: null,
	name: '',
	title: '',
	lecturerName: '',
	description: '',
	role: '',
	startDate: '',
	endDate: '',
	images: [],
	imagePreviews: [],
	primaryImageIndex: 0,
	lecturerQuote: '',
	categories: [],
	releaseDate: '',
});

const formError = ref('');
const errors = ref({});
const isDragging = ref(false);
const imageInputRef = ref(null);

// Image Zoom / Lightbox Preview State
const previewingImage = ref(null);
const openImagePreview = (imgUrl) => {
	previewingImage.value = imgUrl;
};
const closeImagePreview = () => {
	previewingImage.value = null;
};

// Searchable Dropdown for Lecturer Name
const isNameDropdownOpen = ref(false);
const lecturerSearchQuery = ref('');
const lecturerSearchInputRef = ref(null);

const filteredLecturers = computed(() => {
	const q = lecturerSearchQuery.value.toLowerCase().trim();
	if (!q) return props.availableProfiles;

	return props.availableProfiles.filter((p) =>
		p.name.toLowerCase().includes(q)
	);
});

const toggleNameDropdown = () => {
	isNameDropdownOpen.value = !isNameDropdownOpen.value;
	if (isNameDropdownOpen.value) {
		lecturerSearchQuery.value = '';
		nextTick(() => {
			lecturerSearchInputRef.value?.focus();
		});
	}
};

const selectLecturer = (lec) => {
	form.value.user_id = lec.user_id || lec.id;
	form.value.lecturerName = lec.name;
	errors.value.lecturerName = '';
	isNameDropdownOpen.value = false;
	lecturerSearchQuery.value = '';
};

// Categories checkboxes logic
const toggleCategory = (cat) => {
	const idx = form.value.categories.indexOf(cat);
	if (idx === -1) {
		form.value.categories.push(cat);
		errors.value.categories = '';
	} else {
		form.value.categories.splice(idx, 1);
	}
};

// Format Current Date in Indonesian (e.g., "21 Januari 2026")
const getFormattedToday = () => {
	const months = [
		'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
		'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
	];
	const now = new Date();
	const day = now.getDate();
	const month = months[now.getMonth()];
	const year = now.getFullYear();
	return `${day} ${month} ${year}`;
};

watch(
	() => props.show,
	(isOpen) => {
		if (isOpen) {
			formError.value = '';
			errors.value = {};
			isNameDropdownOpen.value = false;
			lecturerSearchQuery.value = '';
			previewingImage.value = null;

			if (props.isEditing && props.initialData) {
				const cats = props.initialData.categories || (props.initialData.category ? [props.initialData.category] : []);
				form.value = {
					user_id: props.initialData.user_id || null,
					name: props.initialData.name || props.initialData.title || '',
					title: props.initialData.title || props.initialData.name || '',
					lecturerName: props.initialData.lecturerName || props.initialData.lecturer || '',
					description: props.initialData.description || '',
					role: props.initialData.role || '',
					startDate: props.initialData.startDate || '',
					endDate: props.initialData.endDate || '',
					images: props.initialData.images || [],
					imagePreviews: props.initialData.imagePreviews || (props.initialData.images ? [...props.initialData.images] : []),
					primaryImageIndex: Number(props.initialData.primaryImageIndex ?? 0),
					lecturerQuote: props.initialData.lecturerQuote && props.initialData.lecturerQuote !== '-' ? props.initialData.lecturerQuote : '',
					categories: [...cats],
					releaseDate: props.initialData.publishDate || props.initialData.date || getFormattedToday(),
				};
			} else {
				form.value = {
					user_id: null,
					name: '',
					title: '',
					lecturerName: '',
					description: '',
					role: '',
					startDate: '',
					endDate: '',
					images: [],
					imagePreviews: [],
					primaryImageIndex: 0,
					lecturerQuote: '',
					categories: [],
					releaseDate: getFormattedToday(),
				};
			}
		}
	},
	{ immediate: true }
);

const handleFiles = (files) => {
	if (!files || files.length === 0) return;

	const remainingSlots = 3 - form.value.images.length;
	if (remainingSlots <= 0) {
		formError.value = 'Maksimal 3 gambar yang dapat diupload.';
		return;
	}

	const filesToProcess = Array.from(files).slice(0, remainingSlots);

	filesToProcess.forEach((file) => {
		if (!file.type.startsWith('image/')) {
			formError.value = 'Harap upload file gambar yang valid (PNG, JPG, JPEG).';
			return;
		}
		if (file.size > 10 * 1024 * 1024) {
			formError.value = 'Ukuran gambar tidak boleh melebihi 10MB.';
			return;
		}

		form.value.images.push(file);

		const reader = new FileReader();
		reader.onload = (e) => {
			form.value.imagePreviews.push(e.target.result);
		};
		reader.readAsDataURL(file);
	});
};

const handleImageDrop = (e) => {
	isDragging.value = false;
	const files = e.dataTransfer.files;
	handleFiles(files);
};

const handleImageChange = (e) => {
	const files = e.target.files;
	handleFiles(files);
	e.target.value = '';
};

const setPrimaryImage = (index) => {
	form.value.primaryImageIndex = index;
};

// Delete Image Confirmation State
const showDeleteImageModal = ref(false);
const imageIndexToDelete = ref(null);

const confirmRemoveImage = (index) => {
	imageIndexToDelete.value = index;
	showDeleteImageModal.value = true;
};

const executeRemoveImage = () => {
	if (imageIndexToDelete.value !== null) {
		removeImage(imageIndexToDelete.value);
		imageIndexToDelete.value = null;
	}
	showDeleteImageModal.value = false;
};

const removeImage = (index) => {
	form.value.images.splice(index, 1);
	form.value.imagePreviews.splice(index, 1);
	if (form.value.primaryImageIndex === index) {
		form.value.primaryImageIndex = 0;
	} else if (form.value.primaryImageIndex > index) {
		form.value.primaryImageIndex -= 1;
	}
};

const triggerFileInput = () => {
	imageInputRef.value?.click();
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

const handleClose = () => {
	formError.value = '';
	errors.value = {};
	isNameDropdownOpen.value = false;
	emit('close');
};

const handleSubmit = () => {
	formError.value = '';
	errors.value = {};
	const title = form.value.title.trim();
	const lecturerName = form.value.lecturerName.trim();
	const rawDescription = form.value.description || '';
	const cleanDesc = rawDescription.replace(/<[^>]*>/g, '').trim();
	const role = form.value.role.trim();

	if (!lecturerName) {
		errors.value.lecturerName = 'Nama Dosen wajib dipilih.';
	}
	if (!title) {
		errors.value.title = 'Judul Aktivitas wajib diisi.';
	}
	if (form.value.categories.length === 0) {
		errors.value.categories = 'Pilih minimal 1 kategori aktivitas.';
	}
	if (!role) {
		errors.value.role = 'Peran dalam kegiatan wajib diisi.';
	}
	if (!cleanDesc) {
		errors.value.description = 'Deskripsi aktivitas wajib diisi.';
	}
	if (!form.value.startDate) {
		errors.value.startDate = 'Tanggal Mulai wajib diisi.';
	}

	if (Object.keys(errors.value).length > 0) {
		return;
	}

	emit('submit', {
		user_id: form.value.user_id,
		name: title,
		title: title,
		lecturer: lecturerName,
		lecturerName: lecturerName,
		description: rawDescription,
		role: role,
		startDate: form.value.startDate,
		endDate: form.value.endDate || form.value.startDate,
		images: form.value.images,
		imagePreviews: form.value.imagePreviews,
		primaryImageIndex: form.value.primaryImageIndex,
		lecturerQuote: form.value.lecturerQuote.trim() || '-',
		category: form.value.categories.length > 0 ? form.value.categories[0] : 'Lainnya',
		categories: [...form.value.categories],
		date: form.value.releaseDate || getFormattedToday(),
	});

	handleClose();
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
				class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/40 backdrop-blur-sm p-4 sm:p-6"
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
						class="w-full max-w-[1240px] transform rounded-[18px] bg-white p-7 shadow-2xl font-poppins border border-[#e2e8f0] max-h-[92vh] overflow-y-auto sm:p-10 lg:p-12"
						@click="isNameDropdownOpen = false"
					>
			<!-- Header Title -->
			<h2 class="text-left text-[24px] font-bold text-[#183669]">
				{{ isEditing ? 'Form Edit Aktivitas' : 'Form Tambah Aktivitas' }}
			</h2>

			<!-- Error Alert Message -->
			<div v-if="formError" class="mt-4 rounded-[10px] bg-red-50 p-3.5 font-inter text-[13px] text-red-600 border border-red-200">
				{{ formError }}
			</div>

			<form @submit.prevent="handleSubmit" novalidate class="mt-6 font-poppins">
				<!-- 2 Column Layout with Balanced Grid & Spacing -->
				<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:gap-10 xl:gap-12">
					<!-- ================= LEFT COLUMN ================= -->
					<div class="space-y-4">
						<!-- Nama Dosen (Searchable Dropdown from Profile Dosen) -->
						<div class="relative flex flex-col" @click.stop>
							<div>
								<label class="block text-[14px] font-bold text-[#183669]">
									Nama Dosen<span class="text-red-500">*</span>
								</label>
								<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">Pilih nama dosen berdasarkan profile dosen</p>
							</div>

							<!-- Dropdown Trigger Button -->
							<button
								type="button"
								@click="toggleNameDropdown"
								class="mt-1.5 flex h-[44px] w-full items-center justify-between rounded-[10px] border bg-white px-3.5 font-inter text-[13px] text-[#1e3456] transition-colors duration-150 focus:outline-none"
								:class="errors.lecturerName ? 'border-red-400 focus:border-red-500 bg-red-50/20' : (isNameDropdownOpen ? 'border-[#183669] bg-white' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff]')"
							>
								<span :class="form.lecturerName ? 'font-medium text-[#1e3456] truncate' : 'text-[#a6b7cb] truncate'">
									{{ form.lecturerName || 'Pilih / Cari Nama Dosen' }}
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
							<p v-if="errors.lecturerName" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
								<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
								</svg>
								<span>{{ errors.lecturerName }}</span>
							</p>

							<!-- Searchable Dropdown Menu Popover -->
							<div
								v-if="isNameDropdownOpen"
								class="absolute left-0 right-0 top-full z-40 mt-1.5 rounded-[10px] border border-[#d6e0ee] bg-white p-2 shadow-2xl font-inter"
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
										v-for="lec in filteredLecturers"
										:key="lec.id"
										type="button"
										@click="selectLecturer(lec)"
										:class="[
											'flex w-full items-center justify-between rounded-[6px] px-3 py-2 text-left text-xs transition-colors',
											form.lecturerName === lec.name
												? 'bg-[#183669] font-bold text-white'
												: 'text-[#435b76] hover:bg-slate-100'
										]"
									>
										<div class="min-w-0 pr-2">
											<p class="truncate font-medium">{{ lec.name }}</p>
											<p :class="form.lecturerName === lec.name ? 'text-white/80' : 'text-[#8ca1b9]'" class="text-[10px]">
												{{ lec.division || 'Dosen' }}
											</p>
										</div>
									</button>

									<div v-if="filteredLecturers.length === 0" class="py-4 text-center text-xs text-[#8ca1b9]">
										Tidak ada profile dosen yang cocok
									</div>
								</div>
							</div>
						</div>

						<!-- Judul Aktivitas -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Judul Aktivitas<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">Masukkan judul aktivitas yang diajukan di sini!</p>
							<input
								v-model="form.title"
								type="text"
								placeholder="Pelatihan manajer KDMP"
								@input="errors.title = ''"
								class="mt-1.5 h-[44px] w-full rounded-[10px] border bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
								:class="errors.title ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
							/>
							<p v-if="errors.title" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
								<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
								</svg>
								<span>{{ errors.title }}</span>
							</p>
						</div>

						<!-- Kategori Checkboxes -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Kategori<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">Masukkan kategori dari aktivitas dosen</p>

							<div class="mt-2 flex flex-wrap items-center gap-4">
								<label
									v-for="cat in categoryOptions"
									:key="cat"
									class="flex cursor-pointer items-center gap-2 select-none group"
								>
									<input
										type="checkbox"
										:checked="form.categories.includes(cat)"
										@change="toggleCategory(cat)"
										class="h-4 w-4 rounded border-[#a6b7cb] text-[#183669] transition-colors hover:border-[#183669] focus:ring-0 focus:ring-offset-0 cursor-pointer"
									/>
									<span class="font-inter text-[13px] font-medium text-[#435b76] group-hover:text-[#183669] transition-colors">{{ cat }}</span>
								</label>
							</div>
							<p v-if="errors.categories" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
								<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
								</svg>
								<span>{{ errors.categories }}</span>
							</p>
						</div>

						<!-- Peran -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Peran<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">Masukkan peran dalam kegiatan (e.g. Narasumber)</p>
							<input
								v-model="form.role"
								type="text"
								placeholder="Narasumber"
								@input="errors.role = ''"
								class="mt-1.5 h-[44px] w-full rounded-[10px] border bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
								:class="errors.role ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
							/>
							<p v-if="errors.role" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
								<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
								</svg>
								<span>{{ errors.role }}</span>
							</p>
						</div>

						<!-- Deskripsi (Rich Text Editor) -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Deskripsi<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3] mt-0.5 mb-1.5">Jelaskan gambaran kegiatan ini yah!</p>
							<RichTextEditor
								v-model="form.description"
								placeholder="Pelatihan manajer KDMP..."
								min-height="125px"
								@update:modelValue="errors.description = ''"
							/>
							<p v-if="errors.description" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
								<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
								</svg>
								<span>{{ errors.description }}</span>
							</p>
						</div>
					</div>

					<!-- ================= RIGHT COLUMN ================= -->
					<div class="space-y-4">
						<!-- Gambar Upload Area (Previews rendered INSIDE the upload field box) -->
						<div>
							<div class="flex items-center justify-between">
								<label class="block text-[14px] font-bold text-[#183669]">
									Gambar
								</label>
								<span class="text-[11px] font-semibold text-[#7188a3]">{{ form.imagePreviews.length }}/3 Gambar</span>
							</div>
							<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">
								Pilih 1 gambar sebagai Thumbnail utama (MAX 10mb, JPG/PNG, 3 Gambar)
							</p>

							<!-- Image Upload Box Container -->
							<div
								@dragover.prevent="isDragging = true"
								@dragleave.prevent="isDragging = false"
								@drop.prevent="handleImageDrop"
								:class="[
									'mt-1.5 flex flex-col items-center justify-center rounded-[12px] border-2 border-dashed p-3.5 text-center transition-colors min-h-[145px]',
									isDragging ? 'border-[#183669] bg-[#183669]/5' : 'border-[#183669]/30 bg-[#fafcff] hover:border-[#183669]/60'
								]"
							>
								<!-- State 1: No images uploaded yet -->
								<div v-if="form.imagePreviews.length === 0" class="flex flex-col items-center justify-center py-2.5">
									<svg class="h-9 w-9 text-[#8c9eb5]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
									</svg>
									<p class="mt-1.5 font-inter text-[12px] text-[#7188a3]">
										Upload gambar atau seret gambar ke form ini
									</p>
									<button
										type="button"
										@click="triggerFileInput"
										class="mt-2 rounded-[8px] border border-[#a6b7cb] bg-white px-5 py-1 font-inter text-[12px] font-semibold text-[#5a718d] transition hover:bg-slate-50 shadow-xs"
									>
										Upload
									</button>
								</div>

								<!-- State 2: Images uploaded (Grid INSIDE the container box) -->
								<div v-else class="w-full">
									<div class="grid grid-cols-3 gap-2.5">
										<div
											v-for="(img, idx) in form.imagePreviews"
											:key="idx"
											:class="[
												'group relative aspect-video overflow-hidden rounded-[8px] border transition-all shadow-xs bg-slate-100',
												form.imagePreviews.length > 1 && form.primaryImageIndex === idx
													? 'border-[#183669] ring-2 ring-[#183669]'
													: 'border-[#d6e0ee]'
											]"
										>
											<img :src="img" alt="Preview Gambar" class="h-full w-full object-cover" />

											<!-- Top-Left: Thumbnail Badge or Set Primary Button (if > 1 image) -->
											<div v-if="form.imagePreviews.length > 1" class="absolute left-1.5 top-1.5 z-20">
												<!-- Is Primary Badge -->
												<span
													v-if="form.primaryImageIndex === idx"
													class="inline-flex h-[21px] items-center justify-center gap-1 rounded-[6px] bg-[#183669] px-2 text-[9.5px] font-bold text-white shadow-md leading-none select-none"
												>
													<svg class="h-2.5 w-2.5 shrink-0 fill-amber-400 text-amber-400" viewBox="0 0 24 24">
														<path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
													</svg>
													<span class="inline-block leading-none translate-y-[0.5px]">Utama</span>
												</span>
												<!-- Set Primary Button (Touch & Desktop friendly) -->
												<button
													v-else
													type="button"
													@click.stop="setPrimaryImage(idx)"
													class="inline-flex h-[21px] items-center justify-center gap-1 rounded-[6px] bg-white/95 px-2 text-[9.5px] font-bold text-[#183669] shadow-md backdrop-blur-xs transition hover:bg-white active:scale-95 leading-none select-none sm:opacity-0 sm:group-hover:opacity-100"
													title="Jadikan gambar utama"
												>
													<svg class="h-2.5 w-2.5 shrink-0 fill-none text-[#183669]" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
													</svg>
													<span class="inline-block leading-none translate-y-[0.5px]">Set Utama</span>
												</button>
											</div>

											<!-- Top-Right: Delete Button with Trash Icon from assets -->
											<button
												type="button"
												@click.stop="confirmRemoveImage(idx)"
												class="absolute right-1.5 top-1.5 z-20 flex h-6 w-6 items-center justify-center rounded-full bg-white/95 text-red-600 shadow-md backdrop-blur-xs transition hover:bg-white hover:scale-110 active:scale-95 focus:outline-none sm:opacity-0 sm:group-hover:opacity-100"
												title="Hapus Gambar"
											>
												<img src="/assets/icons/delete.svg" alt="Hapus" class="h-3 w-3 object-contain" />
											</button>

											<!-- Bottom-Right: Zoom Preview Button -->
											<button
												type="button"
												@click.stop="openImagePreview(img)"
												class="absolute right-1.5 bottom-1.5 z-20 flex h-6 w-6 items-center justify-center rounded-full bg-slate-900/80 text-white shadow-md backdrop-blur-xs transition hover:bg-slate-900 hover:scale-110 active:scale-95 focus:outline-none sm:opacity-0 sm:group-hover:opacity-100"
												title="Lihat Ukuran Penuh"
											>
												<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
												</svg>
											</button>
										</div>

										<!-- Add More Slot Button (if < 3) -->
										<button
											v-if="form.imagePreviews.length < 3"
											type="button"
											@click="triggerFileInput"
											class="flex aspect-video flex-col items-center justify-center rounded-[8px] border-2 border-dashed border-[#183669]/40 bg-white/80 text-[#183669] transition hover:border-[#183669] hover:bg-[#183669]/5"
										>
											<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
											</svg>
											<span class="mt-0.5 text-[10px] font-semibold">+ Foto</span>
										</button>
									</div>
								</div>

								<input
									ref="imageInputRef"
									type="file"
									accept="image/png, image/jpeg, image/jpg"
									multiple
									class="hidden"
									@change="handleImageChange"
								/>
							</div>
						</div>

						<!-- Tanggal Mulai & Tanggal Selesai Subgrid -->
						<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 items-start">
							<!-- Tanggal Mulai -->
							<div class="flex flex-col">
								<div class="min-h-[38px] flex flex-col justify-start">
									<label class="block text-[14px] font-bold text-[#183669]">
										Tanggal Mulai<span class="text-red-500">*</span>
									</label>
									<p class="font-inter text-[11px] text-[#7188a3] leading-tight">Masukkan tanggal mulai aktivitas</p>
								</div>
								<div class="mt-1.5">
									<DatePicker
										v-model="form.startDate"
										placeholder="Pilih tanggal mulai"
										@update:modelValue="errors.startDate = ''"
									/>
								</div>
								<p v-if="errors.startDate" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
									<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
									</svg>
									<span>{{ errors.startDate }}</span>
								</p>
							</div>

							<!-- Tanggal Selesai -->
							<div class="flex flex-col">
								<div class="min-h-[38px] flex flex-col justify-start">
									<label class="block text-[14px] font-bold text-[#183669]">
										Tanggal Selesai
									</label>
									<p class="font-inter text-[11px] text-[#7188a3] leading-tight">Masukkan batas selesai aktivitas</p>
								</div>
								<div class="mt-1.5">
									<DatePicker
										v-model="form.endDate"
										placeholder="Pilih tanggal selesai"
									/>
								</div>
							</div>
						</div>

						<!-- Kata-kata Dosen -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Pesan Singkat Dosen<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">Masukkan testimoni atau kutipan dari aktivitas dosen!</p>
							<textarea
								v-model="form.lecturerQuote"
								rows="5"
								placeholder="Pelatihan ini sangat keren dan hebat, saya merasa berkembang setelah mengikuti kegiatan ini"
								class="mt-1.5 w-full rounded-[10px] border border-[#d6e0ee] bg-white p-3 font-inter text-[13px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0 resize-y min-h-[160px] max-h-[250px]"
							></textarea>
						</div>

					</div>
				</div>

				<!-- Modal Footer Buttons -->
				<div class="mt-7 flex items-center justify-end gap-3.5 pt-4 border-t border-[#f0f4f9]">
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
						{{ isEditing ? 'Simpan Perubahan' : 'Tambah Aktivitas' }}
					</button>
				</div>
			</form>
					</div>
				</Transition>
			</div>
		</Transition>

	</Teleport>

	<!-- Separate Top-Level Lightbox Image Modal Preview (z-[100] above all modals) -->
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
				v-if="previewingImage"
				class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-md p-4 transition-all"
				@click="closeImagePreview"
			>
				<Transition
					enter-active-class="ease-out duration-200"
					enter-from-class="opacity-0 scale-95"
					enter-to-class="opacity-100 scale-100"
					leave-active-class="ease-in duration-150"
					leave-from-class="opacity-100 scale-100"
					leave-to-class="opacity-0 scale-95"
				>
					<div
						v-if="previewingImage"
						class="relative max-h-[90vh] max-w-[90vw] overflow-hidden rounded-xl bg-transparent"
						@click.stop
					>
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
				</Transition>
			</div>
		</Transition>
	</Teleport>

	<!-- Modal Delete Confirmation for Image -->
	<ModalDeleteConfirmation
		:show="showDeleteImageModal"
		title="Hapus Gambar?"
		:item-name="`Foto Lampiran ke-${imageIndexToDelete !== null ? imageIndexToDelete + 1 : ''}`"
		message="Apakah Anda yakin ingin menghapus gambar ini dari form aktivitas?"
		confirm-button-text="Hapus Gambar"
		@close="showDeleteImageModal = false"
		@confirm="executeRemoveImage"
	/>
</template>
