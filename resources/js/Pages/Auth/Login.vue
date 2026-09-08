<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

// State untuk membolak-balik form
const isForgotPassword = ref(false);

// --- FORM LOGIN ---
const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

// --- FORM LUPA PASSWORD ---
const formReset = useForm({
    email: '',
});

const submitResetPassword = () => {
    formReset.post(route('password.email'), {
        onSuccess: () => {
            isForgotPassword.value = false;
            formReset.reset();
            // Pesan sukses akan otomatis masuk ke variabel 'status' bawaan Laravel
        },
    });
};
</script>

<template>
    <Head :title="isForgotPassword ? 'Lupa Password | DosenManhut' : 'Log in | DosenManhut'" />

    <!-- Background Utama Full Screen -->
    <div class="min-h-screen bg-[#183669] relative overflow-hidden flex items-center justify-center p-4 sm:p-6 font-sans">
        
        <!-- Ornamen Background -->
        <svg class="absolute -top-10 -right-10 w-64 h-64 md:w-96 md:h-96 opacity-40 pointer-events-none" viewBox="0 0 200 200" fill="none">
           <path d="M 200 0 A 150 150 0 0 0 50 200" stroke="white" stroke-width="1.5" stroke-dasharray="8 8"/>
        </svg>
        <svg class="absolute -bottom-10 -left-10 w-64 h-64 md:w-80 md:h-80 opacity-40 pointer-events-none" viewBox="0 0 200 200" fill="none">
           <path d="M 0 50 A 100 100 0 0 1 100 200" stroke="white" stroke-width="1.5" stroke-dasharray="8 8"/>
        </svg>
        <svg class="absolute top-24 right-[15%] md:right-[25%] w-10 h-10 md:w-14 md:h-14 text-white/30 pointer-events-none" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z"/>
        </svg>
        <svg class="absolute bottom-32 left-[15%] md:left-[25%] w-8 h-8 md:w-10 md:h-10 text-white/30 pointer-events-none" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z"/>
        </svg>

        <!-- KARTU LOGIN (FORM) -->
        <div class="w-full max-w-[420px] rounded-[20px] bg-white p-7 sm:p-9 shadow-[0_20px_50px_rgba(15,36,71,0.25)] border border-[#e2e8f0] relative z-10 transition-all duration-300 font-sans">
            
            <!-- Academic Brand Badge -->
            <!-- <div class="mx-auto mb-3.5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#183669]/10 text-[#183669] ring-1 ring-[#183669]/15">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                    <path d="M6 12v5c3 3 9 3 12 0v-5" />
                </svg>
            </div> -->

            <h2 
                :class="[
                    'text-[22px] sm:text-[24px] font-bold text-[#183669] text-center font-poppins leading-tight',
                    isForgotPassword ? 'mb-1.5' : 'mb-6'
                ]"
            >
                {{ isForgotPassword ? 'Lupa Password' : 'Login DosenManhut' }}
            </h2>

            <p v-if="isForgotPassword" class="text-center font-inter text-[12px] sm:text-[13px] text-[#64748b] mb-6">
                Masukkan email Anda untuk menerima tautan reset password.
            </p>

            <div v-if="status && !isForgotPassword" class="mb-5 font-inter text-[12px] sm:text-[13px] font-medium text-emerald-700 text-center bg-emerald-50 border border-emerald-200 p-2.5 rounded-[10px]">
                {{ status }}
            </div>

            <!-- ============================== -->
            <!-- TAMPILAN 1: FORM LOGIN         -->
            <!-- ============================== -->
            <form v-if="!isForgotPassword" @submit.prevent="submit" class="flex flex-col gap-4">
                
                <!-- Input USERNAME -->
                <div>
                    <label for="username" class="block font-inter text-[13px] font-semibold text-[#183669] mb-1.5">
                        Username / NIP
                    </label>
                    <div 
                        class="relative flex items-center rounded-[10px] border bg-white transition-colors duration-150"
                        :class="form.errors.username ? 'border-red-400 focus-within:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] focus-within:border-[#183669]'"
                    >
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#7188a3]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input 
                            id="username"
                            type="text" 
                            v-model="form.username" 
                            placeholder="Masukkan username atau NIP" 
                            class="h-[44px] w-full rounded-[10px] bg-transparent pl-10 pr-3.5 font-inter text-[13px] sm:text-[14px] text-[#1e293b] placeholder:text-[#94a3b8] focus:outline-none border-none ring-0 focus:ring-0"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                    <InputError class="mt-1 font-inter text-[12px]" :message="form.errors.username" />
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block font-inter text-[13px] font-semibold text-[#183669] mb-1.5">
                        Password
                    </label>
                    <div 
                        class="relative flex items-center rounded-[10px] border bg-white transition-colors duration-150"
                        :class="form.errors.password ? 'border-red-400 focus-within:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] focus-within:border-[#183669]'"
                    >
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#7188a3]">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <input 
                            id="password"
                            :type="showPassword ? 'text' : 'password'" 
                            v-model="form.password" 
                            placeholder="Masukkan password" 
                            class="h-[44px] w-full rounded-[10px] bg-transparent pl-10 pr-10 font-inter text-[13px] sm:text-[14px] text-[#1e293b] placeholder:text-[#94a3b8] focus:outline-none border-none ring-0 focus:ring-0"
                            required
                            autocomplete="current-password"
                        >
                        <button 
                            type="button" 
                            @click="togglePassword" 
                            class="absolute right-0 inset-y-0 flex items-center pr-3 text-[#7188a3] hover:text-[#183669] transition-colors focus:outline-none"
                            :title="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                            :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                        >
                            <img 
                                :src="showPassword ? '/assets/icons/shown.svg' : '/assets/icons/hidden.svg'" 
                                :alt="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                                class="w-[18px] h-[14px] object-contain select-none opacity-70 hover:opacity-100 transition-opacity"
                            />
                        </button>
                    </div>
                    <InputError class="mt-1 font-inter text-[12px]" :message="form.errors.password" />
                </div>

                <!-- Checkbox & Lupa Password -->
                <div class="flex items-center justify-between font-inter text-[12px] sm:text-[13px] pt-1">
                    <label class="flex items-center cursor-pointer gap-2 group select-none">
                        <input 
                            type="checkbox" 
                            name="remember"
                            v-model="form.remember" 
                            class="h-4 w-4 rounded-[4px] border-[#cbd5e1] text-[#183669] focus:ring-0 focus:ring-offset-0 focus:outline-none cursor-pointer"
                        >
                        <span class="font-medium text-[#526b88] group-hover:text-[#183669] transition-colors">Ingat saya</span>
                    </label>
                    
                    <!-- Pemicu Halaman Lupa Password -->
                    <button 
                        v-if="canResetPassword" 
                        type="button"
                        @click="isForgotPassword = true" 
                        class="font-semibold text-[#183669] hover:text-[#122b54] hover:underline transition-colors bg-transparent border-none p-0 cursor-pointer"
                    >
                        Lupa Password?
                    </button>
                </div>

                <!-- Tombol Login -->
                <button 
                    type="submit" 
                    :disabled="form.processing"
                    class="mt-2 flex h-[44px] sm:h-[46px] w-full items-center justify-center rounded-[10px] bg-[#183669] font-poppins text-[14px] sm:text-[15px] font-semibold text-white shadow-sm transition hover:bg-[#122b54] active:scale-[0.98] focus:outline-none disabled:opacity-60"
                    :class="{ 'cursor-not-allowed': form.processing }"
                >
                    <svg v-if="form.processing" class="mr-2 h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span>{{ form.processing ? 'Memproses...' : 'Login' }}</span>
                </button>
            </form>

            <!-- ============================== -->
            <!-- TAMPILAN 2: FORM LUPA PASSWORD -->
            <!-- ============================== -->
            <form v-else @submit.prevent="submitResetPassword" class="flex flex-col gap-4">
                <!-- Input Email -->
                <div>
                    <label for="reset_email" class="block font-inter text-[13px] font-semibold text-[#183669] mb-1.5">
                        Email Terdaftar
                    </label>
                    <div 
                        class="relative flex items-center rounded-[10px] border bg-white transition-colors duration-150"
                        :class="formReset.errors.email ? 'border-red-400 focus-within:border-red-500 bg-red-50/20' : 'border-[#d6e0ee] hover:border-[#a6b7cb] focus-within:border-[#183669]'"
                    >
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#7188a3]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input 
                            id="reset_email"
                            type="email" 
                            v-model="formReset.email" 
                            placeholder="nama@institusi.ac.id" 
                            class="h-[44px] w-full rounded-[10px] bg-transparent pl-10 pr-3.5 font-inter text-[13px] sm:text-[14px] text-[#1e293b] placeholder:text-[#94a3b8] focus:outline-none border-none ring-0 focus:ring-0"
                            required
                            autofocus
                        >
                    </div>
                    <InputError class="mt-1 font-inter text-[12px]" :message="formReset.errors.email" />
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center gap-3 mt-2">
                    <button 
                        type="button" 
                        @click="isForgotPassword = false"
                        class="flex h-[44px] sm:h-[46px] w-full items-center justify-center rounded-[10px] border border-[#d6e0ee] bg-[#f8fafc] font-poppins text-[14px] font-semibold text-[#475569] transition hover:bg-[#eef2f6] hover:text-[#1e293b] active:scale-[0.98] focus:outline-none"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="formReset.processing"
                        class="flex h-[44px] sm:h-[46px] w-full items-center justify-center rounded-[10px] bg-[#183669] font-poppins text-[14px] font-semibold text-white shadow-sm transition hover:bg-[#122b54] active:scale-[0.98] focus:outline-none disabled:opacity-60"
                        :class="{ 'cursor-not-allowed': formReset.processing }"
                    >
                        <svg v-if="formReset.processing" class="mr-2 h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span>{{ formReset.processing ? 'Mengirim...' : 'Kirim Tautan' }}</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</template>

<style scoped>
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus, 
input:-webkit-autofill:active{
    -webkit-box-shadow: 0 0 0 30px #ffffff inset !important;
}
</style>