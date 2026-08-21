<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormProfileDosen from '@/Components/ModalFormProfileDosen.vue';

// Available Lecturers (from Daftar Dosen)
const availableLecturers = [
	{ id: 1, name: 'Farhan Hakim', nip: 'J0403231075', email: 'farhanhakim123@apps.ipb.ac.id', phone: '+62 812 1234 1234' },
	{ id: 2, name: 'Fauzan Fuadiansyah', nip: 'J0403231076', email: 'fauzanfuadiansyah@apps.ipb.ac.id', phone: '+62 812 1234 1234' },
	{ id: 3, name: 'Rintan Arufafa Aji', nip: 'J0403231113', email: 'contohajakaloyangpanjang@apps.ipb.ac.id', phone: '+62 812 1234 1234' },
	{ id: 4, name: 'Muhammad Fauzan Fuadiansyah S.Kom., M.Cs.', nip: 'J0403231077', email: '-', phone: '-' },
	{ id: 5, name: 'Dakota Johnson', nip: 'J0403231078', email: '-', phone: '-' },
	{ id: 6, name: 'Dr. Ir. Budi Rahardjo M.Sc.', nip: 'J0403231080', email: 'budi.rahardjo@apps.ipb.ac.id', phone: '+62 813 9876 5432' },
	{ id: 7, name: 'Prof. Dr. Sulistyo Handoko', nip: 'J0403231091', email: 'sulistyo.h@apps.ipb.ac.id', phone: '+62 812 8899 0011' },
	{ id: 8, name: 'Siti Aminah S.Si., M.Kom.', nip: 'J0403231044', email: 'siti_aminah@apps.ipb.ac.id', phone: '+62 856 7788 9900' },
	{ id: 9, name: 'Ahmad Dahlan S.T., M.Eng.', nip: 'J0403231032', email: 'a.dahlan@apps.ipb.ac.id', phone: '-' },
	{ id: 10, name: 'Rian Hidayat S.Kom., M.T.', nip: 'J0403231021', email: 'rian.hidayat@apps.ipb.ac.id', phone: '+62 817 6543 2109' },
	{ id: 11, name: 'Dewi Lestari M.Kom.', nip: 'J0403231015', email: 'dewi.lestari@apps.ipb.ac.id', phone: '+62 812 3344 5566' },
	{ id: 12, name: 'Hendra Setiawan Ph.D.', nip: 'J0403231055', email: 'hendra.s@apps.ipb.ac.id', phone: '-' },
	{ id: 13, name: 'Nurul Hidayati S.Pd., M.Pd.', nip: 'J0403231062', email: 'nurul.h@apps.ipb.ac.id', phone: '+62 819 0123 4567' },
	{ id: 14, name: 'Prof. Bambang Subagyo', nip: 'J0403231070', email: 'bambang.subagyo@apps.ipb.ac.id', phone: '+62 811 2233 4455' },
	{ id: 15, name: 'Andi Pratama S.Kom., M.M.', nip: 'J0403231088', email: '-', phone: '+62 815 6789 0123' },
	{ id: 16, name: 'Tri Wahyuni M.Sc.', nip: 'J0403231095', email: 'tri.wahyuni@apps.ipb.ac.id', phone: '-' },
	{ id: 17, name: 'Agus Susanto S.Si., M.Si.', nip: 'J0403231102', email: 'agus.susanto@apps.ipb.ac.id', phone: '+62 818 7654 3210' },
	{ id: 18, name: 'Dian Permatasari M.Kom.', nip: 'J0403231110', email: 'dian.permatasari@apps.ipb.ac.id', phone: '+62 813 4567 8901' },
	{ id: 19, name: 'Eko Prasetyo S.T., M.Kom.', nip: 'J0403231125', email: '-', phone: '-' },
	{ id: 20, name: 'Fitri Handayani M.Pd.', nip: 'J0403231130', email: 'fitri.handayani@apps.ipb.ac.id', phone: '+62 812 9012 3456' },
];

