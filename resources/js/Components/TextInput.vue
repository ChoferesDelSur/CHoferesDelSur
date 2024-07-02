<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="rounded-md shadow-sm border border-gray-300 focus:border-indigo-500 px-2"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
