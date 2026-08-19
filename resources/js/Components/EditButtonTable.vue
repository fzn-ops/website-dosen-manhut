<script setup>
defineEmits(['click']);

defineProps({
	label: {
		type: String,
		default: 'Edit data',
	},
});
</script>

<template>
	<button
		type="button"
		:aria-label="label"
		class="rounded-[10px] bg-[#ffd56a] p-2 text-[#f4a300] transition-colors hover:bg-[#ffcc54]"
		@click="$emit('click')"
	>
		<img src="/assets/icons/edit.svg" alt="" class="h-4 w-4 object-contain" aria-hidden="true" />
	</button>
</template>
