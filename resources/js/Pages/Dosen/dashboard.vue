<script setup>
import DosenLayout from '@/Layouts/DosenLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, defineAsyncComponent, onMounted, onUnmounted, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

const ModalFormAktivitas = defineAsyncComponent(() => import('@/Components/dosen/ModalFormAktivitas.vue'));
const ModalDeleteConfirmation = defineAsyncComponent(() => import('@/Components/ModalDeleteConfirmation.vue'));
import { Line } from 'vue-chartjs';
import {
	Chart as ChartJS,
	CategoryScale,
	LinearScale,
	PointElement,
	LineElement,
	Tooltip,
	Legend,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend);

const props = defineProps({
	currentLecturer: {
		type: Object,
		default: () => ({
			name: 'Dosen',
			nip: '-',
			division: '-',
			email: '-',
		}),
	},
	hasProfile: {
		type: Boolean,
		default: true,
	},
	stats: {
		type: Array,
		default: () => [
			{ label: 'Seminar', value: '0', color: 'bg-[#7c72ff]', isTotal: false },
			{ label: 'Lokakarya', value: '0', color: 'bg-[#ff8b85]', isTotal: false },
			{ label: 'Workshop', value: '0', color: 'bg-[#56d4f8]', isTotal: false },
			{ label: 'Lainnya', value: '0', color: 'bg-[#ffbb66]', isTotal: false },
			{ label: 'Total Aktivitas', value: '0', color: 'bg-[#183669]', isTotal: true },
		],
	},
	years: {
		type: Array,
		default: () => ['2023', '2024', '2025', '2026'],
	},
	chartSeries: {
		type: Array,
		default: () => [
			{ name: 'Seminar', color: '#7c72ff', values: [0, 0, 0, 0] },
			{ name: 'Lokakarya', color: '#ff8b85', values: [0, 0, 0, 0] },
			{ name: 'Workshop', color: '#56d4f8', values: [0, 0, 0, 0] },
			{ name: 'Lainnya', color: '#ffbb66', values: [0, 0, 0, 0] },
		],
	},
	activities: {
		type: Array,
		default: () => [],
	},
});

const page = usePage();

const chartRef = ref(null);

const hideChartTooltip = () => {
	const chartInstance = chartRef.value?.chart;
	if (chartInstance && chartInstance.tooltip) {
		chartInstance.tooltip.setActiveElements([], { x: 0, y: 0 });
		chartInstance.setActiveElements([]);
		chartInstance.update();
	}
};

const handleDocumentTouchOrClick = (event) => {
	const canvas = chartRef.value?.chart?.canvas;
	if (!canvas) return;
	if (!canvas.contains(event.target)) {
		hideChartTooltip();
	}
};

const isLoading = ref(true);
onMounted(() => {
	setTimeout(() => {
		isLoading.value = false;
	}, 350);

	window.addEventListener('pointerdown', handleDocumentTouchOrClick, { passive: true });
	window.addEventListener('touchstart', handleDocumentTouchOrClick, { passive: true });
	window.addEventListener('click', handleDocumentTouchOrClick, { passive: true });
});

onUnmounted(() => {
	window.removeEventListener('pointerdown', handleDocumentTouchOrClick);
	window.removeEventListener('touchstart', handleDocumentTouchOrClick);
	window.removeEventListener('click', handleDocumentTouchOrClick);
});

const currentLecturer = computed(() => props.currentLecturer || { name: 'Dosen' });

const stats = computed(() => {
	return props.stats && props.stats.length ? props.stats : [
		{ label: 'Seminar', value: '0', color: 'bg-[#7c72ff]', isTotal: false },
		{ label: 'Lokakarya', value: '0', color: 'bg-[#ff8b85]', isTotal: false },
		{ label: 'Workshop', value: '0', color: 'bg-[#56d4f8]', isTotal: false },
		{ label: 'Lainnya', value: '0', color: 'bg-[#ffbb66]', isTotal: false },
		{ label: 'Total Aktivitas', value: '0', color: 'bg-[#183669]', isTotal: true },
	];
});

