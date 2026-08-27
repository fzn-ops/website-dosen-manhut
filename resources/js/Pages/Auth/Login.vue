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
</script>

<template>
    <Head title="Log in | DosenManhut" />

    <!-- Background Utama Full Screen -->
    <div class="min-h-screen bg-[#1a3675] relative overflow-hidden flex items-center justify-center font-sans">
        
        <!-- Ornamen Background -->
        <svg class="absolute -top-10 -right-10 w-64 h-64 md:w-96 md:h-96 opacity-60 pointer-events-none" viewBox="0 0 200 200" fill="none">
           <path d="M 200 0 A 150 150 0 0 0 50 200" stroke="white" stroke-width="1.5" stroke-dasharray="8 8"/>
        </svg>
        <svg class="absolute -bottom-10 -left-10 w-64 h-64 md:w-80 md:h-80 opacity-60 pointer-events-none" viewBox="0 0 200 200" fill="none">
           <path d="M 0 50 A 100 100 0 0 1 100 200" stroke="white" stroke-width="1.5" stroke-dasharray="8 8"/>
        </svg>
        <svg class="absolute top-24 right-[15%] md:right-[25%] w-10 h-10 md:w-16 md:h-16 text-white pointer-events-none" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z"/>
        </svg>
        <svg class="absolute bottom-32 left-[15%] md:left-[25%] w-8 h-8 md:w-12 md:h-12 text-white pointer-events-none" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 0C12 6.627 17.373 12 24 12C17.373 12 12 17.373 12 24C12 17.373 6.627 12 0 12C6.627 12 12 6.627 12 0Z"/>
        </svg>

        <!-- KARTU LOGIN (FORM) -->
        <div class="bg-[#fafafc] w-[90%] max-w-[420px] rounded-3xl p-8 md:p-10 z-10 shadow-2xl relative">
            
            <h2 class="text-2xl md:text-3xl font-bold text-[#1a3675] text-center mb-8 font-poppins">
                Login DosenManhut
            </h2>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-6">
                
                <!-- Input USERNAME -->
                <div>
                    <div class="relative flex items-center border-b-[1.5px] border-gray-400 focus-within:border-[#1a3675] pb-2 transition-colors">
                        <svg class="w-5 h-5 text-[#1a3675] mr-3" fill="none" stroke="#17334F" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <input 
                            id="username"
                            type="text" 
                            v-model="form.username" 
                            placeholder="Username atau NIP" 
                            class="w-full bg-transparent outline-none border-none ring-0 focus:ring-0 text-gray-700 placeholder-gray-400 text-sm font-medium p-0"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                    <!-- Error dikaitkan ke form.errors.username -->
                    <InputError class="mt-2" :message="form.errors.username" />
                </div>

                <!-- Input Password -->
                <div>
                    <div class="relative flex items-center border-b-[1.5px] border-gray-400 focus-within:border-[#1a3675] pb-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#1a3675] mr-3" viewBox="0 0 24 24" fill="none" stroke="#17334F " stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4">
                        </path></svg>
                        <input 
                            id="password"
                            :type="showPassword ? 'text' : 'password'" 
                            v-model="form.password" 
                            placeholder="Password" 
                            class="w-full bg-transparent outline-none border-none ring-0 focus:ring-0 text-gray-700 placeholder-gray-400 text-sm font-medium pr-8 p-0"
                            required
                            autocomplete="current-password"
                        >
                        <button 
                            type="button" 
                            @click="togglePassword" 
                            class="absolute right-0 text-[#1a3675] hover:text-blue-800 transition-colors focus:outline-none"
                        >
                            <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Checkbox & Lupa Password -->
                <div class="flex justify-between items-center text-[11px] md:text-xs text-[#1a3675] font-bold mt-2">
                    <label class="flex items-center cursor-pointer gap-2 group">
                        <input 
                            type="checkbox" 
                            name="remember"
                            v-model="form.remember" 
                            class="w-3.5 h-3.5 md:w-4 md:h-4 rounded border-gray-400 text-[#1a3675] focus:ring-[#1a3675]"
                        >
                        <span class="group-hover:text-blue-800 transition-colors">Remember me</span>
                    </label>
                    
                    <Link 
                        v-if="canResetPassword" 
                        :href="route('password.request')" 
                        class="hover:text-blue-800 hover:underline transition-all"
                    >
                        Lupa Password?
                    </Link>
                </div>

                <!-- Tombol Login -->
                <button 
                    type="submit" 
                    :disabled="form.processing"
                    class="w-full bg-[#1a3675] hover:bg-blue-900 text-white font-bold py-3 md:py-3.5 rounded-full transition-all active:scale-95 shadow-[0_4px_14px_0_rgba(26,54,117,0.39)] mt-2"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus, 
input:-webkit-autofill:active{
    -webkit-box-shadow: 0 0 0 30px #fafafc inset !important;
}
</style>