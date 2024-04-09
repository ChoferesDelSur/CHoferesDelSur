<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import TopContent from '@/Components/TopContent.vue';
import OpcionesNav from '@/Components/Principal/OpcionesNav.vue';


const props = defineProps({
  title: String,
  color: { String, default: '' },
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
  router.put(route('current-team.update'), {
    team_id: team.id,
  }, {
    preserveState: false,
  });
};

</script>

<template>
  <div class="flex flex-col h-screen bg-yellow-300">

    <Head :title="title" />
    <h3 class="font-bold text-center text-xl pt-5">Estoy en Principal Layout</h3>
    <div class="flex-1 flex flex-wrap">
      <!-- Barra lateral de navegación (oculta en dispositivos pequeños) -->
      <div class="p-2 bg-white w-full md:w-60 flex flex-col md:flex" id="sideNav">
        <nav>
          <div class="w-60 h-16 justify-start items-center px-2 inline-flex">
            <div class="w-12 h-12 relative">
            </div>
            <div class="flex-col justify-start items-center inline-flex">

            </div>
          </div>
          <!-- Señalador de ubicación -->
          <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mt-2"></div>

          <OpcionesNav />

        </nav>
        <!-- Señalador de ubicación -->
        <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mt-2"></div>
        <!-- Ítem de Cerrar Sesión -->

      </div>


    </div>
  </div>
</template>