<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import TablePagination from '@/Components/TablePagination.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import SearchBarTable from '@/Components/SearchBarTable.vue';
import ModalDeleteConfirmation from '@/Components/ModalDeleteConfirmation.vue';
import ModalBulkDeletePublication from '@/Components/admin/ModalBulkDeletePublication.vue';
import ModalSyncLoading from '@/Components/admin/ModalSyncLoading.vue';
import ModalSelectLecturerSync from '@/Components/admin/ModalSelectLecturerSync.vue';
import axios from 'axios'; 

const props = defineProps({
    publications: {
        type: Array,
        default: () => [],
    },
    lecturers: {
        type: Array,
        default: () => [],
    },
    availableProfiles: {
        type: Array,
        default: () => [],
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
    toast.value = { show: true, type, title, message };
};

const closeToast = () => {
    toast.value.show = false;
};

// Check flash messages on page mount
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) showToast('success', 'Berhasil', flash.success);
        else if (flash?.error) showToast('error', 'Gagal', flash.error);
    },
    { immediate: true, deep: true }
);

const publications = ref(Array.isArray(props.publications) ? [...props.publications] : []);

watch(
    () => props.publications,
    (val) => {
        publications.value = Array.isArray(val) ? [...val] : [];
    },
    { immediate: true, deep: true }
);

const isLoading = ref(true);
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 350);
});

// --- FILTER & SEARCH STATE ---
const searchQuery = ref('');
const selectedLecturerFilter = ref('');
const selectedYears = ref([]);
const isFilterOpen = ref(false);
const filterSearchQuery = ref('');

// Daftar tahun unik dari data publikasi (terurut terbaru ke terlama)
const availableYears = computed(() => {
    const pubList = Array.isArray(publications.value) && publications.value.length > 0
        ? publications.value
        : (Array.isArray(props.publications) ? props.publications : []);
    const set = new Set();
    pubList.forEach((p) => {
        if (p && p.year !== null && p.year !== undefined) {
            const y = String(p.year).trim();
            if (y !== '' && !isNaN(Number(y))) {
                set.add(Number(y));
            }
        }
    });
    return Array.from(set).sort((a, b) => b - a);
});

const toggleFilterDropdown = () => {
    isFilterOpen.value = !isFilterOpen.value;
    if (isFilterOpen.value) filterSearchQuery.value = '';
};

const closeAllDropdowns = () => {
    isFilterOpen.value = false;
};

onMounted(() => document.addEventListener('click', closeAllDropdowns));
onBeforeUnmount(() => document.removeEventListener('click', closeAllDropdowns));

const lecturerFilterList = computed(() => {
    const rawList = Array.isArray(props.lecturers) && props.lecturers.length > 0
        ? props.lecturers
        : (Array.isArray(props.availableProfiles) ? props.availableProfiles : []);
    const list = rawList.map((p) => p?.name).filter(Boolean);
    return ['Semua Dosen', ...list];
});

const filteredLecturerFilterList = computed(() => {
    const q = filterSearchQuery.value ? filterSearchQuery.value.toLowerCase().trim() : '';
    const baseList = lecturerFilterList.value || [];
    if (!q) return baseList;
    return baseList.filter((lec) => lec && lec.toLowerCase().includes(q));
});

const setLecturerFilter = (lec) => {
    selectedLecturerFilter.value = lec === 'Semua Dosen' ? '' : lec;
    currentPage.value = 1;
};

const toggleYearFilter = (year) => {
    const yNum = Number(year);
    const idx = selectedYears.value.indexOf(yNum);
    if (idx > -1) selectedYears.value.splice(idx, 1);
    else selectedYears.value.push(yNum);
    currentPage.value = 1;
};

const resetAllFilters = () => {
    selectedYears.value = [];
    selectedLecturerFilter.value = '';
    filterSearchQuery.value = '';
    currentPage.value = 1;
};

