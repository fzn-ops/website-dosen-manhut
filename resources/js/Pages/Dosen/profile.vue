<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, nextTick, ref } from 'vue';
import DosenLayout from '@/Layouts/DosenLayout.vue';

// 1. State Data Diri
const formPersonal = ref({
	name: 'Dr. John Doe, M.Si',
	nip: '198503122010121002',
	photo: null,
	photoPreview: null,
});

const isDragging = ref(false);
const fileInputRef = ref(null);
const previewingImage = ref(null);

const openImagePreview = (img) => {
	previewingImage.value = img;
};

const closeImagePreview = () => {
	previewingImage.value = null;
};

const handleFileSelect = (e) => {
	const file = e.target.files?.[0];
	if (file) {
		processFile(file);
	}
};

const handleFileDrop = (e) => {
	isDragging.value = false;
	const file = e.dataTransfer?.files?.[0];
	if (file) {
		processFile(file);
	}
};

const processFile = (file) => {
	if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
		alert('Format file harus berupa JPG, JPEG, atau PNG');
		return;
	}
	if (file.size > 10 * 1024 * 1024) {
		alert('Ukuran file maksimal 10MB');
		return;
	}
	formPersonal.value.photo = file;
	formPersonal.value.photoPreview = URL.createObjectURL(file);
};

const removePhoto = () => {
	formPersonal.value.photo = null;
	formPersonal.value.photoPreview = null;
	if (fileInputRef.value) {
		fileInputRef.value.value = '';
	}
};

// 2. State Data Akun (Email kosong & No Handphone terisi untuk demo)
const formAccount = ref({
	email: '',
	phone: '+62 812-3456-7890',
});

const isEditingEmail = ref(false);
const isEditingPhone = ref(false);
const emailInputRef = ref(null);
const phoneInputRef = ref(null);

// Helper untuk mengecek apakah field akun terkunci
const isEmailLocked = computed(() => formAccount.value.email.trim() !== '' && !isEditingEmail.value);
const isPhoneLocked = computed(() => formAccount.value.phone.trim() !== '' && !isEditingPhone.value);

const toggleEditEmail = () => {
	isEditingEmail.value = !isEditingEmail.value;
	if (isEditingEmail.value) {
		nextTick(() => {
			emailInputRef.value?.focus();
		});
	}
};

const toggleEditPhone = () => {
	isEditingPhone.value = !isEditingPhone.value;
	if (isEditingPhone.value) {
		nextTick(() => {
			phoneInputRef.value?.focus();
		});
	}
};

const onBlurEmail = () => {
	isEditingEmail.value = false;
};

const onBlurPhone = () => {
	isEditingPhone.value = false;
};

