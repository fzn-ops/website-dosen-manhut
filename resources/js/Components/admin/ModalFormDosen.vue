<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import PhoneInput from '@/Components/PhoneInput.vue';

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
		default: () => ({ nip: '', name: '', email: '', phone: '', password: '' }),
	},
	editingId: {
		type: [Number, String, null],
		default: null,
	},
	existingLecturers: {
		type: Array,
		default: () => [],
	},
});

const emit = defineEmits(['close', 'submit']);

const form = ref({
	nip: '',
	name: '',
	username: '',
	password: '',
	email: '',
	phone: '',
});

const formError = ref('');
const errors = ref({});
const showPassword = ref(false);
const isPasswordManuallyEdited = ref(false);

// Auto-fill password with NIP if not manually modified
watch(
	() => form.value.nip,
	(newNip) => {
		if (!props.isEditing && !isPasswordManuallyEdited.value) {
			form.value.password = newNip;
		}
	}
);

watch(
	() => props.show,
	(isOpen) => {
		if (isOpen) {
			formError.value = '';
			errors.value = {};
			showPassword.value = false;
			isPasswordManuallyEdited.value = false;

			const currentNip = props.initialData?.nip !== '-' ? (props.initialData?.nip || '') : '';

			form.value = {
				nip: currentNip,
				name: props.initialData?.name || '',
				username: props.initialData?.username || '',
				password: props.isEditing ? '' : (currentNip || ''),
				email: props.initialData?.email !== '-' ? (props.initialData?.email || '') : '',
				phone: props.initialData?.phone !== '-' ? (props.initialData?.phone || '') : '',
			};
		}
	},
	{ immediate: true }
);

const handleClose = () => {
	formError.value = '';
	errors.value = {};
	emit('close');
};

