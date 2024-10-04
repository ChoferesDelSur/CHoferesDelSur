<script setup>
import Swal from 'sweetalert2';
import { ref, reactive } from 'vue';
import * as XLSX from 'xlsx';
import axios from 'axios';
import jsPDF from 'jspdf';
/* import 'jspdf-autotable'; */

const entradas = ref([]);

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    unidad: {
        type: Object,
        default: () => ({}),
    },
    operador: {
        type: Object,
        default: () => ({}),
    },
    entrada: {
        type: Object,
        default: () => ({}),
    },
});

const form = reactive({
    unidad: null, // Puedes inicializarlo con algún valor predeterminado si lo deseas
    operador: null
});

const fetchEntradas = async (idUnidad, periodo) => {
    let url = '';
    if (periodo.tipo === 'semana') {
        url = route('reportes.entradasSemana', { idUnidad: idUnidad, semana: periodo.valor });
    } else if (periodo.tipo === 'mes' || typeof periodo === 'number') {
        url = route('reportes.entradasMes', { idUnidad: idUnidad, mes: periodo.valor });
    } else if (periodo.tipo === 'anio') {
        url = route('reportes.entradasAnio', { idUnidad: idUnidad, anio: periodo.valor });
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
    // Validar que se haya seleccionado la unidad y el periodo
    if (!idUnidad || !periodoSeleccionado.tipo || !periodoSeleccionado.valor) {
        Swal.fire({
            title: 'Error',
            text: 'Por favor seleccione los parámetros para poder generar el archivo.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

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
        if (reporte.titulo === 'Entradas') {
            if (formato === 'pdf') {
                generarPDF(reporte.titulo, periodo); // Pasa el objeto periodo completo
            } else if (formato === 'excel') {
                generarExcel(reporte.titulo, periodo);
            } else if (formato === 'imprimir') {
                imprimirReporte(reporte.titulo, periodo);
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
    const periodoTexto = periodoSeleccionado.tipo === 'semana'
        ? `Semana ${periodoSeleccionado.valor}`
        : periodoSeleccionado.tipo === 'mes'
        ? months[periodoSeleccionado.valor - 1]
        : periodoSeleccionado.valor;

    const nombreArchivo = `${tipo}-${periodoTexto}.pdf`;
    // Crear una instancia de jsPDF
    const doc = new jsPDF('landscape');
    // Agregar título
    doc.setFontSize(12);
    doc.text(`Reporte de ${tipo} - Período: ${periodoTexto}`, 14, 20);
    // Configurar tabla
    // Configurar columnas y filas
    const columns = [
        { header: 'Ruta', dataKey: 'ruta' },
        { header: 'Fecha', dataKey: 'fecha' },
        { header: 'Numero Unidad', dataKey: 'numeroUnidad' },
        { header: 'Socio/Prestador', dataKey: 'socioPrestador' },
        { header: 'Hora Entrada', dataKey: 'horaEntrada' },
        { header: 'Tipo Entrada', dataKey: 'tipoEntrada' },
        { header: 'Extremo', dataKey: 'extremo' },
        { header: 'Operador', dataKey: 'operador' }
    ];
    // Formatear datos para jsPDF
    const rows = entradas.value.map(entry => ({
        ruta: entry.ruta?.nombreRuta || 'N/A',
        fecha: entry.created_at ? new Date(entry.created_at).toLocaleDateString() : 'N/A',
        numeroUnidad: entry.unidad?.numeroUnidad || 'N/A',
        socioPrestador: entry.directivo ? `${entry.directivo.nombre_completo}` : 'N/A',
        horaEntrada: entry.horaEntrada ? entry.horaEntrada.substring(0, 5) : 'N/A',
        tipoEntrada: entry.tipoEntrada || "Tarde",//Agregué retardo
        extremo: entry.extremo || 'N/A',
        operador: entry.operador ? `${entry.operador.nombre_completo}` : 'N/A'
    }));
    // Agregar la tabla al PDF
    doc.autoTable({
        head: [columns.map(col => col.header)],
        body: rows.map(row => columns.map(col => row[col.dataKey])),
        startY: 24, // Ajustar la posición vertical de la tabla
        styles: {
            fontSize: 10,
            cellPadding: 4,
            halign: 'center'
        },
    });
    // Agregar fecha de creación en el pie de página
    const fechaCreacion = new Date().toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    doc.setFontSize(10);
    doc.text(`Fecha de creación: ${fechaCreacion}`, 14, doc.internal.pageSize.height - 10);

    // Descargar el PDF
    doc.save(nombreArchivo);
};

const generarExcel = (tipo, periodoSeleccionado) => {
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

    const nombreArchivo = `${tipo}-${periodoTexto}.xlsx`;
    // Crear datos para el archivo Excel
    const data = [['Ruta', 'Fecha', 'Numero Unidad', 'Socio/Prestador', 'Hora Entrada', 'Tipo Entrada', 'Extremo', 'Operador']];
    entradas.value.forEach(entry => {
        const ruta = entry.ruta ? entry.ruta.nombreRuta : 'N/A';
        const fecha = entry.created_at ? new Date(entry.created_at).toLocaleDateString() : 'N/A';
        const numeroUnidad = entry.unidad?.numeroUnidad || 'N/A';
        const directivo = entry.directivo ? `${entry.directivo.nombre_completo}` : 'N/A';
        const horaEntrada = entry.horaEntrada ? entry.horaEntrada.substring(0, 5) : 'N/A';
        const tipoEntrada = entry.tipoEntrada;
        const extremo = entry.extremo || 'N/A';
        const operador = entry.operador ? `${entry.operador.nombre_completo}` : 'N/A';
        data.push([ruta, fecha, numeroUnidad, directivo, horaEntrada, tipoEntrada, extremo, operador]);
    });
    // Crear libro de Excel
    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.aoa_to_sheet(data);

    XLSX.utils.book_append_sheet(workbook, worksheet, 'Reporte_Entradas');
    // Guardar el archivo Excel
    XLSX.writeFile(workbook, nombreArchivo);
};

const reportes = [
    { titulo: 'Entradas', periodo: 'semana', periodoSeleccionado: 'semana' },
];

const formatos = [
    { tipo: 'pdf', texto: 'Generar PDF', clase: 'bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded', icono: 'fa-solid fa-file-pdf' },
    { tipo: 'excel', texto: 'Generar Excel', clase: 'bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded', icono: 'fa-solid fa-file-excel' },
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
    <div v-for="reporte in reportes" :key="reporte.titulo" class="mb-4 bg-zinc-100 rounded-lg p-4">

        <h3 class="text-lg font-bold ">{{ reporte.titulo }}</h3>
        <div class="bg-gradient-to-r from-cyan-500 to-cyan-500 h-px mb-2"></div>
        <div class="flex flex-wrap gap-4 mb-3">
            <h2 class="font-semibold text-l pt-0">Buscar por: </h2>
            <div>
                <div>
                    <select name="unidad" id="unidad" v-model="form.unidad"
                        class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option disabled select value="">----- Unidad ----- </option>
                        <option value="todas">Todas las unidades</option>
                        <option v-for="carro in unidad" :key="carro.idUnidad" :value="carro.idUnidad">
                            {{ carro.numeroUnidad }}
                        </option>
                    </select>
                </div>
            </div>
            <!-- <h2 class="font-semibold text-l pt-0"> o </h2>
            <div>
                <div>
                    <select name="operador" id="operador" v-model="form.operador"
                        class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="" disabled selected>----------- Operador -----------</option>
                        <option value="todas">Todos los operadores</option>
                        <option v-for="chofer in operador" :key="chofer.idOperador" :value="chofer.idOperador">
                            {{ chofer.nombre_completo }}
                        </option>
                    </select>
                </div>
            </div> -->
        </div>
        <div class="flex flex-wrap gap-4 mb-3">
            <div class="flex flex-wrap space-x-3 mb-2">
                <h2 class="font-semibold text-l pt-0">Periodo: </h2>
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
        </div>
        <div class="flex flex-wrap space-x-3">
            <button v-for="formato in formatos" :key="formato.tipo" :class="formato.clase"
                @click="generarArchivo(reporte, formato.tipo, form.unidad, { tipo: reporte.periodoSeleccionado, valor: reporte.periodoSeleccionado === 'semana' ? semanaSeleccionada : reporte.periodoSeleccionado === 'mes' ? mesSeleccionado : reporte.periodoSeleccionado === 'anio' ? anioSeleccionado : '' })">
                <i :class="formato.icono + ' mr-2 jump-icon'"></i> {{ formato.texto }}
            </button>
        </div>
    </div>
</template>


<style>
.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}
</style>