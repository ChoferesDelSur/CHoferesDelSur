<script setup>
import { ref} from 'vue';

const { message, color } = defineProps({
  message: String,
  color: String
});

const getIcon = (type) => {
  switch (type) {
    case 'success':
      return 'fa fa-circle-check';
    case 'error':
      return 'fas fa-times-circle';
    case 'info':
      return 'fas fa-info-circle';
    case 'warning':
      return 'fas fa-exclamation-circle';
    default:
      return '';
  }
};

const esVisible = ref(true);//Si se le pone true aparece el mensaje


const ocultarMensaje = () => {
  esVisible.value = false;
};

</script>

<template>
  <transition name="message-scale">
    <div v-if="esVisible" class="message-container"
      :class="`bg-${$page.props.color}-100 text-${$page.props.color}-700`"> <i :class="getIcon($page.props.type)"></i>
      {{ $page.props.message }}
      <button v-if="$page.props.message" @click="ocultarMensaje" class="ml-2">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </transition>
</template>

<style>
.message-container {
  position: relative;
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 0.5rem;
}

.message-container button {
  position: absolute;
  top: 0.4rem;
  /* Ajusta este valor según sea necesario */
  right: 0.6rem;
  /* Ajusta este valor según sea necesario */
}

.message-scale-enter-active,
.message-scale-leave-active {
  transition: transform 0.5s;
}

.message-scale-enter,
.message-scale-leave-to {
  transform: scale(0);
}
</style>