// Chart Data Setup
const chartData = computed(() => ({
	labels: props.years,
	datasets: (props.chartSeries || []).map((series) => ({
		label: series.name,
		data: series.values,
		borderColor: series.color,
		backgroundColor: series.color,
		pointRadius: 4.5,
		pointHoverRadius: 7,
		pointHitRadius: 14,
		pointBorderWidth: 2,
		pointBackgroundColor: '#ffffff',
		pointHoverBackgroundColor: series.color,
		pointHoverBorderColor: '#ffffff',
		pointHoverBorderWidth: 2.5,
		tension: 0.35,
		borderWidth: 2.5,
		fill: false,
	})),
}));

const chartOptions = {
	responsive: true,
	maintainAspectRatio: false,
	onClick: (event, elements, chart) => {
		if (!elements || elements.length === 0) {
			chart.tooltip.setActiveElements([], { x: 0, y: 0 });
			chart.setActiveElements([]);
			chart.update();
		}
	},
	interaction: {
		mode: 'nearest',
		intersect: true,
		axis: 'xy',
	},
	layout: {
		padding: {
			top: 10,
			right: 14,
			bottom: 4,
			left: 6,
		},
	},
	plugins: {
		legend: {
			position: 'bottom',
			labels: {
				usePointStyle: true,
				pointStyle: 'circle',
				boxWidth: 7,
				boxHeight: 7,
				padding: 18,
				color: '#64748b',
				font: {
					family: 'Inter',
					size: 12,
					weight: 500,
				},
			},
		},
		tooltip: {
			enabled: true,
			backgroundColor: '#183669',
			titleColor: '#ffffff',
			bodyColor: '#e2e8f0',
			borderColor: 'rgba(255, 255, 255, 0.15)',
			borderWidth: 1,
			padding: { top: 8, right: 12, bottom: 8, left: 12 },
			cornerRadius: 8,
			displayColors: true,
			boxWidth: 8,
			boxHeight: 8,
			usePointStyle: true,
			titleFont: { family: 'Poppins', size: 12, weight: '600' },
			bodyFont: { family: 'Inter', size: 11.5, weight: '500' },
			callbacks: {
				title: (items) => `Tahun ${items[0]?.label || ''}`,
				label: (item) => ` ${item.dataset.label}: ${item.raw} aktivitas`,
			},
		},
	},
	scales: {
		x: {
			grid: {
				color: '#f1f5f9',
				borderDash: [4, 4],
			},
			ticks: {
				color: '#64748b',
				font: {
					family: 'Inter',
					size: 12,
					weight: 500,
				},
			},
			border: {
				color: '#e2e8f0',
			},
		},
		y: {
			beginAtZero: true,
			min: 0,
			grid: {
				color: '#f1f5f9',
			},
			ticks: {
				stepSize: 1,
				precision: 0,
				color: '#64748b',
				font: {
					family: 'Inter',
					size: 11,
					weight: 500,
				},
			},
			border: {
				color: '#e2e8f0',
			},
		},
	},
};

// Recent Activities State (Max 5 items)
const activities = ref([]);

watch(
	() => props.activities,
	(newVal) => {
		activities.value = Array.isArray(newVal) ? [...newVal].slice(0, 5) : [];
	},
	{ immediate: true }
);

const columns = [
	{ key: 'name', label: 'Nama Aktivitas', sortable: true, cellAlign: 'left', width: 'w-[240px]' },
	{ key: 'category', label: 'Kategori', sortable: true, cellAlign: 'left', width: 'w-[140px]' },
	{ key: 'role', label: 'Peran', sortable: true, cellAlign: 'left', width: 'w-[130px]' },
	{ key: 'description', label: 'Deskripsi', sortable: true, cellAlign: 'left', width: 'w-[260px]' },
	{ key: 'dateSort', label: 'Tanggal', sortable: true, cellAlign: 'left', width: 'w-[160px]' },
	{ key: 'action', label: 'Aksi', sortable: false, cellAlign: 'center', width: 'w-[90px]' },
];

