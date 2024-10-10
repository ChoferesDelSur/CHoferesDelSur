<script setup>
import { useForm, put } from '@inertiajs/inertia-vue3';
import { ref, watch, computed } from 'vue';
import Modal from '../Modal.vue';
import { route } from 'ziggy-js';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import 'vue-select';
import Swal from 'sweetalert2';
import { Inertia } from '@inertiajs/inertia';
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
    _method: 'DELETE',
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
    form.tipoUltimaCorrida = newVal.idTipoUltimaCorrida;
})

watch(() => selectedRegistro, (newVal) => {
    if (newVal) {
        camposFormulario.value = definirCamposForm
            (newVal);
    }
});

// Tipos de registros relacionados con tablas
const tiposRegistro = [
    { label: 'Entrada', value: 'entradas' },
    { label: 'Corte', value: 'cortes' },
    { label: 'Castigo', value: 'castigos' },
    { label: 'Ultima Corrida', value: 'ultima_corrida' },
];

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
    selectedUnidad.value = null;
    selectedRegistro.value = null;
    registrosDisponibles.value = [];
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
            console.log(response.data); // Ver los datos
            registrosDisponibles.value = response.data;

            // Evitar la selección automática si solo hay un registro
            if (response.data.length === 1) {
                registroSeleccionado.value = null; // No seleccionar automáticamente
            } else {
                registroSeleccionado.value = null; // Limpiar la selección si hay varios
            }
        } catch (error) {
            console.error('Error fetching records:', error);
        }
    }
};

const unidadError = ref('');
const tipoRegistroError = ref('');

const eliminarRegistro = async () => {
    if (!registroSeleccionado.value) {
        alert('Debes seleccionar un registro para eliminar.');
        return;
    }

    Swal.fire({
        title: '¿Estás seguro de que deseas eliminar este registro?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                let registroId = null;
                let tipoRegistro = null;

                // Verifica el tipo de registro seleccionado y asigna el ID y tipo correctos
                if (registroSeleccionado.value.idCorte) {
                    registroId = registroSeleccionado.value.idCorte;
                    tipoRegistro = 'cortes';
                } else if (registroSeleccionado.value.idCastigo) {
                    registroId = registroSeleccionado.value.idCastigo;
                    tipoRegistro = 'castigos';
                } else if (registroSeleccionado.value.idEntrada) {
                    registroId = registroSeleccionado.value.idEntrada;
                    tipoRegistro = 'entradas';
                } else if (registroSeleccionado.value.idUltimaCorrida) {
                    registroId = registroSeleccionado.value.idUltimaCorrida;
                    tipoRegistro = 'ultima_corrida';
                }

                if (!registroId || !tipoRegistro) {
                    console.error('No se pudo determinar el ID o tipo de registro');
                    return;
                }

                console.log("ID del registro:", registroId);
                console.log("Tipo de registro:", tipoRegistro);

                // Enviar la solicitud DELETE con Inertia.js
                await Inertia.post(route('servicio.eliminarRegistro', { id: registroId }), {
                    tipoRegistro,  // Enviar el tipo de registro al backend
                    _method: 'DELETE'  // Indicar que es una petición DELETE
                });

                // Mostrar mensaje de éxito y actualizar registros
                Swal.fire({
                    title: 'Registro eliminado exitosamente',
                    icon: 'success'
                });

                registroSeleccionado.value = null;  // Limpiar la selección
                obtenerRegistros();  // Actualizar la lista de registros

            } catch (error) {
                console.error('Error al eliminar el registro:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al eliminar el registro',
                    icon: 'error'
                });
            }
        }
    });
};

const formatearHora = (hora) => {
    if (!hora) return ''; // Retornar cadena vacía si no hay hora

    // Supongamos que la hora está en formato HH:mm:ss o HH:mm
    const partes = hora.split(':'); // Separa la hora en partes
    if (partes.length < 2) return 'Formato de hora inválido'; // Validación básica

    // Retorna solo las horas y minutos
    return `${partes[0]}:${partes[1]}`; // Formato HH:mm
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
                                <span v-if="unidadError" class="text-sm text-red-600">{{ unidadError }}</span>
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
                                <span v-if="tipoRegistroError" class="text-sm text-red-600">{{ tipoRegistroError
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registros Disponibles -->
                <div class="mt-4">
                    <label class="block text-md font-bold leading-6 text-gray-900">Registros Disponibles</label>
                    <div v-if="registrosDisponibles.length">
                        <div v-for="registro in registrosDisponibles"
                            :key="registro.idEntrada || registro.idCorte || registro.idCastigo || registro.idUltimaCorrida">
                            <input type="radio" :value="registro" v-model="registroSeleccionado" class="mr-2">

                            <span>
                                <template v-if="registro.idCorte">
                                    {{ formatearHora(registro.horaCorte) }} - {{ registro.causa }}
                                </template>
                                <template v-else-if="registro.idCastigo">
                                    {{ registro.castigo }} - Inicio: {{ formatearHora(registro.horaInicio) }}, Fin: {{
                                    formatearHora(registro.horaFin) }}
                                </template>
                                <template v-else-if="registro.idUltimaCorrida">
                                    Última Corrida: Inicio: {{ formatearHora(registro.horaInicioUC) }}, Fin: {{
                                    formatearHora(registro.horaFinUC) }}, Tipo: {{ registro.idTipoUltimaCorrida }}
                                </template>
                                <template v-else-if="registro.idEntrada">
                                    Entrada: {{ formatearHora(registro.horaEntrada) }} - Extremo: {{ registro.extremo }}
                                </template>
                            </span>
                        </div>
                    </div>
                    <div v-else>
                        <p>No hay registros disponibles.</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button"
                        class="text-sm font-semibold leading-6 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded"
                        @click="close">
                        <i class="fa-solid fa-ban"></i> Cerrar Formulario
                    </button>
                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
                        @click="eliminarRegistro">
                        <i class="fa-solid fa-trash mr-2"></i> Eliminar
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