const resetYearFilter = () => {
    selectedYears.value = [];
    currentPage.value = 1;
};

const resetLecturerFilter = () => {
    selectedLecturerFilter.value = '';
    filterSearchQuery.value = '';
    currentPage.value = 1;
};

// --- TABLE COLUMNS CONFIG ---
const columns = [
    { key: 'dosen', label: 'Dosen', sortable: true, align: 'left', width: 'w-[15%]' },
    { key: 'title', label: 'Judul Publikasi', sortable: true, align: 'left', width: 'w-[25%]' },
    { key: 'authors', label: 'Penulis', sortable: true, align: 'left', width: 'w-[18%]' },
    { key: 'publisher', label: 'Publisher/Jurnal', sortable: true, align: 'left', width: 'w-[16%]' },
    { key: 'cited_by', label: 'Sitasi', sortable: true, align: 'center', width: 'w-[8%]' },
    { key: 'year', label: 'Tahun', sortable: true, align: 'center', width: 'w-[8%]' },
    { key: 'action', label: 'Aksi', sortable: false, align: 'center', width: 'w-[10%]' },
];

const sortKey = ref('year');
const sortDirection = ref('desc'); // Default: Tahun terbaru di nomor 1

const toggleSort = (key) => {
    if (sortKey.value === key) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortDirection.value = 'asc';
    }
};

// --- FILTERING & SORTING LOGIC ---
const filteredAndSortedPublications = computed(() => {
    const pubList = Array.isArray(publications.value) ? publications.value : [];
    let list = [...pubList];

    // 1. Filter by Lecturer Name
    if (selectedLecturerFilter.value) {
        list = list.filter(
            (p) => (p.user?.name || p.dosen?.name || p.lecturerName) === selectedLecturerFilter.value
        );
    }

    // 2. Filter by Year (Multi-select)
    if (selectedYears.value.length > 0) {
        const selectedYearSet = new Set(selectedYears.value.map(Number));
        list = list.filter((p) => p && p.year && selectedYearSet.has(Number(p.year)));
    }

    // 3. Search Query (Judul, Author, Publisher, atau Dosen)
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase().trim();
        list = list.filter(
            (p) =>
                (p.title && p.title.toLowerCase().includes(q)) ||
                (p.authors && p.authors.toLowerCase().includes(q)) ||
                (p.publisher && p.publisher.toLowerCase().includes(q)) ||
                (p.user?.name && p.user.name.toLowerCase().includes(q))
        );
    }

    // 4. Sorting
    if (sortKey.value) {
        list.sort((a, b) => {
            let valA = a[sortKey.value] ?? '';
            let valB = b[sortKey.value] ?? '';

            if (sortKey.value === 'dosen') {
                valA = a.user?.name ?? a.dosen?.name ?? '';
                valB = b.user?.name ?? b.dosen?.name ?? '';
            }

            // Handle sorting angka (Sitasi & Tahun)
            if (sortKey.value === 'cited_by' || sortKey.value === 'year') {
                valA = Number(valA);
                valB = Number(valB);
                return sortDirection.value === 'asc' ? valA - valB : valB - valA;
            }

            valA = valA.toString().toLowerCase();
            valB = valB.toString().toLowerCase();

            if (valA === valB) return 0;
            if (sortDirection.value === 'asc') return valA > valB ? 1 : -1;
            return valA < valB ? 1 : -1;
        });
    }

    return list;
});

// --- PAGINATION ---
const currentPage = ref(1);
const rowsPerPage = ref(10);

const totalPages = computed(() => {
    const count = Math.ceil(filteredAndSortedPublications.value.length / rowsPerPage.value);
    return count > 0 ? count : 1;
});

const paginatedPublications = computed(() => {
    const start = (currentPage.value - 1) * rowsPerPage.value;
    return filteredAndSortedPublications.value.slice(start, start + rowsPerPage.value);
});

