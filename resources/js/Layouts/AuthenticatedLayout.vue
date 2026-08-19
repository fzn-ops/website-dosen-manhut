<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import SidebarAdmin from '@/Components/SidebarAdmin.vue';
import TopbarAdmin from '@/Components/TopbarAdmin.vue';

const showingSidebar = ref(true);
const isMobile = ref(false);

const updateViewport = () => {
    const nextIsMobile = window.innerWidth < 768;

    if (nextIsMobile !== isMobile.value) {
        showingSidebar.value = !nextIsMobile;
    }

    isMobile.value = nextIsMobile;
};

const sidebarCollapsed = computed(() => !showingSidebar.value);

watch([isMobile, showingSidebar], () => {
	if (isMobile.value && showingSidebar.value) {
		document.body.classList.add('overflow-hidden');
		return;
	}

	document.body.classList.remove('overflow-hidden');
});

onMounted(() => {
	updateViewport();
	window.addEventListener('resize', updateViewport);
});

onBeforeUnmount(() => {
	window.removeEventListener('resize', updateViewport);
	document.body.classList.remove('overflow-hidden');
});
</script>

<template>
    <div class="flex min-h-screen bg-[#eef2f7]">
        <div
            v-if="isMobile && showingSidebar"
            class="fixed inset-0 z-30 bg-[#102653]/45"
            aria-hidden="true"
            @click="showingSidebar = false"
        ></div>

        <SidebarAdmin
            :collapsed="sidebarCollapsed"
            :mobile="isMobile"
            @navigate="isMobile && (showingSidebar = false)"
        />

        <div :class="['min-w-0 flex-1', isMobile ? 'ml-[80px]' : '']">
            <TopbarAdmin @toggle="showingSidebar = !showingSidebar" />

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
