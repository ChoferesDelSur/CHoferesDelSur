<script setup>
import { Head, useForm } from '@inertiajs/inertia-vue3';
import AuthenticationCard from '../../Components/AuthenticationCard.vue';
import InputLabel from '../../Components/InputLabel.vue';
import TextInput from '../../Components/TextInput.vue';
import PrimaryButton from '../../Components/PrimaryButton.vue';

const form = useForm({
    nombre: '',
    apellidoP: '',
    apellidoM: '',
    usuario: '',
    password: '',
});

const submit = () => {
    try {
        const url = route('registrarse');
        console.log('Request URL:', url); // Verifica que esta URL sea HTTPS
        form.post(url);
    } catch (e) {
        console.log(e);
    }
};
</script>

<template>

    <Head title="Registrarse" />
    <AuthenticationCard>
        <!-- <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div> -->
        <div class="flex flex-col items-center">
            <!-- <i class="fa fa-user-circle-o" aria-hidden="true"></i> -->
            <i class="fa fa-user-circle text-6xl text-blue-600" aria-hidden="true"></i>
            <h2 class="text-black text-2xl text-center font-semibold p-5">Registrarse</h2>
            <div class="p-4 mb-4 text-sm text-justify rounded-lg">
                <span class="">Bienvenido al sistema de control y gestión de la empresa Sociedad Cooperativa de Choferes
                    del Sur S.C.L.. Al ser el primer ingreso al sistema es necesario que cree las credenciales del
                    usuario Administrador.</span>
            </div>
        </div>
        <!-- <Mensaje /> -->
        <form @submit.prevent="submit">
            <div class="">
                <InputLabel for="nombre" value="Nombre(s)" />
                <TextInput id="nombre" v-model="form.nombre" type="text" class="mt-1 block w-full" required autofocus
                    autocomplete="nombre" />
            </div>
            <div class="">
                <InputLabel for="apellidoP" value="Apellido Paterno" />
                <TextInput id="apellidoP" v-model="form.apellidoP" type="text" class="mt-1 block w-full" required
                    autocomplete="apellidoP" />
            </div>
            <div class="">
                <InputLabel for="apellidoM" value="Apellido Materno" />
                <TextInput id="apellidoM" v-model="form.apellidoM" type="text" class="mt-1 block w-full" required
                    autocomplete="apellidoM" />
            </div>
            <div class="">
                <InputLabel for="usuario" value="Usuario" />
                <TextInput id="usuario" v-model="form.usuario" type="text" class="mt-1 block w-full" required
                    autocomplete="usuario" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required
                    autocomplete="password" />
            </div>
            <div class="flex items-center justify-end mt-4">

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Registrarse
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
