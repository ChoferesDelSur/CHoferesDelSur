<script setup>
import { Head, useForm } from '@inertiajs/inertia-vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Mensaje from '../../Components/Mensaje.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    usuario: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? true : false,
    })).post(route('inicioSesion'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="Iniciar sesión" />

    <AuthenticationCard>


        <!-- <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div> -->
        <div>
            <h2 class="text-black text-2xl text-center font-semibold p-5">Iniciar Sesion</h2>
            <Mensaje />
            <div class="p-4 mb-4 text-sm text-justify rounded-lg">
                <span class="">Bienvenido al sistema de control y gestión de la empresa Sociedad Cooperativa de Choferes
                    del Sur S.C.L. Para acceder a la información es necesario que inicie sesión.</span>
            </div>
        </div>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <div class="flex items-center">
                    <i class="fa fa-user mr-2" aria-hidden="true"></i>
                    <div>
                        <InputLabel for="usuario" value="Usuario" />
                    </div>
                </div>
                <TextInput id="usuario" v-model="form.usuario" type="text" class="mt-1 block w-full" required autofocus
                    autocomplete="usuario" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mb-4">
                <div class="flex items-center">
                    <i class="fa fa-unlock-alt mr-2" aria-hidden="true"></i>
                    <div>
                        <InputLabel for="password" value="Password" />
                    </div>
                </div>
                <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required
                    autocomplete="password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-end mt-4">


                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Iniciar sesion
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
