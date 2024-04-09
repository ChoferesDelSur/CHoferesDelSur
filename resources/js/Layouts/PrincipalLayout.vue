<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

import OpcionesNav from '@/Components/Principal/OpcionesNav.vue';
//import TopContent from '@/Components/Principal/TopContent.vue';

const props = defineProps({
  title: String,
});


</script>

<template>
  <div class="flex flex-col h-screen bg-gray-100">

    <Head :title="title" />

    <div class="flex-1 flex flex-wrap">
      <!-- Barra lateral de navegación (oculta en dispositivos pequeños) -->
      <div class="p-2 bg-white w-full md:w-60 flex flex-col md:flex" id="sideNav">
        <nav>
          <div class="w-60 h-16 justify-start items-center px-2 inline-flex">
            <div class="w-12 h-12 relative">
              <div class="w-12 h-12 left-0 top-0 absolute bg-zinc-300 rounded-full">
                <img class="w-12 h-12 left-0 top-0 absolute" :src="'Imagenes/ChoferesDelSurLogo.png'" />
              </div>
            </div>
            <div class="flex-col justify-start items-center inline-flex">
              <div class="text-center text-black text-base font-semibold font-\['DM Sans'\] px-2">Hola</div>
              <div class="text-center text-black text-base font-semibold font-\['DM Sans'\] px-2">Mundo</div>
            </div>
          </div>
          <!-- Señalador de ubicación -->
          <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mt-2"></div>

          <OpcionesNav />

        </nav>
        <!-- Señalador de ubicación -->
        <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mt-2"></div>
        <!-- Ítem de Cerrar Sesión -->
        <a
          class="block text-gray-500 py-2.5 px-4 my-2 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white mt-auto">
          <i class="fas fa-sign-out-alt mr-2"></i>Cerrar sesión
        </a>
      </div>

      <!-- Área de contenido principal -->
      <div class="flex-1 p-4 w-full md:w-1/2">
        <!-- Page Content -->
        <main>
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>