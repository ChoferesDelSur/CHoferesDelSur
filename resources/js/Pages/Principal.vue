<script setup>
import PrincipalLayout from '@/Layouts/PrincipalLayout.vue';

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
});

</script>

<template>

    <PrincipalLayout title="Inicio">
        <div class="mt-8 bg-yellow-200 p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-5">Bienvenido al sistema de la empresa Choferes del Sur</h2>
        </div>

    </PrincipalLayout>
</template>
<style>
.text-lgg {
    font-size: 1.190rem
        /* 20px */
    ;
    line-height: 1.75rem
        /* 28px */
    ;
}
</style>