// 3. State Ganti Password (Show / Hide toggles)
const formPassword = ref({
	currentPassword: '',
	newPassword: '',
	confirmPassword: '',
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

// Save Handlers
const savePersonalData = () => {
	if (!formPersonal.value.name.trim()) {
		alert('Nama tidak boleh kosong');
		return;
	}
};

const saveAccountData = () => {
	if (!formAccount.value.email.trim()) {
		alert('Email tidak boleh kosong');
		return;
	}
	isEditingEmail.value = false;
	isEditingPhone.value = false;
};

const savePassword = () => {
	if (!formPassword.value.currentPassword) {
		alert('Password saat ini wajib diisi');
		return;
	}
	if (!formPassword.value.newPassword) {
		alert('Password baru wajib diisi');
		return;
	}
	if (formPassword.value.newPassword.length < 8) {
		alert('Password baru minimal 8 karakter');
		return;
	}
	if (formPassword.value.newPassword !== formPassword.value.confirmPassword) {
		alert('Konfirmasi password baru tidak cocok');
		return;
	}

	formPassword.value.currentPassword = '';
	formPassword.value.newPassword = '';
	formPassword.value.confirmPassword = '';
};
</script>

<template>
	<Head title="Profile Dosen" />

	<DosenLayout>
		<section class="mx-auto w-full max-w-[1520px] px-4 py-6 font-poppins sm:px-6 sm:py-8 lg:px-8">
			<div class="space-y-6">
				<!-- Header Section -->
				<div class="space-y-1.5">
					<h1 class="mt-1 text-[32px] font-bold leading-[1.05] tracking-[-0.03em] text-[#173a63] sm:text-[40px] lg:text-[46px]">
						Profile
					</h1>
					<p class="mt-1 font-inter text-[13px] font-medium leading-tight text-[#4d6786] sm:text-[15px]">
						Lihat data profile, perbarui, atau tambahkan profile baru
					</p>
				</div>

				<!-- Main Profile Form Grid -->
				<div class="grid grid-cols-1 items-stretch gap-6 lg:grid-cols-12">
					<!-- LEFT COLUMN: Data Diri (Card 1) -->
					<div class="flex h-full flex-col justify-between rounded-[12px] bg-white p-5 shadow-sm ring-1 ring-[#d6e0ee] sm:p-6 lg:col-span-5">
						<div>
							<h2 class="font-poppins text-[18px] font-bold text-[#173a63] sm:text-[20px]">
								Data diri
							</h2>

							<form @submit.prevent="savePersonalData" class="mt-5 space-y-4">
								<!-- Upload Foto Diri (Matching ModalFormAktivitas.vue Dropzone Style) -->
								<div>
									<div class="flex items-center justify-between">
										<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
											Foto diri
										</label>
										<span class="text-[11px] font-semibold text-[#7188a3]">
											{{ formPersonal.photoPreview ? '1/1' : '0/1' }} Foto
										</span>
									</div>
									<p class="mt-0.5 font-inter text-[12px] text-[#7188a3]">
										Masukan foto terbaikmu berupa jpg/png/jpeg (MAX 10mb)
									</p>

									<!-- Dropzone Box Container (Fixed 250px Height, Contained Preview) -->
									<div
										@dragover.prevent="isDragging = true"
										@dragleave.prevent="isDragging = false"
										@drop.prevent="handleFileDrop"
										:class="[
											'mt-2 flex h-[250px] max-h-[250px] w-full flex-col items-center justify-center overflow-hidden rounded-[12px] border-2 border-dashed p-3 text-center transition-colors',
											isDragging
												? 'border-[#183669] bg-[#183669]/5'
												: 'border-[#183669]/30 bg-[#fafcff] hover:border-[#183669]/60'
										]"
									>
										<!-- State 1: No Photo Uploaded -->
										<div v-if="!formPersonal.photoPreview" class="flex flex-col items-center justify-center py-2.5">
											<svg class="h-9 w-9 text-[#8c9eb5]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
											</svg>
											<p class="mt-1.5 font-inter text-[12px] text-[#7188a3]">
												Upload gambar atau seret gambar ke form ini
											</p>
											<button
												type="button"
												@click="fileInputRef?.click()"
												class="mt-2 rounded-[8px] border border-[#a6b7cb] bg-white px-5 py-1 font-inter text-[12px] font-semibold text-[#5a718d] transition hover:bg-slate-50 shadow-xs"
											>
												Upload
											</button>
										</div>

										<!-- State 2: 1 Single Image Preview with Contained Size & Hover Actions -->
										<div v-else class="flex h-full w-full items-center justify-center p-1">
											<div class="group relative flex max-h-[220px] max-w-[90%] items-center justify-center overflow-hidden rounded-[10px] border border-[#d6e0ee] bg-slate-100 shadow-sm">
												<img
													:src="formPersonal.photoPreview"
													alt="Preview Foto Diri"
													class="max-h-[220px] w-auto max-w-full rounded-[9px] object-contain cursor-pointer transition hover:opacity-95"
													@click="openImagePreview(formPersonal.photoPreview)"
												/>

												<!-- Action Overlays on Hover (Matching ModalFormAktivitas.vue) -->
												<div class="absolute inset-0 flex items-center justify-center gap-2 bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
													<!-- Preview / Zoom Button -->
													<button
														type="button"
														@click.stop="openImagePreview(formPersonal.photoPreview)"
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
														@click.stop="fileInputRef?.click()"
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
														@click.stop="removePhoto"
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
											ref="fileInputRef"
											type="file"
											accept="image/jpeg,image/png,image/jpg"
											class="hidden"
											@change="handleFileSelect"
										/>
									</div>
								</div>

								<!-- Nama (Non-editable / Non-selectable) -->
								<div>
									<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
										Nama
									</label>
									<div class="mt-1 flex h-[42px] items-center border-b border-[#d6e0ee] font-inter text-[14px] font-medium text-[#173a63] select-none cursor-default">
										{{ formPersonal.name }}
									</div>
								</div>

								<!-- NIP (Non-editable / Non-selectable) -->
								<div>
									<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
										NIP
									</label>
									<div class="mt-1 flex h-[42px] items-center border-b border-[#d6e0ee] font-inter text-[14px] font-medium text-[#173a63] select-none cursor-default">
										{{ formPersonal.nip }}
									</div>
								</div>
							</form>
						</div>

						<!-- Submit Button Data Diri (Always at the bottom) -->
						<div class="flex justify-end pt-4">
							<button
								type="button"
								@click="savePersonalData"
								class="inline-flex items-center gap-2 rounded-[8px] bg-[#183669] px-6 py-2.5 font-poppins text-[14px] font-semibold text-white shadow-sm transition hover:bg-[#122b54] active:scale-[0.98]"
							>
								<img src="/assets/icons/save.svg" alt="Save Icon" class="h-4 w-4 object-contain" />
								<span>Simpan</span>
							</button>
						</div>
					</div>

					<!-- RIGHT COLUMN: Data Akun (Card 2) & Ganti Password (Card 3) -->
					<div class="w-full space-y-6 lg:col-span-7">
						<!-- Card 2: Data Akun -->
						<div class="rounded-[12px] bg-white p-5 shadow-sm ring-1 ring-[#d6e0ee] sm:p-6">
							<h2 class="font-poppins text-[18px] font-bold text-[#173a63] sm:text-[20px]">
								Data Akun
							</h2>

							<form @submit.prevent="saveAccountData" class="mt-5 space-y-4">
								<div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
									<!-- Email Field -->
									<div class="flex flex-col">
										<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
											Email<span class="text-red-500">*</span>
										</label>
										<p class="mt-0.5 min-h-[20px] font-inter text-[12px] leading-snug text-[#7188a3] sm:min-h-[36px] xl:min-h-[20px]">
											Masukkan email aktif yang kamu gunakan
										</p>
										<div class="relative mt-1">
											<input
												ref="emailInputRef"
												v-model="formAccount.email"
												type="email"
												:readonly="isEmailLocked"
												:tabindex="isEmailLocked ? -1 : 0"
												@blur="onBlurEmail"
												placeholder="contoh: nama@email.com"
												:class="[
													'custom-input h-[46px] w-full rounded-[10px] bg-transparent pl-3.5 pr-10 font-inter text-[14px] text-[#173a63] placeholder-[#a8bed4] transition-colors focus:outline-none',
													isEditingEmail
														? 'border-[#183669]'
														: isEmailLocked
															? 'border-[#d6e0ee] cursor-not-allowed select-none pointer-events-none'
															: 'border-[#d6e0ee] hover:border-[#183669]'
												]"
											/>
											<button
												type="button"
												@mousedown.prevent
												@click="toggleEditEmail"
												class="absolute inset-y-0 right-0 flex items-center pr-3 transition hover:opacity-80 focus:outline-none"
												:title="isEditingEmail ? 'Kunci input email' : 'Buka untuk mengedit email'"
											>
												<svg class="h-4 w-4 text-[#183669]" viewBox="0 0 19 19" fill="currentColor">
													<path d="M4.92119 4.92074C4.92119 5.18177 4.81749 5.43211 4.63291 5.61669C4.44833 5.80126 4.19798 5.90496 3.93695 5.90496H2.95271C2.69168 5.90496 2.44133 6.00865 2.25675 6.19323C2.07217 6.3778 1.96847 6.62814 1.96847 6.88917V15.7471C1.96847 16.0082 2.07217 16.2585 2.25675 16.4431C2.44133 16.6277 2.69168 16.7313 2.95271 16.7313H11.8108C12.0719 16.7313 12.3222 16.6277 12.5068 16.4431C12.6914 16.2585 12.7951 16.0082 12.7951 15.7471V14.7629C12.7951 14.5019 12.8988 14.2515 13.0834 14.067C13.2679 13.8824 13.5183 13.7787 13.7793 13.7787C14.0404 13.7787 14.2907 13.8824 14.4753 14.067C14.6599 14.2515 14.7636 14.5019 14.7636 14.7629V15.7471C14.7636 16.5302 14.4525 17.2812 13.8987 17.835C13.345 18.3887 12.594 18.6998 11.8108 18.6998H2.95271C2.1696 18.6998 1.41857 18.3887 0.864829 17.835C0.311088 17.2812 0 16.5302 0 15.7471V6.88917C0 6.10608 0.311088 5.35506 0.864829 4.80134C1.41857 4.24761 2.1696 3.93652 2.95271 3.93652H3.93695C4.19798 3.93652 4.44833 4.04022 4.63291 4.22479C4.81749 4.40937 4.92119 4.65971 4.92119 4.92074Z" />
													<path d="M11.413 2.96342L15.7358 7.2861L9.55481 13.4896C9.4634 13.5813 9.3548 13.6541 9.23522 13.7037C9.11564 13.7534 8.98744 13.779 8.85797 13.779H5.90526C5.64422 13.779 5.39388 13.6753 5.2093 13.4907C5.02472 13.3061 4.92102 13.0558 4.92102 12.7948V9.84211C4.92105 9.71264 4.94662 9.58444 4.99628 9.46487C5.04593 9.34529 5.11869 9.23669 5.21039 9.14528L11.413 2.96342ZM17.8067 0.893608C18.3495 1.43606 18.6678 2.16332 18.6979 2.93014C18.728 3.69697 18.4677 4.44693 17.9691 5.03027L17.8076 5.20743L17.1256 5.89048L12.8077 1.57272L13.4918 0.893608C14.064 0.32144 14.84 0 15.6492 0C16.4584 0 17.2345 0.32144 17.8067 0.893608Z" />
												</svg>
											</button>
										</div>
									</div>

									<!-- Nomor Handphone Field -->
									<div class="flex flex-col">
										<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
											Nomor Handphone
										</label>
										<p class="mt-0.5 min-h-[20px] font-inter text-[12px] leading-snug text-[#7188a3] sm:min-h-[36px] xl:min-h-[20px]">
											Masukkan nomor handphone aktif yang kamu gunakan
										</p>
										<div class="relative mt-1">
											<input
												ref="phoneInputRef"
												v-model="formAccount.phone"
												type="tel"
												:readonly="isPhoneLocked"
												:tabindex="isPhoneLocked ? -1 : 0"
												@blur="onBlurPhone"
												placeholder="contoh: +62 812-3456-7890"
												:class="[
													'custom-input h-[46px] w-full rounded-[10px] bg-transparent pl-3.5 pr-10 font-inter text-[14px] text-[#173a63] placeholder-[#a8bed4] transition-colors focus:outline-none',
													isEditingPhone
														? 'border-[#183669]'
														: isPhoneLocked
															? 'border-[#d6e0ee] cursor-not-allowed select-none pointer-events-none'
															: 'border-[#d6e0ee] hover:border-[#183669]'
												]"
											/>
											<button
												type="button"
												@mousedown.prevent
												@click="toggleEditPhone"
												class="absolute inset-y-0 right-0 flex items-center pr-3 transition hover:opacity-80 focus:outline-none"
												:title="isEditingPhone ? 'Kunci input handphone' : 'Buka untuk mengedit nomor handphone'"
											>
												<svg class="h-4 w-4 text-[#183669]" viewBox="0 0 19 19" fill="currentColor">
													<path d="M4.92119 4.92074C4.92119 5.18177 4.81749 5.43211 4.63291 5.61669C4.44833 5.80126 4.19798 5.90496 3.93695 5.90496H2.95271C2.69168 5.90496 2.44133 6.00865 2.25675 6.19323C2.07217 6.3778 1.96847 6.62814 1.96847 6.88917V15.7471C1.96847 16.0082 2.07217 16.2585 2.25675 16.4431C2.44133 16.6277 2.69168 16.7313 2.95271 16.7313H11.8108C12.0719 16.7313 12.3222 16.6277 12.5068 16.4431C12.6914 16.2585 12.7951 16.0082 12.7951 15.7471V14.7629C12.7951 14.5019 12.8988 14.2515 13.0834 14.067C13.2679 13.8824 13.5183 13.7787 13.7793 13.7787C14.0404 13.7787 14.2907 13.8824 14.4753 14.067C14.6599 14.2515 14.7636 14.5019 14.7636 14.7629V15.7471C14.7636 16.5302 14.4525 17.2812 13.8987 17.835C13.345 18.3887 12.594 18.6998 11.8108 18.6998H2.95271C2.1696 18.6998 1.41857 18.3887 0.864829 17.835C0.311088 17.2812 0 16.5302 0 15.7471V6.88917C0 6.10608 0.311088 5.35506 0.864829 4.80134C1.41857 4.24761 2.1696 3.93652 2.95271 3.93652H3.93695C4.19798 3.93652 4.44833 4.04022 4.63291 4.22479C4.81749 4.40937 4.92119 4.65971 4.92119 4.92074Z" />
													<path d="M11.413 2.96342L15.7358 7.2861L9.55481 13.4896C9.4634 13.5813 9.3548 13.6541 9.23522 13.7037C9.11564 13.7534 8.98744 13.779 8.85797 13.779H5.90526C5.64422 13.779 5.39388 13.6753 5.2093 13.4907C5.02472 13.3061 4.92102 13.0558 4.92102 12.7948V9.84211C4.92105 9.71264 4.94662 9.58444 4.99628 9.46487C5.04593 9.34529 5.11869 9.23669 5.21039 9.14528L11.413 2.96342ZM17.8067 0.893608C18.3495 1.43606 18.6678 2.16332 18.6979 2.93014C18.728 3.69697 18.4677 4.44693 17.9691 5.03027L17.8076 5.20743L17.1256 5.89048L12.8077 1.57272L13.4918 0.893608C14.064 0.32144 14.84 0 15.6492 0C16.4584 0 17.2345 0.32144 17.8067 0.893608Z" />
												</svg>
											</button>
										</div>
									</div>
								</div>

								<!-- Submit Button Data Akun -->
								<div class="flex justify-end pt-2">
									<button
										type="submit"
										class="inline-flex items-center gap-2 rounded-[8px] bg-[#183669] px-6 py-2.5 font-poppins text-[14px] font-semibold text-white shadow-sm transition hover:bg-[#122b54] active:scale-[0.98]"
									>
										<img src="/assets/icons/save.svg" alt="Save Icon" class="h-4 w-4 object-contain" />
										<span>Simpan</span>
									</button>
								</div>
							</form>
						</div>

						<!-- Card 3: Ganti Password -->
						<div class="rounded-[12px] bg-white p-5 shadow-sm ring-1 ring-[#d6e0ee] sm:p-6">
							<h2 class="font-poppins text-[18px] font-bold text-[#173a63] sm:text-[20px]">
								Ganti Password
							</h2>

							<form @submit.prevent="savePassword" class="mt-5 space-y-4">
								<div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
									<!-- Kolom Kiri: Password Saat Ini -->
									<div class="flex flex-col">
										<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
											Password<span class="text-red-500">*</span>
										</label>
										<p class="mt-0.5 min-h-[20px] font-inter text-[12px] leading-snug text-[#7188a3] sm:min-h-[36px] xl:min-h-[20px]">
											Masukkan password yang kamu gunakan sekarang
										</p>
										<div class="relative mt-1">
											<input
												v-model="formPassword.currentPassword"
												:type="showCurrentPassword ? 'text' : 'password'"
												placeholder="Masukkan password saat ini"
												class="custom-input h-[46px] w-full rounded-[10px] border-[#d6e0ee] bg-transparent pl-3.5 pr-10 font-inter text-[14px] text-[#173a63] placeholder-[#a8bed4] transition-colors hover:border-[#183669] focus:outline-none"
											/>
											<button
												type="button"
												@click="showCurrentPassword = !showCurrentPassword"
												class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none"
												:title="showCurrentPassword ? 'Sembunyikan password' : 'Lihat password'"
											>
												<img
													v-if="showCurrentPassword"
													src="/assets/icons/shown.svg"
													alt="Lihat password"
													class="h-4 w-4 object-contain opacity-70 transition hover:opacity-100"
												/>
												<img
													v-else
													src="/assets/icons/hidden.svg"
													alt="Sembunyikan password"
													class="h-4 w-4 object-contain opacity-70 transition hover:opacity-100"
												/>
											</button>
										</div>
									</div>

									<!-- Kolom Kanan: Password Baru & Konfirmasi Password Baru -->
									<div class="flex flex-col space-y-6">
										<!-- Password Baru -->
										<div class="flex flex-col">
											<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
												Password Baru<span class="text-red-500">*</span>
											</label>
											<p class="mt-0.5 min-h-[20px] font-inter text-[12px] leading-snug text-[#7188a3] sm:min-h-[36px] xl:min-h-[20px]">
												Masukkan password baru kamu
											</p>
											<div class="relative mt-1">
												<input
													v-model="formPassword.newPassword"
													:type="showNewPassword ? 'text' : 'password'"
													placeholder="Minimal 8 karakter"
													class="custom-input h-[46px] w-full rounded-[10px] border-[#d6e0ee] bg-transparent pl-3.5 pr-10 font-inter text-[14px] text-[#173a63] placeholder-[#a8bed4] transition-colors hover:border-[#183669] focus:outline-none"
												/>
												<button
													type="button"
													@click="showNewPassword = !showNewPassword"
													class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none"
													:title="showNewPassword ? 'Sembunyikan password' : 'Lihat password'"
												>
													<img
														v-if="showNewPassword"
														src="/assets/icons/shown.svg"
														alt="Lihat password"
														class="h-4 w-4 object-contain opacity-70 transition hover:opacity-100"
													/>
													<img
														v-else
														src="/assets/icons/hidden.svg"
														alt="Sembunyikan password"
														class="h-4 w-4 object-contain opacity-70 transition hover:opacity-100"
													/>
												</button>
											</div>
										</div>

										<!-- Konfirmasi Password Baru -->
										<div class="flex flex-col">
											<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
												Konfirmasi Password Baru<span class="text-red-500">*</span>
											</label>
											<p class="mt-0.5 min-h-[20px] font-inter text-[12px] leading-snug text-[#7188a3]">
												Masukkan ulang password baru kamu
											</p>
											<div class="relative mt-1">
												<input
													v-model="formPassword.confirmPassword"
													:type="showConfirmPassword ? 'text' : 'password'"
													placeholder="Ulangi password baru"
													class="custom-input h-[46px] w-full rounded-[10px] border-[#d6e0ee] bg-transparent pl-3.5 pr-10 font-inter text-[14px] text-[#173a63] placeholder-[#a8bed4] transition-colors hover:border-[#183669] focus:outline-none"
												/>
												<button
													type="button"
													@click="showConfirmPassword = !showConfirmPassword"
													class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none"
													:title="showConfirmPassword ? 'Sembunyikan password' : 'Lihat password'"
												>
													<img
														v-if="showConfirmPassword"
														src="/assets/icons/shown.svg"
														alt="Lihat password"
														class="h-4 w-4 object-contain opacity-70 transition hover:opacity-100"
													/>
													<img
														v-else
														src="/assets/icons/hidden.svg"
														alt="Sembunyikan password"
														class="h-4 w-4 object-contain opacity-70 transition hover:opacity-100"
													/>
												</button>
											</div>
										</div>
									</div>
								</div>

								<!-- Submit Button Ganti Password -->
								<div class="flex justify-end pt-2">
									<button
										type="submit"
										class="inline-flex items-center gap-2 rounded-[8px] bg-[#183669] px-6 py-2.5 font-poppins text-[14px] font-semibold text-white shadow-sm transition hover:bg-[#122b54] active:scale-[0.98]"
									>
										<img src="/assets/icons/save.svg" alt="Save Icon" class="h-4 w-4 object-contain" />
										<span>Simpan</span>
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<!-- Lightbox Image Modal Preview (Identical to ModalFormAktivitas.vue) -->
			<div
				v-if="previewingImage"
				class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 transition-all"
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
		</section>
	</DosenLayout>
</template>

<style scoped>
.custom-input {
	border-style: solid;
	border-width: 1.5px;
}
.custom-input:not([readonly]):focus {
	outline: none !important;
	box-shadow: none !important;
	--tw-ring-shadow: none !important;
	--tw-ring-color: transparent !important;
	border-color: #183669 !important;
	border-width: 1.5px !important;
}
</style>