watch([rowsPerPage, searchQuery, selectedLecturerFilter, selectedYears], () => {
    currentPage.value = 1;
}, { deep: true });


// --- TOMBOL SINKRONISASI GOOGLE SCHOLAR ---
const isSelectLecturerModalOpen = ref(false);
const isSyncing = ref(false);
const isSyncFinished = ref(false);
const selectedSyncCount = ref(1);

const openSyncModal = () => {
    isSelectLecturerModalOpen.value = true;
};

const handleConfirmSync = async (selectedDosenIds) => {
    isSelectLecturerModalOpen.value = false;
    selectedSyncCount.value = selectedDosenIds.length;
    isSyncing.value = true;
    isSyncFinished.value = false;

    try {
        const response = await axios.post('/admin/publikasi/run', {
            dosen_ids: selectedDosenIds,
        });

        // Trigger 100% state
        isSyncFinished.value = true;
        await new Promise((resolve) => setTimeout(resolve, 700));

        showToast('success', 'Sinkronisasi Selesai', response.data.message);

        // Refresh data dari database setelah sukses ditarik scraper
        router.reload({ only: ['publications', 'lecturers', 'availableProfiles'] });
    } catch (error) {
        showToast('error', 'Gagal Sinkronisasi', error.response?.data?.message || 'Terjadi kesalahan sistem.');
    } finally {
        isSyncing.value = false;
        isSyncFinished.value = false;
    }
};


// --- DELETE LOGIC ---
const isDeleteModalOpen = ref(false);
const deletingPublication = ref(null);
const isDeleting = ref(false);
const isTruncateModalOpen = ref(false);
const isTruncating = ref(false);

const openDeleteModal = (pub) => {
    deletingPublication.value = pub;
    isDeleteModalOpen.value = true;
};

const confirmDeletePublication = () => {
    if (!deletingPublication.value) return;
    const pub = deletingPublication.value;
    isDeleting.value = true;
    router.delete(route('admin.publikasi.destroy', pub.id), {
        preserveScroll: true,
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            publications.value = publications.value.filter((p) => p.id !== pub.id);
            deletingPublication.value = null;
            isDeleting.value = false;
            showToast('success', 'Berhasil Dihapus', 'Data publikasi berhasil dihapus.');
        },
        onError: (err) => {
            isDeleting.value = false;
            showToast('error', 'Gagal Menghapus', Object.values(err)[0] || 'Terjadi kesalahan.');
        },
    });
};

// --- BULK DELETE LOGIC ---
const isBulkDeleteModalOpen = ref(false);
const isBulkDeleting = ref(false);

const openBulkDeleteModal = () => {
    isBulkDeleteModalOpen.value = true;
};

const handleConfirmBulkDelete = async ({ type, dosen_ids, years }) => {
    isBulkDeleting.value = true;
    try {
        const response = await axios.delete(route('admin.publikasi.destroyAll'), {
            data: { type, dosen_ids, years },
        });

        showToast('success', 'Berhasil Dihapus', response.data.message);
        isBulkDeleteModalOpen.value = false;

        // Kosongkan / filter data publikasi di layar secara real-time
        if (type === 'all') {
            publications.value = [];
        } else if (type === 'lecturers') {
            publications.value = publications.value.filter((p) => !dosen_ids.includes(p.user_id));
        } else if (type === 'years') {
            publications.value = publications.value.filter((p) => !years.includes(String(p.year)));
        }

        router.reload({ only: ['publications', 'lecturers', 'availableProfiles'] });
    } catch (error) {
        showToast(
            'error',
            'Gagal Menghapus',
            error.response?.data?.message || 'Terjadi kesalahan sistem.'
        );
    } finally {
        isBulkDeleting.value = false;
    }
};

// Lock background scroll when any modal is open
const isAnyModalOpen = computed(() => {
    return (
        isDeleteModalOpen.value ||
        isBulkDeleteModalOpen.value ||
        isSyncing.value ||
        isSelectLecturerModalOpen.value
    );
});

