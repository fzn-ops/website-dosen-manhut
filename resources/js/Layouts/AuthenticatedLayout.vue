<script setup>
import { ref } from 'vue';
import SidebarAdmin from '@/Components/SidebarAdmin.vue';
import TopbarAdmin from '@/Components/TopbarAdmin.vue';

const showingSidebar = ref(true);
</script>

<template>
    <div class="flex min-h-screen bg-[#eef2f7]">
        <SidebarAdmin :collapsed="!showingSidebar" />

        <div class="min-w-0 flex-1">
            <TopbarAdmin @toggle="showingSidebar = !showingSidebar" />

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
