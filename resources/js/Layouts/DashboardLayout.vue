<script setup>
import BarraLateral from '../Components/Principal/BarraLateral.vue';
import Encabezado from '../Components/Principal/Encabezado.vue';
</script>

<template>
  <div class="flex h-screen bg-gray-200 font-roboto">
    <BarraLateral />

    <div class="flex-1 flex flex-col overflow-hidden">
      <Encabezado />

      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
        <div class="container mx-auto px-6 py-8">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>