// Initial Profile Data with official 3 Divisions
const initialProfiles = [
	{
		id: 1,
		name: 'Farhan Hakim',
		division: 'Perencanaan Kehutanan',
		educations: [
			{ university: 'IPB University', major: 'Pendidikan Hutan', graduationYear: '2026' },
		],
		educationSummary: 'Pendidikan Hutan - IPB University - 2026',
		research: 'Kayu Jati Luhur',
		contact: 'farhanhakim123@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 2,
		name: 'Fauzan Fuadiansyah',
		division: 'Perencanaan Kehutanan',
		educations: [
			{ university: 'IPB University', major: 'Pendidikan Hutan', graduationYear: '2026' },
		],
		educationSummary: 'Pendidikan Hutan - IPB University - 2026',
		research: 'Kayu Jati Luhur',
		contact: 'fauzanfuadiansyah@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 3,
		name: 'Rintan Arufafa Aji',
		division: 'Pemanfaatan Sumberdaya Hutan',
		educations: [
			{ university: 'IPB University', major: 'Pendidikan Hutan', graduationYear: '2026' },
		],
		educationSummary: 'Pendidikan Hutan - IPB University - 2026',
		research: 'Kayu Jati Luhur',
		contact: 'contohajakaloyangpanjang@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 4,
		name: 'Muhammad Fauzan Fuadiansyah S.Kom., M.Cs.',
		division: 'Kebijakan Kehutanan',
		educations: [
			{ university: 'IPB University', major: 'Pendidikan Hutan', graduationYear: '2026' },
		],
		educationSummary: 'Pendidikan Hutan - IPB University - 2026',
		research: 'Kayu Jati Luhur',
		contact: 'fauzan.cs@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 5,
		name: 'Dakota Johnson',
		division: 'Pemanfaatan Sumberdaya Hutan',
		educations: [
			{ university: 'IPB University', major: 'Pendidikan Hutan', graduationYear: '2026' },
		],
		educationSummary: 'Pendidikan Hutan - IPB University - 2026',
		research: 'Kayu Jati Luhur',
		contact: 'dakota.j@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 6,
		name: 'Dr. Ir. Budi Rahardjo M.Sc.',
		division: 'Perencanaan Kehutanan',
		educations: [
			{ university: 'IPB University', major: 'Pendidikan Hutan', graduationYear: '2026' },
		],
		educationSummary: 'Pendidikan Hutan - IPB University - 2026',
		research: 'Kayu Jati Luhur',
		contact: 'budi.rahardjo@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 7,
		name: 'Prof. Dr. Sulistyo Handoko',
		division: 'Kebijakan Kehutanan',
		educations: [
			{ university: 'IPB University', major: 'Pendidikan Hutan', graduationYear: '2026' },
		],
		educationSummary: 'Pendidikan Hutan - IPB University - 2026',
		research: 'Kayu Jati Luhur',
		contact: 'sulistyo.h@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 8,
		name: 'Siti Aminah S.Si., M.Kom.',
		division: 'Pemanfaatan Sumberdaya Hutan',
		educations: [
			{ university: 'IPB University', major: 'Pendidikan Hutan', graduationYear: '2026' },
		],
		educationSummary: 'Pendidikan Hutan - IPB University - 2026',
		research: 'Kayu Jati Luhur',
		contact: 'siti_aminah@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 9,
		name: 'Ahmad Dahlan S.T., M.Eng.',
		division: 'Perencanaan Kehutanan',
		educations: [
			{ university: 'IPB University', major: 'Pendidikan Hutan', graduationYear: '2026' },
		],
		educationSummary: 'Pendidikan Hutan - IPB University - 2026',
		research: 'Kayu Jati Luhur',
		contact: 'a.dahlan@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 10,
		name: 'Rian Hidayat S.Kom., M.T.',
		division: 'Perencanaan Kehutanan',
		educations: [
			{ university: 'Universitas Gadjah Mada', major: 'Kehutanan Tropika', graduationYear: '2020' },
		],
		educationSummary: 'Kehutanan Tropika - Universitas Gadjah Mada - 2020',
		research: 'Perencanaan Hutan Lestari',
		contact: 'rian.hidayat@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 11,
		name: 'Dewi Lestari M.Kom.',
		division: 'Pemanfaatan Sumberdaya Hutan',
		educations: [
			{ university: 'IPB University', major: 'Silvikultur Tropika', graduationYear: '2022' },
		],
		educationSummary: 'Silvikultur Tropika - IPB University - 2022',
		research: 'Agroforestri Berkelanjutan',
		contact: 'dewi.lestari@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
	{
		id: 12,
		name: 'Hendra Setiawan Ph.D.',
		division: 'Kebijakan Kehutanan',
		educations: [
			{ university: 'Kyoto University', major: 'Forest Ecology', graduationYear: '2019' },
		],
		educationSummary: 'Forest Ecology - Kyoto University - 2019',
		research: 'Ekologi Satwa Liar',
		contact: 'hendra.s@apps.ipb.ac.id',
		scholarLink: 'https://scholar.google.com',
		linkedinLink: 'https://linkedin.com',
	},
];

const profiles = ref([...initialProfiles]);

// Search & Filter Query
const searchQuery = ref('');
const selectedDivisionFilter = ref('');
const isFilterOpen = ref(false);

const divisionFilterList = [
	'Semua Divisi',
	'Perencanaan Kehutanan',
	'Pemanfaatan Sumberdaya Hutan',
	'Kebijakan Kehutanan',
];

const setDivisionFilter = (div) => {
	selectedDivisionFilter.value = div === 'Semua Divisi' ? '' : div;
	isFilterOpen.value = false;
	currentPage.value = 1;
};

// Table Columns Config (Tanpa kolom Pendidikan)
const columns = [
	{ key: 'name', label: 'Nama Dosen', sortable: true, align: 'left', width: 'w-[26%]' },
	{ key: 'division', label: 'Divisi', sortable: true, align: 'left', width: 'w-[24%]' },
	{ key: 'research', label: 'Ketertarikan', sortable: true, align: 'left', width: 'w-[22%]' },
	{ key: 'contact', label: 'Kontak', sortable: true, align: 'left', width: 'w-[18%]' },
	{ key: 'action', label: 'Aksi', sortable: false, align: 'center', width: 'w-[10%]' },
];

const isRowsDropdownOpen = ref(false);

const sortKey = ref('name');
const sortDirection = ref('asc');

const toggleSort = (key) => {
	if (sortKey.value === key) {
		sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
	} else {
		sortKey.value = key;
		sortDirection.value = 'asc';
	}
};

// Filtered and Sorted Profiles
const filteredAndSortedProfiles = computed(() => {
	let list = [...profiles.value];

	// Filter by Division
	if (selectedDivisionFilter.value) {
		list = list.filter((p) => p.division === selectedDivisionFilter.value);
	}

	// Search Query
	if (searchQuery.value.trim()) {
		const q = searchQuery.value.toLowerCase().trim();
		list = list.filter(
			(p) =>
				p.name.toLowerCase().includes(q) ||
				p.division.toLowerCase().includes(q) ||
				(p.educationSummary && p.educationSummary.toLowerCase().includes(q)) ||
				p.research.toLowerCase().includes(q) ||
				p.contact.toLowerCase().includes(q)
		);
	}

	// Sorting
	if (sortKey.value) {
		list.sort((a, b) => {
			const valA = (a[sortKey.value] ?? '').toString().toLowerCase();
			const valB = (b[sortKey.value] ?? '').toString().toLowerCase();

			if (valA === valB) return 0;

			if (sortDirection.value === 'asc') {
				return valA > valB ? 1 : -1;
			}
			return valA < valB ? 1 : -1;
		});
	}

	return list;
});

// Pagination State & Controls
const currentPage = ref(1);
const rowsPerPage = ref(10);
const pageInput = ref(1);

const totalPages = computed(() => {
	const count = Math.ceil(filteredAndSortedProfiles.value.length / rowsPerPage.value);
	return count > 0 ? count : 1;
});

const paginatedProfiles = computed(() => {
	const start = (currentPage.value - 1) * rowsPerPage.value;
	return filteredAndSortedProfiles.value.slice(start, start + rowsPerPage.value);
});

// Sync input when page changes
watch(currentPage, (val) => {
	pageInput.value = val;
});

// Reset to page 1 on search, filter, or rowsPerPage change
watch([searchQuery, selectedDivisionFilter, rowsPerPage], () => {
	currentPage.value = 1;
	pageInput.value = 1;
});

const goToPage = (page) => {
	if (typeof page === 'number' && page >= 1 && page <= totalPages.value) {
		currentPage.value = page;
	}
};

const prevPage = () => {
	if (currentPage.value > 1) {
		currentPage.value--;
	}
};

const nextPage = () => {
	if (currentPage.value < totalPages.value) {
		currentPage.value++;
	}
};

const handlePageInput = () => {
	let p = parseInt(pageInput.value, 10);
	if (isNaN(p) || p < 1) p = 1;
	if (p > totalPages.value) p = totalPages.value;
	currentPage.value = p;
	pageInput.value = p;
};

// Visible page buttons logic
const visiblePages = computed(() => {
	const total = totalPages.value;
	const current = currentPage.value;

	if (total <= 7) {
		return Array.from({ length: total }, (_, i) => i + 1);
	}

	if (current <= 3) {
		return [1, 2, 3, '...', total - 2, total - 1, total];
	}

	if (current >= total - 2) {
		return [1, 2, 3, '...', total - 2, total - 1, total];
	}

	return [1, '...', current - 1, current, current + 1, '...', total];
});

// Modal State & Handlers
const isModalOpen = ref(false);
const isEditing = ref(false);
const selectedProfile = ref(null);
const editingId = ref(null);

const openCreateModal = () => {
	isEditing.value = false;
	selectedProfile.value = null;
	editingId.value = null;
	isModalOpen.value = true;
};

const openEditModal = (profile) => {
	isEditing.value = true;
	selectedProfile.value = profile;
	editingId.value = profile.id;
	isModalOpen.value = true;
};

const handleProfileSubmit = (formData) => {
	if (isEditing.value) {
		const index = profiles.value.findIndex((p) => p.id === editingId.value);
		if (index !== -1) {
			profiles.value[index] = {
				...profiles.value[index],
				...formData,
			};
		}
	} else {
		const newId = profiles.value.length ? Math.max(...profiles.value.map((p) => p.id)) + 1 : 1;
		profiles.value.unshift({
			id: newId,
			...formData,
		});
	}
};

const deleteProfile = (profile) => {
	if (confirm(`Apakah Anda yakin ingin menghapus profile dosen ${profile.name}?`)) {
		profiles.value = profiles.value.filter((p) => p.id !== profile.id);
	}
};
</script>

<template>
	<Head title="Profile Dosen" />

	<AuthenticatedLayout>
		<section class="mx-auto w-full max-w-[1520px] px-4 py-6 font-poppins sm:px-6 sm:py-8 lg:px-8">
			<div class="space-y-6">
				<!-- Header Title & Subtitle -->
				<div class="space-y-1.5">
					<h1 class="mt-1 text-[34px] font-bold leading-[1.02] tracking-[-0.03em] text-[#173a63] sm:text-[42px] lg:text-[48px]">
						Profile Dosen
					</h1>
					<p class="mt-1.5 font-inter text-[14px] font-medium leading-tight text-[#4d6786] sm:text-[16px]">
						Lihat data profile, perbarui, atau tambahkan profile dosen baru
					</p>
				</div>

				<!-- Action Bar (Search, Filter, Tambah Button) -->
				<div class="flex items-center gap-3">
					<!-- Search Input -->
					<div class="relative flex-1">
						<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
							<svg class="h-5 w-5 text-[#aeaeae]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
							</svg>	
						</div>
						<input
							v-model="searchQuery"
							type="text"
							placeholder="Cari Nama Dosen disini"
							class="h-[46px] w-full rounded-[10px] border-2 border-[#d6e0ee] bg-transparent pl-12 pr-4 font-inter text-[14px] text-[#173a63] placeholder-[#8ca1b9] transition-colors focus:border-[#183669] focus:outline-none focus:ring-0"
						/>
					</div>

					<!-- Filter Button with /assets/icons/filter.svg -->
					<div class="relative">
						<button
							type="button"
							@click="isFilterOpen = !isFilterOpen"
							class="flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-[10px] border-2 border-[#d6e0ee] bg-transparent text-[#183669] transition hover:border-[#183669] hover:bg-[#183669]/5 focus:border-[#183669] focus:outline-none active:border-[#183669]"
							title="Filter Divisi Dosen"
						>
							<img src="/assets/icons/filter.svg" alt="Filter Icon" class="h-5 w-5 object-contain" />
						</button>

						<!-- Filter Dropdown Menu -->
						<div
							v-if="isFilterOpen"
							class="absolute right-0 z-30 mt-2 w-64 rounded-[10px] border border-[#d6e0ee] bg-white p-2 shadow-xl"
						>
							<p class="px-3 py-1.5 font-poppins text-xs font-bold text-[#183669] border-b border-[#f0f4f9]">
								Filter Berdasarkan Divisi:
							</p>
							<div class="mt-1 space-y-1">
								<button
									v-for="div in divisionFilterList"
									:key="div"
									type="button"
									@click="setDivisionFilter(div)"
									:class="[
										'w-full rounded-[6px] px-3 py-1.5 text-left font-inter text-xs transition-colors',
										(selectedDivisionFilter === div || (!selectedDivisionFilter && div === 'Semua Divisi'))
											? 'bg-[#183669] font-bold text-white'
											: 'text-[#435b76] hover:bg-slate-100'
									]"
								>
									{{ div }}
								</button>
							</div>
						</div>
					</div>

					<!-- Tambah Button -->
					<button
						type="button"
						@click="openCreateModal"
						class="flex h-[46px] shrink-0 items-center justify-center rounded-[10px] bg-[#183669] px-7 font-poppins text-[15px] font-semibold text-white shadow-sm transition hover:bg-[#122b54]"
					>
						Tambah
					</button>
				</div>

				<!-- Table Section -->
				<div class="overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
					<table class="w-full min-w-[900px] table-fixed border-collapse text-sm">
						<thead class="bg-[#183669]">
							<tr class="h-[48px]">
								<th class="w-[50px] px-3 py-2.5 text-center font-poppins text-[13px] font-semibold text-white">No</th>
								<th
									v-for="col in columns"
									:key="col.key"
									:class="[
										col.width,
										'px-3 py-2.5 font-poppins text-[13px] font-semibold text-white select-none',
										col.align === 'center'
									]"
								>
									<button
										v-if="col.sortable"
										type="button"
										@click="toggleSort(col.key)"
										:class="[
											'group inline-flex items-center gap-1.5 transition-colors hover:text-white/80 focus:outline-none',
											col.align === 'center' ? 'mx-auto justify-center' : 'justify-start'
										]"
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
									<span v-else>{{ col.label }}</span>
								</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-[#d6e0ee] font-inter text-[14px] text-[#435b76]">
							<tr
								v-for="(profile, idx) in paginatedProfiles"
								:key="profile.id"
								class="h-[52px] transition-colors hover:bg-[#f7f9fd]"
							>
								<td class="px-3 py-2.5 text-center font-medium">{{ (currentPage - 1) * rowsPerPage + idx + 1 }}</td>
								<td class="px-3 py-2.5 text-left font-medium text-[#2f4b6e]" :title="profile.name">
									<span class="block truncate">{{ profile.name }}</span>
								</td>
								<td class="px-3 py-2.5 text-left" :title="profile.division">
									<span class="block truncate">{{ profile.division }}</span>
								</td>
								<td class="px-3 py-2.5 text-left" :title="profile.research">
									<span class="block truncate">{{ profile.research }}</span>
								</td>
								<td :class="['px-3 py-2.5', profile.contact && profile.contact !== '-' ? 'text-left' : 'text-center']" :title="profile.contact">
									<a
										v-if="profile.contact && profile.contact.includes('@')"
										:href="`mailto:${profile.contact}`"
										class="block truncate text-[#2a68c4] underline decoration-[#2a68c4] transition hover:text-[#1d4d96]"
									>
										{{ profile.contact }}
									</a>
									<span v-else-if="profile.contact && profile.contact !== '-'" class="block truncate">{{ profile.contact }}</span>
									<span v-else class="block truncate text-[#7890a8]">-</span>
								</td>
								<td class="px-3 py-2.5 text-center">
									<div class="flex items-center justify-center gap-2">
										<EditButtonTable :label="`Edit Profile ${profile.name}`" @click="openEditModal(profile)" />
										<DeleteButtonTable :label="`Hapus Profile ${profile.name}`" @click="deleteProfile(profile)" />
									</div>
								</td>
							</tr>
							<tr v-if="filteredAndSortedProfiles.length === 0">
								<td colspan="6" class="py-8 text-center text-[#7890a8]">
									Tidak ada data profile dosen yang sesuai filter atau pencarian.
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Pagination Section (Matching Image 1 with custom Rows per page Dropup) -->
				<div class="flex flex-col gap-4 py-2 font-inter text-[13px] text-[#4d6786] sm:flex-row sm:items-center sm:justify-between">
					<!-- Left: Page [ 1 ] of 10 | Rows per page [ 10 v ] -->
					<div class="flex items-center gap-2">
						<span>Page</span>
						<input
							type="number"
							min="1"
							:max="totalPages"
							v-model.number="pageInput"
							@keydown.enter="handlePageInput"
							@blur="handlePageInput"
							class="h-8 w-11 rounded-[6px] border border-[#d6e0ee] bg-white p-0 text-center font-inter text-[13px] font-medium text-[#173a63] focus:border-[#183669] focus:outline-none focus:ring-1 focus:ring-[#183669]"
						/>
						<span>of {{ totalPages }}</span>

						<span class="mx-1.5 text-[#cbd6e2]">|</span>

						<span>Rows per page</span>
						
						<!-- Custom Rows Per Page Dropup matching filter dropdown style -->
						<div class="relative" @click.stop>
							<button
								type="button"
								@click="isRowsDropdownOpen = !isRowsDropdownOpen"
								class="flex h-8 min-w-[56px] items-center justify-between gap-2 rounded-[6px] border border-[#d6e0ee] bg-white px-2.5 font-inter text-[13px] font-medium text-[#173a63] transition hover:border-[#183669] focus:border-[#183669] focus:outline-none"
								:class="{ 'border-[#183669] ring-1 ring-[#183669]/20': isRowsDropdownOpen }"
							>
								<span>{{ rowsPerPage }}</span>
								<svg
									:class="['h-3.5 w-3.5 text-[#8ca1b9] transition-transform duration-200', isRowsDropdownOpen ? 'rotate-180 text-[#183669]' : '']"
									fill="none"
									stroke="currentColor"
									stroke-width="2"
									viewBox="0 0 24 24"
								>
									<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
								</svg>
							</button>

							<!-- Dropup Popover -->
							<div
								v-if="isRowsDropdownOpen"
								class="absolute bottom-full left-0 z-30 mb-1.5 w-20 rounded-[8px] border border-[#d6e0ee] bg-white p-1 shadow-xl font-inter space-y-0.5"
							>
								<button
									v-for="r in [5, 10, 20, 50]"
									:key="r"
									type="button"
									@click="rowsPerPage = r; isRowsDropdownOpen = false"
									:class="[
										'w-full rounded-[4px] px-2 py-1 text-center text-xs transition-colors',
										rowsPerPage === r
											? 'bg-[#183669] font-bold text-white'
											: 'text-[#435b76] hover:bg-slate-100'
									]"
								>
									{{ r }}
								</button>
							</div>
						</div>
					</div>

					<!-- Right: << < 1 2 3 ... 8 9 10 > >> -->
					<div class="flex items-center gap-1">
						<!-- First Page (<<) -->
						<button
							type="button"
							@click="goToPage(1)"
							:disabled="currentPage === 1"
							class="flex h-8 w-8 items-center justify-center rounded-[6px] border border-[#d6e0ee] bg-white text-[#4d6786] transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
							aria-label="First Page"
						>
							<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
							</svg>
						</button>

						<!-- Prev Page (<) -->
						<button
							type="button"
							@click="prevPage"
							:disabled="currentPage === 1"
							class="flex h-8 w-8 items-center justify-center rounded-[6px] border border-[#d6e0ee] bg-white text-[#4d6786] transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
							aria-label="Previous Page"
						>
							<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
							</svg>
						</button>

						<!-- Page Numbers -->
						<template v-for="(p, index) in visiblePages" :key="index">
							<button
								v-if="p !== '...'"
								type="button"
								@click="goToPage(p)"
								:class="[
									'flex h-8 min-w-[32px] items-center justify-center rounded-[6px] px-1 text-[13px] transition-colors',
									p === currentPage
										? 'border border-[#cbd8e8] bg-[#f0f4f9] font-bold text-[#183669]'
										: 'border border-transparent text-[#4d6786] hover:bg-slate-100'
								]"
							>
								{{ p }}
							</button>
							<span v-else class="flex h-8 w-7 items-center justify-center text-[#8ca1b9]">...</span>
						</template>

						<!-- Next Page (>) -->
						<button
							type="button"
							@click="nextPage"
							:disabled="currentPage === totalPages"
							class="flex h-8 w-8 items-center justify-center rounded-[6px] border border-[#d6e0ee] bg-white text-[#4d6786] transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
							aria-label="Next Page"
						>
							<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
							</svg>
						</button>

						<!-- Last Page (>>) -->
						<button
							type="button"
							@click="goToPage(totalPages)"
							:disabled="currentPage === totalPages"
							class="flex h-8 w-8 items-center justify-center rounded-[6px] border border-[#d6e0ee] bg-white text-[#4d6786] transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
							aria-label="Last Page"
						>
							<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
							</svg>
						</button>
					</div>
				</div>
			</div>
		</section>

		<!-- MODAL FORM PROFILE DOSEN (COMPONENT)       -->
		<ModalFormProfileDosen
			:show="isModalOpen"
			:is-editing="isEditing"
			:initial-data="selectedProfile"
			:available-lecturers="availableLecturers"
			:existing-profiles="profiles"
			@close="isModalOpen = false"
			@submit="handleProfileSubmit"
		/>
	</AuthenticatedLayout>
</template>
