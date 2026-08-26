<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="text-[11px] md:text-xs font-semibold text-red-500 mt-1">
            {{ message }}
        </p>
    </div>
</template>