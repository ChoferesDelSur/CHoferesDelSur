<script setup>
import { useForm, put } from '@inertiajs/inertia-vue3';
import { ref, watch, computed } from 'vue';
import Modal from '../Modal.vue';
import { route } from 'ziggy-js';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import 'vue-select';
import axios from 'axios';

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
    entrada: {
        type: Object,
        default: () => ({}),
    },
    corte: {
        type: Object,
        default: () => ({}),
    },
    castigo: {
        type: Object,
        default: () => ({}),
    },
    ultimaCorrida: {
        type: Object,
        default: () => ({}),
    },
    unidadesConOperador: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})
const emit = defineEmits(['close']);
console.log("Estoy en el formulario editar registro"
);
const form = useForm({
    idEntrada: props.entrada.idEntrada,
    horaEntrada: props.entrada.horaEntrada,
    extremo: props.entrada.extremo,
    horaCorte: props.corte.horaCorte,
    causa: props.corte.causa,
    horaRegreso: props.corte.horaRegreso,
    castigo: props.castigo.castigo,
    observaciones: props.castigo.observaciones,
    horaInicio: props.castigo.horaInicio,
    horaFin: props.castigo.horaFin,
    horaInicioUC: props.ultimaCorrida.horaInicioUC,
    horaFinUC: props.ultimaCorrida.horaFinUC,
    tipoUltimaCorrida: props.ultimaCorrida.idTipoUltimaCorrida,
});

const selectedUnidad = ref(null);
const selectedRegistro = ref(null);
const camposFormulario = ref([]);
const formData = ref({});

// Función para definir campos del formulario según el tipo de registro
const definirCamposForm = (tipoRegistro) => {
    switch (tipoRegistro) {
        case 'entradas':
            return {
                horaEntrada: { label: 'Hora de Entrada', type: 'time' },
                extremo: { label: 'Extremo', type: 'radio', options: ['SI', 'NO'] },
            };
        case 'cortes':
            return {
                horaCorte: { label: 'Hora de Corte', type: 'time' },
                causa: { label: 'Causa', type: 'text' }, // Campo agregado
                horaRegreso: { label: 'Hora de Regreso', type: 'time' },
            };
        case 'castigos':
            return {
                castigo: { label: 'Motivo', type: 'text' },
                observaciones: { label: 'Observaciones', type: 'text' },
                horaInicio: { label: 'Hora de Inicio', type: 'time' },
                horaFin: { label: 'Hora de Fin', type: 'time' },
            };
        case 'ultima_corrida':
            return {
                horaInicioUC: { label: 'Hora de Inicio de Última Corrida', type: 'time' },
                horaFinUC: { label: 'Hora de Fin de Última Corrida', type: 'time' },
                idTipoUltimaCorrida: { label: 'Tipo de Última Corrida', type: 'select', options: [] },
            };
    }
};

const cargarDatosRegistro = (registro) => {
    // Asigna los valores del registro a los campos del formulario
    form.horaEntrada = registro.horaEntrada;
    form.extremo = registro.extremo;

    form.horaCorte = registro.horaCorte;
    form.causa = registro.causa;
    form.horaRegreso = registro.horaRegreso;

    form.castigo = registro.castigo;
    form.observaciones = registro.observaciones;
    form.horaInicio = registro.horaInicio;
    form.horaFin = registro.horaFin;

    form.horaInicioUC = registro.horaInicioUC;
    form.horaFinUC = registro.horaFinUC;
    form.tipoUltimaCorrida = registro.tipoUltimaCorrida;
    // Si hay más campos para asignar, hazlo aquí
};

watch(() => props.entrada, async (newVal) => {
    form.idEntrada = newVal.idEntrada;
    form.horaEntrada = newVal.horaEntrada;
    form.extremo = newVal.extremo;
}, { deep: true }
);

watch(() => props.corte, async (newVal) => {
    form.horaCorte = newVal.horaCorte;
    form.causa = newVal.causa;
    form.horaRegreso = newVal.horaRegreso;
})

watch(() => selectedRegistro, (newVal) => {
    console.log('Tipo de Registro seleccionado:', newVal);
    if (newVal) {
        camposFormulario.value = definirCamposForm
            (newVal);
        console.log('Form Fields después de la selección:', camposFormulario.value);
    }
});


// Validación de los select 
const validateSelect = (selectedValue) => {
    if (selectedValue == undefined) {
        return false;
    }
    return true;
};

const validateRadio = (selectedValue) => {
    return selectedValue !== null && selectedValue !== undefined; // Validar si el valor seleccionado no es 'null' ni 'undefined'
};

