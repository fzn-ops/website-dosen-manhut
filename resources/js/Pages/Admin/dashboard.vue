<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const stats = [
	{ label: 'Total Dosen', value: '48', detail: '+4 bulan ini', color: 'bg-[#3c5da1]' },
	{ label: 'Aktivitas Bulan Ini', value: '24', detail: '+12% dari bulan lalu', color: 'bg-[#2f8f83]' },
	{ label: 'Total Kegiatan', value: '126', detail: '18 menunggu laporan', color: 'bg-[#d28b36]' },
	{ label: 'Departemen', value: '6', detail: 'Aktif semester ini', color: 'bg-[#7a5aa6]' },
];

const activities = [
	{ number: '01', name: 'Lokakarya Desa Siman', lecturer: 'Prof. Sulana', role: 'Narasumber', date: '21 Januari 2026', status: 'Selesai' },
	{ number: '02', name: 'Seminar Hasil Penelitian', lecturer: 'Dr. Budi Santoso', role: 'Ketua Panitia', date: '18 Januari 2026', status: 'Berjalan' },
	{ number: '03', name: 'Pelatihan Pengolahan Data', lecturer: 'Ir. Siti Aminah', role: 'Pemateri', date: '15 Januari 2026', status: 'Selesai' },
	{ number: '04', name: 'Rapat Evaluasi Akademik', lecturer: 'Dr. Rian Hadi', role: 'Peserta', date: '10 Januari 2026', status: 'Selesai' },
];
</script>

<template>
	<Head title="Dashboard Admin" />

	<AuthenticatedLayout>
		<section class="mx-auto max-w-[1180px] px-5 py-8 sm:px-8">
			<div class="mb-7">
				<p class="mb-2 text-sm font-semibold uppercase tracking-[0.18em] text-[#3c5da1]">Ringkasan sistem</p>
				<h1 class="text-4xl font-bold tracking-tight text-[#173653]">Dashboard</h1>
				<p class="mt-2 text-sm text-slate-500">Lihat data dosen, aktivitas, dan perkembangan kegiatan akademik.</p>
			</div>

			<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
				<article v-for="stat in stats" :key="stat.label" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
					<div class="flex items-start justify-between gap-3">
						<p class="text-sm font-semibold text-slate-500">{{ stat.label }}</p>
						<span :class="['h-3 w-3 rounded-full', stat.color]" aria-hidden="true"></span>
					</div>
					<p class="mt-4 text-3xl font-bold text-[#173653]">{{ stat.value }}</p>
					<p class="mt-1 text-xs font-medium text-emerald-600">{{ stat.detail }}</p>
				</article>
			</div>

			<div class="mt-7 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-7">
				<div class="flex flex-wrap items-end justify-between gap-3">
					<div>
						<h2 class="text-lg font-bold text-[#173653]">Statistik Aktivitas Dosen</h2>
						<p class="text-xs text-slate-500">Jumlah aktivitas dalam empat tahun terakhir</p>
					</div>
					<span class="rounded-full bg-[#eef2f7] px-3 py-1 text-xs font-semibold text-[#3c5da1]">2023 - 2026</span>
				</div>
				<div class="mt-7 grid h-56 grid-cols-4 items-end gap-4 border-b border-l border-slate-200 px-3 pb-0 pt-5 sm:gap-10">
					<div v-for="(year, index) in ['2023', '2024', '2025', '2026']" :key="year" class="flex h-full flex-col items-center justify-end gap-3">
						<div class="flex h-full items-end gap-1.5 sm:gap-2">
							<span :style="{ height: `${[82, 58, 42, 18][index]}%` }" class="w-3 rounded-t bg-[#3c5da1] sm:w-5"></span>
							<span :style="{ height: `${[52, 42, 36, 28][index]}%` }" class="w-3 rounded-t bg-[#5cb9d5] sm:w-5"></span>
							<span :style="{ height: `${[30, 24, 46, 12][index]}%` }" class="w-3 rounded-t bg-[#f0a04b] sm:w-5"></span>
						</div>
						<span class="text-xs font-medium text-slate-500">{{ year }}</span>
					</div>
				</div>
				<div class="mt-5 flex flex-wrap justify-center gap-5 text-xs text-slate-500">
					<span><i class="mr-1 inline-block h-2 w-2 rounded-full bg-[#3c5da1]"></i>Seminar</span>
					<span><i class="mr-1 inline-block h-2 w-2 rounded-full bg-[#5cb9d5]"></i>Workshop</span>
					<span><i class="mr-1 inline-block h-2 w-2 rounded-full bg-[#f0a04b]"></i>Lainnya</span>
				</div>
			</div>

			<div class="mt-8">
				<div class="mb-4 flex items-center justify-between gap-3">
					<h2 class="text-lg font-bold text-[#173653]">Aktivitas Dosen Terbaru</h2>
					<span class="text-xs font-semibold text-[#3c5da1]">4 aktivitas</span>
				</div>
				<div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
					<table class="w-full min-w-[760px] text-left text-sm">
						<thead class="bg-[#1d3a7a] text-xs uppercase tracking-wide text-white">
							<tr><th class="px-5 py-4">No</th><th class="px-5 py-4">Nama Aktivitas</th><th class="px-5 py-4">Nama Dosen</th><th class="px-5 py-4">Peran</th><th class="px-5 py-4">Tanggal</th><th class="px-5 py-4">Status</th></tr>
						</thead>
						<tbody class="divide-y divide-slate-200 text-slate-600">
							<tr v-for="activity in activities" :key="activity.number" class="hover:bg-slate-50"><td class="px-5 py-4 font-semibold text-[#173653]">{{ activity.number }}</td><td class="px-5 py-4 font-medium text-[#173653]">{{ activity.name }}</td><td class="px-5 py-4">{{ activity.lecturer }}</td><td class="px-5 py-4">{{ activity.role }}</td><td class="whitespace-nowrap px-5 py-4">{{ activity.date }}</td><td class="px-5 py-4"><span :class="activity.status === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'" class="rounded-full px-3 py-1 text-xs font-semibold">{{ activity.status }}</span></td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</section>
	</AuthenticatedLayout>
</template>
