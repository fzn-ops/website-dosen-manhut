<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormAktivitasDosen from '@/Components/admin/ModalFormAktivitasDosen.vue';
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

// Available Lecturer Profiles (disinkronkan dengan aktivitasdosen.vue & profiledosen.vue)
const availableProfiles = [
	{ id: 1, name: 'Farhan Hakim', division: 'Perencanaan Kehutanan' },
	{ id: 2, name: 'Fauzan Fuadiansyah', division: 'Perencanaan Kehutanan' },
	{ id: 3, name: 'Rintan Arufafa Aji', division: 'Pemanfaatan Sumberdaya Hutan' },
	{ id: 4, name: 'Muhammad Fauzan Fuadiansyah S.Kom., M.Cs.', division: 'Kebijakan Kehutanan' },
	{ id: 5, name: 'Dakota Johnson', division: 'Pemanfaatan Sumberdaya Hutan' },
	{ id: 6, name: 'Dr. Ir. Budi Rahardjo M.Sc.', division: 'Perencanaan Kehutanan' },
	{ id: 7, name: 'Prof. Dr. Sulistyo Handoko', division: 'Kebijakan Kehutanan' },
	{ id: 8, name: 'Siti Aminah S.Si., M.Kom.', division: 'Pemanfaatan Sumberdaya Hutan' },
	{ id: 9, name: 'Ahmad Dahlan S.T., M.Eng.', division: 'Perencanaan Kehutanan' },
	{ id: 10, name: 'Rian Hidayat S.Kom., M.T.', division: 'Perencanaan Kehutanan' },
	{ id: 11, name: 'Dewi Lestari M.Kom.', division: 'Pemanfaatan Sumberdaya Hutan' },
	{ id: 12, name: 'Hendra Setiawan Ph.D.', division: 'Kebijakan Kehutanan' },
];

const initialActivities = [
	{
		id: 5,
		title: 'Rapat Evaluasi Akademik',
		name: 'Rapat Evaluasi Akademik',
		lecturer: 'Prof. Dr. Sulistyo Handoko',
		lecturerName: 'Prof. Dr. Sulistyo Handoko',
		description: 'Evaluasi capaian semester ganjil dan rencana perbaikan kurikulum.',
		role: 'Peserta',
		startDate: '2026-01-22',
		endDate: '2026-01-22',
		categories: ['Lainnya'],
		category: 'Lainnya',
		date: '22 Januari 2026',
		dateSort: '2026-01-22',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Diskusi yang sangat produktif untuk kemajuan departemen.',
	},
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
		title: 'Seminar Kurikulum Merdeka',
		name: 'Seminar Kurikulum Merdeka',
		lecturer: 'Dr. Ir. Budi Rahardjo M.Sc.',
		lecturerName: 'Dr. Ir. Budi Rahardjo M.Sc.',
		description: 'Pemaparan strategi implementasi kurikulum adaptif di kampus.',
		role: 'Pemateri Utama',
		startDate: '2026-01-18',
		endDate: '2026-01-18',
		categories: ['Seminar'],
		category: 'Seminar',
		date: '18 Januari 2026',
		dateSort: '2026-01-18',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Antusiasme peserta sangat luar biasa dalam menyerap materi.',
	},
	{
		id: 2,
		title: 'Workshop Metodologi Riset',
		name: 'Workshop Metodologi Riset',
		lecturer: 'Siti Aminah S.Si., M.Kom.',
		lecturerName: 'Siti Aminah S.Si., M.Kom.',
		description: 'Pelatihan teknik sampling dan validasi instrumen penelitian.',
		role: 'Fasilitator',
		startDate: '2026-01-15',
		endDate: '2026-01-15',
		categories: ['Workshop'],
		category: 'Workshop',
		date: '15 Januari 2026',
		dateSort: '2026-01-15',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Semoga mahasiswa dapat mengaplikasikan metode penelitian dengan tepat.',
	},
	{
		id: 1,
		title: 'Pelatihan SPSS Dasar',
		name: 'Pelatihan SPSS Dasar',
		lecturer: 'Rian Hidayat S.Kom., M.T.',
		lecturerName: 'Rian Hidayat S.Kom., M.T.',
		description: 'Praktik olah data kuantitatif untuk tugas akhir mahasiswa.',
		role: 'Instruktur',
		startDate: '2026-01-12',
		endDate: '2026-01-12',
		categories: ['Workshop'],
		category: 'Workshop',
		date: '12 Januari 2026',
		dateSort: '2026-01-12',
		images: [],
		imagePreviews: [],
		lecturerQuote: 'Pemahaman statistik sangat penting dalam penyusunan tugas akhir.',
	},
];

