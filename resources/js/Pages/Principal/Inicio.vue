<script setup>
import Mensaje from '../../Components/Mensaje.vue';
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
});
</script>

<template>
    <PrincipalLayout title="Inicio">
        <div class="mt-5 bg-white p-4 shadow rounded-lg h-5/6">

            <Mensaje />
            
            <div class="flex items-start mb-2">
                <!-- <img :src="'/Imagenes/Logo.jpg'"
                    class="self-start h-20 w-20 md:h-32 md:w-32 ml-4 md:mx-1 object-cover rounded-lg"> -->
                <div class="flex-1">
                    <h1 class="font-bold text-center text-xl pt-0"> Bienvenido al sistema de la empresa Choferes del Sur
                    </h1>
                </div>
                <!-- <img :src="'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSjjWteVbsRhdwMVVzQ_iC0LN3KGixAvG8_3J3ZdsWiCQ&s'"
                    class="self-start h-20 w-20 md:h-32 md:w-32 ml-4 md:mx-1 object-cover rounded-lg"> -->
            </div>
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