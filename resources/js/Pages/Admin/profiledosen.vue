<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import EditButtonTable from '@/Components/EditButtonTable.vue';
import DeleteButtonTable from '@/Components/DeleteButtonTable.vue';
import ModalFormProfileDosen from '@/Components/admin/ModalFormProfileDosen.vue';
import TablePagination from '@/Components/TablePagination.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import SearchBarTable from '@/Components/SearchBarTable.vue';
import ModalDeleteConfirmation from '@/Components/ModalDeleteConfirmation.vue';

const props = defineProps({
	profiles: {
		type: Array,
		default: () => [],
	},
	availableLecturers: {
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

// Watch for flash session messages from Laravel
watch(
	() => page.props.flash,
	(flash) => {
		if (flash?.success) {
			showToast('success', 'Berhasil', flash.success);
		} else if (flash?.error) {
			showToast('error', 'Gagal', flash.error);
		}
	},
	{ deep: true, immediate: true }
);

const lecturers = computed(() => props.availableLecturers || []);

const profiles = ref(props.profiles || []);

watch(
	() => props.profiles,
	(newProfiles) => {
		profiles.value = newProfiles || [];
	},
	{ deep: true }
);

const isLoading = ref(true);
onMounted(() => {
	setTimeout(() => {
		isLoading.value = false;
	}, 350);
});

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
	{ key: 'division', label: 'Divisi', sortable: true, align: 'left', width: 'w-[20%]' },
	{ key: 'research', label: 'Ketertarikan', sortable: true, align: 'left', width: 'w-[26%]' },
	{ key: 'contact', label: 'Kontak', sortable: true, align: 'left', width: 'w-[18%]' },
	{ key: 'action', label: 'Aksi', sortable: false, align: 'center', width: 'w-[10%]' },
];

const isRowsDropdownOpen = ref(false);

// Close all dropdowns when clicking outside
const closeAllDropdowns = () => {
	isFilterOpen.value = false;
	isRowsDropdownOpen.value = false;
};

onMounted(() => document.addEventListener('click', closeAllDropdowns));
onBeforeUnmount(() => document.removeEventListener('click', closeAllDropdowns));

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

// Filtered and Sorted Profiles
const filteredAndSortedProfiles = computed(() => {
	let list = [...profiles.value];

	// Filter by Division
	if (selectedDivisionFilter.value) {
		list = list.filter((p) => p.division === selectedDivisionFilter.value);
	}

	// Search Query (Nama Dosen - case-insensitive sesuai placeholder)
	if (searchQuery.value.trim()) {
		const q = searchQuery.value.toLowerCase().trim();
		list = list.filter((p) => p.name && p.name.toLowerCase().includes(q));
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

const totalPages = computed(() => {
	const count = Math.ceil(filteredAndSortedProfiles.value.length / rowsPerPage.value);
	return count > 0 ? count : 1;
});

const paginatedProfiles = computed(() => {
	const start = (currentPage.value - 1) * rowsPerPage.value;
	return filteredAndSortedProfiles.value.slice(start, start + rowsPerPage.value);
});

// Reset to page 1 on search, filter, or rowsPerPage change
watch([searchQuery, selectedDivisionFilter, rowsPerPage], () => {
	currentPage.value = 1;
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
	const payload = {
		division: formData.division,
		research: formData.research !== '-' ? formData.research : null,
		educations: formData.educations,
		image: formData.image,
		scholar_link: formData.scholar_link || (formData.scholarLink !== '-' ? formData.scholarLink : null),
		linkedin_link: formData.linkedin_link || (formData.linkedinLink !== '-' ? formData.linkedinLink : null),
	};

	if (isEditing.value && editingId.value) {
		if (formData.image instanceof File) {
			router.post(`/admin/profile-dosen/${editingId.value}`, {
				_method: 'PUT',
				...payload,
			}, {
				forceFormData: true,
				preserveScroll: true,
				onSuccess: () => {
					isModalOpen.value = false;
					showToast('success', 'Berhasil Diperbarui', 'Data profil dosen berhasil diperbarui.');
				},
				onError: (errors) => {
					const firstError = Object.values(errors)[0] || 'Terjadi kesalahan saat memperbarui profil.';
					showToast('error', 'Gagal Memperbarui', firstError);
				},
			});
		} else {
			router.put(`/admin/profile-dosen/${editingId.value}`, payload, {
				preserveScroll: true,
				onSuccess: () => {
					isModalOpen.value = false;
					showToast('success', 'Berhasil Diperbarui', 'Data profil dosen berhasil diperbarui.');
				},
				onError: (errors) => {
					const firstError = Object.values(errors)[0] || 'Terjadi kesalahan saat memperbarui profil.';
					showToast('error', 'Gagal Memperbarui', firstError);
				},
			});
		}
	} else {
		let userId = formData.user_id;
		if (!userId) {
			const found = lecturers.value.find(
				(l) => l.name.toLowerCase().trim() === formData.name.toLowerCase().trim()
			);
			userId = found?.id;
		}

		router.post('/admin/profile-dosen', {
			user_id: userId,
			...payload,
		}, {
			forceFormData: true,
			preserveScroll: true,
			onSuccess: () => {
				isModalOpen.value = false;
				showToast('success', 'Berhasil Ditambahkan', 'Data profil dosen baru berhasil ditambahkan.');
			},
			onError: (errors) => {
				const firstError = Object.values(errors)[0] || 'Terjadi kesalahan saat menambahkan profil.';
				showToast('error', 'Gagal Menambahkan', firstError);
			},
		});
	}
};

// Delete Confirmation Modal State
const isDeleteModalOpen = ref(false);
const deletingProfile = ref(null);
const isDeleting = ref(false);

const openDeleteModal = (profile) => {
	deletingProfile.value = profile;
	isDeleteModalOpen.value = true;
};

const confirmDeleteProfile = () => {
	if (!deletingProfile.value) return;
	const profile = deletingProfile.value;
	isDeleting.value = true;
	router.delete(`/admin/profile-dosen/${profile.id}`, {
		preserveScroll: true,
		onSuccess: () => {
			isDeleteModalOpen.value = false;
			deletingProfile.value = null;
			isDeleting.value = false;
			showToast('success', 'Berhasil Dihapus', `Data profil dosen "${profile.name}" berhasil dihapus.`);
		},
		onError: (errors) => {
			isDeleting.value = false;
			const firstError = Object.values(errors)[0] || 'Gagal menghapus profil dosen.';
			showToast('error', 'Gagal Menghapus', firstError);
		},
	});
};
</script>

<template>
	<Head title="Profile Dosen" />

	<AdminLayout>
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
					<!-- Search Input Component -->
					<SearchBarTable
						v-model="searchQuery"
						placeholder="Cari Nama Dosen disini"
					/>

					<!-- Filter Button with /assets/icons/filter.svg (Border-only hover) -->
					<div class="relative" @click.stop @keydown.escape="isFilterOpen = false">
						<button
							type="button"
							@click="isFilterOpen = !isFilterOpen"
							class="relative flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-[10px] border-2 bg-transparent text-[#183669] transition-colors focus:outline-none focus:ring-0 focus-visible:outline-none select-none"
							:class="isFilterOpen
								? 'border-[#183669]'
								: 'border-[#d6e0ee] hover:border-[#8ea9cb]'"
							title="Filter Divisi Dosen"
						>
							<img
								src="/assets/icons/filter.svg"
								alt="Filter Icon"
								class="h-5 w-5 object-contain"
							/>
							<!-- Red active indicator dot -->
							<span
								v-if="selectedDivisionFilter !== ''"
								class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-[#ef4444] ring-2 ring-[#eef2f7]"
							></span>
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

					<!-- Tambah Button (Icon + on Mobile, Text on Desktop) -->
					<button
						type="button"
						@click="(e) => { e.currentTarget?.blur(); openCreateModal(); }"
						class="flex h-[46px] w-[46px] sm:w-auto shrink-0 items-center justify-center rounded-[10px] bg-[#183669] px-0 sm:px-7 font-poppins text-[15px] font-semibold text-white shadow-sm transition hover:bg-[#122b54] active:scale-95 focus:outline-none focus:ring-0 focus-visible:outline-none select-none"
						title="Tambah Profile Dosen"
					>
						<svg class="h-5 w-5 sm:hidden" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
						</svg>
						<span class="hidden sm:inline">Tambah</span>
					</button>
				</div>

				<!-- Table Section -->
				<div class="overflow-x-auto rounded-[12px] bg-white shadow-sm ring-1 ring-[#d6e0ee]">
					<table class="w-full min-w-[900px] table-fixed border-collapse text-sm">
						<thead class="bg-[#183669]">
							<tr class="h-[48px]">
								<th class="w-[50px] px-3 py-2.5 text-center font-poppins text-[13px] font-semibold text-white border-r border-white/15 lg:border-r-0">No</th>
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
									v-for="n in 6"
									:key="`skeleton-profile-${n}`"
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
									<td class="px-3 py-2.5 text-center">
										<div class="mx-auto h-4 w-28 rounded-md bg-slate-200"></div>
									</td>
									<td class="px-3 py-2.5">
										<div class="h-4 w-32 rounded-md bg-slate-200"></div>
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
									<td class="px-3 py-2.5 text-center" :title="profile.research">
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
											<DeleteButtonTable :label="`Hapus Profile ${profile.name}`" @click="openDeleteModal(profile)" />
										</div>
									</td>
								</tr>
								<tr v-if="filteredAndSortedProfiles.length === 0">
									<td colspan="6" class="py-8 text-center text-[#7890a8]">
										Tidak ada data profile dosen yang sesuai filter atau pencarian.
									</td>
								</tr>
							</template>
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<TablePagination
					:current-page="currentPage"
					:total-pages="totalPages"
					:rows-per-page="rowsPerPage"
					@update:current-page="currentPage = $event"
					@update:rows-per-page="rowsPerPage = $event; currentPage = 1"
				/>
			</div>
		</section>

		<!-- MODAL FORM PROFILE DOSEN (COMPONENT)       -->
		<ModalFormProfileDosen
			:show="isModalOpen"
			:is-editing="isEditing"
			:initial-data="selectedProfile"
			:available-lecturers="lecturers"
			:existing-profiles="profiles"
			@close="isModalOpen = false"
			@submit="handleProfileSubmit"
		/>

		<!-- MODAL DELETE CONFIRMATION (COMPONENT)      -->
		<ModalDeleteConfirmation
			:show="isDeleteModalOpen"
			title="Hapus Profil Dosen"
			:item-name="deletingProfile?.name"
			:loading="isDeleting"
			@close="isDeleteModalOpen = false"
			@confirm="confirmDeleteProfile"
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
