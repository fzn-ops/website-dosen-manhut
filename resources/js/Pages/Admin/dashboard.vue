<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormAktivitasDosen from '@/Components/admin/ModalFormAktivitasDosen.vue';
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

const props = defineProps({
	activities: {
		type: Array,
		default: () => [],
	},
	availableProfiles: {
		type: Array,
		default: () => [],
	},
	years: {
		type: Array,
		default: () => ['2023', '2024', '2025', '2026'],
	},
	chartSeries: {
		type: Array,
		default: () => [
			{ name: 'Seminar', color: '#7c72ff', values: [100, 50, 30, 5] },
			{ name: 'Lokakarya', color: '#ff8b85', values: [20, 12, 4, 3] },
			{ name: 'Workshop', color: '#56d4f8', values: [40, 20, 21, 4] },
			{ name: 'Lainnya', color: '#ffbb66', values: [30, 24, 43, 4] },
		],
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

// Check flash messages on page mount
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

const isLoading = ref(true);
onMounted(() => {
	setTimeout(() => {
		isLoading.value = false;
	}, 350);
});

// Chart Data Setup
const chartData = computed(() => ({
	labels: props.years,
	datasets: props.chartSeries.map((series) => ({
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
}));

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

// Activities State (Top 5 items)
const activities = ref([]);

watch(
	() => props.activities,
	(newVal) => {
		activities.value = Array.isArray(newVal) ? [...newVal].slice(0, 5) : [];
	},
	{ immediate: true }
);

// Table Columns Config matching aktivitasdosen.vue
const columns = [
	{ key: 'name', label: 'Nama Aktivitas', sortable: true, align: 'left', width: 'w-[22%]' },
	{ key: 'lecturer', label: 'Nama Dosen', sortable: true, align: 'left', width: 'w-[18%]' },
	{ key: 'category', label: 'Kategori', sortable: true, align: 'left', width: 'w-[15%]' },
	{ key: 'role', label: 'Peran', sortable: true, align: 'left', width: 'w-[14%]' },
	{ key: 'dateSort', label: 'Tanggal Publish', sortable: true, align: 'center', width: 'w-[18%]' },
	{ key: 'action', label: 'Aksi', sortable: false, align: 'center', width: 'w-[13%]' },
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
	const data = new FormData();
	if (formData.user_id) data.append('user_id', formData.user_id);
	data.append('lecturerName', formData.lecturerName || formData.lecturer);
	data.append('title', formData.title || formData.name);
	data.append('role', formData.role);
	data.append('description', formData.description);
	data.append('startDate', formData.startDate);
	if (formData.endDate) data.append('endDate', formData.endDate);
	if (formData.lecturerQuote && formData.lecturerQuote !== '-') data.append('lecturerQuote', formData.lecturerQuote);
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
		router.post(route('admin.aktivitasdosen.update', editingActivity.value.id), data, {
			forceFormData: true,
			onSuccess: () => {
				isModalOpen.value = false;
				showToast('success', 'Berhasil Diperbarui', 'Data aktivitas dosen berhasil diperbarui.');
			},
			onError: (err) => {
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
	deletingActivity.value = activity;
	isDeleteModalOpen.value = true;
};

const confirmDeleteActivity = () => {
	if (!deletingActivity.value) return;
	const activity = deletingActivity.value;
	isDeleting.value = true;
	router.delete(route('admin.aktivitasdosen.destroy', activity.id), {
		preserveScroll: true,
		onSuccess: () => {
			isDeleteModalOpen.value = false;
			deletingActivity.value = null;
			isDeleting.value = false;
			showToast('success', 'Berhasil Dihapus', 'Aktivitas dosen berhasil dihapus.');
		},
		onError: (err) => {
			isDeleting.value = false;
			showToast('error', 'Gagal Menghapus', Object.values(err)[0] || 'Terjadi kesalahan saat menghapus data.');
		},
	});
};
</script>

<template>
	<Head title="Dashboard Admin" />

	<AdminLayout>
		<section class="mx-auto w-full max-w-[1520px] px-4 py-6 font-poppins sm:px-6 sm:py-8 lg:px-8">
			<div class="space-y-6">
				<!-- Header Section -->
				<div class="space-y-1.5">
					<h1 class="mt-1 text-[34px] font-bold leading-[1.02] tracking-[-0.03em] text-[#173a63] sm:text-[42px] lg:text-[48px]">Dashboard</h1>
					<p class="mt-1.5 font-inter text-[14px] font-medium leading-tight text-[#4d6786] sm:text-[16px]">Lihat ringkasan statistik dan aktivitas terbaru dosen</p>
				</div>

				<!-- Chart Section -->
				<div class="rounded-[12px] bg-white px-3 py-4 shadow-sm ring-1 ring-[#d6e0ee] sm:px-5 sm:py-5">
					<div class="flex flex-wrap items-start justify-between gap-3">
						<div class="w-full text-center">
							<h2 class="text-[20px] font-bold leading-none text-[#173a63]">Statistik Aktivitas Dosen</h2>
							<p class="font-inter text-[12px] font-medium text-[#4d6786]">Dalam kurun waktu 4 tahun kebelakang</p>
						</div>
					</div>

					<div class="mt-4 rounded-[10px] border border-[#dbe4f0] p-2 sm:p-4">
						<div v-if="isLoading" class="h-[240px] w-full sm:h-[320px] lg:h-[360px] animate-pulse flex flex-col justify-between p-4 bg-slate-50/50 rounded-lg">
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
						<div v-else class="h-[240px] w-full sm:h-[320px] lg:h-[360px]">
							<Line :data="chartData" :options="chartOptions" aria-label="Grafik aktivitas dosen" />
						</div>
					</div>
				</div>

				<!-- Recent Activities Section (Top 5) -->
				<div>
					<div class="mb-4 flex items-center justify-between gap-3">
						<h2 class="text-[20px] font-bold leading-none text-[#173a63]">Aktivitas Dosen Terbaru</h2>
						<Link
							:href="route('admin.aktivitasdosen')"
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
									<th class="w-[60px] px-3 py-2.5 text-center font-poppins text-[13px] font-semibold text-white border-r border-white/15 lg:border-r-0">No</th>
									<th
										v-for="col in columns"
										:key="col.key"
										:class="[
											col.width,
											'px-3 py-2.5 font-poppins text-[13px] font-semibold text-white select-none border-r border-white/15 last:border-r-0 lg:border-r-0',
											col.align === 'center'
										]"
									>
										<button
											v-if="col.sortable"
											type="button"
											@click="toggleSort(col.key)"
											:class="[
												'group transition-colors hover:text-white/80 focus:outline-none max-w-full',
												col.align === 'center'
													? 'mx-auto flex items-center justify-center'
													: 'inline-flex items-center gap-1.5 justify-start'
											]"
										>
											<!-- Balanced spacer for center-aligned columns so text is optically centered and arrow never overflows cell -->
											<span
												v-if="col.align === 'center'"
												class="h-3.5 w-3.5 shrink-0 opacity-0 pointer-events-none mr-1.5"
												aria-hidden="true"
											></span>
											<span class="truncate">{{ col.label }}</span>
											<span class="inline-flex shrink-0 items-center ml-1.5 text-white/70 group-hover:text-white">
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
										<span v-else class="block truncate">{{ col.label }}</span>
									</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-[#d6e0ee] font-inter text-[14px] text-[#435b76]">
								<!-- Skeleton Loading Rows -->
								<template v-if="isLoading">
									<tr
										v-for="n in 5"
										:key="`skeleton-dash-${n}`"
										class="h-[52px] animate-pulse bg-white"
									>
										<td class="px-3 py-2.5 text-center">
											<div class="mx-auto h-4 w-5 rounded-md bg-slate-200"></div>
										</td>
										<td class="px-3 py-2.5">
											<div class="h-4 w-44 rounded-md bg-slate-200"></div>
										</td>
										<td class="px-3 py-2.5">
											<div class="h-4 w-36 rounded-md bg-slate-200"></div>
										</td>
										<td class="px-3 py-2.5">
											<div class="h-5 w-20 rounded-full bg-slate-200"></div>
										</td>
										<td class="px-3 py-2.5">
											<div class="h-4 w-24 rounded-md bg-slate-200"></div>
										</td>
										<td class="px-3 py-2.5 text-center">
											<div class="mx-auto h-4 w-24 rounded-md bg-slate-200"></div>
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
										<td class="px-3 py-2.5 text-left font-medium text-[#2f4b6e]" :title="activity.name || activity.title">
											<span class="block truncate">{{ activity.name || activity.title }}</span>
										</td>
										<td class="px-3 py-2.5 text-left" :title="activity.lecturer || activity.lecturerName">
											<span class="block truncate">{{ activity.lecturer || activity.lecturerName }}</span>
										</td>
										<td class="px-3 py-2.5 text-left" :title="Array.isArray(activity.categories) && activity.categories.length > 0 ? activity.categories.join(', ') : (activity.category || '-')">
											<span class="block truncate">{{ Array.isArray(activity.categories) && activity.categories.length > 0 ? activity.categories.join(', ') : (activity.category || '-') }}</span>
										</td>
										<td class="px-3 py-2.5 text-left" :title="activity.role">
											<span class="block truncate">{{ activity.role }}</span>
										</td>
										<td class="px-3 py-2.5 text-center" :title="activity.publishDate || activity.date">
											<span class="block truncate">{{ activity.publishDate || activity.date }}</span>
										</td>
										<td class="px-3 py-2.5 text-center">
											<div class="flex items-center justify-center gap-2">
												<EditButtonTable :label="`Edit Aktivitas ${activity.name || activity.title}`" @click="openEditModal(activity)" />
												<DeleteButtonTable :label="`Hapus Aktivitas ${activity.name || activity.title}`" @click="openDeleteModal(activity)" />
											</div>
										</td>
									</tr>
									<tr v-if="sortedActivities.length === 0">
										<td colspan="7" class="py-8 text-center text-[#7890a8]">
											Belum ada data aktivitas dosen.
										</td>
									</tr>
								</template>
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

		<!-- MODAL DELETE CONFIRMATION (COMPONENT)  -->
		<ModalDeleteConfirmation
			:show="isDeleteModalOpen"
			title="Hapus Aktivitas Dosen"
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
	</AdminLayout>
</template>
