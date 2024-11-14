<script setup>
import Swal from 'sweetalert2';
import { ref, reactive } from 'vue';
import * as XLSX from 'xlsx';
import axios from 'axios';
import jsPDF from 'jspdf';

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    unidad: {
        type: Object,
        default: () => ({}),
    },
    entrada: {
        type: Object,
        default: () => ({}),
    },
    corte: {
        type: Object,
        default: () => ({}),
    },
    operador: {
        type: Object,
        default: () => ({}),
    },
});

const form = reactive({
    unidad: null, // Puedes inicializarlo con algún valor predeterminado si lo deseas
    operador: null
});
console.log("Estoy en componente ReportesMultasDominicales");
const entradas = ref([]); // Aquí guardamos los datos del reporte

// Función para obtener la semana del año
const getWeek = (date) => {
    const d = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const dayNum = d.getDay() || 7;
    d.setDate(d.getDate() + 3 - dayNum);
    const firstThursday = d.getDate();
    d.setMonth(0, 1);
    return Math.ceil(((d.getDate() - firstThursday) / 7) + 1);
};

// Función para obtener el domingo de una semana dada
const obtenerDomingoPorSemana = (semana) => {
    const hoy = new Date();
    const inicioDelAno = new Date(hoy.getFullYear(), 0, 1);
    const dias = (semana - 1) * 7;
    const fechaInicioSemana = new Date(inicioDelAno.setDate(inicioDelAno.getDate() + dias));
    const diaSemana = fechaInicioSemana.getDay();
    const diferencia = diaSemana === 0 ? 0 : diaSemana;
    const domingo = new Date(fechaInicioSemana);
    domingo.setDate(fechaInicioSemana.getDate() - diferencia);
    return domingo.toLocaleDateString(); // Fecha en formato legible
};

let semanaSeleccionada = 1; // Por defecto, la primera semana
console.log("Semana seleccionada:", semanaSeleccionada);
const reportes = [
    { titulo: 'Multas Dominicales' },
];

const formatos = [
    { tipo: 'pdf', texto: 'Generar PDF', clase: 'bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded', icono: 'fa-solid fa-file-pdf' },
    { tipo: 'excel', texto: 'Generar Excel', clase: 'bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded', icono: 'fa-solid fa-file-excel' },
];

const formatearHora = (hora) => {
    if (!hora) return 'Sin registro'; // Si no hay hora, devolvemos "Sin registro"

    // El formato de hora debería ser algo como "08:30:00"
    const [horas, minutos] = hora.split(':'); // Dividimos la cadena en horas, minutos y segundos
    return `${horas}:${minutos}`; // Solo devolvemos horas y minutos
};