const sortKey = ref('id');
const sortDirection = ref('desc');

const toggleSort = (key) => {
	if (sortKey.value === key) {
		sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
	} else {
		sortKey.value = key;
		sortDirection.value = key === 'id' || key === 'dateSort' ? 'desc' : 'asc';
	}
};

const sortedActivities = computed(() => {
	return [...activities.value].sort((a, b) => {
		if (sortKey.value === 'id') {
			const idA = Number(a.id) || 0;
			const idB = Number(b.id) || 0;
			return sortDirection.value === 'asc' ? idA - idB : idB - idA;
		}

		let left = a[sortKey.value] ?? '';
		let right = b[sortKey.value] ?? '';

		if (sortKey.value === 'category') {
			left = Array.isArray(a.categories) && a.categories.length > 0 ? a.categories.join(', ') : (a.category || '');
			right = Array.isArray(b.categories) && b.categories.length > 0 ? b.categories.join(', ') : (b.category || '');
		}

		if (typeof left === 'number' && typeof right === 'number') {
			return sortDirection.value === 'asc' ? left - right : right - left;
		}

		left = left.toString().toLowerCase();
		right = right.toString().toLowerCase();

		if (left === right) return 0;

		if (sortDirection.value === 'asc') {
			return left > right ? 1 : -1;
		}

		return left < right ? 1 : -1;
	});
});

// Toast State
const toast = ref({
	show: false,
	type: 'success',
	title: '',
	message: '',
});

const showToast = (type, title, message) => {
	toast.value = {
		show: true,
		type,
		title,
		message,
	};
};

const closeToast = () => {
	toast.value.show = false;
};

// Check flash messages on page mount / update
watch(
	() => page.props.flash,
	(flash) => {
		if (flash?.success) {
			showToast('success', 'Berhasil', flash.success);
		} else if (flash?.error) {
			showToast('error', 'Gagal', flash.error);
		}
	},
	{ immediate: true, deep: true }
);

// Modal State & Handlers
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingActivity = ref(null);
const isSaving = ref(false);

const openEditModal = (activity) => {
	if (!props.hasProfile) {
		showToast('error', 'Profil Belum Tersedia', 'Profil publik belum dibuat oleh Administrator.');
		return;
	}
	isEditing.value = true;
	editingActivity.value = JSON.parse(JSON.stringify(activity));
	isModalOpen.value = true;
};

const handleModalSubmit = (formData) => {
	if (!props.hasProfile) {
		showToast('error', 'Profil Belum Tersedia', 'Profil publik belum dibuat oleh Administrator.');
		return;
	}
	isSaving.value = true;
	const data = new FormData();
	data.append('title', formData.title || formData.name);
	data.append('role', formData.role);
	data.append('description', formData.description);
	data.append('startDate', formData.startDate);
	if (formData.endDate) {
		data.append('endDate', formData.endDate);
	}
	if (formData.lecturerQuote && formData.lecturerQuote !== '-') {
		data.append('lecturerQuote', formData.lecturerQuote);
	}
	data.append('primaryImageIndex', formData.primaryImageIndex ?? 0);

	if (Array.isArray(formData.categories)) {
		formData.categories.forEach((cat, idx) => {
			data.append(`categories[${idx}]`, cat);
		});
	}

	if (Array.isArray(formData.images)) {
		let fileIndex = 0;
		let existingIndex = 0;
		formData.images.forEach((img) => {
			if (img instanceof File || img instanceof Blob) {
				data.append(`images[${fileIndex}]`, img);
				fileIndex++;
			} else if (typeof img === 'string') {
				data.append(`existingImages[${existingIndex}]`, img);
				existingIndex++;
			}
		});
	}

	if (isEditing.value && editingActivity.value?.id) {
		router.post(route('dosen.aktivitas.update', editingActivity.value.id), data, {
			forceFormData: true,
			onSuccess: () => {
				isSaving.value = false;
				isModalOpen.value = false;
				editingActivity.value = null;
				showToast('success', 'Berhasil Diperbarui', 'Data aktivitas berhasil diperbarui.');
			},
			onError: (err) => {
				isSaving.value = false;
				showToast('error', 'Gagal Memperbarui', Object.values(err)[0] || 'Terjadi kesalahan.');
			},
		});
	}
};

