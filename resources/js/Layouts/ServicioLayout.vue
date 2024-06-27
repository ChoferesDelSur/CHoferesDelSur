<script setup>
//import { defineProps } from 'vue';
import { Head, router } from '@inertiajs/inertia-vue3';
import TopContentServ from '../Components/Servicio/TopContentServ.vue';
import OpcionesNavServ from '../Components/Servicio/OpcionesNavServ.vue';

const props = defineProps({
  title: String,
  usuario: { type: Object }
});

</script>


<template>
  <div class="flex flex-col h-screen bg-gray-100"><!-- Es lo que está de fondo fondo -->

    <Head :title="title" />

    <TopContentServ :usuario="props.usuario"/> <!--LLama al componente TopContent-->

    <div class="flex-1 flex overflow-hidden"><!-- el overflow-hidden: es el encargaddo de ocultar la barra de dezplazamiento 
    que afecta al menu y el encabezado -->

      <!-- Barra lateral de navegación (oculta en dispositivos pequeños) -->
      <div class="p-2 bg-gray-800 w-full md:w-60 flex flex-col" id="sideNav"> <!--Color de la barra de menu-->
        <nav>
          <div class="w-60 h-16 justify-start items-center px-2 inline-flex">
            <div class="w-12 h-12 relative">
              <div class="w-12 h-12 left-0 top-0 absolute bg-zinc-300 rounded-full">
                <img class="w-12 h-12 left-0 top-0 absolute"
                  src="https://cdn-icons-png.flaticon.com/512/9069/9069049.png" />
              </div>
            </div>
            <div class="flex-col justify-start items-center inline-flex">
              <div class="text-center text-white text-base font-semibold font-['DM Sans'] px-2">{{
                props.usuario.nombre }} {{ props.usuario.apellidoP }} {{ props.usuario.apellidoM }}
              </div>
              <div class="text-center text-white text-sm font-normal font-['DM Sans']"> {{
                props.usuario.usuario }}
              </div>
            </div>
          </div>
          <!-- Señalador de ubicación -->
          <div class="bg-gradient-to-r from-white to-white h-px mt-2"></div> <!-- Esto es una linea -->

          <OpcionesNavServ /> <!--LLama al componente OpcionesNav-->

        </nav>
        <!-- Señalador de ubicación -->
        <div class="bg-gradient-to-r from-white to-white h-px mt-2"></div> <!-- Esto es una linea -->
        <!-- Ítem de Cerrar Sesión -->
        <a class="logout-button block text-white py-2.5 px-4 my-2 rounded transition duration-200 hover:bg-gradient-to-r hover:from-red-500 hover:to-red-500 hover:text-white mt-auto"
          :href="route('cerrarSesion')">
          <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
        </a>
      </div>

      <!-- Área de contenido principal -->
      <div class="flex-1 p-4 overflow-y-auto">
        <!-- Page Content -->
        <main>
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>

<style>
.logout-button {
  border: 2px solid transparent;
  transition: background-color 0.3s, transform 0.3s, border-color 0.3s;
  position: relative;
  display: inline-block;
  overflow: hidden;
}

.logout-button:hover {
  background-color: #e60000;
  /* Color rojo */
  transform: scale(1.05);
  border-color: #e60000;
}

.logout-button i {
  transition: transform 0.3s;
}

.logout-button:hover i {
  transform: rotate(360deg);
}

/* Estilos CSS para el contenido principal */
main {
  /* Establece una altura máxima para el contenido principal */
  max-height: calc(100vh - 64px);
  /* Ajusta el valor según el tamaño de la barra de navegación superior */
}

/* Opcional: Estilos para el scroll si el contenido excede la altura del contenido principal */
main::-webkit-scrollbar {
  width: 8px;
}

main::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2);
  border-radius: 4px;
}

main::-webkit-scrollbar-track {
  background-color: rgba(0, 0, 0, 0.1);
}
</style>