const activities = ref([...initialActivities]);

const columns = [
	{ key: 'name', label: 'Nama Aktivitas', sortable: true, cellAlign: 'left', width: 'w-[24%]' },
	{ key: 'lecturer', label: 'Nama Dosen', sortable: true, cellAlign: 'left', width: 'w-[20%]' },
	{ key: 'category', label: 'Kategori', sortable: true, cellAlign: 'left', width: 'w-[16%]' },
	{ key: 'role', label: 'Peran', sortable: true, cellAlign: 'left', width: 'w-[15%]' },
	{ key: 'dateSort', label: 'Tanggal', sortable: true, cellAlign: 'left', width: 'w-[15%]' },
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

// Modal State & Handlers
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingActivity = ref(null);

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
	}
};

const deleteActivity = (activity) => {
	if (confirm(`Apakah Anda yakin ingin menghapus aktivitas "${activity.name || activity.title}"?`)) {
		activities.value = activities.value.filter((a) => a.id !== activity.id);
	}
};
</script>

<template>
	<Head title="Dashboard Admin" />

	<AdminLayout>
		<section class="mx-auto w-full max-w-[1520px] px-4 py-6 font-poppins sm:px-6 sm:py-8 lg:px-8">
			<div class="space-y-6">
				<div class="space-y-1.5">
					<h1 class="mt-1 text-[34px] font-bold leading-[1.02] tracking-[-0.03em] text-[#173a63] sm:text-[42px] lg:text-[48px]">Dashboard</h1>
					<p class="mt-1.5 font-inter text-[14px] font-medium leading-tight text-[#4d6786] sm:text-[16px]">Lihat ringkasan statistik dan aktivitas terbaru dosen</p>
				</div>

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
						<Link
							href="/aktivitas"
							class="group inline-flex items-center gap-1.5 font-inter text-[14px] font-semibold text-[#183669] transition hover:text-[#122b54] hover:underline"
						>
							<span>Selengkapnya</span>
							<svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
							</svg>
						</Link>
					</div>

					<div class="overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
						<table class="w-full min-w-[950px] table-fixed border-collapse text-sm">
							<thead class="bg-[#183669]">
								<tr class="h-[48px]">
									<th class="w-[60px] px-3 py-2.5 text-center font-poppins text-[13px] font-semibold text-white">No</th>
									<th
										v-for="col in columns"
										:key="col.key"
										:class="[
											col.width,
											'px-3 py-2.5 text-center font-poppins text-[13px] font-semibold text-white select-none'
										]"
									>
										<button
											v-if="col.sortable"
											type="button"
											@click="toggleSort(col.key)"
											class="group mx-auto inline-flex items-center justify-center gap-1.5 transition-colors hover:text-white/80 focus:outline-none"
										>
											<span>{{ col.label }}</span>
											<span class="inline-flex items-center text-white/70 group-hover:text-white">
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
										<span v-else class="block text-center">{{ col.label }}</span>
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
									<td class="px-4 py-2.5 text-left" :title="activity.lecturer">
										<span class="block truncate">{{ activity.lecturer }}</span>
									</td>
									<td class="px-4 py-2.5 text-left" :title="Array.isArray(activity.categories) && activity.categories.length > 0 ? activity.categories.join(', ') : (activity.category || '-')">
										<span class="block truncate">{{ Array.isArray(activity.categories) && activity.categories.length > 0 ? activity.categories.join(', ') : (activity.category || '-') }}</span>
									</td>
									<td class="px-3 py-2.5 text-left" :title="activity.role">
										<span class="block truncate">{{ activity.role }}</span>
									</td>
									<td class="px-3 py-2.5 text-center" :title="activity.date">
										<span class="block truncate">{{ activity.date }}</span>
									</td>
									<td class="px-3 py-2.5 text-center">
										<div class="flex items-center justify-center gap-2">
											<EditButtonTable :label="`Edit ${activity.name}`" @click="openEditModal(activity)" />
											<DeleteButtonTable :label="`Hapus ${activity.name}`" @click="deleteActivity(activity)" />
										</div>
									</td>
								</tr>
								<tr v-if="sortedActivities.length === 0">
									<td colspan="7" class="py-8 text-center text-[#7890a8]">
										Tidak ada data aktivitas yang tersedia.
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>

		<!-- MODAL FORM AKTIVITAS DOSEN (COMPONENT) -->
		<ModalFormAktivitasDosen
			:show="isModalOpen"
			:is-editing="isEditing"
			:initial-data="editingActivity"
			:available-profiles="availableProfiles"
			@close="isModalOpen = false"
			@submit="handleModalSubmit"
		/>
	</AdminLayout>
</template>
