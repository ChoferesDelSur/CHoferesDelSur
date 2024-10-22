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
    rolServicio: {
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
    idRolServicio: props.rolServicio.idRolServicio,
    unidad: [], // Inicializa como un array vacío   
    trabajaDomingo: props.rolServicio.trabajaDomingo,
    unidadesSi: [],  // Unidades que trabajarán (SI)
    unidadesNo: [],  // Unidades que no trabajarán (NO)
});

watch(() => props.rolServicio, async (newVal) => {
    form.idRolServicio = newVal.idRolServicio;
    form.trabajaDomingo = newVal.trabajaDomingo;
}, { deep: true }
);

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

const validateSelect = (unidadesSi, unidadesNo) => {
    return unidadesSi.length > 0 || unidadesNo.length > 0;
};

const unidadError = ref('');

const save = async () => {
    unidadError.value = validateSelect(form.unidadesSi, form.unidadesNo) ? '' : 'Seleccione al menos una unidad en cualquiera de las opciones.';

    if (unidadError.value) {
        return;
    }

    form.post(route('servicio.trabDomingo'), {
        data: form.data, // Envía los datos del formulario al controlador
        onSuccess: () => {
            close()
            unidadError.value = '';
        }
    })
}

const obtenerDomingoAnterior = () => {
    const hoy = new Date();
    const diaDeSemana = hoy.getDay();
    const diasDesdeDomingo = diaDeSemana === 0 ? 7 : diaDeSemana; // Si hoy es domingo, mostrar el domingo pasado
    const domingoAnterior = new Date(hoy);
    domingoAnterior.setDate(hoy.getDate() - diasDesdeDomingo);
    return domingoAnterior.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
};

const obtenerProximoDomingo = () => {
    const hoy = new Date();
    const diaDeSemana = hoy.getDay();
    const diasParaDomingo = 7 - diaDeSemana;
    const proximoDomingo = new Date(hoy);
    proximoDomingo.setDate(hoy.getDate() + diasParaDomingo);
    return proximoDomingo.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
};

const DomingoAnterior = obtenerDomingoAnterior();
const proximoDomingo = obtenerProximoDomingo();

// Función para ordenar unidades por la parte numérica
function ordenarUnidadesPorNumero(unidades) {
    return unidades.sort((a, b) => {
        const numA = parseInt(a.numeroUnidad.split('-')[1], 10);
        const numB = parseInt(b.numeroUnidad.split('-')[1], 10);
        return numA - numB;
    });
}

// Ordenar las unidades cuando cambien
watch(() => props.unidad, (newUnidades) => {
    // Solo afecta al orden dentro del select
    form.unidad = ordenarUnidadesPorNumero([...newUnidades]);  // Crea una copia de newUnidades y ordena
}, { immediate: true });


</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="(op === '1' ? save() : update())">
                <h2 class="text-base font-semibold leading-7 text-gray-900">{{ title }}</h2>

                <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Seleccione las unidades que trabajaron el :
                    <strong>{{ DomingoAnterior }}</strong>.
                </p>

                <div class="sm:col-span-2 px-4">
                    <label for="unidadesNo" class="block text-sm font-medium leading-6 text-gray-900">Unidad</label>
                    <div class="mt-2">
                        <select name="unidadesNo" id="unidadesNo" v-model="form.unidadesNo" multiple size="10"
                            class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option disabled>Seleccione una o más unidades</option>
                            <option v-for="carro in form.unidad" :key="carro.idUnidad" :value="carro.idUnidad">
                                {{ carro.numeroUnidad }}
                            </option>
                        </select>
                    </div>
                    <!-- <div v-if="unidadError != ''" class="text-red-500 text-xs mt-1">{{ unidadError }}</div> -->
                </div>

                <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Seleccione las unidades que trabajarán el próximo:
                    <strong>{{ proximoDomingo }}</strong>.
                </p>

                <div class="sm:col-span-2 px-4">
                    <label for="unidadesSi" class="block text-sm font-medium leading-6 text-gray-900">Unidad</label>
                    <div class="mt-2">
                        <select name="unidadesSi" id="unidadesSi" v-model="form.unidadesSi" multiple size="10"
                            class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option disabled>Seleccione una o más unidades</option>
                            <option v-for="carro in form.unidad" :key="carro.idUnidad" :value="carro.idUnidad">
                                {{ carro.numeroUnidad }}
                            </option>
                        </select>
                    </div>
                    <!-- <div v-if="unidadError != ''" class="text-red-500 text-xs mt-1">{{ unidadError }}</div> -->
                </div>

                <div v-if="unidadError" class="text-red-500 text-xs mt-1 mb-4">{{ unidadError }}</div>

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