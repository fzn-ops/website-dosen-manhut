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
	availableProfiles: {
		type: Array,
		default: () => [],
	},
});

const emit = defineEmits(['close', 'submit']);

const categoryOptions = ['Lokakarya', 'Seminar', 'Workshop', 'Lainnya'];

const form = ref({
	title: '',
	lecturerName: '',
	description: '',
	role: '',
	startDate: '',
	endDate: '',
	images: [],
	imagePreviews: [],
	lecturerQuote: '',
	categories: ['Lokakarya'],
	releaseDate: '',
});

const formError = ref('');
const isDragging = ref(false);
const imageInputRef = ref(null);

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
	form.value.lecturerName = lec.name;
	isNameDropdownOpen.value = false;
	lecturerSearchQuery.value = '';
};

// Category Toggle
const toggleCategory = (cat) => {
	const idx = form.value.categories.indexOf(cat);
	if (idx > -1) {
		if (form.value.categories.length > 1) {
			form.value.categories.splice(idx, 1);
		}
	} else {
		form.value.categories.push(cat);
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
			isNameDropdownOpen.value = false;
			lecturerSearchQuery.value = '';

			if (props.isEditing && props.initialData) {
				form.value = {
					title: props.initialData.title || props.initialData.name || '',
					lecturerName: props.initialData.lecturerName || props.initialData.lecturer || '',
					description: props.initialData.description || '',
					role: props.initialData.role || '',
					startDate: props.initialData.startDate || '',
					endDate: props.initialData.endDate || '',
					images: props.initialData.images || [],
					imagePreviews: props.initialData.imagePreviews || (props.initialData.image ? [props.initialData.image] : []),
					lecturerQuote: props.initialData.lecturerQuote !== '-' ? props.initialData.lecturerQuote : '',
					categories: Array.isArray(props.initialData.categories) && props.initialData.categories.length > 0
						? [...props.initialData.categories]
						: [props.initialData.category || 'Lokakarya'],
					releaseDate: props.initialData.date || props.initialData.releaseDate || getFormattedToday(),
				};
			} else {
				form.value = {
					title: '',
					lecturerName: '',
					description: '',
					role: '',
					startDate: '',
					endDate: '',
					images: [],
					imagePreviews: [],
					lecturerQuote: '',
					categories: ['Lokakarya'],
					releaseDate: getFormattedToday(),
				};
			}
		}
	},
	{ immediate: true }
);

// Image Upload Handling (Up to 3 images inside the container)
const handleImageSelect = (e) => {
	const files = Array.from(e.target.files || []);
	processImageFiles(files);
};

const handleImageDrop = (e) => {
	isDragging.value = false;
	const files = Array.from(e.dataTransfer?.files || []);
	processImageFiles(files);
};

