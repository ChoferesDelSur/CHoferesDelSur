<script setup>
import Mensaje from '../../Components/Mensaje.vue';
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
import Swal from 'sweetalert2';
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
import { ref, reactive } from 'vue';
import axios from 'axios';

// Cargar fuentes personalizadas
pdfMake.vfs = pdfFonts.pdfMake.vfs;

const entradas = ref([]);

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    unidad: {
        type: Object,
        default: () => ({}),
    },
});

const form = reactive({
    unidad: null // Puedes inicializarlo con algún valor predeterminado si lo deseas
});

const fetchEntradas = async (idUnidad, periodo) => {
    let url = '';
    if (periodo === 'semana') {
        url = route('reportes.entradasSemana', { idUnidad: idUnidad });
    } else if (periodo === 'mes') {
        url = route('reportes.entradasMes', { idUnidad: idUnidad });
    } else {
        url = route('reportes.entradasUnidad', { idUnidad: idUnidad });
    }

    try {
        const response = await axios.get(url);
        entradas.value = response.data;
    } catch (error) {
        console.error(error);
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron obtener los datos',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const generarArchivo = async (reporte, formato, idUnidad, periodoSeleccionado) => {
    try {
        await fetchEntradas(idUnidad, periodoSeleccionado);

        if (reporte.titulo === 'Entradas') {
            if (formato === 'pdf') {
                generarPDF(reporte.titulo, periodoSeleccionado);
            } else if (formato === 'excel') {
                generarExcel(reporte.titulo, periodoSeleccionado);
            } else if (formato === 'imprimir') {
                imprimirReporte(reporte.titulo, periodoSeleccionado);
            }
        } else {
            // Aquí puedes manejar lógica para otros tipos de reporte si es necesario
            Swal.fire({
                title: `Generar el reporte "${reporte.titulo}" en ${formato}`,
                text: 'Lógica para generar este tipo de reporte aquí',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron obtener los datos',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};


const generarPDF = (tipo, periodoSeleccionado) => {
    let periodoTexto = periodoSeleccionado;
    if (typeof periodoSeleccionado === 'number' && periodoSeleccionado >= 1 && periodoSeleccionado <= 12) {
        periodoTexto = months[periodoSeleccionado - 1];
    } else if (periodoSeleccionado === 'año') {
        periodoTexto = selectedYear;
    }

    const bodyContent = entradas.value.map(entry => {
        const ruta = entry.unidad?.ruta ? entry.unidad.ruta.nombreRuta : 'N/A';
        const numeroUnidad = entry.unidad?.numeroUnidad || 'N/A';
        const directivo = entry.unidad?.directivo ? `${entry.unidad.directivo.nombre_completo}` : 'N/A';
        const horaEntrada = entry.horaEntrada || 'N/A';
        const tipoEntrada = entry.tipoEntrada;
        const extremo = entry.extremo || 'N/A';
        const operador = entry.unidad?.operador ? `${entry.unidad.operador.nombre_completo}` : 'N/A';
        
        return [ruta,numeroUnidad,directivo, horaEntrada, tipoEntrada, extremo, operador];
    });

    const docDefinition = {
        content: [
            { text: `Reporte de ${tipo} - Período: ${periodoTexto}`, style: 'header' },
            {
                table: {
                    headerRows: 1,
                    widths: ['*', 'auto', 'auto', 'auto', '*', 'auto', 'auto'], // Ajustar según el número de columnas
                    body: [
                        ['Ruta','Numero Unidad','Socio/Prestador','Hora Entrada', 'Tipo Entrada', 'Extremo', 'Operador'],
                        ...bodyContent
                    ]
                }
            }
        ],
        styles: {
            header: { fontSize: 18, bold: true },
            tableHeader: { bold: true }
        },
        pageOrientation: 'landscape'
    };
    pdfMake.createPdf(docDefinition).download(`${tipo}.pdf`);
};

const generarExcel = (tipo) => {
    Swal.fire({
        title: `Generar el reporte "${tipo}" en Excel`,
        text: 'Lógica para generar Excel aquí',
        icon: 'info',
        confirmButtonText: 'OK'
    });
};

const imprimirReporte = (tipo) => {
    Swal.fire({
        title: `Imprimir el reporte "${tipo}"`,
        text: 'Lógica para imprimir aquí',
        icon: 'info',
        confirmButtonText: 'OK'
    });
};

const reportes = [
    { titulo: 'Entradas', periodo: 'dia', periodoSeleccionado: 'dia' },
    { titulo: 'Cortes con regreso', periodo: 'dia', periodoSeleccionado: 'dia' },
    { titulo: 'Cortes sin regreso', periodo: 'dia', periodoSeleccionado: 'dia' }, // Ejemplo: Podrías establecerlo en 'dia' por defecto
    { titulo: 'Retardos', periodo: 'dia', periodoSeleccionado: 'dia' } // Ejemplo: Podrías establecerlo en 'dia' por defecto
];

const formatos = [
    { tipo: 'pdf', texto: 'Generar PDF', clase: 'bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded', icono: 'fa-solid fa-file-pdf' },
    { tipo: 'excel', texto: 'Generar Excel', clase: 'bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded', icono: 'fa-solid fa-file-excel' },
    { tipo: 'imprimir', texto: 'Imprimir', clase: 'bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded', icono: 'fa-solid fa-print' }
];

// Definir semanas
const weeks = Array.from({ length: 52 }, (_, i) => i + 1);

// Definir meses
const months = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];
// Definir años a partir de 2024
const currentYear = new Date().getFullYear();
const startYear = 2024; // Año inicial deseado
const years = Array.from({ length: currentYear - startYear + 1 }, (_, i) => startYear + i);

let selectedWeek = 1; // Por defecto, la primera semana
let selectedMonth = 1; // Por defecto, enero
let selectedYear = currentYear; // Por defecto, el año actual


</script>

<template>
    <PrincipalLayout title="Reportes">
        <div class="mt-1 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Reportes</h2>
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-1"></div>
            <Mensaje />

            <div class="sm:col-span-2 px-4 mb-4">
                <label for="unidad" class="block text-sm font-medium leading-6 text-gray-900">Unidad</label>
                <div class="mt-2">
                    <select name="unidad" id="unidad" v-model="form.unidad"
                        class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="" disabled selected>Seleccione la unidad</option>
                        <option value="todas">Todas las unidades</option>
                        <option v-for="carro in unidad" :key="carro.idUnidad" :value="carro.idUnidad">
                            {{ carro.numeroUnidad }}
                        </option>
                    </select>
                </div>
            </div>

            <div v-for="reporte in reportes" :key="reporte.titulo" class="mb-4 bg-zinc-100 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-2">{{ reporte.titulo }}</h3>
                <div class="flex flex-wrap space-x-3 mb-2">
                    <select v-model="reporte.periodoSeleccionado"
                        class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="dia">Hoy</option>
                        <option value="semana">Semanal</option>
                        <option value="mes">Mensual</option>
                        <option value="año">Anual</option>
                    </select>
                    <template v-if="reporte.periodoSeleccionado === 'semana'">
                        <select v-model="selectedWeek"
                            class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option v-for="(week, index) in weeks" :key="index" :value="week">Semana {{ week }}</option>
                        </select>
                    </template>
                    <template v-else-if="reporte.periodoSeleccionado === 'mes'">
                        <select v-model="selectedMonth"
                            class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option v-for="(month, index) in months" :key="index" :value="index + 1">{{ month }}
                            </option>
                        </select>
                    </template>
                    <template v-else-if="reporte.periodoSeleccionado === 'año'">
                        <select v-model="selectedYear"
                            class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </template>
                </div>
                <div class="flex flex-wrap space-x-3">
                    <button v-for="formato in formatos" :key="formato.tipo" :class="formato.clase"
    @click="generarArchivo(reporte, formato.tipo, form.unidad, reporte.periodoSeleccionado === 'semana' ? selectedWeek : reporte.periodoSeleccionado === 'mes' ? selectedMonth : reporte.periodoSeleccionado === 'año' ? selectedYear : '')">
    <i :class="formato.icono + ' mr-2 jump-icon'"></i> {{ formato.texto }}
</button>
                </div>
            </div>
        </div>
    </PrincipalLayout>
</template>


<style>
.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}
</style>