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
		class="rounded-[10px] bg-[#ff9ca1] p-2 text-[#ff2f35] transition-colors hover:bg-[#ff888e]"
		@click="$emit('click')"
	>
		<img src="/assets/icons/delete.svg" alt="" class="h-4 w-4 object-contain" aria-hidden="true" />
	</button>
</template>