watch(isAnyModalOpen, (isOpen) => {
    if (typeof document === 'undefined') return;
    if (isOpen) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

onBeforeUnmount(() => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = '';
    }
});
</script>

<template>
    <Head title="Publikasi Dosen" />

    <AdminLayout>
        <section class="mx-auto w-full max-w-[1520px] px-4 py-6 font-poppins sm:px-6 sm:py-8 lg:px-8">
            <div class="space-y-6">
                <!-- Header Title & Subtitle -->
                <div class="space-y-1.5">
                    <h1 class="mt-1 text-[28px] font-bold leading-[1.05] tracking-[-0.03em] text-[#173a63] sm:text-[38px] lg:text-[44px]">
                        Daftar Publikasi
                    </h1>
                    <p class="mt-1 font-inter text-[13px] font-medium leading-tight text-[#4d6786] sm:text-[15px]">
                        Lihat data publikasi dosen hasil sinkronisasi otomatis dari Google Scholar
                    </p>
                </div>

                <!-- Action Bar (Matches dosen.vue responsive pattern: icons on mobile, text on desktop) -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Search Input Component (takes remaining width) -->
                    <div class="flex-1 min-w-0">
                        <SearchBarTable
                            v-model="searchQuery"
                            placeholder="Cari judul, penulis, atau jurnal..."
                        />
                    </div>

                    <!-- Unified Filter Dropdown -->
                    <div class="relative" @click.stop @keydown.escape="isFilterOpen = false">
                        <button
                            type="button"
                            @click="toggleFilterDropdown"
                            class="relative flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-[10px] border-2 bg-transparent text-[#183669] transition-colors focus:outline-none select-none"
                            :class="isFilterOpen ? 'border-[#183669]' : 'border-[#d6e0ee] hover:border-[#8ea9cb]'"
                            title="Filter Publikasi"
                        >
                            <img src="/assets/icons/filter.svg" alt="Filter Icon" class="h-5 w-5 object-contain" />
                            <span
                                v-if="selectedYears.length > 0 || selectedLecturerFilter !== ''"
                                class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-[#ef4444] ring-2 ring-[#eef2f7]"
                            ></span>
                        </button>

                        <div v-if="isFilterOpen" class="absolute right-0 z-30 mt-2 w-72 sm:w-80 max-w-[90vw] rounded-[16px] border border-[#d6e0ee] bg-white p-4 shadow-2xl font-inter">
                            <!-- Header -->
                            <div class="flex items-center justify-between border-b border-[#f0f4f9] pb-2.5">
                                <div class="flex items-center gap-2">
                                    <p class="font-poppins text-xs font-bold text-[#183669]">Filter Publikasi</p>
                                    <span
                                        v-if="selectedYears.length > 0 || selectedLecturerFilter !== ''"
                                        class="rounded-full bg-blue-100 text-[#183669] text-[10px] px-2 py-0.5 font-bold"
                                    >
                                        {{ (selectedYears.length > 0 ? 1 : 0) + (selectedLecturerFilter ? 1 : 0) }} Aktif
                                    </span>
                                </div>
                                <button
                                    v-if="selectedYears.length > 0 || selectedLecturerFilter !== ''"
                                    type="button"
                                    @click="resetAllFilters"
                                    class="font-inter text-[11px] font-semibold text-[#dc2626] hover:underline"
                                >
                                    Reset Semua
                                </button>
                            </div>

                            <!-- 1. Tahun Section (Clean simple checklist, NO presets) -->
                            <div class="mt-3">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <p class="font-poppins text-[11px] font-bold text-[#183669]">Tahun Terbit</p>
                                        <span v-if="selectedYears.length > 0" class="rounded-full bg-[#183669] text-white text-[10px] px-1.5 py-0.2 font-mono">
                                            {{ selectedYears.length }}
                                        </span>
                                    </div>
                                    <button
                                        v-if="selectedYears.length > 0"
                                        type="button"
                                        @click="resetYearFilter"
                                        class="font-inter text-[11px] font-semibold text-[#dc2626] hover:underline"
                                    >
                                        Reset Tahun
                                    </button>
                                </div>

                                <div class="grid grid-cols-3 gap-1.5 max-h-40 overflow-y-auto pr-1">
                                    <button
                                        v-for="year in availableYears"
                                        :key="year"
                                        type="button"
                                        @click="toggleYearFilter(year)"
                                        :class="[
                                            'rounded-[8px] py-1.5 px-2 text-center font-inter text-xs transition-all select-none',
                                            selectedYears.includes(year)
                                                ? 'bg-[#183669] font-bold text-white shadow-sm ring-1 ring-[#183669]'
                                                : 'bg-[#f4f7fb] hover:bg-slate-200 text-[#334e68] border border-[#d6e0ee]/70'
                                        ]"
                                    >
                                        {{ year }}
                                    </button>
                                    <div v-if="availableYears.length === 0" class="col-span-3 py-3 text-center text-xs text-gray-400">
                                        Tidak ada data tahun
                                    </div>
                                </div>
                            </div>

                            <div class="my-3 border-t border-[#f0f4f9]"></div>

                            <!-- 2. Dosen Section -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <p class="font-poppins text-[11px] font-bold text-[#183669]">Dosen</p>
                                        <span v-if="selectedLecturerFilter" class="text-[11px] text-[#183669] font-semibold truncate max-w-[110px]">
                                            : {{ selectedLecturerFilter }}
                                        </span>
                                    </div>
                                    <button
                                        v-if="selectedLecturerFilter !== ''"
                                        type="button"
                                        @click="resetLecturerFilter"
                                        class="font-inter text-[11px] font-semibold text-[#dc2626] hover:underline"
                                    >
                                        Reset Dosen
                                    </button>
                                </div>

                                <div class="group relative mb-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-2.5 flex items-center text-[#8ca1b9] transition-colors group-hover:text-[#183669] group-focus-within:text-[#183669]">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                        </svg>
                                    </div>
                                    <input
                                        v-model="filterSearchQuery"
                                        type="text"
                                        placeholder="Cari nama dosen..."
                                        class="h-[34px] w-full rounded-[8px] border-2 border-[#d6e0ee] bg-transparent pl-8 pr-2.5 text-xs text-[#1e3456] placeholder-[#8ca1b9] transition-colors hover:border-[#8ea9cb] focus:border-[#183669] focus:outline-none focus:ring-0"
                                        @click.stop
                                    />
                                </div>

                                <div class="max-h-36 overflow-y-auto space-y-0.5 pr-0.5">
                                    <button
                                        v-for="lec in filteredLecturerFilterList"
                                        :key="lec"
                                        type="button"
                                        @click="setLecturerFilter(lec)"
                                        :class="[
                                            'w-full rounded-[6px] px-2.5 py-1 text-left font-inter text-xs transition-colors truncate',
                                            (selectedLecturerFilter === '' && lec === 'Semua Dosen') || selectedLecturerFilter === lec ? 'bg-[#183669] font-bold text-white' : 'text-[#435b76] hover:bg-slate-100'
                                        ]"
                                    >
                                        {{ lec }}
                                    </button>
                                    <div v-if="filteredLecturerFilterList.length === 0" class="py-2 text-center text-xs text-[#8ca1b9]">
                                        Tidak ada dosen yang cocok
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Hapus (Square Icon on Mobile, Text on Desktop) -->
                    <button
                        type="button"
                        @click="openBulkDeleteModal"
                        class="flex h-[46px] w-[46px] sm:w-auto shrink-0 items-center justify-center gap-2 rounded-[10px] bg-red-600 px-0 sm:px-4 font-poppins text-[14px] font-semibold text-white shadow-sm transition hover:bg-red-700 active:scale-95 focus:outline-none select-none"
                        title="Hapus Data Publikasi"
                    >
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        <span class="hidden sm:inline">Hapus Banyak</span>
                    </button>

                    <!-- Tombol Sinkronisasi (Square Icon on Mobile, Text on Desktop) -->
                    <button
                        type="button"
                        @click="openSyncModal"
                        :disabled="isSyncing"
                        class="flex h-[46px] w-[46px] sm:w-auto shrink-0 items-center justify-center gap-2 rounded-[10px] bg-[#1a3675] px-0 sm:px-5 font-poppins text-[14px] font-semibold text-white shadow-sm transition hover:bg-[#122b54] active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed focus:outline-none select-none"
                        title="Tarik Data dari Google Scholar"
                    >
                        <svg v-if="isSyncing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span class="hidden sm:inline">{{ isSyncing ? 'Menyinkronkan...' : 'Sinkron Google Scholar' }}</span>
                    </button>
                </div>

                <!-- Table Section -->
                <div class="overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
                    <table class="w-full min-w-[950px] table-fixed border-collapse text-sm">
                        <thead class="bg-[#183669]">
                            <tr class="h-[48px]">
                                <th class="w-[50px] px-3 py-2.5 text-center font-poppins text-[13px] font-semibold text-white border-r border-white/15 lg:border-r-0">No</th>
                                <th
                                    v-for="col in columns"
                                    :key="col.key"
                                    :class="[col.width, 'px-3 py-2.5 font-poppins text-[13px] font-semibold text-white select-none border-r border-white/15 last:border-r-0 lg:border-r-0', col.align === 'center']"
                                >
                                    <button
                                        v-if="col.sortable"
                                        type="button"
                                        @click="toggleSort(col.key)"
                                        :class="['group transition-colors hover:text-white/80 focus:outline-none max-w-full', col.align === 'center' ? 'mx-auto flex items-center justify-center' : 'inline-flex items-center gap-1.5 justify-start']"
                                    >
                                        <span v-if="col.align === 'center'" class="h-3.5 w-3.5 shrink-0 opacity-0 pointer-events-none mr-1.5" aria-hidden="true"></span>
                                        <span class="truncate">{{ col.label }}</span>
                                        <span class="inline-flex shrink-0 items-center ml-1.5 text-white/70 group-hover:text-white">
                                            <svg v-if="sortKey === col.key" :class="['h-3.5 w-3.5 transition-transform duration-200', sortDirection === 'asc' ? 'rotate-180' : '']" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.69l3.72-3.72a.75.75 0 111.06 1.06l-5 5a.75.75 0 01-1.06 0l-5-5a.75.75 0 111.06-1.06l3.72 3.72V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                            </svg>
                                            <svg v-else class="h-3.5 w-3.5 opacity-50 transition-opacity group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.69l3.72-3.72a.75.75 0 111.06 1.06l-5 5a.75.75 0 01-1.06 0l-5-5a.75.75 0 111.06-1.06l3.72 3.72V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>
                                    <span v-else class="block truncate">{{ col.label }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d6e0ee] font-inter text-[14px] text-[#435b76]">
                            <template v-if="isLoading">
                                <tr v-for="n in 6" :key="`skeleton-pub-${n}`" class="h-[52px] animate-pulse bg-white">
                                    <td class="px-3 py-2.5 text-center"><div class="mx-auto h-4 w-5 rounded-md bg-slate-200"></div></td>
                                    <td class="px-3 py-2.5"><div class="h-4 w-28 rounded-md bg-slate-200"></div></td>
                                    <td class="px-3 py-2.5"><div class="h-4 w-48 rounded-md bg-slate-200"></div></td>
                                    <td class="px-3 py-2.5"><div class="h-4 w-36 rounded-md bg-slate-200"></div></td>
                                    <td class="px-3 py-2.5"><div class="h-4 w-32 rounded-md bg-slate-200"></div></td>
                                    <td class="px-3 py-2.5 text-center"><div class="mx-auto h-5 w-10 rounded-full bg-slate-200"></div></td>
                                    <td class="px-3 py-2.5 text-center"><div class="mx-auto h-4 w-12 rounded-md bg-slate-200"></div></td>
                                    <td class="px-3 py-2.5 text-center"><div class="mx-auto h-7 w-7 rounded-lg bg-slate-200"></div></td>
                                </tr>
                            </template>

                            <template v-else>
                                <tr v-for="(pub, idx) in paginatedPublications" :key="pub.id" class="h-[52px] transition-colors hover:bg-[#f7f9fd]">
                                    <td class="px-3 py-2.5 text-center font-medium">{{ (currentPage - 1) * rowsPerPage + idx + 1 }}</td>
                                    <td class="px-3 py-2.5 text-left font-semibold text-[#183669]" :title="pub.user?.name">
                                        <span class="block truncate max-w-[160px]">{{ pub.user?.name || '-' }}</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-left font-medium text-[#2f4b6e]" :title="pub.title">
                                        <span class="block truncate max-w-[260px]">{{ pub.title }}</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-left" :title="pub.authors">
                                        <span class="block truncate max-w-[180px]">{{ pub.authors || '-' }}</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-left" :title="pub.publisher">
                                        <span class="block truncate max-w-[160px]">{{ pub.publisher || '-' }}</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center">
                                        <span class="inline-flex items-center justify-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                            {{ pub.cited_by || 0 }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-medium">{{ pub.year || '-' }}</td>
                                    <td class="px-3 py-2.5 text-center">
                                        <!-- Tombol Edit dihilangkan karena data murni hasil sinkronisasi -->
                                        <DeleteButtonTable :label="`Hapus ${pub.title}`" @click="openDeleteModal(pub)" class="mx-auto" />
                                    </td>
                                </tr>
                                <tr v-if="filteredAndSortedPublications.length === 0">
                                    <td colspan="8" class="py-8 text-center text-[#7890a8]">
                                        Tidak ada data publikasi yang sesuai filter atau pencarian.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <TablePagination
                    :current-page="currentPage"
                    :total-pages="totalPages"
                    :rows-per-page="rowsPerPage"
                    @update:current-page="currentPage = $event"
                    @update:rows-per-page="rowsPerPage = $event; currentPage = 1"
                />
            </div>
        </section>

<!-- MODAL 1: Hapus Satuan -->
        <ModalDeleteConfirmation
            :show="isDeleteModalOpen"
            title="Hapus Data Publikasi"
            :item-name="deletingPublication?.title"
            :loading="isDeleting"
            @close="isDeleteModalOpen = false"
            @confirm="confirmDeletePublication" 
        />

        <!-- MODAL 2: Hapus Massal (Dosen, Tahun, Semua) -->
        <ModalBulkDeletePublication
            :show="isBulkDeleteModalOpen"
            :lecturers="props.lecturers"
            :publications="publications"
            :loading="isBulkDeleting"
            @close="isBulkDeleteModalOpen = false"
            @confirm="handleConfirmBulkDelete"
        />

        <!-- MODAL 3: Loading Scraping Google Scholar -->
        <ModalSyncLoading
            :show="isSyncing"
            :finished="isSyncFinished"
            :selected-count="selectedSyncCount"
        />

        <!-- MODAL 4: Pilih Dosen untuk Sinkronisasi -->
        <ModalSelectLecturerSync
            :show="isSelectLecturerModalOpen"
            :lecturers="props.lecturers"
            :publications="publications"
            @close="isSelectLecturerModalOpen = false"
            @confirm="handleConfirmSync"
        />

        <ToastNotification
            :show="toast.show"
            :type="toast.type"
            :title="toast.title"
            :message="toast.message"
            @close="closeToast"
        />
    </AdminLayout>
</template>