const handleSubmit = () => {
	formError.value = '';
	errors.value = {};
	const inputNip = form.value.nip.trim();
	const inputName = form.value.name.trim();
	const inputUsername = form.value.username ? form.value.username.trim() : '';
	const inputPassword = form.value.password ? form.value.password.trim() : '';

	if (!inputNip) {
		errors.value.nip = 'NIP wajib diisi.';
	}
	if (!inputName) {
		errors.value.name = 'Nama dosen wajib diisi.';
	}

	let finalPassword = null;
	if (!props.isEditing) {
		finalPassword = inputPassword || inputNip;
		if (!finalPassword) {
			errors.value.password = 'Password wajib diisi.';
		} else if (finalPassword.length < 6) {
			errors.value.password = 'Password minimal 6 karakter.';
		}
	} else {
		if (inputPassword) {
			if (inputPassword.length < 6) {
				errors.value.password = 'Password minimal 6 karakter.';
			} else {
				finalPassword = inputPassword;
			}
		}
	}

	if (Object.keys(errors.value).length > 0) {
		return;
	}

	// Validate duplicate NIP against existingLecturers
	const isNipDuplicate = props.existingLecturers.some((l) => {
		if (props.isEditing && l.id === props.editingId) return false;
		return (l.nip || '').toLowerCase().trim() === inputNip.toLowerCase().trim();
	});

	if (isNipDuplicate) {
		formError.value = `NIP "${inputNip}" sudah terdaftar. NIP tidak boleh duplikat.`;
		return;
	}

	// Validate duplicate Username against existingLecturers
	if (inputUsername) {
		const isUsernameDuplicate = props.existingLecturers.some((l) => {
			if (props.isEditing && l.id === props.editingId) return false;
			return (l.username || '').toLowerCase().trim() === inputUsername.toLowerCase().trim();
		});

		if (isUsernameDuplicate) {
			formError.value = `Username "${inputUsername}" sudah digunakan oleh akun lain.`;
			return;
		}
	}

	emit('submit', {
		nip: inputNip,
		name: inputName,
		username: inputUsername || null,
		password: finalPassword,
		email: form.value.email.trim() ? form.value.email.trim() : null,
		phone: form.value.phone.trim() ? form.value.phone.trim() : null,
	});

	handleClose();
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
						class="w-full max-w-[500px] max-h-[92vh] overflow-y-auto transform rounded-[18px] bg-white p-7 sm:p-9 shadow-2xl font-poppins border border-[#e2e8f0]"
					>
			<h2 class="text-center text-[22px] font-bold text-[#183669]">
				{{ isEditing ? 'Form Edit Dosen' : 'Form Tambah Dosen' }}
			</h2>

			<!-- Error Alert Box -->
			<div v-if="formError" class="mt-4 rounded-[10px] bg-red-50 p-3 font-inter text-[13px] text-red-600 border border-red-200">
				{{ formError }}
			</div>

			<form @submit.prevent="handleSubmit" novalidate class="mt-6 space-y-4">
				<!-- 1. NIP -->
				<div>
					<label class="block text-[14px] font-bold text-[#183669]">
						NIP<span class="text-red-500">*</span>
					</label>
					<p class="font-inter text-[11px] text-[#7188a3]">Masukkan nomor NIP Dosen</p>
					<input
						v-model="form.nip"
						type="text"
						placeholder="J0403231075"
						@input="errors.nip = ''"
						class="mt-1.5 h-[42px] w-full rounded-[10px] border bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
						:class="errors.nip ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
					/>
					<p v-if="errors.nip" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
						<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
						</svg>
						<span>{{ errors.nip }}</span>
					</p>
				</div>

				<!-- 2. Nama Dosen -->
				<div>
					<label class="block text-[14px] font-bold text-[#183669]">
						Nama Dosen<span class="text-red-500">*</span>
					</label>
					<p class="font-inter text-[11px] text-[#7188a3]">Masukkan nama lengkap dan gelar Dosen</p>
					<input
						v-model="form.name"
						type="text"
						placeholder="Dr. Ir. John Doe M.Sc."
						@input="errors.name = ''"
						class="mt-1.5 h-[42px] w-full rounded-[10px] border bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
						:class="errors.name ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
					/>
					<p v-if="errors.name" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
						<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
						</svg>
						<span>{{ errors.name }}</span>
					</p>
				</div>

				<!-- 3. Username (Opsional) -->
				<div>
					<label class="block text-[14px] font-bold text-[#183669]">
						Username
					</label>
					<p class="font-inter text-[11px] text-[#7188a3]">Masukkan username akun (optional)</p>
					<input
						v-model="form.username"
						type="text"
						placeholder="johndoe"
						@input="errors.username = ''"
						class="mt-1.5 h-[42px] w-full rounded-[10px] border bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
						:class="errors.username ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
					/>
					<p v-if="errors.username" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
						<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
						</svg>
						<span>{{ errors.username }}</span>
					</p>
				</div>

				<!-- 4. Password (Kredensial Akun) -->
				<div>
					<div class="flex items-center justify-between">
						<label class="block text-[14px] font-bold text-[#183669]">
							Password<span v-if="!isEditing" class="text-red-500">*</span>
						</label>
						<span v-if="isEditing" class="font-inter text-[11px] font-medium text-[#7188a3]">
							(Kosongkan jika tidak ingin diubah)
						</span>
					</div>
					<p class="font-inter text-[11px] text-[#7188a3]">
						{{ isEditing ? 'Isi password baru untuk mengganti password dosen ini' : 'Masukkan password akun dosen ini' }}
					</p>
					<div class="relative mt-1.5">
						<input
							v-model="form.password"
							:type="showPassword ? 'text' : 'password'"
							:placeholder="isEditing ? 'Masukkan password baru (opsional)' : 'Password dosen (default NIP)'"
							@input="isPasswordManuallyEdited = true; errors.password = ''"
							class="h-[42px] w-full rounded-[10px] border bg-white pl-3.5 pr-11 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 focus:outline-none focus:ring-0"
							:class="errors.password ? 'border-red-400 focus:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white'"
						/>
						<button
							type="button"
							@click="showPassword = !showPassword"
							class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#17334F] hover:opacity-75 focus:outline-none"
							:title="showPassword ? 'Sembunyikan Password' : 'Lihat Password'"
						>
							<img
								:src="showPassword ? '/assets/icons/shown.svg' : '/assets/icons/hidden.svg'"
								:alt="showPassword ? 'Sembunyikan Password' : 'Lihat Password'"
								class="h-4 w-4 object-contain"
							/>
						</button>
					</div>
					<p v-if="errors.password" class="mt-1 flex items-center gap-1 font-inter text-[11px] font-medium text-red-500">
						<svg class="h-3.5 w-3.5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
						</svg>
						<span>{{ errors.password }}</span>
					</p>
				</div>

				<!-- 5. Email (Kontak Opsional) -->
				<div>
					<label class="block text-[14px] font-bold text-[#183669]">
						Email
					</label>
					<p class="font-inter text-[11px] text-[#7188a3]">Masukkan email aktif Dosen (optional)</p>
					<input
						v-model="form.email"
						type="email"
						placeholder="example@email.com"
						class="mt-1.5 h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0"
					/>
				</div>

				<!-- 6. Nomor Handphone (Kontak Opsional) -->
				<div>
					<label class="block text-[14px] font-bold text-[#183669]">
						Nomor Handphone
					</label>
					<p class="font-inter text-[11px] text-[#7188a3]">Masukkan nomor aktif Dosen (optional)</p>
					<PhoneInput
						v-model="form.phone"
						height-class="h-[42px]"
						class="mt-1.5"
						placeholder="812-3456-7890"
					/>
				</div>

				<!-- Action Buttons -->
				<div class="mt-7 flex items-center justify-center gap-4 pt-2">
					<button
						type="button"
						@click="handleClose"
						class="h-[44px] min-w-[140px] px-6 rounded-[10px] border-2 border-[#d6e0ee] bg-white font-poppins text-[15px] font-bold text-[#183669] transition hover:border-[#183669] hover:bg-slate-50 focus:border-[#183669] focus:outline-none active:border-[#183669]"
					>
						Batal
					</button>
					<button
						type="submit"
						class="h-[44px] min-w-[140px] px-6 rounded-[10px] bg-[#183669] font-poppins text-[15px] font-bold text-white transition hover:bg-[#122b54]"
					>
						{{ isEditing ? 'Simpan Perubahan' : 'Tambah Dosen' }}
					</button>
				</div>
			</form>
					</div>
				</Transition>
			</div>
		</Transition>
	</Teleport>
</template>
