<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
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

const years = ['2023', '2024', '2025', '2026'];
const chartSeries = [
	{ name: 'Seminar', color: '#7c72ff', values: [100, 50, 30, 5] },
	{ name: 'Lokakarya', color: '#ff8b85', values: [20, 12, 4, 3] },
	{ name: 'Workshop', color: '#56d4f8', values: [40, 20, 21, 4] },
	{ name: 'Lainnya', color: '#ffbb66', values: [30, 24, 43, 4] },
];

const chartData = {
	labels: years,
	datasets: chartSeries.map((series) => ({
		label: series.name,
		data: series.values,
		borderColor: series.color,
		backgroundColor: series.color,
		pointRadius: 2.5,
		pointHoverRadius: 4,
		pointBorderWidth: 1.5,
		pointBackgroundColor: '#ffffff',
		tension: 0,
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
			max: 100,
			grid: {
				color: '#eef2f8',
			},
			ticks: {
				stepSize: 20,
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

const activities = [
	{ id: 1, name: 'Lokakarya Desa Siman', lecturer: 'Prof. Sulana', description: 'Pendampingan pengelolaan UMKM desa berbasis digital.', role: 'Narasumber', date: '21 Januari 2026', dateSort: '2026-01-21' },
	{ id: 2, name: 'Seminar Kurikulum Merdeka', lecturer: 'Dr. Budi Santoso', description: 'Pemaparan strategi implementasi kurikulum adaptif di kampus.', role: 'Pemateri Utama', date: '18 Januari 2026', dateSort: '2026-01-18' },
	{ id: 3, name: 'Workshop Metodologi Riset', lecturer: 'Ir. Siti Aminah', description: 'Pelatihan teknik sampling dan validasi instrumen penelitian.', role: 'Fasilitator', date: '15 Januari 2026', dateSort: '2026-01-15' },
	{ id: 4, name: 'Pelatihan SPSS Dasar', lecturer: 'Dr. Rian Hadi', description: 'Praktik olah data kuantitatif untuk tugas akhir mahasiswa.', role: 'Instruktur', date: '12 Januari 2026', dateSort: '2026-01-12' },
	{ id: 5, name: 'Rapat Evaluasi Akademik', lecturer: 'Prof. Dina Lestari', description: 'Evaluasi capaian semester ganjil dan rencana perbaikan.', role: 'Peserta', date: '10 Januari 2026', dateSort: '2026-01-10' },
];

const columns = [
	{ key: 'name', label: 'Nama Aktivitas', sortable: true },
	{ key: 'lecturer', label: 'Nama Dosen', sortable: true },
	{ key: 'description', label: 'Deskripsi', sortable: true },
	{ key: 'role', label: 'Peran', sortable: true },
	{ key: 'dateSort', label: 'Tanggal', sortable: true },
	{ key: 'action', label: 'Aksi', sortable: false },
];

const sortKey = ref('dateSort');
const sortDirection = ref('desc');

const setSort = (key, direction) => {
	sortKey.value = key;
	sortDirection.value = direction;
};

const sortedActivities = computed(() => {
	return [...activities].sort((a, b) => {
		const left = a[sortKey.value] ?? '';
		const right = b[sortKey.value] ?? '';

		if (left === right) return 0;

		if (sortDirection.value === 'asc') {
			return left > right ? 1 : -1;
		}

		return left < right ? 1 : -1;
	});
});
</script>

<template>
	<Head title="Dashboard Admin" />

	<AuthenticatedLayout>
		<section class="mx-auto w-full max-w-[1520px] px-4 py-6 font-poppins sm:px-6 sm:py-8 lg:px-8">
			<div class="space-y-6">
				<div class="space-y-1.5">
				<h1 class="mt-1 text-[34px] font-bold leading-[1.02] tracking-[-0.03em] text-[#173a63] sm:text-[42px] lg:text-[48px]">Dashboard</h1>
				<p class="mt-1.5 font-inter text-[14px] font-medium leading-tight text-[#4d6786] sm:text-[16px]">Lihat data mahasiswa, perbarui, atau tambahkan mahasiswa baru</p>
				</div>

			<!-- <div class="mb-6 mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
				<article v-for="stat in stats" :key="stat.label" class="rounded-[12px] bg-white p-4 shadow-sm ring-1 ring-[#d6e0ee]">
					<div class="flex items-start justify-between gap-3">
						<p class="font-inter text-[15px] font-semibold text-[#6f84a3]">{{ stat.label }}</p>
						<span :class="['h-3 w-3 rounded-full', stat.color]" aria-hidden="true"></span>
					</div>
					<p class="mt-2 text-[44px] font-bold leading-none text-[#173a63]">{{ stat.value }}</p>
					<p class="mt-1 font-inter text-[14px] font-medium text-[#1b9d6f]">{{ stat.detail }}</p>
				</article>
			</div> -->

			<div class="rounded-[12px] bg-white px-3 py-4 shadow-sm ring-1 ring-[#d6e0ee] sm:px-5 sm:py-5">
				<div class="flex flex-wrap items-start justify-between gap-3">
					<div class="w-full text-center">
						<h2 class="text-[20px] font-bold leading-none text-[#173a63]">Statistik Aktivitas Dosen</h2>
						<p class="font-inter text-[12px] font-medium text-[#4d6786]">Dalam kurun waktu 4 tahun kebelakang</p>
					</div>
				</div>

				<div class="mt-4 rounded-[10px] border border-[#dbe4f0] p-2 sm:p-4">
					<div class="h-[240px] w-full sm:h-[320px] lg:h-[360px]">
						<Line :data="chartData" :options="chartOptions" aria-label="Grafik aktivitas dosen 2023 sampai 2026" />
					</div>
				</div>
			</div>

			<div>
				<div class="mb-4 flex items-center justify-between gap-3">
					<h2 class="text-[20px] font-bold leading-none text-[#173a63]">Aktivitas Dosen Terbaru</h2>
					<!-- <span class="font-inter text-sm font-semibold text-[#3c5da1]">{{ sortedActivities.length }} aktivitas</span> -->
				</div>

				<div class="overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
					<table class="w-full min-w-[1080px] text-sm">
						<thead class="bg-[#244286] text-xs uppercase tracking-wide text-white">
							<tr>
								<th class="px-4 py-3 text-center">No</th>
								<th v-for="column in columns" :key="column.key" class="px-4 py-3 text-center">
									<div class="flex items-center justify-center gap-2">
										<span>{{ column.label }}</span>
										<div v-if="column.sortable" class="flex flex-col items-center leading-none">
											<button type="button" class="rounded p-0.5 transition-colors hover:bg-white/20" :class="sortKey === column.key && sortDirection === 'asc' ? 'bg-white/25' : ''" @click="setSort(column.key, 'asc')" aria-label="Urut naik">
												<svg class="h-2.5 w-2.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 5 4 13h12L10 5Z" /></svg>
											</button>
											<button type="button" class="rounded p-0.5 transition-colors hover:bg-white/20" :class="sortKey === column.key && sortDirection === 'desc' ? 'bg-white/25' : ''" @click="setSort(column.key, 'desc')" aria-label="Urut turun">
												<svg class="h-2.5 w-2.5" viewBox="0 0 20 20" fill="currentColor"><path d="m10 15 6-8H4l6 8Z" /></svg>
											</button>
										</div>
									</div>
								</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-[#d6e0ee] font-inter text-[15px] text-[#435b76]">
							<tr v-for="(activity, idx) in sortedActivities" :key="activity.id" class="h-[52px] hover:bg-[#f7f9fd]">
								<td class="px-4 py-3 text-center font-medium">{{ idx + 1 }}</td>
								<td class="px-4 py-3 text-left font-medium text-[#2f4b6e]">{{ activity.name }}</td>
								<td class="px-4 py-3 text-left">{{ activity.lecturer }}</td>
								<td class="px-4 py-3 text-left">{{ activity.description }}</td>
								<td class="px-4 py-3 text-left">{{ activity.role }}</td>
								<td class="whitespace-nowrap px-4 py-3 text-left">{{ activity.date }}</td>
								<td class="px-4 py-3 text-left">
									<div class="flex items-center justify-start gap-2">
										<EditButtonTable :label="`Edit ${activity.name}`" />
										<DeleteButtonTable :label="`Hapus ${activity.name}`" />
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			</div>
		</section>
	</AuthenticatedLayout>
</template>
