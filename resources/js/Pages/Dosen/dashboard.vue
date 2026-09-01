<script setup>
import DosenLayout from '@/Layouts/DosenLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormAktivitas from '@/Components/dosen/ModalFormAktivitas.vue';
import ModalDeleteConfirmation from '@/Components/ModalDeleteConfirmation.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
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

const currentLecturer = {
	name: 'Dr. John Doe, M.Si',
	nip: 'J0403231075',
	division: 'Perencanaan Kehutanan',
	email: 'johndoe@apps.ipb.ac.id',
};

const stats = [
	{ label: 'Seminar', value: '5', detail: '2 sebagai pemateri', color: 'bg-[#7c72ff]' },
	{ label: 'Lokakarya', value: '4', detail: '1 skala nasional', color: 'bg-[#ff8b85]' },
	{ label: 'Workshop', value: '5', detail: '2 instansi luar', color: 'bg-[#56d4f8]' },
	{ label: 'Lainnya', value: '3', detail: 'Kegiatan penunjang', color: 'bg-[#ffbb66]' },
	{ label: 'Total Aktivitas', value: '17', detail: 'Total seluruh kegiatan', color: 'bg-[#183669]' },
];

const years = ['2023', '2024', '2025', '2026'];
const chartSeries = [
	{ name: 'Seminar', color: '#7c72ff', values: [2, 3, 4, 1] },
	{ name: 'Lokakarya', color: '#ff8b85', values: [1, 2, 1, 1] },
	{ name: 'Workshop', color: '#56d4f8', values: [1, 1, 3, 2] },
	{ name: 'Lainnya', color: '#ffbb66', values: [0, 1, 2, 1] },
];

const chartData = {
	labels: years,
	datasets: chartSeries.map((series) => ({
		label: series.name,
		data: series.values,
		borderColor: series.color,
		backgroundColor: series.color,
		pointRadius: 3,
		pointHoverRadius: 5,
		pointBorderWidth: 1.5,
		pointBackgroundColor: '#ffffff',
		tension: 0.2,
		borderWidth: 2,
		fill: false,
	})),
};

const chartOptions = {
	responsive: true,
	maintainAspectRatio: false,
	layout: {
		padding: {
			top: 8,
			right: 12,
			bottom: 8,
			left: 12,
		},
	},
	plugins: {
		legend: {
			position: 'bottom',
			labels: {
				usePointStyle: true,
				pointStyle: 'circle',
				boxWidth: 6,
				boxHeight: 6,
				padding: 16,
				color: '#6f84a3',
				font: {
					family: 'Inter',
					size: 12,
					weight: 500,
				},
			},
		},
		tooltip: {
			enabled: true,
		},
	},
	scales: {
		x: {
			grid: {
				color: '#e2e9f4',
				borderDash: [3, 4],
			},
			ticks: {
				color: '#7487a2',
				font: {
					family: 'Inter',
					size: 11,
				},
			},
			border: {
				color: '#d7e1ee',
			},
		},
		y: {
			min: 0,
			max: 6,
			grid: {
				color: '#eef2f8',
			},
			ticks: {
				stepSize: 1,
				color: '#7487a2',
				font: {
					family: 'Inter',
					size: 11,
				},
			},
			border: {
				color: '#d7e1ee',
			},
		},
	},
};

const initialActivities = [
	{
		id: 4,
		title: 'Lokakarya Desa Siman',
		name: 'Lokakarya Desa Siman',
		lecturer: 'Farhan Hakim',
		lecturerName: 'Farhan Hakim',
		description: 'Pendampingan pengelolaan UMKM desa berbasis digital.',
		role: 'Narasumber',
		startDate: '2026-01-21',
		endDate: '2026-01-21',
		categories: ['Lokakarya'],
		category: 'Lokakarya',
		date: '21 Januari 2026',
		dateSort: '2026-01-21',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Pelatihan ini sangat keren dan hebat, saya merasa berkembang setelah mengikuti kegiatan ini.',
	},
	{
		id: 3,
		title: 'Konsultasi Perencanaan Hutan Lestari',
		name: 'Konsultasi Perencanaan Hutan Lestari',
		lecturer: 'Farhan Hakim',
		lecturerName: 'Farhan Hakim',
		description: 'Penyusunan dokumen tata kelola wilayah hutan kemasyarakatan.',
		role: 'Konsultan Ahli',
		startDate: '2026-01-10',
		endDate: '2026-01-11',
		categories: ['Workshop'],
		category: 'Workshop',
		date: '10 Januari 2026',
		dateSort: '2026-01-10',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Kolaborasi yang baik antara akademisi dan pengelola hutan.',
	},
	{
		id: 2,
		title: 'Webinar Tata Kelola Kehutanan 4.0',
		name: 'Webinar Tata Kelola Kehutanan 4.0',
		lecturer: 'Farhan Hakim',
		lecturerName: 'Farhan Hakim',
		description: 'Strategi pemanfaatan drone dan GIS dalam inventarisasi hutan.',
		role: 'Pemateri',
		startDate: '2025-12-18',
		endDate: '2025-12-18',
		categories: ['Seminar'],
		category: 'Seminar',
		date: '18 Desember 2025',
		dateSort: '2025-12-18',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Teknologi geospasial memudahkan pemetaan wilayah tutupan lahan.',
	},
];

