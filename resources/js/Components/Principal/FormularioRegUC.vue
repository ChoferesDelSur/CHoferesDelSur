<script setup>
import { useForm, put } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import Modal from '../Modal.vue';
import { route } from 'ziggy-js';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
        hora: String,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    ultimaCorrida: {
        type: Object,
        default: () => ({}),
    },
    tipoUltimaCorrida: {
        type: Object,
        default: () => ({}),
    },
    unidad: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})

const emit = defineEmits(['close']);

const form = useForm({
    idUltimaCorrida: props.ultimaCorrida.idUltimaCorrida,
    unidad: props.ultimaCorrida.idUnidad,
    tipoUltimaCorrida: props.ultimaCorrida.idTipoUltimaCorrida,
    horaInicioUC: props.ultimaCorrida.horaInicioUC,
    horaFinUC: props.ultimaCorrida.horaFinUC,
});

watch(() => props.ultimaCorrida, async (newVal) => {
    form.idUltimaCorrida = newVal.idUltimaCorrida;
    form.tipoUltimaCorrida = newVal.tipoUltimaCorrida;
    form.horaInicioUC = newVal.horaInicioUC;
    form.horaFinUC = newVal.horaFinUC;
}, { deep: true }
);

// Validación de los select 
const validateSelect = (selectedValue) => {
    if (selectedValue == undefined) {
        return false;
    }
    return true;
};

const validateSelectTipoCorrida = (selectedValue) => {
    return selectedValue != null;
};

const unidadError = ref('');
const horaInicioError = ref('');
const tipoUltimaCorridaError = ref('');

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

const save = async () => {
    horaInicioError.value = validateSelect(form.horaInicioUC) ? '' : 'Seleccione la hora de inicio de la última corrida';
    unidadError.value = validateSelect(form.unidad) ? '' : 'Seleccione una unidad';
    tipoUltimaCorridaError.value = validateSelectTipoCorrida(form.tipoUltimaCorrida) ? '' : 'Seleccione al un tipo de corrida'


    if (
        horaInicioError.value || unidadError.value || tipoUltimaCorridaError.value
    ) {
        return;
    }
    form.post(route('principal.registrarUC'), {
        onSuccess: () => {
            close()
            horaInicioError.value = '';
            unidadError.value = '';
            tipoUltimaCorridaError.value = '';
        }
    })
}
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="(op === '1' ? save() : update())">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">{{ title }}</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Rellene el formulario para poder registrar la
                        última corrida de una unidad. Los campos con <span class="text-red-500">*</span> son
                        obligatorios.
                    </p>
                    <div class="flex flex-wrap -mx-4">
                        <div class="sm:col-span-2 px-4">
                            <label for="unidad" class="block text-sm font-medium leading-6 text-gray-900">Unidad <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="unidad" :id="'unidad' + op" v-model="form.unidad"
                                    placeholder="Seleccione la unidad"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione la unidad</option>
                                    <option v-for="carro in unidad" :key="carro.idUnidad" :value="carro.idUnidad">
                                        {{ carro.numeroUnidad }}

                                    </option>
                                </select>
                            </div>
                            <div v-if="unidadError != ''" class="text-red-500 text-xs mt-1">{{ unidadError }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="tipoCorrida" class="block text-sm font-medium leading-6 text-gray-900">Tipo de corrida <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="tipoCorrida" :id="'tipoCorrida' + op" v-model="form.tipoUltimaCorrida"
                                    placeholder="Seleccione el tipo de la ultima corrida"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione el tipo de corrida</option>
                                    <option v-for="tipoCorrida in tipoUltimaCorrida" :key="tipoCorrida.idTipoUltimaCorrida" :value="tipoCorrida.idTipoUltimaCorrida">
                                        {{ tipoCorrida.tipoUltimaCorrida }}

                                    </option>
                                </select>
                            </div>
                            <div v-if="tipoUltimaCorridaError != ''" class="text-red-500 text-xs mt-1">{{ tipoUltimaCorridaError }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="horaInicioUC" class="block text-sm font-medium leading-6 text-gray-900">Hora de
                                inicio <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="time" name="horaInicioUC" :id="'horaInicioUC' + op" v-model="form.horaInicioUC"
                                    placeholder="Seleccione la hora de inicio"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                            </div>
                            <div v-if="horaInicioError != ''" class="text-red-500 text-xs">{{ horaInicioError}}</div>
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="horaFinUC" class="block text-sm font-medium leading-6 text-gray-900">Hora fin</label>
                            <div class="mt-2">
                                <input type="time" name="horaFinUC" :id="'horaFinUC' + op" v-model="form.horaFinUC"
                                    placeholder="Seleccione la hora de finalización"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" :id="'cerrar' + op"
                        class="text-sm font-semibold leading-6 bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-white py-2 px-4 rounded"
                        data-bs.dismiss="modal" @click="close"><i class="fa-solid fa-ban"></i> Cancelar</button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                        :disabled="form.processing"> <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </Modal>
</template>