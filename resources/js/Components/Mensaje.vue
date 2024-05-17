<script setup>
import { ref, defineProps, onMounted } from 'vue';

const { message, color } = defineProps({
  message: String,
  color: String
});

const showMessage = ref(true);

// Ocultar el mensaje después de 5 segundos (5000 milisegundos)
onMounted(() => {
  setTimeout(() => {
    showMessage.value = false;
  }, 5000);
});

</script>

<template>
  <div v-if="$page.props.message" class="p-4 mb-4 rounded-lg"
    :class="`bg-${$page.props.color}-100 text-${$page.props.color}-700`">
    <span>{{ $page.props.message }}</span>
    <div v-if="showMessage"></div>
  </div>
</template>