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
		class="inline-flex h-[32px] w-[32px] shrink-0 items-center justify-center rounded-[8px] bg-[#ffd56a] text-[#f4a300] transition-colors hover:bg-[#ffcc54]"
		@click="$emit('click')"
	>
		<img src="/assets/icons/edit.svg" alt="" class="h-[18px] w-[18px] object-contain" aria-hidden="true" />
	</button>
</template>