// Delete Confirmation Modal State
const isDeleteModalOpen = ref(false);
const deletingActivity = ref(null);
const isDeleting = ref(false);

const openDeleteModal = (activity) => {
	if (!props.hasProfile) return;
	deletingActivity.value = activity;
	isDeleteModalOpen.value = true;
};

const confirmDeleteActivity = () => {
	if (!props.hasProfile || !deletingActivity.value) return;
	const activity = deletingActivity.value;
	isDeleting.value = true;
	router.delete(route('dosen.aktivitas.destroy', activity.id), {
		preserveScroll: true,
		onSuccess: () => {
			isDeleteModalOpen.value = false;
			deletingActivity.value = null;
			isDeleting.value = false;
			showToast('success', 'Berhasil Dihapus', `Aktivitas "${activity.name || activity.title}" berhasil dihapus.`);
		},
		onError: (errors) => {
			isDeleting.value = false;
			const firstError = Object.values(errors)[0] || 'Gagal menghapus aktivitas.';
			showToast('error', 'Gagal Menghapus', firstError);
		},
	});
};
</script>

<template>
	<Head title="Dashboard Dosen" />

	<DosenLayout>
		<section class="mx-auto w-full max-w-[1520px] px-4 py-6 font-poppins sm:px-6 sm:py-8 lg:px-8">
			<div class="space-y-6">
				<!-- Header Title & Subtitle -->
				<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
					<div class="space-y-1.5">
						<h1 class="mt-1 text-[26px] font-bold leading-[1.2] tracking-[-0.03em] text-[#173a63] sm:text-[36px] sm:leading-tight lg:text-[42px]">
							<span class="block sm:inline">Selamat Datang,</span>
							<span class="block sm:inline sm:ml-2">{{ currentLecturer.name }} !</span>
						</h1>
						<p class="font-inter text-[13.5px] font-medium leading-normal text-[#4d6786] sm:text-[15px] sm:leading-tight">
							Pantau ringkasan statistik, perkembangan dan kelola seluruh aktivitas Anda
						</p>
					</div>
				</div>

				<!-- Stats Cards Grid (5 Cards - Responsive: 2 cols on mobile, 3 on tablet, 5 on desktop) -->
				<div class="grid grid-cols-2 gap-2.5 sm:gap-3.5 sm:grid-cols-3 lg:grid-cols-5">
					<article
						v-for="(stat, index) in stats"
						:key="stat.label"
						:class="[
							'flex flex-col justify-between rounded-[12px] p-3.5 sm:p-4 shadow-sm transition hover:shadow-md',
							stat.isTotal
								? 'bg-gradient-to-br from-[#183669] to-[#0f2447] text-white ring-1 ring-[#183669] shadow-md shadow-[#183669]/10'
								: 'bg-white ring-1 ring-[#d6e0ee]',
							index === 4 ? 'col-span-2 sm:col-span-1' : ''
						]"
					>
						<div class="flex items-center justify-between gap-2">
							<p
								:class="[
									'font-inter text-[12px] font-semibold sm:text-[13px] truncate',
									stat.isTotal ? 'text-blue-100' : 'text-[#6f84a3]'
								]"
							>
								{{ stat.label }}
							</p>
							<span
								v-if="!stat.isTotal"
								:class="['h-2 w-2 shrink-0 rounded-full sm:h-2.5 sm:w-2.5', stat.color]"
								aria-hidden="true"
							></span>
							<span
								v-else
								class="inline-flex items-center justify-center rounded-full bg-white/15 px-2 py-0.5 text-[10px] font-semibold text-blue-100 ring-1 ring-inset ring-white/20"
							>
								Semua
							</span>
						</div>
						<div class="mt-2 sm:mt-2.5">
							<div
								v-if="isLoading"
								:class="[
									'h-7 sm:h-8 w-14 rounded-md animate-pulse',
									stat.isTotal ? 'bg-white/20' : 'bg-slate-200'
								]"
							></div>
							<p
								v-else
								:class="[
									'text-[26px] font-bold leading-none sm:text-[32px]',
									stat.isTotal ? 'text-white' : 'text-[#173a63]'
								]"
							>
								{{ stat.value }}
							</p>
						</div>
					</article>
				</div>

				<!-- Chart Section -->
				<div class="rounded-[12px] bg-white px-4 py-5 shadow-sm ring-1 ring-[#d6e0ee] sm:px-6 sm:py-6">
					<div class="flex flex-wrap items-center justify-between gap-3">
						<div>
							<h2 class="text-[18px] font-bold text-[#173a63] sm:text-[20px]">Statistik Aktivitas Saya</h2>
							<p class="font-inter text-[12.5px] font-medium text-[#4d6786] sm:text-[13.5px]">Perkembangan aktivitas tahun ke tahun</p>
						</div>
						<div v-if="years && years.length" class="inline-flex items-center gap-1.5 rounded-full bg-[#f0f4f9] px-3 py-1 font-inter text-[11.5px] font-semibold text-[#183669] ring-1 ring-inset ring-[#d6e0ee]">
							<svg class="h-3.5 w-3.5 text-[#183669]/70" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253 18.75m3-18.75H3.75a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 3.75 21h16.5a2.25 2.25 0 0 0 2.25-2.25V5.25a2.25 2.25 0 0 0-2.25-2.25Z" />
							</svg>
							<span>{{ years[0] }} - {{ years[years.length - 1] }}</span>
						</div>
					</div>

					<div class="mt-4 rounded-[10px] border border-[#dbe4f0] bg-[#fafcff]/50 p-2 sm:p-4">
						<div v-if="isLoading" class="h-[250px] w-full sm:h-[300px] lg:h-[320px] animate-pulse flex flex-col justify-between p-4 bg-slate-50/50 rounded-lg">
							<div class="flex items-center justify-between">
								<div class="h-4 w-32 rounded bg-slate-200"></div>
								<div class="flex gap-4">
									<div class="h-3 w-16 rounded bg-slate-200"></div>
									<div class="h-3 w-16 rounded bg-slate-200"></div>
									<div class="h-3 w-16 rounded bg-slate-200"></div>
								</div>
							</div>
							<div class="space-y-4 my-auto">
								<div class="h-1.5 w-full rounded bg-slate-200/60"></div>
								<div class="h-1.5 w-full rounded bg-slate-200/60"></div>
								<div class="h-1.5 w-full rounded bg-slate-200/60"></div>
							</div>
							<div class="flex justify-between">
								<div v-for="y in 4" :key="y" class="h-3 w-12 rounded bg-slate-200"></div>
							</div>
						</div>
						<div v-else class="h-[250px] w-full sm:h-[300px] lg:h-[320px]">
							<Line ref="chartRef" :data="chartData" :options="chartOptions" aria-label="Grafik aktivitas saya" />
						</div>
					</div>
				</div>

				<!-- Recent Activities Table Section -->
				<div>
					<div class="mb-4 flex items-center justify-between gap-3">
						<h2 class="text-[20px] font-bold leading-none text-[#173a63]">Aktivitas Terbaru Saya</h2>
						<Link
							:href="route('dosen.aktivitas')"
							class="group inline-flex items-center gap-1.5 font-inter text-[14px] font-semibold text-[#183669] transition hover:text-[#122b54] hover:underline"
						>
							<span>Selengkapnya</span>
							<svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
							</svg>
						</Link>
					</div>

					<div class="overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
						<table class="w-full min-w-[1070px] table-fixed border-collapse text-sm">
							<thead class="bg-[#183669]">
							<tr class="h-[48px]">
								<th class="w-[50px] px-2 py-2.5 text-center font-poppins text-[13px] font-semibold text-white select-none border-r border-white/15 lg:border-r-0">
									<button
										type="button"
										@click="toggleSort('id')"
										class="group relative inline-flex items-center justify-center mx-auto transition-colors hover:text-white/80 focus:outline-none whitespace-nowrap"
										title="Urutkan No"
									>
										<span>No</span>
										<span class="absolute left-full ml-0.5 top-1/2 -translate-y-1/2 inline-flex shrink-0 items-center text-white/70 group-hover:text-white">
											<svg
												v-if="sortKey === 'id'"
												:class="[
													'h-3.5 w-3.5 text-white transition-transform duration-200',
													sortDirection === 'desc' ? 'rotate-180' : ''
												]"
												viewBox="0 0 20 20"
												fill="currentColor"
											>
												<path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.69l3.72-3.72a.75.75 0 111.06 1.06l-5 5a.75.75 0 01-1.06 0l-5-5a.75.75 0 111.06-1.06l3.72 3.72V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
											</svg>
											<svg
												v-else
												class="h-3.5 w-3.5 opacity-50 transition-opacity group-hover:opacity-100"
												viewBox="0 0 20 20"
												fill="currentColor"
											>
												<path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.69l3.72-3.72a.75.75 0 111.06 1.06l-5 5a.75.75 0 01-1.06 0l-5-5a.75.75 0 111.06-1.06l3.72 3.72V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
											</svg>
										</span>
									</button>
								</th>
								<th
									v-for="col in columns"
									:key="col.key"
									:class="[
										col.width,
										'px-2 py-2.5 text-center font-poppins text-[13px] font-semibold text-white select-none border-r border-white/15 last:border-r-0 lg:border-r-0'
									]"
								>
									<button
										v-if="col.sortable"
										type="button"
										@click="toggleSort(col.key)"
										class="group inline-flex items-center justify-center gap-1 mx-auto transition-colors hover:text-white/80 focus:outline-none whitespace-nowrap"
										:title="`Urutkan ${col.label}`"
									>
										<span>{{ col.label }}</span>
										<span class="inline-flex shrink-0 items-center text-white/70 group-hover:text-white">
											<svg
												v-if="sortKey === col.key"
												:class="[
													'h-3.5 w-3.5 text-white transition-transform duration-200',
													sortDirection === 'desc' ? 'rotate-180' : ''
												]"
												viewBox="0 0 20 20"
												fill="currentColor"
											>
												<path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.69l3.72-3.72a.75.75 0 111.06 1.06l-5 5a.75.75 0 01-1.06 0l-5-5a.75.75 0 111.06-1.06l3.72 3.72V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
											</svg>
											<svg
												v-else
												class="h-3.5 w-3.5 opacity-50 transition-opacity group-hover:opacity-100"
												viewBox="0 0 20 20"
												fill="currentColor"
											>
												<path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.69l3.72-3.72a.75.75 0 111.06 1.06l-5 5a.75.75 0 01-1.06 0l-5-5a.75.75 0 111.06-1.06l3.72 3.72V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
											</svg>
										</span>
									</button>
									<span v-else class="whitespace-nowrap">{{ col.label }}</span>
								</th>
							</tr>
						</thead>
							<tbody class="divide-y divide-[#d6e0ee] font-inter text-[14px] text-[#435b76]">
								<!-- Skeleton Loading Rows -->
								<template v-if="isLoading">
									<tr
										v-for="n in 5"
										:key="`skeleton-dosen-dash-${n}`"
										class="h-[52px] animate-pulse bg-white"
									>
										<td class="px-3 py-2.5 text-center">
											<div class="mx-auto h-4 w-5 rounded-md bg-slate-200"></div>
										</td>
										<td class="px-4 py-2.5">
											<div class="h-4 w-44 rounded-md bg-slate-200"></div>
										</td>
										<td class="px-3 py-2.5">
											<div class="h-5 w-20 rounded-full bg-slate-200"></div>
										</td>
										<td class="px-3 py-2.5">
											<div class="h-4 w-24 rounded-md bg-slate-200"></div>
										</td>
										<td class="px-4 py-2.5">
											<div class="h-4 w-56 rounded-md bg-slate-200"></div>
										</td>
										<td class="px-3 py-2.5">
											<div class="h-4 w-24 rounded-md bg-slate-200"></div>
										</td>
										<td class="px-3 py-2.5 text-center">
											<div class="flex items-center justify-center gap-2">
												<div class="h-7 w-7 rounded-lg bg-slate-200"></div>
												<div class="h-7 w-7 rounded-lg bg-slate-200"></div>
											</div>
										</td>
									</tr>
								</template>

								<!-- Real Data Rows -->
								<template v-else>
									<tr
										v-for="(activity, idx) in sortedActivities"
										:key="activity.id"
										class="h-[52px] transition-colors hover:bg-[#f7f9fd]"
									>
										<td class="px-3 py-2.5 text-center font-medium">{{ idx + 1 }}</td>
										<td class="px-4 py-2.5 text-left font-medium text-[#2f4b6e]" :title="activity.name || activity.title">
											<span class="block truncate">{{ activity.name || activity.title }}</span>
										</td>
										<td class="px-3 py-2.5 text-left" :title="Array.isArray(activity.categories) && activity.categories.length > 0 ? activity.categories.join(', ') : (activity.category || '-')">
											<span class="block truncate">{{ Array.isArray(activity.categories) && activity.categories.length > 0 ? activity.categories.join(', ') : (activity.category || '-') }}</span>
										</td>
										<td class="px-3 py-2.5 text-left" :title="activity.role">
											<span class="block truncate">{{ activity.role }}</span>
										</td>
										<td class="px-4 py-2.5 text-left" :title="activity.description ? activity.description.replace(/<[^>]*>/g, '') : ''">
											<span class="block truncate">{{ activity.description ? activity.description.replace(/<[^>]*>/g, '') : '' }}</span>
										</td>
										<td class="px-3 py-2.5 text-left" :title="activity.date || activity.publishDate">
											<span class="block truncate">{{ activity.date || activity.publishDate }}</span>
										</td>
										<td class="px-3 py-2.5 text-center">
											<div class="flex items-center justify-center gap-2">
												<EditButtonTable :label="`Edit ${activity.name || activity.title}`" @click="openEditModal(activity)" />
												<DeleteButtonTable :label="`Hapus ${activity.name || activity.title}`" @click="openDeleteModal(activity)" />
											</div>
										</td>
									</tr>
									<tr v-if="sortedActivities.length === 0">
										<td colspan="7" class="py-8 text-center text-[#7890a8]">
											Belum ada aktivitas yang ditambahkan.
										</td>
									</tr>
								</template>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>

		<!-- MODAL FORM AKTIVITAS SAYA (DOSEN COMPONENT) -->
		<ModalFormAktivitas
			:show="isModalOpen"
			:is-editing="isEditing"
			:initial-data="editingActivity"
			:lecturer-name="currentLecturer.name"
			:loading="isSaving"
			@close="isModalOpen = false"
			@submit="handleModalSubmit"
		/>

		<!-- MODAL DELETE CONFIRMATION -->
		<ModalDeleteConfirmation
			:show="isDeleteModalOpen"
			title="Hapus Aktivitas"
			:item-name="deletingActivity?.name || deletingActivity?.title"
			:loading="isDeleting"
			@close="isDeleteModalOpen = false"
			@confirm="confirmDeleteActivity"
		/>

		<!-- TOAST NOTIFICATION -->
		<ToastNotification
			:show="toast.show"
			:type="toast.type"
			:title="toast.title"
			:message="toast.message"
			@close="closeToast"
		/>
	</DosenLayout>
</template>
