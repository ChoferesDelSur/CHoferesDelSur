<script>

</script>

<template>
  <div>
    <p class="block text-black py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.inicio')">
      <i class="fas fa-home mr-2"></i> Inicio
    </p>
    <p class="block text-black py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white"
      :href="route('principal.formarUnidades')">
      <i class="fa fa-list-alt" aria-hidden="true"></i> Formar Unidades
    </p>
    <p
      class="block text-black py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white">
      <i class="fa fa-briefcase" aria-hidden="true"></i> Administrar trabajadores
    </p>
    <p
      class="block text-black py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white">
      <i class="fa fa-bus" aria-hidden="true"></i> Registrar Unidad
    </p>
    <p
      class="block text-black py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white">
      <i class="fa fa-id-card" aria-hidden="true"></i> Registrar Chofer
    </p>
    <p
      class="block text-black py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white">
      <i class="fa fa-male" aria-hidden="true"></i> Registrar Despachador
    </p>
    <p
      class="block text-black py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white">
      <i class="fa fa-file-text" aria-hidden="true"></i> Generar Reportes
    </p>
    <p
      class="block text-black py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-cyan-500 hover:text-white">
      <i class="fa fa-folder-open" aria-hidden="true"></i> Archivos
    </p>
  </div>
</template>