<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, onMounted } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import ServicioLayout from '../../Layouts/ServicioLayout.vue';
import Mensaje from '../../Components/Mensaje.vue';
import axios from 'axios';

const props = defineProps({
    usuario: { type: Object },
});

const form = useForm({
    password_actual: '',
    password_nueva: '',
    password_confirmacion: '',
    idUsuario: props.usuario.idUsuario
});

const contraActualError = ref('');
const contraNuevaError = ref('');
const contraConfirmacionError = ref('');

const validateStringNotEmpty = (value) => {
    return typeof value === 'string' && value.trim() !== '';
};

const validateContrasenias = (value1, value2) => {
    return value1 === value2 && value1.trim() !== '' && value2.trim() !== '';
};

const validateLongContrasenias = (value1, value2) => {
    return value1.length >= 8 && value2.length >= 8;
};

const update = () => {
    console.log("Entró en update");
    contraActualError.value = validateStringNotEmpty(form.password_actual) ? '' : 'Ingrese la contraseña actual';
    contraNuevaError.value = validateStringNotEmpty(form.password_nueva) ? '' : 'Ingrese la nueva contraseña';
    contraConfirmacionError.value = validateStringNotEmpty(form.password_confirmacion) ? '' : 'Ingrese nuevamente la contraseña creada';
    contraNuevaError.value = validateContrasenias(form.password_nueva, form.password_confirmacion) ? '' : 'Las contraseñas no coinciden';
    contraConfirmacionError.value = validateContrasenias(form.password_nueva, form.password_confirmacion) ? '' : 'Las contraseñas no coinciden';
    contraNuevaError.value = validateLongContrasenias(form.password_nueva, form.password_confirmacion) ? '' : 'La contraseña tiene que ser igual o mayor a 8 digitos';


    if (contraActualError.value || contraNuevaError.value || contraConfirmacionError.value) {
        return;
    }
    console.log("Ya estoy por entrar en axios");
    form.put(route('servicio.actualizarContrasenia', form.idUsuario), {
        onSuccess: () => {
            console.log("Estoy despues de onSuccess");
            form.password_actual = '';
            form.password_nueva = '';
            form.password_confirmacion = '';
        }
    });
}

onMounted(async () => {
    form.idUsuario = props.usuario.idUsuario;
});

const showPasswordActual = ref(false);
const showPasswordNueva = ref(false);
const showPasswordConfirmacion = ref(false);

const togglePasswordVisibilityActual = () => {
    showPasswordActual.value = !showPasswordActual.value;
};

const togglePasswordVisibilityNueva = () => {
    showPasswordNueva.value = !showPasswordNueva.value;
};

const togglePasswordVisibilityConfirmacion = () => {
    showPasswordConfirmacion.value = !showPasswordConfirmacion.value;
};
</script>


