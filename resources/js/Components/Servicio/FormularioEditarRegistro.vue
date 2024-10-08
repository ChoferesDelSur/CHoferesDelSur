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
    tipoUltimaCorrida: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})
const emit = defineEmits(['close']);

const form = useForm({
    _method: 'PUT',
    idEntrada: props.entrada.idEntrada,
    horaEntrada: props.entrada.horaEntrada,
    extremo: props.entrada.extremo,
    idCorte: props.corte.idCorte,
    horaCorte: props.corte.horaCorte,
    causa: props.corte.causa,
    horaRegreso: props.corte.horaRegreso,
    idCastigo: props.castigo.idCastigo,
    castigo: props.castigo.castigo,
    observaciones: props.castigo.observaciones,
    horaInicio: props.castigo.horaInicio,
    horaFin: props.castigo.horaFin,
    idUltimaCorrida: props.ultimaCorrida.idUltimaCorrida,
    horaInicioUC: props.ultimaCorrida.horaInicioUC,
    horaFinUC: props.ultimaCorrida.horaFinUC,
    tipoUltimaCorrida: props.ultimaCorrida.idTipoUltimaCorrida,
});

const selectedUnidad = ref(null);
const selectedRegistro = ref(null);
const camposFormulario = ref([]);
const formData = ref({});
const registrosDisponibles = ref([]);
const registroSeleccionado = ref(null);

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
                tipoUltimaCorrida: {
                    label: 'Tipo de Última Corrida',
                    type: 'select',
                    options: props.tipoUltimaCorrida.map(tipo => ({ value: tipo.idTipoUltimaCorrida, label: tipo.tipoUltimaCorrida })),
                },
            };
    }
};

watch(() => registroSeleccionado.value, (newVal) => {
    if (newVal) {
        cargarDatosRegistro(newVal);
        console.log("Registro seleccionado:", registroSeleccionado.value);
    }
});

const cargarDatosRegistro = (registro) => {
    // Asigna los valores del registro a los campos del formulario
    form.idEntrada = registro.idEntrada;
    form.horaEntrada = registro.horaEntrada;
    form.extremo = registro.extremo;

    form.idCorte = registro.idCorte;
    form.horaCorte = registro.horaCorte;
    form.causa = registro.causa;
    form.horaRegreso = registro.horaRegreso;

    form.idCastigo = registro.idCastigo;
    form.castigo = registro.castigo;
    form.observaciones = registro.observaciones;
    form.horaInicio = registro.horaInicio;
    form.horaFin = registro.horaFin;

    form.idUltimaCorrida = registro.idUltimaCorrida;
    form.horaInicioUC = registro.horaInicioUC;
    form.horaFinUC = registro.horaFinUC;
    form.tipoUltimaCorrida = registro.idTipoUltimaCorrida;
    // Si hay más campos para asignar, hazlo aquí
};

watch(() => props.entrada, async (newVal) => {
    form.idEntrada = newVal.idEntrada;
    form.horaEntrada = newVal.horaEntrada;
    form.extremo = newVal.extremo;
}, { deep: true }
);

watch(() => props.corte, async (newVal) => {
    form.idCorte = newVal.idCorte;
    form.horaCorte = newVal.horaCorte;
    form.causa = newVal.causa;
    form.horaRegreso = newVal.horaRegreso;
})

watch(() => props.castigo, async (newVal) => {
    form.idCastigo = newVal.idCastigo;
    form.castigo = newVal.castigo;
    form.observaciones = newVal.observaciones;
    form.horaInicio = newVal.horaInicio;
    form.horaFin = newVal.horaFin;
})

watch(() => props.ultimaCorrida, async (newVal) => {
    form.idUltimaCorrida = newVal.idUltimaCorrida;
    form.horaInicioUC = newVal.horaInicioUC;
    form.horaFinUC = newVal.horaFinUC;
    form.idTipoUltimaCorrida = newVal.idTipoUltimaCorrida;
})

watch(() => selectedRegistro, (newVal) => {
    if (newVal) {
        camposFormulario.value = definirCamposForm
            (newVal);
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
    if (selectedUnidad.value && selectedRegistro.value) {
        try {
            const response = await axios.get(route('servicio.obtenerRegistros', {
                tipoRegistro: selectedRegistro.value.value
            }), {
                params: {
                    idUnidad: selectedUnidad.value.value
                }
            });
            registrosDisponibles.value = response.data;

            // Si solo hay un registro, selecciona automáticamente
            if (registrosDisponibles.value.length === 1) {
                registroSeleccionado.value = registrosDisponibles.value[0];
                cargarDatosRegistro(registroSeleccionado.value);
            } else {
                registroSeleccionado.value = null; // Reinicia la selección si hay más de uno
            }

            camposFormulario.value = definirCamposForm(selectedRegistro.value.value);
        } catch (error) {
            console.error('Error fetching records:', error);
        }
    }
};

const editarRegistro = async () => {
    console.log("Estoy dentro de la funcion editarRegistro");
    // Determina la ruta de actualización según el tipo de registro seleccionado
    let ruta;
    // Obtén el valor correcto
    const tipoRegistro = selectedRegistro.value.value; // Ajuste aquí
    console.log("Tipo registo:", tipoRegistro);
    switch (tipoRegistro) {
        case 'entradas':
            ruta = route('servicio.actualizarEntrada', { id: form.idEntrada });
            break;
        case 'cortes':
            ruta = route('servicio.actualizarCorte', { id: form.idCorte });
            break;
        case 'castigos':
            ruta = route('servicio.actualizarCastigo', { id: form.idCastigo });
            break;
        case 'ultima_corrida':
            ruta = route('servicio.actualizarUltimaCorrida', { id: form.idUltimaCorrida });
            break;
        default:
            console.error('Tipo de registro no válido');
            return;
    }

    try {
        // Envía la solicitud como POST, simulando PUT con _method: 'PUT'
        form.post(ruta, {
            onSuccess: () => {
                close(); // Cierra el modal después de la actualización
                // Puedes mostrar un mensaje de éxito o manejar la respuesta aquí
            }
        });
    } catch (error) {
        console.error('Error al actualizar el registro:', error);
        // Maneja el error, por ejemplo mostrando un mensaje al usuario
    }
};

</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="editarRegistro">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-bold leading-7 text-gray-900">{{ title }}</h2>

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

                <!-- Registros Disponibles -->
                <div class="mt-4" v-if="registrosDisponibles.length > 1">
                    <label class="block text-md font-bold leading-6 text-gray-900">Registros Disponibles</label>
                    <div v-for="registro in registrosDisponibles" :key="registro.idEntrada"
                        class="flex items-center mb-2">
                        <input type="radio" :value="registro" v-model="registroSeleccionado" class="mr-2">
                        <span>{{ registro.horaCorte }} - {{ registro.causa }}</span>
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