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
const showPassword = ref(false);
const isPasswordManuallyEdited = ref(false);

// Auto-sync password with NIP if user hasn't manually edited it
watch(
	() => form.value.nip,
	(newNip) => {
		if (!isPasswordManuallyEdited.value) {
			form.value.password = (newNip || '').trim();
		}
	}
);

watch(
	() => props.show,
	(isOpen) => {
		if (isOpen) {
			formError.value = '';
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
	emit('close');
};

const handleSubmit = () => {
	formError.value = '';
	const inputNip = form.value.nip.trim();
	const inputName = form.value.name.trim();
	const inputPassword = form.value.password;

	if (!inputNip || !inputName) {
		formError.value = 'NIP dan Nama Dosen wajib diisi.';
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

			<form @submit.prevent="handleSubmit" class="mt-4 space-y-4 font-poppins">
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
						required
						class="mt-1.5 h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
					/>
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
						required
						class="mt-1.5 h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
					/>
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
							@input="isPasswordManuallyEdited = true"
							required
							class="h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white pl-3.5 pr-11 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
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
						class="mt-1.5 h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
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
						class="mt-1.5 h-[42px] w-full rounded-[10px] border border-[#d6e0ee] bg-white px-3.5 font-inter text-[14px] text-[#1e3456] placeholder-[#a6b7cb] focus:border-[#183669] focus:outline-none focus:ring-0"
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