<template>
    <ServicioLayout title="Perfil" :usuario="props.usuario">
        <div class=" bg-white p-4 shadow rounded-lg h-5/6 mt-10 sm:mt-0">
            <h2 class="text-black text-2xl text-center font-semibold p-5">Perfil</h2>
            <div class="my-1"></div>
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6"></div>
            <div>
                <form>
                    <div>
                        <div class="md:grid md:grid-cols-8">
                            <div class="col-span-4">
                                <h3 class="m-3 text-lg font-medium text-gray-900">
                                    Informacion de la cuenta
                                </h3>
                                <p class="m-3 text-sm text-gray-600 text-justify">
                                    Para realizar modificaciones a este tipo de informacion, es necesario comunicarse
                                    con el administrador.
                                </p>
                            </div>
                            <div class="col-span-4 m-2 md:grid md:grid-4">
                                <div class="col-span-3 sm:col-span-4 mt-2">
                                    <InputLabel for="nombre" value="Nombre" />
                                    <TextInput id="nombre" v-model="props.usuario.nombre" type="text"
                                        class="mt-1 block w-full" disabled />
                                </div>
                                <div class="col-span-6 sm:col-span-4 mt-2">
                                    <InputLabel for="apellidoP" value="Apellido Paterno" />
                                    <TextInput id="apellidoP" v-model="props.usuario.apellidoP" type="text"
                                        class="mt-1 block w-full" disabled />
                                </div>
                                <div class="col-span-6 sm:col-span-4 mt-2">
                                    <InputLabel for="apellidoM" value="Apellido Materno" />
                                    <TextInput id="apellidoM" v-model="props.usuario.apellidoM" type="text"
                                        class="mt-1 block w-full" disabled />
                                </div>
                                <div class="col-span-6 sm:col-span-4 mt-2">
                                    <InputLabel for="usuario" value="Usuario" />
                                    <TextInput id="usuario" v-model="props.usuario.usuario" type="text"
                                        class="mt-1 block w-full" disabled />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6 mt-5"></div>
            <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////// -->
            <!--  //Mensaje para mostrar el mensaje de que se ha borrado o agregado correctamente un tutor               -->
            <Mensaje />
            <div>
                <form @submit.prevent="update()">
                    <div>
                        <div class="md:grid md:grid-cols-8">
                            <div class="col-span-4">
                                <h3 class="m-3 text-lg font-medium text-gray-900">
                                    Cambiar contraseña
                                </h3>
                                <p class="m-3 text-sm text-gray-600 text-justify">
                                    Para realizar el cambio de contraseña es necesario recordar la contraseña actual, en
                                    caso de haberlo olvidado, es necesario comunicarse con el administrador para su
                                    recuperación.
                                    La nueva contraseña tiene que ser mayor a 8 digitos.
                                </p>
                            </div>
                            <div class="col-span-4 m-2 md:grid md:grid-4">
                                <div class="col-span-3 sm:col-span-4 mt-2">
                                    <InputLabel for="password_actual" value="Contraseña actual" />
                                    <div class="relative">
                                        <TextInput id="password_actual" v-model="form.password_actual"
                                            :type="showPasswordActual ? 'text' : 'password'" class="mt-1 block w-full"
                                            autocomplete="current-password" />
                                        <button type="button"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center focus:outline-none"
                                            @click="togglePasswordVisibilityActual">
                                            <i class="fa" :class="showPasswordActual ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="contraActualError != ''" class="text-red-500 text-xs mt-1">{{
                                    contraActualError
                                }}</div>
                                <div class="col-span-6 sm:col-span-4 mt-2">
                                    <InputLabel for="password_nueva" value="Contraseña nueva" />
                                    <div class="relative">
                                        <TextInput id="password_nueva" v-model="form.password_nueva"
                                            :type="showPasswordNueva ? 'text' : 'password'" class="mt-1 block w-full"
                                            autocomplete="new-password" />
                                        <button type="button"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center focus:outline-none"
                                            @click="togglePasswordVisibilityNueva">
                                            <i class="fa" :class="showPasswordNueva ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="contraNuevaError != ''" class="text-red-500 text-xs mt-1">{{ contraNuevaError
                                    }}
                                </div>
                                <div class="col-span-6 sm:col-span-4 mt-2">
                                    <InputLabel for="password_confirmacion" value="Confirmar contraseña" />
                                    <div class="relative">
                                        <TextInput id="password_confirmacion" v-model="form.password_confirmacion"
                                            :type="showPasswordConfirmacion ? 'text' : 'password'"
                                            class="mt-1 block w-full" autocomplete="new-password" />
                                        <button type="button"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center focus:outline-none"
                                            @click="togglePasswordVisibilityConfirmacion">
                                            <i class="fa"
                                                :class="showPasswordConfirmacion ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="contraConfirmacionError != ''" class="text-red-500 text-xs mt-1">{{
                                    contraConfirmacionError }}</div>
                                <div class="col-span-6 sm:col-span-4 mt-2" hidden>
                                    <InputLabel for="idUsuario" value="idUsuario" />
                                    <input id="idUsuario" v-model="form.idUsuario" type="number"
                                        class="mt-1 block w-full" />
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded right"
                                :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> <i
                                    class="fa-solid fa-floppy-disk mr-2"></i>Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </ServicioLayout>
</template>