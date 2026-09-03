<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import DosenLayout from '@/Layouts/DosenLayout.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import PhoneInput from '@/Components/PhoneInput.vue';

const props = defineProps({
	userData: {
		type: Object,
		default: () => null,
	},
	isDefaultPassword: {
		type: Boolean,
		default: false,
	},
	isEmailEmpty: {
		type: Boolean,
		default: false,
	},
	isLocked: {
		type: Boolean,
		default: false,
	},
	hasProfileDosen: {
		type: Boolean,
		default: false,
	},
	hasPhoto: {
		type: Boolean,
		default: false,
	},
});

const page = usePage();

// Toast State
const toast = ref({
	show: false,
	type: 'success',
	title: '',
	message: '',
});

const showToast = (type, title, message) => {
	toast.value = {
		show: true,
		type,
		title,
		message,
	};
};

// Check flash messages
watch(
	() => page.props.flash,
	(newFlash) => {
		if (newFlash?.success) {
			showToast('success', 'Berhasil', newFlash.success);
		}
		if (newFlash?.error) {
			showToast('error', 'Terjadi Kesalahan', newFlash.error);
		}
		if (newFlash?.warning) {
			showToast('warning', 'Peringatan Keamanan', newFlash.warning);
		}
	},
	{ immediate: true, deep: true }
);

const currentUser = computed(() => props.userData || page.props.auth?.user || {});
const isDefaultPass = computed(() => props.isDefaultPassword ?? page.props.auth?.user?.is_default_password ?? false);
const isEmailEmp = computed(() => props.isEmailEmpty ?? page.props.auth?.user?.is_email_empty ?? false);
const isLockedAccount = computed(() => props.isLocked ?? page.props.auth?.user?.is_locked ?? (isDefaultPass.value || isEmailEmp.value));

const isLoading = ref(true);
onMounted(() => {
	setTimeout(() => {
		isLoading.value = false;
	}, 350);
});

// 1. State Data Diri (Read-only dari tabel users & profile_dosen)
const formPersonal = ref({
	name: currentUser.value.name || '',
	nip: currentUser.value.nip || currentUser.value.NIP || '',
	photoPreview: currentUser.value.profile_picture || null,
});

const hasPhoto = computed(() => {
	if (props.hasPhoto !== undefined && props.hasPhoto !== null) {
		return !!props.hasPhoto;
	}
	return !!formPersonal.value.photoPreview;
});

const previewingImage = ref(null);

const openImagePreview = (img) => {
	previewingImage.value = img;
};

const closeImagePreview = () => {
	previewingImage.value = null;
};

const handleKeyDown = (e) => {
	if (e.key === 'Escape' && previewingImage.value) {
		closeImagePreview();
	}
};

onMounted(() => {
	document.addEventListener('keydown', handleKeyDown);
});

onBeforeUnmount(() => {
	document.removeEventListener('keydown', handleKeyDown);
});

// 2. State Data Akun
const formAccount = ref({
	username: currentUser.value.username || '',
	email: currentUser.value.email || '',
	phone: currentUser.value.phone || '',
});

const isEditingUsername = ref(false);
const isEditingEmail = ref(false);
const isEditingPhone = ref(false);
const usernameInputRef = ref(null);
const emailInputRef = ref(null);
const phoneInputRef = ref(null);
const accountErrors = ref({});
const isSavingAccount = ref(false);

watch(
	() => currentUser.value,
	(val) => {
		if (val) {
			formPersonal.value.name = val.name || '';
			formPersonal.value.nip = val.nip || val.NIP || '';
			formPersonal.value.photoPreview = val.profile_picture || null;

			if (!isEditingUsername.value) {
				formAccount.value.username = val.username && val.username !== '-' ? val.username : '';
			}
			if (!isEditingEmail.value) {
				formAccount.value.email = val.email && val.email !== '-' ? val.email : '';
			}
			if (!isEditingPhone.value) {
				formAccount.value.phone = val.phone && val.phone !== '-' ? val.phone : '';
			}
		}
	},
	{ immediate: true, deep: true }
);

const hasInitialUsername = computed(() => !!props.userData?.username && props.userData.username !== '-');
const hasInitialEmail = computed(() => !!props.userData?.email && props.userData.email !== '-');
const hasInitialPhone = computed(() => !!props.userData?.phone && props.userData.phone !== '-');

const isUsernameLocked = computed(() => hasInitialUsername.value && !isEditingUsername.value);
const isEmailLocked = computed(() => hasInitialEmail.value && !isEditingEmail.value);
const isPhoneLocked = computed(() => hasInitialPhone.value && !isEditingPhone.value);

const cancelEditUsername = () => {
	formAccount.value.username = props.userData?.username && props.userData.username !== '-' ? props.userData.username : '';
	isEditingUsername.value = false;
	if (accountErrors.value?.username) {
		delete accountErrors.value.username;
	}
	usernameInputRef.value?.blur();
};

const cancelEditEmail = () => {
	formAccount.value.email = props.userData?.email && props.userData.email !== '-' ? props.userData.email : '';
	isEditingEmail.value = false;
	if (accountErrors.value?.email) {
		delete accountErrors.value.email;
	}
	emailInputRef.value?.blur();
};

