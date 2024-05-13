<script setup>
//import { defineProps } from 'vue';
import { Head } from '@inertiajs/inertia-vue3';
import TopContent from '../Components/Principal/TopContent.vue';
import OpcionesNav from '../Components/Principal/OpcionesNav.vue';

const props = defineProps({
  title: String,
});

</script>


<template>
  <div class="flex flex-col h-screen bg-gray-100">

    <Head :title="title" />

    <TopContent /> <!--LLama al componente TopContent-->

    <div class="flex-1 flex flex-wrap">
      <!-- Barra lateral de navegación (oculta en dispositivos pequeños) -->
      <div class="p-2 bg-gray-800 w-full md:w-60 flex flex-col md:flex" id="sideNav"> <!--Color de la barra de menu-->
        <nav>
          <div class="w-60 h-16 justify-start items-center px-2 inline-flex">
            <div class="w-12 h-12 relative">
              <div class="w-12 h-12 left-0 top-0 absolute bg-zinc-300 rounded-full">
                <img class="w-12 h-12 left-0 top-0 absolute"
                  src="https://cdn-icons-png.flaticon.com/512/9069/9069049.png" />
              </div>
            </div>
          </div>
          <!-- Señalador de ubicación -->
          <div class="bg-gradient-to-r from-white to-white h-px mt-2"></div> <!-- Esto es una linea -->

          <OpcionesNav /> <!--LLama al componente OpcionesNav-->

        </nav>
        <!-- Señalador de ubicación -->
        <div class="bg-gradient-to-r from-white to-white h-px mt-2"></div> <!-- Esto es una linea -->
        <!-- Ítem de Cerrar Sesión -->
        <a
          class="block text-white py-2.5 px-4 my-2 rounded transition duration-200 hover:bg-gradient-to-r hover:from-red-500 hover:to-red-500 hover:text-white mt-auto">
          <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
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