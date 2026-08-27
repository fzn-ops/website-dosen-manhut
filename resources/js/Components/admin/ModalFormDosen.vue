<script setup>
import { ref, watch } from 'vue';

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
			const currentPassword = props.initialData?.password || currentNip;

			form.value = {
				nip: currentNip,
				name: props.initialData?.name || '',
				password: currentPassword,
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
	const inputPassword = form.value.password;

	if (!inputNip) {
		errors.value.nip = 'NIP wajib diisi.';
	}
	if (!inputName) {
		errors.value.name = 'Nama dosen wajib diisi.';
	}
	if (!inputPassword) {
		errors.value.password = 'Password wajib diisi.';
	}

	if (Object.keys(errors.value).length > 0) {
		return;
	}

	// Validate duplicate NIP against existingLecturers
	const isNipDuplicate = props.existingLecturers.some((l) => {
		if (props.isEditing && l.id === props.editingId) return false;
		return l.nip.toLowerCase() === inputNip.toLowerCase();
	});

	if (isNipDuplicate) {
		formError.value = `NIP "${inputNip}" sudah terdaftar. NIP tidak boleh duplikat.`;
		return;
	}

	emit('submit', {
		nip: inputNip,
		name: inputName,
		password: inputPassword || inputNip,
		email: form.value.email.trim() || '-',
		phone: form.value.phone.trim() || '-',
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
		class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/40 p-4 transition-all"
		@mousedown="handleBackdropMouseDown"
		@mouseup="handleBackdropMouseUp"
	>
		<div class="w-full max-w-[500px] max-h-[92vh] overflow-y-auto transform rounded-[10px] bg-white p-7 shadow-2xl transition-all sm:p-9 font-poppins">
			<h2 class="text-center text-[22px] font-bold text-[#183669]">
				{{ isEditing ? 'Form Edit Dosen' : 'Form Tambah Dosen' }}
			</h2>

			<!-- Error alert if duplicate NIP or invalid -->
			<div v-if="formError" class="mt-4 rounded-[8px] bg-red-50 p-3 font-inter text-[12px] text-red-600 border border-red-200">
				{{ formError }}
			</div>

			<form @submit.prevent="handleSubmit" novalidate class="mt-4 space-y-4 font-poppins">
				<!-- 1. NIP (Identitas Utama / Akun) -->
				<div>
					<label class="block text-[14px] font-bold text-[#183669]">
						NIP<span class="text-red-500">*</span>
					</label>
					<p class="font-inter text-[11px] text-[#7188a3]">Masukkan Nomor Induk Pegawai (NIP)</p>
					<input
						v-model="form.nip"
						type="text"
						placeholder="E14XXXXXX"
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
						Nama<span class="text-red-500">*</span>
					</label>
					<p class="font-inter text-[11px] text-[#7188a3]">Masukan Nama Lengkap Dosen</p>
					<input
						v-model="form.name"
						type="text"
						placeholder="Prof. Dr. Ir. ..."
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

				<!-- 3. Password (Kredensial Akun) -->
				<div>
					<div class="flex items-center justify-between">
						<label class="block text-[14px] font-bold text-[#183669]">
							Password<span class="text-red-500">*</span>
						</label>
						<span class="font-inter text-[11px] font-medium text-[#7188a3]">(Default sesuai NIP)</span>
					</div>
					<p class="font-inter text-[11px] text-[#7188a3]">Default otomatis terisi sesuai NIP jika tidak diubah</p>
					<div class="relative mt-1.5">
						<input
							v-model="form.password"
							:type="showPassword ? 'text' : 'password'"
							placeholder="Password dosen"
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

				<!-- 4. Email (Kontak Opsional) -->
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

				<!-- 5. Nomor Handphone (Kontak Opsional) -->
				<div>
					<label class="block text-[14px] font-bold text-[#183669]">
						Nomor Handphone
					</label>
					<p class="font-inter text-[11px] text-[#7188a3]">Masukkan nomor aktif Dosen (optional)</p>
					<input
						v-model="form.phone"
						type="text"
						placeholder="+62 8XX - XXXX - XXXX"
						class="mt-1.5 h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] transition-colors duration-150 hover:border-[#a6b7cb] hover:bg-[#fafcff] focus:border-[#183669] focus:bg-white focus:outline-none focus:ring-0"
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
	</div>
</template>