const fetchEntradas = async (semana) => {
    const url = route('reporte.multasDominicales', { semana });
    console.log("Url:",url);
    try {
        console.log("Estoy despues del try");
        const response = await axios.get(url);
        entradas.value = Object.values(response.data);  // Convertir el objeto en un array de entradas
        console.log(entradas.value);
    } catch (error) {
        console.error("Error al obtener los datos:", error.response ? error.response.data : error.message);
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron obtener los datos',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const generarArchivo = async (reporte, formato, operador, periodo) => {
    console.log("Estoy en generar archivo");
    if (!semanaSeleccionada) {
        Swal.fire({
            title: 'Error',
            text: 'Por favor seleccione una semana para generar el archivo.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    try {
        await fetchEntradas(semanaSeleccionada);
        if (reporte.titulo === 'Multas Dominicales') {
            if (formato === 'pdf') {
                console.log("Estoy en formato pdf");
                generarPDF(reporte.titulo, semanaSeleccionada);
            } else if (formato === 'excel') {
                console.log("Estoy en generarExcel");
                generarExcel(reporte.titulo, semanaSeleccionada);
            }
        }
    } catch (error) {
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron obtener los datos',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};


const formatearOperador = (operador) => {
    if (!operador) return 'Sin registro'; // Si no hay operador, devolvemos "Sin registro"
    return `${operador.nombre} ${operador.apellidoP} ${operador.apellidoM}`;
};

const generarPDF = (tipo) => {
    const doc = new jsPDF('landscape'); // Configuración del documento en modo apaisado
    doc.setFontSize(12);
    doc.text(`Reporte de ${tipo}`, 14, 20);

    const bodyContent = entradas.value.map(entry => {
        const numeroUnidad = entry.numeroUnidad || 'N/A';
        const directivo = entry.directivo ? `${entry.directivo.nombre_completo}` : 'N/A';
        const sabadoEntrada = entry.entradaSabado ? formatearHora(entry.entradaSabado.horaEntrada) : 'Sin registro';
        const operadorSabado = entry.entradaSabado ? formatearOperador(entry.entradaSabado.operador) : 'Sin registro';
        const rutaSabado = entry.entradaSabado && entry.entradaSabado.ruta ? entry.entradaSabado.ruta.nombreRuta : 'Sin registro';
        const lunesEntrada = entry.entradaLunesTemprana ? formatearHora(entry.entradaLunesTemprana.horaEntrada) : 'Sin registro';
        const operadorLunes = entry.entradaLunesTemprana ? formatearOperador(entry.entradaLunesTemprana.operador) : 'Sin registro';
        const rutaLunes = entry.entradaLunesTemprana && entry.entradaLunesTemprana.ruta ? entry.entradaLunesTemprana.ruta.nombreRuta : 'Sin registro';

        return [numeroUnidad, directivo, sabadoEntrada, operadorSabado, rutaSabado, lunesEntrada, operadorLunes, rutaLunes];
    });

    const headers = [['Numero Unidad', 'Socio/Prestador', 'Sábado (horaEntrada)', 'Operador (Sábado)', 'Ruta (Sábado)', 'Lunes (horaEntrada)', 'Operador (Lunes)', 'Ruta (Lunes)']];
    doc.autoTable({ head: headers, body: bodyContent, startY: 24 });

    doc.save(`${tipo}.pdf`);
};

const generarExcel = (tipo, semanaSeleccionada) => {
    const nombreArchivo = `${tipo}-Semana-${semanaSeleccionada}.xlsx`;
    const data = [['Numero Unidad', 'Socio/Prestador', 'Sábado (horaEntrada)', 'Operador (Sábado)', 'Ruta (Sábado)', 'Lunes (horaEntrada)', 'Operador (Lunes)', 'Ruta (Lunes)', 'Justificado']];
    console.log("en generarExcel:", entradas.value);
    entradas.value.forEach(entry => {
        const numeroUnidad = entry.numeroUnidad || 'N/A';
        const directivo = entry.directivo ? `${entry.directivo.nombre_completo}` : 'N/A';
        const sabadoEntrada = entry.entradaSabado ? formatearHora(entry.entradaSabado.horaEntrada) : 'Sin registro';
        const operadorSabado = entry.entradaSabado ? formatearOperador(entry.entradaSabado.operador) : 'Sin registro';
        const rutaSabado = entry.entradaSabado && entry.entradaSabado.ruta ? entry.entradaSabado.ruta.nombreRuta : 'Sin registro';
        const lunesEntrada = entry.entradaLunesTemprana ? formatearHora(entry.entradaLunesTemprana.horaEntrada) : 'Sin registro';
        const operadorLunes = entry.entradaLunesTemprana ? formatearOperador(entry.entradaLunesTemprana.operador) : 'Sin registro';
        const rutaLunes = entry.entradaLunesTemprana && entry.entradaLunesTemprana.ruta ? entry.entradaLunesTemprana.ruta.nombreRuta : 'Sin registro';
        data.push([numeroUnidad, directivo, sabadoEntrada, operadorSabado, rutaSabado, lunesEntrada, operadorLunes, rutaLunes, '']);
    });

    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.aoa_to_sheet(data);
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Reporte_Multas');
    XLSX.writeFile(workbook, nombreArchivo);
};

// Definir semanas
const weeks = Array.from({ length: 52 }, (_, i) => i + 1);
</script>

<template>
    <div v-for="reporte in reportes" :key="reporte.titulo" class="mb-4 bg-zinc-100 rounded-lg p-4">
        <h3 class="text-lg font-bold ">{{ reporte.titulo }}</h3>
        <div class="bg-gradient-to-r from-cyan-500 to-cyan-500 h-px mb-2"></div>

        <div class="flex flex-wrap gap-4 mb-3">
            <div class="flex flex-wrap space-x-3 mb-2">
                <h2 class="font-semibold text-l pt-0">Semana: </h2>
                <select v-model="semanaSeleccionada"
                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 p-2">
                    <option v-for="week in weeks" :key="week" :value="week">Semana {{ week }}</option>
                </select>
            </div>
        </div>

        <!-- <div class="flex flex-wrap space-x-3">
            <button v-for="formato in formatos" :key="formato.tipo" :class="formato.clase"
                @click="generarArchivo(reporte, formato.tipo)">
                <i :class="formato.icono"></i> {{ formato.texto }}
            </button>
        </div> -->
        <div class="flex flex-wrap space-x-3">
            <button v-for="formato in formatos" :key="formato.tipo" :class="formato.clase"
                @click="generarArchivo(reporte, formato.tipo, form.unidad, { tipo: reporte.periodoSeleccionado, valor: reporte.periodoSeleccionado === 'semana' ? semanaSeleccionada /* : reporte.periodoSeleccionado === 'mes' ? mesSeleccionado : reporte.periodoSeleccionado === 'anio' ? anioSeleccionado  */ : '' })">
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