// Tipos de registros relacionados con tablas
const tiposRegistro = [
    { label: 'Entrada', value: 'entradas' },
    { label: 'Corte', value: 'cortes' },
    { label: 'Castigo', value: 'castigos' },
    { label: 'Ultima Corrida', value: 'ultima_corrida' },
];

const unidadError = ref('');
const horaEntradaError = ref('');
const extremoError = ref('');

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

const obtenerRegistros = async () => {
    console.log('Unidad seleccionada:', selectedUnidad.value);
    console.log('Tipo Registro seleccionado:', selectedRegistro.value);
    if (selectedUnidad.value && selectedRegistro.value) {
        console.log('Llamando a la API...');
        try {
            const response = await axios.get(route('servicio.obtenerRegistros', {
                tipoRegistro: selectedRegistro.value.value // Aquí va el tipo de registro en la URL
            }), {
                params: {
                    idUnidad: selectedUnidad.value.value   // Aquí envías el idUnidad como query parameter
                }
            });
            console.log('Fetched Records:', response.data);
            formData.value = response.data;
            if (response.data.length > 0) {
                cargarDatosRegistro(response.data[0]); // Carga datos del primer registro
            }
            camposFormulario.value = definirCamposForm(selectedRegistro.value.value); // Asigna campos dinámicos
            (selectedRegistro.value.value);
            console.log('camposFormulario después de la carga:', camposFormulario.value);
        } catch (error) {
            console.error('Error fetching records:', error);
        }
    } else {
        console.warn('Faltan datos: unidad o tipo de registro no seleccionados.');
    }
};

const save = async () => {
    unidadError.value = validateSelect(form.unidad) ? '' : 'Seleccione una unidad';

    if (
        unidadError.value
    ) {
        return;
    }
    form.post(route('servicio.registarHoraEntrada'), {
        onSuccess: () => {
            close()
            unidadError.value = '';
        }
    })
}
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="save">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">{{ title }}</h2>

                    <div class="flex flex-wrap -mx-4">
                        <!-- Campo Unidad -->
                        <div class="sm:col-span-2 w-64 px-4">
                            <label for="unidad" class="block text-sm font-medium leading-6 text-gray-900">Unidad</label>
                            <div class="mt-2">
                                <v-select v-model="selectedUnidad"
                                    :options="unidadesConOperador.map(carro => ({ label: carro.numeroUnidad, value: carro.idUnidad }))"
                                    placeholder="Seleccione la unidad" @change="obtenerRegistros" class="w-full">
                                </v-select>
                            </div>
                        </div>

                        <!-- Campo Tipo de Registro -->
                        <div class="sm:col-span-2 w-72 px-4">
                            <label for="tipoRegistro" class="block text-sm font-medium leading-6 text-gray-900">Tipo de
                                Registro</label>
                            <div class="mt-2">
                                <v-select v-model="selectedRegistro" :options="tiposRegistro"
                                    placeholder="Seleccione un tipo de registro" @change="obtenerRegistros"
                                    class="w-full">
                                </v-select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos dinámicos según el tipo de registro -->
                <div class="mt-4">
                    <div v-if="camposFormulario">
                        <div v-for="(campo, key) in camposFormulario" :key="key" class="mb-4">
                            <label :for="key" class="block text-sm font-medium leading-6 text-gray-900">{{ campo.label
                                }}</label>
                            <div class="mt-2">
                                <input v-if="campo.type === 'time'" type="time" v-model="form[key]"
                                    class="border rounded p-1 w-full">
                                <input v-if="campo.type === 'text'" type="text" v-model="form[key]"
                                    class="border rounded p-1 w-full">
                                <div v-if="campo.type === 'radio'">
                                    <div v-for="option in campo.options" :key="option" class="flex items-center">
                                        <input type="radio" :value="option" v-model="form[key]" class="mr-2">
                                        <label>{{ option }}</label>
                                    </div>
                                </div>
                                <select v-if="campo.type === 'select'" v-model="form[key]"
                                    class="border rounded p-1 w-full">
                                    <option v-for="option in campo.options" :key="option.value" :value="option.value">{{
                                        option.label }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button"
                        class="text-sm font-semibold leading-6 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded"
                        @click="close">
                        <i class="fa-solid fa-ban"></i> Cancelar
                    </button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                        :disabled="form.processing">
                        <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>

<style>
@import "vue-select/dist/vue-select.css";

.radio-label {
    margin-right: 10px;
    /* Ajusta este valor según lo que necesites */
}

.v-select .vs__dropdown-menu {
    z-index: 9999;
    /* Asegura que el menú desplegable se muestre sobre otros elementos */
}

.modal-content {
    overflow: visible !important;
    /* Permite que el menú desplegable se extienda más allá del modal */
}
</style>