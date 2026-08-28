<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import SidebarAdmin from '@/Components/admin/SidebarAdmin.vue';
import TopbarAdmin from '@/Components/admin/TopbarAdmin.vue';

const checkIsMobile = () => typeof window !== 'undefined' && window.innerWidth < 768;

const getSavedSidebarState = () => {
    if (typeof window === 'undefined') return true;
    if (window.innerWidth < 768) return false;
    const saved = localStorage.getItem('sidebar_expanded');
    if (saved !== null) {
        return saved === 'true';
    }
    return true;
};

const isMobile = ref(checkIsMobile());
const showingSidebar = ref(getSavedSidebarState());

const updateViewport = () => {
    const nextIsMobile = window.innerWidth < 768;

    if (nextIsMobile !== isMobile.value) {
        if (nextIsMobile) {
            showingSidebar.value = false;
        } else {
            showingSidebar.value = getSavedSidebarState();
        }
    }

    isMobile.value = nextIsMobile;
};

const toggleSidebar = () => {
    showingSidebar.value = !showingSidebar.value;
    if (!isMobile.value) {
        localStorage.setItem('sidebar_expanded', showingSidebar.value ? 'true' : 'false');
    }
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
            <TopbarAdmin @toggle="toggleSidebar" />

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
