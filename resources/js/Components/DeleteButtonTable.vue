<script setup>
defineEmits(['click']);

defineProps({
	label: {
		type: String,
		default: 'Hapus data',
	},
});
</script>

<template>
	<button
		type="button"
		:aria-label="label"
		class="inline-flex h-[32px] w-[32px] shrink-0 items-center justify-center rounded-[8px] bg-[#ff9ca1] text-[#ff2f35] transition-colors hover:bg-[#ff888e]"
		@click="$emit('click')"
	>
		<img src="/assets/icons/delete.svg" alt="" class="h-[18px] w-[18px] object-contain" aria-hidden="true" />
	</button>
</template>