const processImageFiles = (files) => {
	const validFiles = files.filter((f) => f.type.startsWith('image/'));
	if (validFiles.length === 0) {
		formError.value = 'Harap unggah file gambar yang valid (jpg, png, jpeg).';
		return;
	}

	const remainingSlots = 3 - form.value.imagePreviews.length;
	if (remainingSlots <= 0) {
		formError.value = 'Maksimal 3 gambar yang dapat diunggah.';
		return;
	}

	const filesToAdd = validFiles.slice(0, remainingSlots);

	filesToAdd.forEach((file) => {
		if (file.size > 10 * 1024 * 1024) {
			formError.value = 'Ukuran gambar maksimal 10MB per file.';
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

const removeImageAt = (index) => {
	form.value.imagePreviews.splice(index, 1);
	if (form.value.images[index]) {
		form.value.images.splice(index, 1);
	}
	if (imageInputRef.value) {
		imageInputRef.value.value = '';
	}
};

const handleClose = () => {
	formError.value = '';
	isNameDropdownOpen.value = false;
	emit('close');
};

const handleSubmit = () => {
	formError.value = '';
	const title = form.value.title.trim();
	const lecturerName = form.value.lecturerName.trim();
	const description = form.value.description.trim();
	const role = form.value.role.trim();

	if (!title) {
		formError.value = 'Judul Aktivitas wajib diisi.';
		return;
	}
	if (!lecturerName) {
		formError.value = 'Nama Dosen wajib dipilih.';
		return;
	}
	if (!description) {
		formError.value = 'Deskripsi aktivitas wajib diisi.';
		return;
	}
	if (!role) {
		formError.value = 'Peran dalam aktivitas wajib diisi.';
		return;
	}
	if (!form.value.startDate) {
		formError.value = 'Tanggal Mulai wajib diisi.';
		return;
	}
	if (form.value.categories.length === 0) {
		formError.value = 'Pilih minimal 1 kategori aktivitas.';
		return;
	}

	emit('submit', {
		name: title,
		title: title,
		lecturer: lecturerName,
		lecturerName: lecturerName,
		description: description,
		role: role,
		startDate: form.value.startDate,
		endDate: form.value.endDate || form.value.startDate,
		images: form.value.images,
		imagePreviews: form.value.imagePreviews,
		lecturerQuote: form.value.lecturerQuote.trim() || '-',
		category: form.value.categories[0],
		categories: form.value.categories,
		date: form.value.releaseDate || getFormattedToday(),
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
			class="w-full max-w-[1240px] transform rounded-[10px] bg-white p-7 shadow-2xl transition-all sm:p-10 lg:p-12 font-poppins max-h-[92vh] overflow-y-auto"
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

			<form @submit.prevent="handleSubmit" class="mt-7 space-y-7 font-poppins">
				<!-- 2 Column Layout with generous spacing -->
				<div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-14 xl:gap-16">
					<!-- ================= LEFT COLUMN ================= -->
					<div class="space-y-5">
						<!-- Subgrid: Judul Aktivitas & Nama Dosen (Aligned pixel-perfect with min-height labels) -->
						<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 items-start">
							<!-- Judul Aktivitas -->
							<div class="flex flex-col">
								<div class="min-h-[44px]">
									<label class="block text-[14px] font-bold text-[#183669]">
										Judul Aktivitas<span class="text-red-500">*</span>
									</label>
									<p class="font-inter text-[11px] text-[#7188a3] leading-tight">Masukkan judul aktivitas yang diajukan di sini yah!</p>
								</div>
								<input
									v-model="form.title"
									type="text"
									placeholder="Pelatihan manajer KDMP"
									required
									class="mt-1.5 h-[44px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
								/>
							</div>

							<!-- Nama Dosen (Searchable Dropdown from Profile Dosen) -->
							<div class="relative flex flex-col" @click.stop>
								<div class="min-h-[44px]">
									<label class="block text-[14px] font-bold text-[#183669]">
										Nama Dosen<span class="text-red-500">*</span>
									</label>
									<p class="font-inter text-[11px] text-[#7188a3] leading-tight">Pilih Nama Dosen (berdasarkan profile dosen)</p>
								</div>

								<!-- Dropdown Trigger Button -->
								<button
									type="button"
									@click="toggleNameDropdown"
									class="mt-1.5 flex h-[44px] w-full items-center justify-between rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[13px] transition focus:border-[#183669] focus:outline-none focus:ring-0"
									:class="{ 'border-[#183669] ring-1 ring-[#183669]/20': isNameDropdownOpen }"
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
											class="h-[36px] w-full rounded-[8px] border border-[#d6e0ee] bg-[#fafcff] pl-8 pr-3 text-xs text-[#1e3456] placeholder-[#8ca1b9] focus:border-[#183669] focus:outline-none focus:ring-0"
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
						</div>

						<!-- Deskripsi -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Deskripsi<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3]">Jelaskan gambaran kegiatan ini yah!</p>
							<textarea
								v-model="form.description"
								rows="3"
								placeholder="Pelatihan manajer KDMP..."
								required
								class="mt-1.5 w-full rounded-[10px] border border-[#d6e0ee] bg-white p-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
							></textarea>
						</div>

						<!-- Peran -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Peran<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3]">Masukkan peran dalam kegiatan ini (e.g. Narasumber, Pemateri, dll.)</p>
							<input
								v-model="form.role"
								type="text"
								placeholder="Narasumber"
								required
								class="mt-1.5 h-[44px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
							/>
						</div>

						<!-- Tanggal Mulai & Tanggal Selesai Subgrid (Aligned pixel-perfect) -->
						<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 items-start">
							<!-- Tanggal Mulai -->
							<div class="flex flex-col">
								<div class="min-h-[40px]">
									<label class="block text-[14px] font-bold text-[#183669]">
										Tanggal Mulai<span class="text-red-500">*</span>
									</label>
									<p class="font-inter text-[11px] text-[#7188a3] leading-tight">Masukkan tanggal mulai aktivitas</p>
								</div>
								<input
									v-model="form.startDate"
									type="date"
									required
									class="mt-1.5 h-[44px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] focus:border-[#183669] focus:outline-none focus:ring-0"
								/>
							</div>

							<!-- Tanggal Selesai -->
							<div class="flex flex-col">
								<div class="min-h-[40px]">
									<label class="block text-[14px] font-bold text-[#183669]">
										Tanggal Selesai
									</label>
									<p class="font-inter text-[11px] text-[#7188a3] leading-tight">Masukkan tanggal batas selesai aktivitas</p>
								</div>
								<input
									v-model="form.endDate"
									type="date"
									class="mt-1.5 h-[44px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] focus:border-[#183669] focus:outline-none focus:ring-0"
								/>
							</div>
						</div>
					</div>

					<!-- ================= RIGHT COLUMN ================= -->
					<div class="space-y-5">
						<!-- Gambar Upload Area (Previews rendered INSIDE the upload field box) -->
						<div>
							<div class="flex items-center justify-between">
								<label class="block text-[14px] font-bold text-[#183669]">
									Gambar<span class="text-red-500">*</span>
								</label>
								<span class="text-[11px] font-semibold text-[#7188a3]">{{ form.imagePreviews.length }}/3 Gambar</span>
							</div>
							<p class="font-inter text-[11px] text-[#7188a3]">
								Masukkan gambar pendukung berupa jpg/png/jpeg! (MAX 10mb, 3 Gambar)
							</p>

							<!-- Image Upload Box Container -->
							<div
								@dragover.prevent="isDragging = true"
								@dragleave.prevent="isDragging = false"
								@drop.prevent="handleImageDrop"
								:class="[
									'mt-1.5 flex flex-col items-center justify-center rounded-[12px] border-2 border-dashed p-4 text-center transition-colors min-h-[160px]',
									isDragging ? 'border-[#183669] bg-[#183669]/5' : 'border-[#183669]/30 bg-[#fafcff] hover:border-[#183669]/60'
								]"
							>
								<!-- State 1: No images uploaded yet -->
								<div v-if="form.imagePreviews.length === 0" class="flex flex-col items-center justify-center py-3">
									<svg class="h-10 w-10 text-[#8c9eb5]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
									</svg>
									<p class="mt-2 font-inter text-[12px] text-[#7188a3]">
										Upload gambar atau seret gambar ke form ini
									</p>
									<button
										type="button"
										@click="$refs.imageInputRef.click()"
										class="mt-2.5 rounded-[8px] border border-[#a6b7cb] bg-white px-6 py-1.5 font-inter text-[12px] font-semibold text-[#5a718d] transition hover:bg-slate-50"
									>
										Upload
									</button>
								</div>

								<!-- State 2: Images uploaded - Rendered INSIDE the upload field box -->
								<div v-else class="flex w-full flex-col items-center gap-3 py-2">
									<div class="flex flex-wrap items-center justify-center gap-3.5">
										<div
											v-for="(img, idx) in form.imagePreviews"
											:key="idx"
											class="relative h-20 w-20 overflow-hidden rounded-[8px] border-2 border-[#183669]/20 shadow-sm group"
										>
											<img :src="img" alt="Preview" class="h-full w-full object-cover" />
											<button
												type="button"
												@click="removeImageAt(idx)"
												class="absolute inset-0 flex items-center justify-center bg-black/60 text-white opacity-0 transition group-hover:opacity-100"
												title="Hapus gambar"
											>
												<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
												</svg>
											</button>
										</div>

										<!-- Add more button if < 3 inside the box -->
										<button
											v-if="form.imagePreviews.length < 3"
											type="button"
											@click="$refs.imageInputRef.click()"
											class="flex h-20 w-20 flex-col items-center justify-center rounded-[8px] border-2 border-dashed border-[#8ca1b9] bg-white text-[#7188a3] transition hover:border-[#183669] hover:text-[#183669]"
											title="Tambah Gambar Lainnya"
										>
											<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
											</svg>
											<span class="text-[10px] font-semibold mt-1">+ Foto</span>
										</button>
									</div>
									<p class="font-inter text-[11px] text-[#7188a3]">Klik pada gambar untuk menghapus</p>
								</div>

								<input
									type="file"
									ref="imageInputRef"
									accept="image/png,image/jpeg,image/jpg"
									multiple
									class="hidden"
									@change="handleImageSelect"
								/>
							</div>
						</div>

						<!-- Kata-kata Dosen -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Kata-kata Dosen<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3]">Masukkan testimoni atau kutipan dari aktivitas dosen!</p>
							<textarea
								v-model="form.lecturerQuote"
								rows="2"
								placeholder="Pelatihan ini sangat keren dan hebat, saya merasa berkembang setelah mengikuti kegiatan ini"
								class="mt-1.5 w-full rounded-[10px] border border-[#d6e0ee] bg-white p-3 font-inter text-[13px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
							></textarea>
						</div>

						<!-- Kategori Checkboxes -->
						<div>
							<label class="block text-[14px] font-bold text-[#183669]">
								Kategori<span class="text-red-500">*</span>
							</label>
							<p class="font-inter text-[11px] text-[#7188a3]">Masukkan kategori dari aktivitas dosen</p>

							<div class="mt-2.5 flex flex-wrap items-center gap-5">
								<label
									v-for="cat in categoryOptions"
									:key="cat"
									class="flex cursor-pointer items-center gap-2 select-none"
								>
									<input
										type="checkbox"
										:checked="form.categories.includes(cat)"
										@change="toggleCategory(cat)"
										class="h-4 w-4 rounded border-[#a6b7cb] text-[#183669] focus:ring-0 focus:ring-offset-0"
									/>
									<span class="font-inter text-[13px] font-medium text-[#435b76]">{{ cat }}</span>
								</label>
							</div>
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
