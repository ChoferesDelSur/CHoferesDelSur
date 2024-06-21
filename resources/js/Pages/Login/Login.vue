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
                        <InputLabel for="password" value="Contraseña" />
                    </div>
                </div>
                <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required
                    autocomplete="password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton :class="{ 'opacity-85': form.processing, 'opacity-100': !form.processing }"
                    :disabled="form.processing">
                    <template v-if="form.processing">
                        <svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                fill="none"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A8.005 8.005 0 0112 4.245v3.8C9.29 8.674 7.326 10.404 6 12.291zM16 8.674v3.8c1.326 1.887 3.29 3.617 6 4.246zm-3.764 7.764A8.005 8.005 0 0112 19.755v-3.8c2.71-1.628 4.674-3.358 6-5.245z">
                            </path>
                        </svg>
                        Iniciando...
                    </template>
                    <template v-else>
                        Iniciar sesión <i class="fa fa-sign-in" aria-hidden="true" style="margin-left: 0.5rem;"></i>
                    </template>
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
