<script setup>
defineProps
  ({

  })

</script>

<template>
  <div>
    <p class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-gray-200 hover:to-gray-200 hover:text-black"
      :href="route('principal.inicio')">
      <i class="fas fa-home mr-2"></i> Inicio
    </p>
    <p class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.formarUni')">
      <i class="fa fa-list-alt" aria-hidden="true"></i> Formar Unidades
    </p>
    <p class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-gray-200 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fa fa-briefcase" aria-hidden="true"></i> Socios y Prestadores
    </p>
    <p class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fa fa-bus" aria-hidden="true"></i> Unidades
    </p>
    <p class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fa fa-id-card" aria-hidden="true"></i> Operadores
    </p>
    <p class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fa fa-male" aria-hidden="true"></i> Despachadores
    </p>
    <p class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.rutas')">
      <i class="fa fa-map" aria-hidden="true"></i> Rutas
    </p>
    <p class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fa fa-file-text" aria-hidden="true"></i> Reportes
    </p>
    <p class="block text-white py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fa fa-folder-open" aria-hidden="true"></i> Archivos
    </p>
  </div>
</template>