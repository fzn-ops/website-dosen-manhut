<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import SidebarAdmin from '@/Components/admin/SidebarAdmin.vue';
import TopbarAdmin from '@/Components/admin/TopbarAdmin.vue';
import ModalLogoutConfirmation from '@/Components/ModalLogoutConfirmation.vue';

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
const showLogoutModal = ref(false);
const isLoggingOut = ref(false);

const handleLogout = () => {
    isLoggingOut.value = true;
    router.post(route('logout'), {}, {
        onFinish: () => {
            isLoggingOut.value = false;
            showLogoutModal.value = false;
        }
    });
};

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

// Lock background scroll when any modal/pop up or mobile sidebar overlay is open
let modalObserver = null;
let removeRouterListener = null;

const checkHasOpenModal = () => {
    if (typeof document === 'undefined') return false;
    const overlays = document.querySelectorAll('.fixed.inset-0');
    return overlays.length > 0;
};

const updateBodyScrollLock = () => {
    if (typeof document === 'undefined') return;
    const shouldLock = checkHasOpenModal() || showLogoutModal.value || (isMobile.value && showingSidebar.value);
    if (shouldLock) {
        document.body.classList.add('overflow-hidden');
        document.documentElement.classList.add('overflow-hidden');
    } else {
        document.body.classList.remove('overflow-hidden');
        document.documentElement.classList.remove('overflow-hidden');
    }
};

watch([showLogoutModal, isMobile, showingSidebar], updateBodyScrollLock);

onMounted(() => {
	updateViewport();
	window.addEventListener('resize', updateViewport);

    // Observe body for teleported modals to lock/unlock background scroll
    modalObserver = new MutationObserver(() => {
        updateBodyScrollLock();
    });
    modalObserver.observe(document.body, { childList: true, subtree: true });

    // Clean up scroll lock on Inertia navigation
    removeRouterListener = router.on('navigate', () => {
        document.body.classList.remove('overflow-hidden');
        document.documentElement.classList.remove('overflow-hidden');
    });
});

onBeforeUnmount(() => {
	window.removeEventListener('resize', updateViewport);
    if (modalObserver) {
        modalObserver.disconnect();
        modalObserver = null;
    }
    if (removeRouterListener) {
        removeRouterListener();
        removeRouterListener = null;
    }
	document.body.classList.remove('overflow-hidden');
    document.documentElement.classList.remove('overflow-hidden');
});
</script>

<template>
    <div class="flex min-h-screen bg-[#eef2f7]">
        <!-- Mobile Backdrop Overlay (Click to close) -->
        <div
            v-if="isMobile && showingSidebar"
            class="fixed inset-0 z-30 bg-[#102653]/20 transition-opacity"
            aria-hidden="true"
            @click="showingSidebar = false"
        ></div>

        <SidebarAdmin
            :collapsed="sidebarCollapsed"
            :mobile="isMobile"
            @navigate="isMobile && (showingSidebar = false)"
            @logout="showLogoutModal = true"
        />

        <div class="min-w-0 flex-1 w-full">
            <TopbarAdmin @toggle="toggleSidebar" @logout="showLogoutModal = true" />

            <main>
                <slot />
            </main>
        </div>

        <!-- Modal Logout Confirmation -->
        <ModalLogoutConfirmation
            :show="showLogoutModal"
            :loading="isLoggingOut"
            @close="showLogoutModal = false"
            @confirm="handleLogout"
        />
    </div>
</template>
