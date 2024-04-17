<script setup>
import { data } from 'jquery';
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';

const props = defineProps({
  message: { String, default: '' },
  color: { String, default: '' },
});

const columnas =[
  {
    data: null,
    render: function (data, type, row, meta) {
            return "";
        }
  }
]


console.log("Estoy en Formar Unidades");

</script>

<template>
  <PrincipalLayout title="FormarUnidades">
    <div class="mt-8 bg-white p-4 shadow rounded-lg h-5/6">
      <h2 class="font-bold text-center text-xl pt-5"> Formar Unidades</h2>
      <div>

      </div>
    </div>
  </PrincipalLayout>
</template>