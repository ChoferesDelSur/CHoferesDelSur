<script setup>
defineProps
  ({

  })

</script>

<template>
  <div>
    <a class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fas fa-home mr-2"></i> Inicio
    </a>
    <a class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.formarUni')">
      <i class="fa fa-list-alt" aria-hidden="true"></i> Formar Unidades
    </a>
    <a class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.unidades')">
      <i class="fa fa-bus" aria-hidden="true"></i> Unidades
    </a>
    <a class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.operadores')">
      <i class="fa fa-id-card" aria-hidden="true"></i> Operadores
    </a>
    <!-- <a class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fa fa-male" aria-hidden="true"></i> Despachadores
    </a> -->
    <a class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.rutas')">
      <i class="fa fa-map" aria-hidden="true"></i> Rutas
    </a>
    <a class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.sociosPrestadores')">
      <i class="fa fa-briefcase" aria-hidden="true"></i> Socios y Prestadores
    </a>
    <!-- <a class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fa fa-file-text" aria-hidden="true"></i> Reportes
    </a>
    <a class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fa fa-folder-open" aria-hidden="true"></i> Archivos
    </a> -->
  </div>
</template>