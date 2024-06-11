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
    console.log("Periodo:", periodo);
    let url = '';
    if (periodo.tipo === 'semana') {
        url = route('reportes.entradasSemana', { idUnidad: idUnidad, semana: periodo.valor });
        console.log("Url semana:", url);
    } else if (periodo.tipo === 'mes' || typeof periodo === 'number') {
        url = route('reportes.entradasMes', { idUnidad: idUnidad, mes: periodo.valor });
        console.log("Url mes:", url);
    } else if (periodo.tipo === 'anio') {
        url = route('reportes.entradasAnio', { idUnidad: idUnidad, anio: periodo.valor });
        console.log("Url año:", url);
    }

    try {
        const response = await axios.get(url);
        entradas.value = response.data;
    } catch (error) {
        /* console.error(error); */
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron obtener los datos',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const generarArchivo = async (reporte, formato, idUnidad, periodoSeleccionado) => {
    let periodo = { tipo: reporte.periodoSeleccionado, valor: '' };
    if (reporte.periodoSeleccionado === 'semana') {
        periodo.valor = semanaSeleccionada;
    } else if (reporte.periodoSeleccionado === 'mes') {
        periodo.valor = mesSeleccionado;
    } else if (reporte.periodoSeleccionado === 'anio') {
        periodo.valor = anioSeleccionado;
    }

    try {
        await fetchEntradas(idUnidad, periodo);
        console.log("periodoSeleccionado en generarArchivo:", periodo);
        if (reporte.titulo === 'Entradas') {
            if (formato === 'pdf') {
                generarPDF(reporte.titulo, periodo); // Pasa el objeto periodo completo
            } else if (formato === 'excel') {
                generarExcel(reporte.titulo, periodo.valor);
            } else if (formato === 'imprimir') {
                imprimirReporte(reporte.titulo, periodo.valor);
            }
        } else {
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
    let periodoTexto;

    // Ajustar el texto del periodo según el tipo
    if (periodoSeleccionado.tipo === 'semana') {
        periodoTexto = `Semana ${periodoSeleccionado.valor}`;
    } else if (periodoSeleccionado.tipo === 'mes') {
        periodoTexto = months[periodoSeleccionado.valor - 1];
    } else if (periodoSeleccionado.tipo === 'anio') {
        periodoTexto = periodoSeleccionado.valor; // Asumiendo que `anioSeleccionado` ya está en `periodoSeleccionado.valor`
    } else {
        periodoTexto = periodoSeleccionado.valor; // Default case
    }

    const bodyContent = entradas.value.map(entry => {
        const ruta = entry.unidad?.ruta ? entry.unidad.ruta.nombreRuta : 'N/A';
        const fecha = entry.created_at ? new Date(entry.created_at).toLocaleDateString() : 'N/A';
        const numeroUnidad = entry.unidad?.numeroUnidad || 'N/A';
        const directivo = entry.unidad?.directivo ? `${entry.unidad.directivo.nombre_completo}` : 'N/A';
        const horaEntrada = entry.horaEntrada ? entry.horaEntrada.substring(0, 5) : 'N/A';
        const tipoEntrada = entry.tipoEntrada;
        const extremo = entry.extremo || 'N/A';
        const operador = entry.unidad?.operador ? `${entry.unidad.operador.nombre_completo}` : 'N/A';

        return [ruta, fecha, numeroUnidad, directivo, horaEntrada, tipoEntrada, extremo, operador];
    });

    // Construye el nombre del archivo con el tipo de reporte y el período
    const nombreArchivo = `${tipo}-${periodoTexto}.pdf`;

    const docDefinition = {
        content: [
            { text: `Reporte de ${tipo} - Período: ${periodoTexto}`, style: 'header' },
            {
                table: {
                    headerRows: 1,
                    widths: ['*', 'auto', 'auto', 'auto', '*', 'auto', 'auto', 'auto'], // Ajustar según el número de columnas
                    body: [
                        [
                            { text: 'Ruta', style: 'tableHeader', alignment: 'center' },
                            { text: 'Fecha', style: 'tableHeader', alignment: 'center' },
                            { text: 'Numero Unidad', style: 'tableHeader', alignment: 'center' },
                            { text: 'Socio/Prestador', style: 'tableHeader', alignment: 'center' },
                            { text: 'Hora Entrada', style: 'tableHeader', alignment: 'center' },
                            { text: 'Tipo Entrada', style: 'tableHeader', alignment: 'center' },
                            { text: 'Extremo', style: 'tableHeader', alignment: 'center' },
                            { text: 'Operador', style: 'tableHeader', alignment: 'center' }
                        ],
                        ...bodyContent
                    ]
                }
            }
        ],
        styles: {
            header: { fontSize: 16, bold: true },
            tableHeader: { bold: true }
        },
        pageOrientation: 'landscape'
    };

    pdfMake.createPdf(docDefinition).download(nombreArchivo);
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
    { titulo: 'Entradas', periodo: 'semana', periodoSeleccionado: 'semana' },
    { titulo: 'Cortes con regreso', periodo: 'semana', periodoSeleccionado: 'semana' },
    { titulo: 'Cortes sin regreso', periodo: 'semana', periodoSeleccionado: 'semana' }, // Ejemplo: Podrías establecerlo en 'dia' por defecto
    { titulo: 'Retardos', periodo: 'semana', periodoSeleccionado: 'semana' } // Ejemplo: Podrías establecerlo en 'dia' por defecto
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

let semanaSeleccionada = 1; // Por defecto, la primera semana
let mesSeleccionado = 1; // Por defecto, enero
let anioSeleccionado = currentYear; // Por defecto, el año actual

</script>

<template>
    <PrincipalLayout title="Reportes">
        <div class="mt-1 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0 mb-2">Reportes</h2>
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px"></div>
            <Mensaje />

            <div class="sm:col-span-2 px-4 mb-4">
                <h3 class="font-bold text-l pt-0">Buscar por: </h3>
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
                        <option value="semana">Semanal</option>
                        <option value="mes">Mensual</option>
                        <option value="anio">Anual</option>
                    </select>
                    <template v-if="reporte.periodoSeleccionado === 'semana'">
                        <select v-model="semanaSeleccionada"
                            class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option v-for="(week, index) in weeks" :key="index" :value="week">Semana {{ week }}</option>
                        </select>
                    </template>
                    <template v-else-if="reporte.periodoSeleccionado === 'mes'">
                        <select v-model="mesSeleccionado"
                            class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option v-for="(month, index) in months" :key="index" :value="index + 1">{{ month }}
                            </option>
                        </select>
                    </template>
                    <template v-else-if="reporte.periodoSeleccionado === 'anio'">
                        <select v-model="anioSeleccionado"
                            class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </template>
                </div>
                <div class="flex flex-wrap space-x-3">
                    <button v-for="formato in formatos" :key="formato.tipo" :class="formato.clase"
                        @click="generarArchivo(reporte, formato.tipo, form.unidad, { tipo: reporte.periodoSeleccionado, valor: reporte.periodoSeleccionado === 'semana' ? semanaSeleccionada : reporte.periodoSeleccionado === 'mes' ? mesSeleccionado : reporte.periodoSeleccionado === 'anio' ? anioSeleccionado : '' })">
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