const activities = ref([...initialActivities]);

const columns = [
	{ key: 'name', label: 'Nama Aktivitas', sortable: true, cellAlign: 'left', width: 'w-[23%]' },
	{ key: 'category', label: 'Kategori', sortable: true, cellAlign: 'left', width: 'w-[14%]' },
	{ key: 'role', label: 'Peran', sortable: true, cellAlign: 'left', width: 'w-[13%]' },
	{ key: 'description', label: 'Deskripsi', sortable: true, cellAlign: 'left', width: 'w-[26%]' },
	{ key: 'dateSort', label: 'Tanggal', sortable: true, cellAlign: 'left', width: 'w-[14%]' },
	{ key: 'action', label: 'Aksi', sortable: false, cellAlign: 'center', width: 'w-[10%]' },
];

const sortKey = ref('id');
const sortDirection = ref('desc');

const toggleSort = (key) => {
	if (sortKey.value === key) {
		sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
	} else {
		sortKey.value = key;
		sortDirection.value = 'asc';
	}
};

const sortedActivities = computed(() => {
	return [...activities.value].sort((a, b) => {
		let left = a[sortKey.value] ?? '';
		let right = b[sortKey.value] ?? '';

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

// Modal State & Handlers
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingActivity = ref(null);

const openAddModal = () => {
	isEditing.value = false;
	editingActivity.value = null;
	isModalOpen.value = true;
};

const openEditModal = (activity) => {
	isEditing.value = true;
	editingActivity.value = JSON.parse(JSON.stringify(activity));
	isModalOpen.value = true;
};

const handleModalSubmit = (formData) => {
	if (isEditing.value && editingActivity.value) {
		const index = activities.value.findIndex((a) => a.id === editingActivity.value.id);
		if (index !== -1) {
			activities.value[index] = {
				...activities.value[index],
				...formData,
			};
		}
	} else {
		const maxId = activities.value.length ? Math.max(...activities.value.map((a) => Number(a.id) || 0)) : 0;
		activities.value.unshift({
			id: maxId + 1,
			...formData,
			lecturer: currentLecturer.name,
			lecturerName: currentLecturer.name,
			dateSort: new Date().toISOString().split('T')[0],
		});
		showToast('success', 'Berhasil Ditambahkan', 'Aktivitas baru berhasil disimpan.');
	}
};

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

// Delete Confirmation Modal State
const isDeleteModalOpen = ref(false);
const deletingActivity = ref(null);

const openDeleteModal = (activity) => {
	deletingActivity.value = activity;
	isDeleteModalOpen.value = true;
};

const confirmDeleteActivity = () => {
	if (!deletingActivity.value) return;
	const activity = deletingActivity.value;
	activities.value = activities.value.filter((a) => a.id !== activity.id);
	isDeleteModalOpen.value = false;
	deletingActivity.value = null;
	showToast('success', 'Berhasil Dihapus', `Aktivitas "${activity.name || activity.title}" berhasil dihapus.`);
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
						<h1 class="mt-1 text-[32px] font-bold leading-[1.02] tracking-[-0.03em] text-[#173a63] sm:text-[40px] lg:text-[44px]">
							Selamat Datang, {{ currentLecturer.name }} !
						</h1>
						<p class="font-inter text-[14px] font-medium leading-tight text-[#4d6786] sm:text-[16px]">
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
							'flex flex-col justify-between rounded-[12px] bg-white p-3.5 sm:p-4 shadow-sm ring-1 ring-[#d6e0ee] transition hover:shadow-md',
							index === 4 ? 'col-span-2 sm:col-span-1' : ''
						]"
					>
						<div class="flex items-center justify-between gap-2">
							<p class="font-inter text-[12px] font-semibold text-[#6f84a3] sm:text-[13px] truncate">{{ stat.label }}</p>
							<span :class="['h-2 w-2 shrink-0 rounded-full sm:h-2.5 sm:w-2.5', stat.color]" aria-hidden="true"></span>
						</div>
						<div class="my-1 sm:my-1.5">
							<p class="text-[26px] font-bold leading-none text-[#173a63] sm:text-[32px]">{{ stat.value }}</p>
						</div>
						<p class="font-inter text-[11px] font-medium leading-tight text-[#1b9d6f] sm:text-[12px] truncate">{{ stat.detail }}</p>
					</article>
				</div>

				<!-- Chart Section -->
				<div class="rounded-[12px] bg-white px-4 py-5 shadow-sm ring-1 ring-[#d6e0ee] sm:px-6 sm:py-6">
					<div class="flex flex-wrap items-start justify-between gap-3">
						<div>
							<h2 class="text-[19px] font-bold text-[#173a63]">Statistik Aktivitas Saya</h2>
							<p class="font-inter text-[13px] font-medium text-[#4d6786]">Perkembangan aktivitas tahun ke tahun</p>
						</div>
					</div>

					<div class="mt-4 rounded-[10px] border border-[#dbe4f0] p-2 sm:p-4">
						<div class="h-[240px] w-full sm:h-[300px] lg:h-[320px]">
							<Line :data="chartData" :options="chartOptions" aria-label="Grafik aktivitas saya" />
						</div>
					</div>
				</div>

				<!-- Recent Activities Table Section -->
				<div>
					<div class="mb-4 flex items-center justify-between gap-3">
						<h2 class="text-[20px] font-bold leading-none text-[#173a63]">Aktivitas Terbaru Saya</h2>
						<Link
							href="/dosen/aktivitas"
							class="group inline-flex items-center gap-1.5 font-inter text-[14px] font-semibold text-[#183669] transition hover:text-[#122b54] hover:underline"
						>
							<span>Selengkapnya</span>
							<svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
							</svg>
						</Link>
					</div>

					<div class="overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
						<table class="w-full min-w-[850px] table-fixed border-collapse text-sm">
							<thead class="bg-[#183669]">
								<tr class="h-[48px]">
									<th class="w-[60px] px-3 py-2.5 text-center font-poppins text-[13px] font-semibold text-white">No</th>
									<th
										v-for="col in columns"
										:key="col.key"
										:class="[
											col.width,
											'px-3 py-2.5 font-poppins text-[13px] font-semibold text-white select-none',
											col.align === 'center' || col.cellAlign === 'center' ? 'text-center' : 'text-left'
										]"
									>
										<button
											v-if="col.sortable"
											type="button"
											@click="toggleSort(col.key)"
											:class="[
												'group transition-colors hover:text-white/80 focus:outline-none',
												col.align === 'center' || col.cellAlign === 'center'
													? 'relative mx-auto inline-flex items-center justify-center'
													: 'inline-flex items-center gap-1.5 justify-start'
											]"
										>
											<span>{{ col.label }}</span>
											<span
												:class="[
													col.align === 'center' || col.cellAlign === 'center'
														? 'absolute left-full ml-1.5 inline-flex items-center text-white/70 group-hover:text-white'
														: 'inline-flex items-center text-white/70 group-hover:text-white'
												]"
											>
												<svg
													v-if="sortKey === col.key"
													:class="[
														'h-3.5 w-3.5 transition-transform duration-200',
														sortDirection === 'asc' ? 'rotate-180' : ''
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
										<span v-else>{{ col.label }}</span>
									</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-[#d6e0ee] font-inter text-[14px] text-[#435b76]">
								<tr
									v-for="(activity, idx) in sortedActivities"
									:key="activity.id"
									class="h-[52px] transition-colors hover:bg-[#f7f9fd]"
								>
									<td class="px-3 py-2.5 text-center font-medium">{{ idx + 1 }}</td>
									<td class="px-4 py-2.5 text-left font-medium text-[#2f4b6e]" :title="activity.name">
										<span class="block truncate">{{ activity.name }}</span>
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
									<td class="px-3 py-2.5 text-left" :title="activity.date">
										<span class="block truncate">{{ activity.date }}</span>
									</td>
									<td class="px-3 py-2.5 text-center">
										<div class="flex items-center justify-center gap-2">
											<EditButtonTable :label="`Edit ${activity.name}`" @click="openEditModal(activity)" />
											<DeleteButtonTable :label="`Hapus ${activity.name}`" @click="openDeleteModal(activity)" />
										</div>
									</td>
								</tr>
								<tr v-if="sortedActivities.length === 0">
									<td colspan="7" class="py-8 text-center text-[#7890a8]">
										Belum ada aktivitas yang ditambahkan.
									</td>
								</tr>
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
			@close="isModalOpen = false"
			@submit="handleModalSubmit"
		/>

		<!-- MODAL DELETE CONFIRMATION -->
		<ModalDeleteConfirmation
			:show="isDeleteModalOpen"
			title="Hapus Aktivitas"
			:item-name="deletingActivity?.name || deletingActivity?.title"
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