const cancelEditPhone = () => {
	formAccount.value.phone = props.userData?.phone && props.userData.phone !== '-' ? props.userData.phone : '';
	isEditingPhone.value = false;
	if (accountErrors.value?.phone) {
		delete accountErrors.value.phone;
	}
	phoneInputRef.value?.blur();
};

const toggleEditUsername = () => {
	if (isEditingUsername.value) {
		cancelEditUsername();
	} else {
		if (isEditingEmail.value) cancelEditEmail();
		if (isEditingPhone.value) cancelEditPhone();
		isEditingUsername.value = true;
		nextTick(() => {
			usernameInputRef.value?.focus();
		});
	}
};

const toggleEditEmail = () => {
	if (isEditingEmail.value) {
		cancelEditEmail();
	} else {
		if (isEditingUsername.value) cancelEditUsername();
		if (isEditingPhone.value) cancelEditPhone();
		isEditingEmail.value = true;
		nextTick(() => {
			emailInputRef.value?.focus();
		});
	}
};

const toggleEditPhone = () => {
	if (isEditingPhone.value) {
		cancelEditPhone();
	} else {
		if (isEditingEmail.value) cancelEditEmail();
		if (isEditingUsername.value) cancelEditUsername();
		isEditingPhone.value = true;
		nextTick(() => {
			phoneInputRef.value?.focus();
		});
	}
};

// Clear error secara otomatis saat user mengetik atau mengubah field akun
watch(() => formAccount.value.email, () => {
	if (accountErrors.value?.email) {
		delete accountErrors.value.email;
	}
});

watch(() => formAccount.value.username, () => {
	if (accountErrors.value?.username) {
		delete accountErrors.value.username;
	}
});

watch(() => formAccount.value.phone, () => {
	if (accountErrors.value?.phone) {
		delete accountErrors.value.phone;
	}
});

// 3. State Ganti Password
const formPassword = ref({
	currentPassword: '',
	newPassword: '',
	confirmPassword: '',
});

// Clear error secara otomatis saat user mengetik password
watch(() => formPassword.value.currentPassword, () => {
	if (passwordErrors.value?.currentPassword) {
		delete passwordErrors.value.currentPassword;
	}
});

watch(() => formPassword.value.newPassword, () => {
	if (passwordErrors.value?.newPassword) {
		delete passwordErrors.value.newPassword;
	}
});

