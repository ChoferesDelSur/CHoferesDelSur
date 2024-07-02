<script setup>
defineProps
  ({

  })

</script>

<template>
  <div class="flex flex-col">
    <a class="menu-button block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('servicio.inicio')"> <!-- El menu-button es para el efecto del menu -->
      <i class="fas fa-home mr-2"></i> Inicio
    </a>
    <a class="menu-button block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('servicio.inicio')">
      <i class="fa fa-list-alt" aria-hidden="true"></i> Formar Unidades
    </a>
    <a class="menu-button block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('servicio.inicio')">
      <i class="fa fa-bus" aria-hidden="true"></i> Unidades
    </a>
    <a class="menu-button block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('servicio.inicio')">
      <i class="fa fa-id-card" aria-hidden="true"></i> Operadores
    </a>
    <a class="menu-button block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('servicio.inicio')">
      <i class="fa fa-briefcase" aria-hidden="true"></i> Socios y Prestadores
    </a>
    <a class="menu-button block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('servicio.inicio')">
      <i class="fa fa-map" aria-hidden="true"></i> Rutas
    </a>
    <a class="menu-button block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('servicio.inicio')">
      <i class="fa fa-folder-open" aria-hidden="true"></i> Reportes
    </a>
    <a class="menu-button block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('servicio.perfil')">
      <i class="fa fa-user" aria-hidden="true"></i> Perfil
    </a>
  </div>
</template>

<style>
.menu-button {
  position: relative;
  display: inline-block;
  overflow: hidden;
  margin-bottom: 0rem;
}

.menu-button i {
  transition: transform 0.2s;
  /* Añadimos una transición suave al cambio de transformación */
}

.menu-button:hover i {
  transform: translateY(-5px);
  /* El ícono salta 3px hacia arriba al pasar el puntero */
}
</style>