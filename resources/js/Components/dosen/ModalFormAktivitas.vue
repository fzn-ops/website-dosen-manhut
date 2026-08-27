<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import DatePicker from '@/Components/DatePicker.vue';

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
	lecturerName: {
		type: String,
		default: 'Farhan Hakim',
	},
});

const emit = defineEmits(['close', 'submit']);

const categoryOptions = ['Lokakarya', 'Seminar', 'Workshop', 'Lainnya'];

const form = ref({
	title: '',
	description: '',
	role: '',
	startDate: '',
	endDate: '',
	images: [],
	imagePreviews: [],
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

// Category Toggle (can check/uncheck freely)
const toggleCategory = (cat) => {
	const idx = form.value.categories.indexOf(cat);
	if (idx > -1) {
		form.value.categories.splice(idx, 1);
	} else {
		form.value.categories.push(cat);
		errors.value.categories = '';
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
			previewingImage.value = null;

			if (props.isEditing && props.initialData) {
				let cats = [];
				if (Array.isArray(props.initialData.categories) && props.initialData.categories.length > 0) {
					cats = [...props.initialData.categories];
				} else if (props.initialData.category && props.initialData.category !== '-') {
					cats = [props.initialData.category];
				}

				form.value = {
					title: props.initialData.title || props.initialData.name || '',
					description: props.initialData.description || '',
					role: props.initialData.role || '',
					startDate: props.initialData.startDate || '',
					endDate: props.initialData.endDate || '',
					images: props.initialData.images || [],
					imagePreviews: props.initialData.imagePreviews || (props.initialData.image ? [props.initialData.image] : []),
					lecturerQuote: props.initialData.lecturerQuote !== '-' ? (props.initialData.lecturerQuote || '') : '',
					categories: cats,
					releaseDate: props.initialData.date || props.initialData.releaseDate || getFormattedToday(),
				};
			} else {
				form.value = {
					title: '',
					description: '',
					role: '',
					startDate: '',
					endDate: '',
					images: [],
					imagePreviews: [],
					lecturerQuote: '',
					categories: [], // Kosong default untuk tambah data baru
					releaseDate: getFormattedToday(),
				};
			}
		}
	},
	{ immediate: true }
);

const handleFiles = (files) => {
	if (!files || files.length === 0) return;

	const remainingSlots = 3 - form.value.imagePreviews.length;
	if (remainingSlots <= 0) {
		formError.value = 'Maksimal upload adalah 3 gambar.';
		return;
	}

	const filesToProcess = Array.from(files).slice(0, remainingSlots);

	filesToProcess.forEach((file) => {
		if (file.size > 10 * 1024 * 1024) {
			formError.value = `File ${file.name} melebihi batas ukuran 10MB.`;
			return;
		}

		if (!file.type.startsWith('image/')) {
			formError.value = `File ${file.name} bukan merupakan format gambar yang valid.`;
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

const removeImage = (index) => {
	form.value.images.splice(index, 1);
	form.value.imagePreviews.splice(index, 1);
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
	emit('close');
};

const handleSubmit = () => {
	formError.value = '';
	errors.value = {};
	const title = form.value.title.trim();
	const rawDescription = form.value.description || '';
	const cleanDesc = rawDescription.replace(/<[^>]*>/g, '').trim();
	const role = form.value.role.trim();

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
		name: title,
		title: title,
		lecturer: props.lecturerName,
		lecturerName: props.lecturerName,
		description: rawDescription,
		role: role,
		startDate: form.value.startDate,
		endDate: form.value.endDate || form.value.startDate,
		images: form.value.images,
		imagePreviews: form.value.imagePreviews,
		lecturerQuote: form.value.lecturerQuote.trim() || '-',
		category: form.value.categories.length > 0 ? form.value.categories[0] : 'Lainnya',
		categories: [...form.value.categories],
		date: form.value.releaseDate || getFormattedToday(),
	});

	handleClose();
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
			class="w-full max-w-[1240px] transform rounded-[10px] bg-white p-7 shadow-2xl transition-all sm:p-10 lg:p-12 font-poppins max-h-[92vh] overflow-y-auto"
		>
			<!-- Header Title -->
			<h2 class="text-left text-[24px] font-bold text-[#183669]">
				{{ isEditing ? 'Form Edit Aktivitas Saya' : 'Form Tambah Aktivitas Saya' }}
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
							<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">Pilih kategori aktivitas yang sesuai</p>
							<div class="mt-2 flex flex-wrap items-center gap-x-6 gap-y-2 font-inter text-[13px] text-[#1e3456]">
								<label
									v-for="cat in categoryOptions"
									:key="cat"
									class="inline-flex cursor-pointer items-center gap-2 select-none group"
								>
									<input
										type="checkbox"
										:value="cat"
										:checked="form.categories.includes(cat)"
										@change="toggleCategory(cat)"
										class="h-4 w-4 rounded border-[#c3d1e4] text-[#183669] transition-colors hover:border-[#183669] focus:ring-0 focus:ring-offset-0 cursor-pointer"
									/>
									<span class="font-medium text-[#2f4b6e] group-hover:text-[#183669] transition-colors">{{ cat }}</span>
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
							<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">Masukkan peran dalam kegiatan (e.g. Narasumber, Pemateri)</p>
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
								min-height="225px"
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
								Masukkan gambar pendukung (MAX 10mb, JPG/PNG, 3 Gambar)
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
											class="group relative aspect-video overflow-hidden rounded-[8px] border border-[#d6e0ee] bg-slate-100 shadow-xs"
										>
											<img :src="img" alt="Preview Gambar" class="h-full w-full object-cover" />

											<!-- Action Overlays on Hover -->
											<div class="absolute inset-0 flex items-center justify-center gap-1.5 bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
												<!-- Zoom Preview Button -->
												<button
													type="button"
													@click.stop="openImagePreview(img)"
													class="flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-[#183669] transition hover:bg-white hover:scale-110"
													title="Lihat Gambar Penuh"
												>
													<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
													</svg>
												</button>
												<!-- Delete Button -->
												<button
													type="button"
													@click.stop="removeImage(idx)"
													class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500/90 text-white transition hover:bg-red-600 hover:scale-110"
													title="Hapus Gambar"
												>
													<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
													</svg>
												</button>
											</div>
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
								<label class="block text-[14px] font-bold text-[#183669]">
									Tanggal Mulai<span class="text-red-500">*</span>
								</label>
								<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">Tanggal mulai aktivitas</p>
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
								<label class="block text-[14px] font-bold text-[#183669]">
									Tanggal Selesai
								</label>
								<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">Tanggal batas selesai</p>
								<div class="mt-1.5">
									<DatePicker
										v-model="form.endDate"
										placeholder="Pilih tanggal selesai"
									/>
								</div>
							</div>
						</div>

						<!-- Pesan Singkat Dosen (Kutipan) -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Pesan Singkat Dosen<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3] mt-0.5">Pesan atau kesan Anda mengenai kegiatan ini</p>
							<textarea
								v-model="form.lecturerQuote"
								placeholder="Pelatihan ini sangat berkesan dan bermanfaat, saya merasa berkembang setelah mengikuti kegiatan ini..."
								class="mt-1.5 h-[245px] min-h-[230px] max-h-[360px] w-full rounded-[10px] border border-[#d6e0ee] bg-white p-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0 resize-y leading-relaxed"
							></textarea>
						</div>
					</div>
				</div>

				<!-- Action Buttons -->
				<div class="mt-8 flex items-center justify-end gap-3.5 border-t border-[#e2e8f0] pt-6">
					<button
						type="button"
						@click="handleClose"
						class="rounded-[8px] border border-[#d6e0ee] bg-white px-6 py-2.5 font-poppins text-[14px] font-semibold text-[#4d6786] shadow-xs transition hover:bg-slate-100 focus:outline-none"
					>
						Batal
					</button>
					<button
						type="submit"
						class="rounded-[8px] bg-[#183669] px-7 py-2.5 font-poppins text-[14px] font-semibold text-white shadow-xs transition hover:bg-[#122b54] focus:outline-none"
					>
						{{ isEditing ? 'Simpan Perubahan' : 'Tambah Aktivitas' }}
					</button>
				</div>
			</form>
		</div>

		<!-- Lightbox Image Modal Preview -->
		<div
			v-if="previewingImage"
			class="fixed inset-0 z-60 flex items-center justify-center bg-black/80 p-4 transition-all"
			@click="closeImagePreview"
		>
			<div class="relative max-h-[90vh] max-w-[90vw] overflow-hidden rounded-xl bg-transparent" @click.stop>
				<button
					type="button"
					@click="closeImagePreview"
					class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/90 focus:outline-none"
				>
					<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
				<img :src="previewingImage" alt="Zoomed Preview" class="max-h-[85vh] max-w-[85vw] rounded-lg object-contain shadow-2xl" />
			</div>
		</div>
	</div>
</template>