watch(() => formPassword.value.confirmPassword, () => {
	if (passwordErrors.value?.confirmPassword) {
		delete passwordErrors.value.confirmPassword;
	}
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);
const passwordErrors = ref({});
const isSavingPassword = ref(false);

// State periksa apakah ada perubahan data akun dibanding data semula
const isAccountChanged = computed(() => {
	const current = currentUser.value || {};
	const origEmail = (current.email && current.email !== '-' ? current.email : '').trim();
	const origUsername = (current.username && current.username !== '-' ? current.username : '').trim();
	const origPhone = (current.phone && current.phone !== '-' ? current.phone : '').trim();

	const newEmail = (formAccount.value.email || '').trim();
	const newUsername = (formAccount.value.username || '').trim();
	const newPhone = (formAccount.value.phone || '').trim();

	const normOrigPhone = origPhone.replace(/[\s-]/g, '');
	const normNewPhone = newPhone.replace(/[\s-]/g, '');

	return (
		newEmail !== origEmail ||
		newUsername !== origUsername ||
		normNewPhone !== normOrigPhone
	);
});

// State periksa apakah form password ada isinya
const isPasswordChanged = computed(() => {
	return (
		!!(formPassword.value.currentPassword || '').trim() ||
		!!(formPassword.value.newPassword || '').trim() ||
		!!(formPassword.value.confirmPassword || '').trim()
	);
});

// Save Handlers
const isSavingPersonal = ref(false);
const savePersonalData = () => {
	isSavingPersonal.value = true;
	const formData = new FormData();
	if (formPersonal.value.photo) {
		formData.append('photo', formPersonal.value.photo);
	}
	if (formPersonal.value.photoPreview === null && !formPersonal.value.photo) {
		formData.append('remove_photo', '1');
	}

	router.post(route('dosen.profile.personal'), formData, {
		preserveScroll: true,
		onSuccess: () => {
			showToast('success', 'Berhasil Disimpan', 'Data diri dan foto profile berhasil disimpan.');
		},
		onError: (errs) => {
			showToast('error', 'Gagal Menyimpan', Object.values(errs)[0] || 'Terjadi kesalahan saat menyimpan.');
		},
		onFinish: () => {
			isSavingPersonal.value = false;
		},
	});
};

const saveAccountData = () => {
	if (!isAccountChanged.value || isSavingAccount.value) return;
	accountErrors.value = {};
	if (!formAccount.value.email.trim()) {
		accountErrors.value.email = 'Email wajib diisi.';
		showToast('error', 'Validasi Gagal', 'Email tidak boleh kosong.');
		return;
	}

	isSavingAccount.value = true;
	router.post(
		route('dosen.profile.account'),
		{
			username: formAccount.value.username ? formAccount.value.username.trim() : null,
			email: formAccount.value.email.trim(),
			phone: formAccount.value.phone.trim(),
		},
		{
			preserveScroll: true,
			onSuccess: () => {
				isEditingUsername.value = false;
				isEditingEmail.value = false;
				isEditingPhone.value = false;
				showToast('success', 'Berhasil Disimpan', 'Data akun berhasil diperbarui.');
			},
			onError: (errs) => {
				accountErrors.value = errs;
				showToast('error', 'Gagal Menyimpan', Object.values(errs)[0] || 'Terjadi kesalahan saat menyimpan.');
			},
			onFinish: () => {
				isSavingAccount.value = false;
			},
		}
	);
};

const savePassword = () => {
	if (!isPasswordChanged.value || isSavingPassword.value) return;
	passwordErrors.value = {};

	if (!formPassword.value.currentPassword) {
		passwordErrors.value.currentPassword = 'Password saat ini wajib diisi.';
		showToast('error', 'Validasi Gagal', 'Password saat ini wajib diisi.');
		return;
	}
	if (!formPassword.value.newPassword) {
		passwordErrors.value.newPassword = 'Password baru wajib diisi.';
		showToast('error', 'Validasi Gagal', 'Password baru wajib diisi.');
		return;
	}
	if (formPassword.value.newPassword.length < 8) {
		passwordErrors.value.newPassword = 'Password baru minimal 8 karakter.';
		showToast('error', 'Validasi Gagal', 'Password baru minimal 8 karakter.');
		return;
	}
	if (formPassword.value.newPassword !== formPassword.value.confirmPassword) {
		passwordErrors.value.confirmPassword = 'Konfirmasi password baru tidak cocok.';
		showToast('error', 'Validasi Gagal', 'Konfirmasi password baru tidak cocok.');
		return;
	}
	if (formPassword.value.newPassword === formPersonal.value.nip) {
		passwordErrors.value.newPassword = 'Password baru tidak boleh sama dengan NIP.';
		showToast('error', 'Validasi Gagal', 'Password baru tidak boleh sama dengan password default (NIP).');
		return;
	}

	isSavingPassword.value = true;
	router.post(
		route('dosen.profile.password'),
		{
			current_password: formPassword.value.currentPassword,
			new_password: formPassword.value.newPassword,
			new_password_confirmation: formPassword.value.confirmPassword,
		},
		{
			preserveScroll: true,
			onSuccess: () => {
				formPassword.value = {
					currentPassword: '',
					newPassword: '',
					confirmPassword: '',
				};
				showToast('success', 'Password Diperbarui', 'Password berhasil diubah! Akses Dashboard & Aktivitas kini telah dibuka.');
			},
			onError: (errs) => {
				passwordErrors.value = errs;
				showToast('error', 'Gagal Mengubah Password', Object.values(errs)[0] || 'Password gagal diubah.');
			},
			onFinish: () => {
				isSavingPassword.value = false;
			},
		}
	);
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

				<!-- Alert Banner Jika Dosen Belum Memiliki Profil / Foto Dosen -->
				<div
					v-if="!hasPhoto"
					class="flex items-start gap-3.5 rounded-[14px] border border-blue-200 bg-blue-50/90 p-4 sm:p-5 shadow-xs"
				>
					<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
						</svg>
					</div>
					<div class="space-y-1">
						<h3 class="font-poppins text-[15px] font-bold text-[#173a63]">
							Profil Publik Belum Tersedia
						</h3>
						<p class="font-inter text-[13px] leading-relaxed text-[#4d6786]">
							Foto diri Anda dikelola oleh Administrator. Saat ini data profile Anda belum tersedia secara publik. Silakan <strong>hubungi Administrator</strong> untuk membuat profile publik agar dapat mengelola aktivitas anda!
						</p>
					</div>
				</div>

				<!-- Security Warning Banner (Jika Dosen Belum Melengkapi Email ATAU Belum Mengganti Password) -->
				<div
					v-if="isLockedAccount"
					class="flex items-start gap-3.5 rounded-[14px] border border-amber-300 bg-amber-50/90 p-4 sm:p-5 shadow-xs"
				>
					<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
						</svg>
					</div>
					<div class="space-y-1">
						<h3 class="font-poppins text-[15px] font-bold text-amber-900">
							<template v-if="isDefaultPass && isEmailEmp">
								Perhatian: Lengkapi Email & Ubah Password Default Anda
							</template>
							<template v-else-if="isEmailEmp">
								Perhatian: Lengkapi Email Aktif Anda
							</template>
							<template v-else>
								Perhatian: Ubah Password Default Anda (NIP)
							</template>
						</h3>
						<p class="font-inter text-[13px] leading-relaxed text-amber-800">
							Demi keamanan akun, akses menu <strong>Dashboard</strong> dan <strong>Aktivitas</strong> masih terkunci.
							Harap <span v-if="isEmailEmp">masukkan <strong>Email aktif</strong> Anda</span><span v-if="isEmailEmp && isDefaultPass"> serta </span><span v-if="isDefaultPass">perbarui <strong>Password baru</strong></span> pada formulir di bawah ini agar seluruh menu terbuka penuh.
						</p>
					</div>
				</div>

				<!-- Main Profile Form Grid -->
				<div class="grid grid-cols-1 items-stretch gap-6 lg:grid-cols-12">
					<!-- LEFT COLUMN: Data Diri (Card 1) -->
					<div class="flex h-full flex-col justify-between rounded-[12px] bg-white p-5 shadow-sm ring-1 ring-[#d6e0ee] sm:p-6 lg:col-span-5">
						<div>
							<div class="flex items-center justify-between">
								<h2 class="font-poppins text-[18px] font-bold text-[#173a63] sm:text-[20px]">
									Data diri
								</h2>
								<span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-inter text-[11px] font-medium text-[#7188a3]">
									Dikelola Admin
								</span>
							</div>

							<div class="mt-5 space-y-5">
								<!-- Foto Diri (Dari tabel profile_dosen) -->
								<div>
									<div class="flex items-center justify-between">
										<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
											Foto diri
										</label>
									</div>
									<p class="mt-0.5 font-inter text-[12px] text-[#7188a3]">
										Foto resmi dosen yang terdaftar pada sistem
									</p>

									<!-- State 0: Loading Skeleton -->
									<div
										v-if="isLoading"
										class="mt-3 flex h-[260px] sm:h-[280px] lg:h-[290px] xl:h-[310px] w-full items-center justify-center overflow-hidden rounded-[12px] border border-[#d6e0ee] bg-[#fafcff] p-3 text-center transition-all animate-pulse"
									>
										<div class="h-full max-h-[235px] sm:max-h-[255px] lg:max-h-[265px] xl:max-h-[285px] w-auto aspect-[3/4] rounded-[10px] bg-slate-200"></div>
									</div>

									<!-- State 1: Memiliki Foto Profil Dosen -->
									<div
										v-else-if="hasPhoto"
										class="mt-3 flex h-[260px] sm:h-[280px] lg:h-[290px] xl:h-[310px] w-full items-center justify-center overflow-hidden rounded-[12px] border border-[#d6e0ee] bg-[#fafcff] p-3 text-center transition-all"
									>
										<div class="group relative flex h-full max-h-[235px] sm:max-h-[255px] lg:max-h-[265px] xl:max-h-[285px] w-auto items-center justify-center overflow-hidden rounded-[10px] border border-[#d6e0ee] bg-slate-100 shadow-xs aspect-[3/4]">
											<img
												:src="formPersonal.photoPreview"
												alt="Foto Profil Dosen"
												class="h-full w-full object-cover object-top"
											/>

											<!-- Zoom / Preview Button -->
											<button
												type="button"
												@click.stop="openImagePreview(formPersonal.photoPreview)"
												class="absolute right-2 bottom-2 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/80 text-white shadow-md backdrop-blur-xs transition hover:bg-slate-900 hover:scale-110 active:scale-95 focus:outline-none opacity-100 lg:opacity-0 lg:pointer-events-none lg:group-hover:opacity-100 lg:group-hover:pointer-events-auto"
												title="Lihat Foto Ukuran Penuh"
											>
												<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
												</svg>
											</button>
										</div>
									</div>

									<!-- State 2: Belum Memiliki Foto Profil / Profile Dosen -->
									<div
										v-else
										class="mt-3 flex h-[260px] sm:h-[280px] lg:h-[290px] xl:h-[310px] w-full flex-col items-center justify-center rounded-[12px] border-2 border-dashed border-[#d6e0ee] bg-[#f8fafc] p-4 text-center transition-all"
									>
										<div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#e8eef8] text-[#173a63]">
											<img
												v-if="userProfilePicture"
												:src="userProfilePicture"
												:alt="userName"
												class="h-full w-full object-cover object-top"
												@error="onImageError"
											/>
											<img
												v-else
												src="/assets/icons/default-profile.svg"
												alt="Profile icon"
												class="h-full w-full rounded-full object-contain p-0.5"
											/>
										</div>
										<p class="mt-3 font-poppins text-[14px] font-semibold text-[#173a63]">
											Belum Ada Foto Profile
										</p>
										<p class="mt-1 font-inter text-[13px] leading-relaxed text-[#7188a3] max-w-[280px]">
											Data profile publik dan foto diri belum ditambahkan. Silakan hubungi Administrator.
										</p>
									</div>
								</div>

								<!-- Nama -->
								<div>
									<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
										Nama
									</label>
									<div v-if="isLoading" class="mt-1 flex h-[42px] items-center border-b border-[#d6e0ee]">
										<div class="h-4 w-44 rounded bg-slate-200 animate-pulse"></div>
									</div>
									<div v-else class="mt-1 flex h-[42px] items-center border-b border-[#d6e0ee] font-inter text-[14px] font-medium text-[#173a63] select-none cursor-default">
										{{ formPersonal.name }}
									</div>
								</div>

								<!-- NIP -->
								<div>
									<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
										NIP
									</label>
									<div v-if="isLoading" class="mt-1 flex h-[42px] items-center border-b border-[#d6e0ee]">
										<div class="h-4 w-32 rounded bg-slate-200 animate-pulse"></div>
									</div>
									<div v-else class="mt-1 flex h-[42px] items-center border-b border-[#d6e0ee] font-inter text-[14px] font-medium text-[#173a63] select-none cursor-default">
										{{ formPersonal.nip }}
									</div>
								</div>
							</div>
						</div>

						<!-- Footer Note Card 1 -->
						<div class="mt-6 border-t border-[#f1f5f9] pt-3 text-[12px] text-[#8c9eb5] font-inter flex items-center gap-1.5">
							<svg class="h-4 w-4 shrink-0 text-[#8c9eb5]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
							</svg>
							<span>Perubahan data diri dilakukan melalui Administrator.</span>
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
									<!-- Email Field (Required) -->
									<div class="flex flex-col">
										<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
											Email<span class="text-red-500">*</span>
										</label>
										<p class="mt-0.5 min-h-[20px] font-inter text-[12px] leading-snug text-[#7188a3] sm:min-h-[36px] xl:min-h-[20px]">
											Masukkan email aktif yang kamu gunakan
										</p>
										<div v-if="isLoading" class="relative mt-1 h-[46px] w-full rounded-[10px] bg-slate-100 border border-[#d6e0ee] animate-pulse"></div>
										<div v-else class="relative mt-1">
											<input
												ref="emailInputRef"
												v-model="formAccount.email"
												type="email"
												:readonly="isEmailLocked"
												:tabindex="isEmailLocked ? -1 : 0"
												@focus="isEmailLocked && $event.target.blur()"
												placeholder="contoh: nama@email.com"
												:class="[
													'custom-input h-[46px] w-full rounded-[10px] pl-3.5 font-inter text-[14px] text-[#173a63] placeholder-[#a8bed4] transition-colors focus:outline-none',
													hasInitialEmail || isEditingEmail ? 'pr-11' : 'pr-3.5',
													isEmailLocked
														? 'border-[#d6e0ee] cursor-not-allowed select-none bg-[#f0f4f9]'
														: 'border-[#d6e0ee] bg-white hover:border-[#183669] focus:border-[#183669]'
												]"
											/>
											<button
												v-if="hasInitialEmail || isEditingEmail"
												type="button"
												@mousedown.prevent
												@click="toggleEditEmail"
												class="absolute inset-y-0 right-0 z-10 flex items-center pr-2.5 focus:outline-none cursor-pointer"
												:title="isEditingEmail ? 'Batalkan perubahan email' : 'Edit Email'"
											>
												<span class="flex h-7 w-7 items-center justify-center rounded-[7px] text-[#183669] transition-colors hover:bg-[#dbe4ef] active:bg-[#ccd9e7]">
													<svg
														v-if="isEditingEmail"
														class="h-4 w-4"
														fill="none"
														viewBox="0 0 24 24"
														stroke="currentColor"
														stroke-width="2.5"
													>
														<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
													</svg>
													<svg
														v-else
														class="h-3.5 w-3.5"
														viewBox="0 0 19 19"
														fill="currentColor"
													>
														<path d="M4.92119 4.92074C4.92119 5.18177 4.81749 5.43211 4.63291 5.61669C4.44833 5.80126 4.19798 5.90496 3.93695 5.90496H2.95271C2.69168 5.90496 2.44133 6.00865 2.25675 6.19323C2.07217 6.3778 1.96847 6.62814 1.96847 6.88917V15.7471C1.96847 16.0082 2.07217 16.2585 2.25675 16.4431C2.44133 16.6277 2.69168 16.7313 2.95271 16.7313H11.8108C12.0719 16.7313 12.3222 16.6277 12.5068 16.4431C12.6914 16.2585 12.7951 16.0082 12.7951 15.7471V14.7629C12.7951 14.5019 12.8988 14.2515 13.0834 14.067C13.2679 13.8824 13.5183 13.7787 13.7793 13.7787C14.0404 13.7787 14.2907 13.8824 14.4753 14.067C14.6599 14.2515 14.7636 14.5019 14.7636 14.7629V15.7471C14.7636 16.5302 14.4525 17.2812 13.8987 17.835C13.345 18.3887 12.594 18.6998 11.8108 18.6998H2.95271C2.1696 18.6998 1.41857 18.3887 0.864829 17.835C0.311088 17.2812 0 16.5302 0 15.7471V6.88917C0 6.10608 0.311088 5.35506 0.864829 4.80134C1.41857 4.24761 2.1696 3.93652 2.95271 3.93652H3.93695C4.19798 3.93652 4.44833 4.04022 4.63291 4.22479C4.81749 4.40937 4.92119 4.65971 4.92119 4.92074Z" />
														<path d="M11.413 2.96342L15.7358 7.2861L9.55481 13.4896C9.4634 13.5813 9.3548 13.6541 9.23522 13.7037C9.11564 13.7534 8.98744 13.779 8.85797 13.779H5.90526C5.64422 13.779 5.39388 13.6753 5.2093 13.4907C5.02472 13.3061 4.92102 13.0558 4.92102 12.7948V9.84211C4.92105 9.71264 4.94662 9.58444 4.99628 9.46487C5.04593 9.34529 5.11869 9.23669 5.21039 9.14528L11.413 2.96342ZM17.8067 0.893608C18.3495 1.43606 18.6678 2.16332 18.6979 2.93014C18.728 3.69697 18.4677 4.44693 17.9691 5.03027L17.8076 5.20743L17.1256 5.89048L12.8077 1.57272L13.4918 0.893608C14.064 0.32144 14.84 0 15.6492 0C16.4584 0 17.2345 0.32144 17.8067 0.893608Z" />
													</svg>
												</span>
											</button>
										</div>
										<p v-if="accountErrors.email" class="mt-1 font-inter text-[11px] font-medium text-red-500">
											{{ accountErrors.email }}
										</p>
									</div>

									<!-- Username Field (Opsional) -->
									<div class="flex flex-col">
										<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
											Username
										</label>
										<p class="mt-0.5 min-h-[20px] font-inter text-[12px] leading-snug text-[#7188a3] sm:min-h-[36px] xl:min-h-[20px]">
											Masukkan username akun kamu (opsional)
										</p>
										<div v-if="isLoading" class="relative mt-1 h-[46px] w-full rounded-[10px] bg-slate-100 border border-[#d6e0ee] animate-pulse"></div>
										<div v-else class="relative mt-1">
											<input
												ref="usernameInputRef"
												v-model="formAccount.username"
												type="text"
												:readonly="isUsernameLocked"
												:tabindex="isUsernameLocked ? -1 : 0"
												@focus="isUsernameLocked && $event.target.blur()"
												placeholder="contoh: johndosen"
												:class="[
													'custom-input h-[46px] w-full rounded-[10px] pl-3.5 font-inter text-[14px] text-[#173a63] placeholder-[#a8bed4] transition-colors focus:outline-none',
													hasInitialUsername || isEditingUsername ? 'pr-11' : 'pr-3.5',
													isUsernameLocked
														? 'border-[#d6e0ee] cursor-not-allowed select-none bg-[#f0f4f9]'
														: 'border-[#d6e0ee] bg-white hover:border-[#183669] focus:border-[#183669]'
												]"
											/>
											<button
												v-if="hasInitialUsername || isEditingUsername"
												type="button"
												@mousedown.prevent
												@click="toggleEditUsername"
												class="absolute inset-y-0 right-0 z-10 flex items-center pr-2.5 focus:outline-none cursor-pointer"
												:title="isEditingUsername ? 'Batalkan perubahan username' : 'Edit Username'"
											>
												<span class="flex h-7 w-7 items-center justify-center rounded-[7px] text-[#183669] transition-colors hover:bg-[#dbe4ef] active:bg-[#ccd9e7]">
													<svg
														v-if="isEditingUsername"
														class="h-4 w-4"
														fill="none"
														viewBox="0 0 24 24"
														stroke="currentColor"
														stroke-width="2.5"
													>
														<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
													</svg>
													<svg
														v-else
														class="h-3.5 w-3.5"
														viewBox="0 0 19 19"
														fill="currentColor"
													>
														<path d="M4.92119 4.92074C4.92119 5.18177 4.81749 5.43211 4.63291 5.61669C4.44833 5.80126 4.19798 5.90496 3.93695 5.90496H2.95271C2.69168 5.90496 2.44133 6.00865 2.25675 6.19323C2.07217 6.3778 1.96847 6.62814 1.96847 6.88917V15.7471C1.96847 16.0082 2.07217 16.2585 2.25675 16.4431C2.44133 16.6277 2.69168 16.7313 2.95271 16.7313H11.8108C12.0719 16.7313 12.3222 16.6277 12.5068 16.4431C12.6914 16.2585 12.7951 16.0082 12.7951 15.7471V14.7629C12.7951 14.5019 12.8988 14.2515 13.0834 14.067C13.2679 13.8824 13.5183 13.7787 13.7793 13.7787C14.0404 13.7787 14.2907 13.8824 14.4753 14.067C14.6599 14.2515 14.7636 14.5019 14.7636 14.7629V15.7471C14.7636 16.5302 14.4525 17.2812 13.8987 17.835C13.345 18.3887 12.594 18.6998 11.8108 18.6998H2.95271C2.1696 18.6998 1.41857 18.3887 0.864829 17.835C0.311088 17.2812 0 16.5302 0 15.7471V6.88917C0 6.10608 0.311088 5.35506 0.864829 4.80134C1.41857 4.24761 2.1696 3.93652 2.95271 3.93652H3.93695C4.19798 3.93652 4.44833 4.04022 4.63291 4.22479C4.81749 4.40937 4.92119 4.65971 4.92119 4.92074Z" />
														<path d="M11.413 2.96342L15.7358 7.2861L9.55481 13.4896C9.4634 13.5813 9.3548 13.6541 9.23522 13.7037C9.11564 13.7534 8.98744 13.779 8.85797 13.779H5.90526C5.64422 13.779 5.39388 13.6753 5.2093 13.4907C5.02472 13.3061 4.92102 13.0558 4.92102 12.7948V9.84211C4.92105 9.71264 4.94662 9.58444 4.99628 9.46487C5.04593 9.34529 5.11869 9.23669 5.21039 9.14528L11.413 2.96342ZM17.8067 0.893608C18.3495 1.43606 18.6678 2.16332 18.6979 2.93014C18.728 3.69697 18.4677 4.44693 17.9691 5.03027L17.8076 5.20743L17.1256 5.89048L12.8077 1.57272L13.4918 0.893608C14.064 0.32144 14.84 0 15.6492 0C16.4584 0 17.2345 0.32144 17.8067 0.893608Z" />
													</svg>
												</span>
											</button>
										</div>
										<p v-if="accountErrors.username" class="mt-1 font-inter text-[11px] font-medium text-red-500">
											{{ accountErrors.username }}
										</p>
									</div>

									<!-- Nomor Handphone Field (Opsional) -->
									<div class="flex flex-col">
										<label class="block font-poppins text-[13px] font-semibold text-[#173a63]">
											Nomor Handphone
										</label>
										<p class="mt-0.5 min-h-[20px] font-inter text-[12px] leading-snug text-[#7188a3] sm:min-h-[36px] xl:min-h-[20px]">
											Masukkan nomor handphone aktif yang kamu gunakan
										</p>
										<div v-if="isLoading" class="relative mt-1 h-[46px] w-full rounded-[10px] bg-slate-100 border border-[#d6e0ee] animate-pulse"></div>
										<div v-else class="relative mt-1">
											<PhoneInput
												ref="phoneInputRef"
												v-model="formAccount.phone"
												:readonly="isPhoneLocked"
												placeholder="XXX-XXXX-XXXX"
												:has-error="!!accountErrors.phone"
											>
												<template v-if="hasInitialPhone || isEditingPhone" #append>
													<button
														type="button"
														@mousedown.prevent
														@click="toggleEditPhone"
														class="flex items-center focus:outline-none cursor-pointer"
														:title="isEditingPhone ? 'Batalkan perubahan nomor handphone' : 'Edit Nomor Handphone'"
													>
														<span class="flex h-7 w-7 items-center justify-center rounded-[7px] text-[#183669] transition-colors hover:bg-[#dbe4ef] active:bg-[#ccd9e7]">
															<svg
																v-if="isEditingPhone"
																class="h-4 w-4"
																fill="none"
																viewBox="0 0 24 24"
																stroke="currentColor"
																stroke-width="2.5"
															>
																<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
															</svg>
															<svg
																v-else
																class="h-3.5 w-3.5"
																viewBox="0 0 19 19"
																fill="currentColor"
															>
																<path d="M4.92119 4.92074C4.92119 5.18177 4.81749 5.43211 4.63291 5.61669C4.44833 5.80126 4.19798 5.90496 3.93695 5.90496H2.95271C2.69168 5.90496 2.44133 6.00865 2.25675 6.19323C2.07217 6.3778 1.96847 6.62814 1.96847 6.88917V15.7471C1.96847 16.0082 2.07217 16.2585 2.25675 16.4431C2.44133 16.6277 2.69168 16.7313 2.95271 16.7313H11.8108C12.0719 16.7313 12.3222 16.6277 12.5068 16.4431C12.6914 16.2585 12.7951 16.0082 12.7951 15.7471V14.7629C12.7951 14.5019 12.8988 14.2515 13.0834 14.067C13.2679 13.8824 13.5183 13.7787 13.7793 13.7787C14.0404 13.7787 14.2907 13.8824 14.4753 14.067C14.6599 14.2515 14.7636 14.5019 14.7636 14.7629V15.7471C14.7636 16.5302 14.4525 17.2812 13.8987 17.835C13.345 18.3887 12.594 18.6998 11.8108 18.6998H2.95271C2.1696 18.6998 1.41857 18.3887 0.864829 17.835C0.311088 17.2812 0 16.5302 0 15.7471V6.88917C0 6.10608 0.311088 5.35506 0.864829 4.80134C1.41857 4.24761 2.1696 3.93652 2.95271 3.93652H3.93695C4.19798 3.93652 4.44833 4.04022 4.63291 4.22479C4.81749 4.40937 4.92119 4.65971 4.92119 4.92074Z" />
														<path d="M11.413 2.96342L15.7358 7.2861L9.55481 13.4896C9.4634 13.5813 9.3548 13.6541 9.23522 13.7037C9.11564 13.7534 8.98744 13.779 8.85797 13.779H5.90526C5.64422 13.779 5.39388 13.6753 5.2093 13.4907C5.02472 13.3061 4.92102 13.0558 4.92102 12.7948V9.84211C4.92105 9.71264 4.94662 9.58444 4.99628 9.46487C5.04593 9.34529 5.11869 9.23669 5.21039 9.14528L11.413 2.96342ZM17.8067 0.893608C18.3495 1.43606 18.6678 2.16332 18.6979 2.93014C18.728 3.69697 18.4677 4.44693 17.9691 5.03027L17.8076 5.20743L17.1256 5.89048L12.8077 1.57272L13.4918 0.893608C14.064 0.32144 14.84 0 15.6492 0C16.4584 0 17.2345 0.32144 17.8067 0.893608Z" />
													</svg>
												</span>
											</button>
										</template>
									</PhoneInput>
								</div>
										<p v-if="accountErrors.phone" class="mt-1 font-inter text-[11px] font-medium text-red-500">
											{{ accountErrors.phone }}
										</p>
									</div>
								</div>

								<!-- Submit Button Data Akun -->
								<div class="flex justify-end pt-2">
									<button
										type="submit"
										:disabled="!isAccountChanged || isSavingAccount"
										:class="[
											'inline-flex items-center justify-center gap-2 rounded-[8px] px-6 py-2.5 font-poppins text-[14px] font-semibold transition duration-150',
											!isAccountChanged || isSavingAccount
												? 'cursor-not-allowed bg-[#f0f4f9] text-[#8c9eb5] border-[1.5px] border-[#d6e0ee] shadow-none'
												: 'cursor-pointer bg-[#183669] text-white border-[1.5px] border-[#183669] shadow-sm hover:bg-[#122b54] hover:border-[#122b54] active:scale-[0.98]'
										]"
										:title="!isAccountChanged ? 'Tidak ada perubahan data untuk disimpan' : 'Simpan perubahan akun'"
									>
										<svg v-if="isSavingAccount" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
											<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
											<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
										</svg>
										<svg
											v-else
											class="h-[15px] w-[15px] shrink-0 translate-y-[0.5px]"
											viewBox="0 0 16 16"
											fill="currentColor"
										>
											<path d="M13.3333 16V8.88889H2.66667V16H0.888889C0.653141 16 0.427048 15.9064 0.260349 15.7397C0.0936505 15.573 0 15.3469 0 15.1111V0.888889C0 0.653141 0.0936505 0.427048 0.260349 0.260349C0.427048 0.0936505 0.653141 0 0.888889 0H12.4444L16 3.55556V15.1111C16 15.3469 15.9064 15.573 15.7397 15.7397C15.573 15.9064 15.3469 16 15.1111 16H13.3333ZM11.5556 16H4.44444V10.6667H11.5556V16Z" />
										</svg>
										<span class="leading-none pt-[0.5px]">{{ isSavingAccount ? 'Menyimpan...' : 'Simpan' }}</span>
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
										<p v-if="passwordErrors.current_password" class="mt-1 font-inter text-[11px] font-medium text-red-500">
											{{ passwordErrors.current_password }}
										</p>
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
											<p v-if="passwordErrors.new_password" class="mt-1 font-inter text-[11px] font-medium text-red-500">
												{{ passwordErrors.new_password }}
											</p>
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
											<p v-if="passwordErrors.confirmPassword" class="mt-1 font-inter text-[11px] font-medium text-red-500">
												{{ passwordErrors.confirmPassword }}
											</p>
										</div>
									</div>
								</div>

								<!-- Submit Button Ganti Password -->
								<div class="flex justify-end pt-2">
									<button
										type="submit"
										:disabled="!isPasswordChanged || isSavingPassword"
										:class="[
											'inline-flex items-center justify-center gap-2 rounded-[8px] px-6 py-2.5 font-poppins text-[14px] font-semibold transition duration-150',
											!isPasswordChanged || isSavingPassword
												? 'cursor-not-allowed bg-[#f0f4f9] text-[#8c9eb5] border-[1.5px] border-[#d6e0ee] shadow-none'
												: 'cursor-pointer bg-[#183669] text-white border-[1.5px] border-[#183669] shadow-sm hover:bg-[#122b54] hover:border-[#122b54] active:scale-[0.98]'
										]"
										:title="!isPasswordChanged ? 'Silakan isi form password terlebih dahulu' : 'Simpan password baru'"
									>
										<svg v-if="isSavingPassword" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
											<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
											<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
										</svg>
										<svg
											v-else
											class="h-[15px] w-[15px] shrink-0 translate-y-[0.5px]"
											viewBox="0 0 16 16"
											fill="currentColor"
										>
											<path d="M13.3333 16V8.88889H2.66667V16H0.888889C0.653141 16 0.427048 15.9064 0.260349 15.7397C0.0936505 15.573 0 15.3469 0 15.1111V0.888889C0 0.653141 0.0936505 0.427048 0.260349 0.260349C0.427048 0.0936505 0.653141 0 0.888889 0H12.4444L16 3.55556V15.1111C16 15.3469 15.9064 15.573 15.7397 15.7397C15.573 15.9064 15.3469 16 15.1111 16H13.3333ZM11.5556 16H4.44444V10.6667H11.5556V16Z" />
										</svg>
										<span class="leading-none pt-[0.5px]">{{ isSavingPassword ? 'Menyimpan...' : 'Simpan' }}</span>
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<!-- Lightbox Image Modal Preview (Identical to ModalFormAktivitas.vue) -->
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
								class="relative flex items-center justify-center bg-transparent"
								@click.stop
							>
								<button
									type="button"
									@click="closeImagePreview"
									class="absolute top-3.5 right-3.5 z-20 flex h-9 w-9 items-center justify-center rounded-full bg-black/60 text-white hover:bg-black/85 backdrop-blur-xs shadow-md transition hover:scale-105 active:scale-95 focus:outline-none"
									title="Tutup Preview"
								>
									<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</button>
								<img
									:src="previewingImage"
									alt="Zoomed Preview"
									class="max-h-[82vh] max-w-[88vw] w-auto h-auto min-w-[280px] sm:min-w-[460px] rounded-xl object-contain shadow-2xl"
								/>
							</div>
						</Transition>
					</div>
				</Transition>
			</Teleport>

			<!-- Toast Notification -->
			<ToastNotification
				:show="toast.show"
				:type="toast.type"
				:title="toast.title"
				:message="toast.message"
				@close="toast.show = false"
			/>
		</section>
	</DosenLayout>
</template>

<style scoped>
.custom-input {
	border-style: solid;
	border-width: 1.5px;
}

.custom-input:focus,
.custom-input:focus-visible,
.custom-input:focus-within,
.custom-input:not([readonly]):focus,
.custom-input[readonly]:focus {
	outline: none !important;
	box-shadow: none !important;
	--tw-ring-shadow: none !important;
	--tw-ring-offset-shadow: none !important;
	--tw-ring-color: transparent !important;
	--tw-ring-offset-color: transparent !important;
	border-width: 1.5px !important;
}

.custom-input:not([readonly]) {
	background-color: #ffffff !important;
}

.custom-input:not([readonly]):focus {
	border-color: #183669 !important;
	background-color: #ffffff !important;
}

.custom-input[readonly] {
	border-color: #d6e0ee !important;
	background-color: #f0f4f9 !important;
	cursor: not-allowed !important;
	user-select: none !important;
}

.custom-input[readonly]:focus {
	border-color: #d6e0ee !important;
	background-color: #f0f4f9 !important;
}
</style>
