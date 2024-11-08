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
});

const form = reactive({
    unidad: null, // Puedes inicializarlo con algún valor predeterminado si lo deseas
    operador: null
});
const fetchEntradas = async (semana) => {
    const url = route('reporte.opSinTrabajar', { semana });

    try {
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
    console.log("Semana seleccionada:", semanaSeleccionada); // Agregar un log para verificar el valor

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
        if (reporte.titulo === 'Operadores Sin Trabajar') {
            if (formato === 'pdf') {
                generarPDF(reporte.titulo, semanaSeleccionada);
            } else if (formato === 'excel') {
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


// Función para obtener el total de días según el periodo seleccionado
const obtenerTotalDias = (tipo, valor) => {
    switch (tipo) {
        case 'semana':
            return 7; // Una semana tiene 7 días
        /* case 'mes':
            const month = parseInt(valor, 10); // Convertir a número
            const year = new Date().getFullYear(); // Obtener el año actual
            return new Date(year, month, 0).getDate(); // Número de días del mes y año específicos
        case 'anio':
            return 365; // Suponiendo que todos los años tienen 365 días
        default:
            return 0; */
    }
};

const generarPDF = (tipo, semanaSeleccionada) => {
    const doc = new jsPDF('portrait'); // Orientación de la hoja
    doc.setFontSize(12);
    doc.text(`Reporte de ${tipo} - Semana ${semanaSeleccionada}`, 14, 20);

    // Texto adicional antes de la tabla
    doc.setFontSize(10);
    doc.text("Los operadores que aparecen en el listado no trabajaron ni al menos 1 día durante la semana seleccionada.", 14, 30);

    // Definir las columnas de la tabla
    const columns = [
        { header: 'Nombre del operador', dataKey: 'nombre' },
        { header: 'Última Día Trabajo', dataKey: 'ultimaFechaTrabajo' }
    ];

    // Agregar las filas con datos, incluyendo la última fecha de trabajo
    const rows = entradas.value.map(entry => ({
        nombre: entry.nombre_completo || 'N/A',
        ultimaFechaTrabajo: entry.ultima_fecha_trabajo || 'No ha trabajado'
    }));

    // Generar la tabla en el PDF
    doc.autoTable({
        columns: columns,
        body: rows,
        startY: 40,
        styles: { fontSize: 10 }
    });

    // Agregar la fecha de creación al pie de la página
    const fechaCreacion = new Date().toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    doc.setFontSize(10);
    doc.text(`Fecha de creación: ${fechaCreacion}`, 14, doc.internal.pageSize.height - 10);

    // Guardar el PDF
    const nombreArchivo = `${tipo}-Semana-${semanaSeleccionada}.pdf`;
    doc.save(nombreArchivo);
};

const generarExcel = (tipo, semanaSeleccionada) => {
    const nombreArchivo = `${tipo}-Semana-${semanaSeleccionada}.xlsx`;

    // Encabezados del reporte
    const data = [['Nombre del Operador', 'Último Día Trabajado']];  // Nueva columna para la fecha

    // Agregar cada entrada con el nombre completo y la última fecha de trabajo
    entradas.value.forEach(entry => {
        const nombreCompleto = entry.nombre_completo || 'N/A';
        const ultimoDiaTrabajado = entry.ultimo_dia_trabajado || 'No ha trabajado';  // Agregar la fecha del último día trabajado
        data.push([nombreCompleto, ultimoDiaTrabajado]);  // Incluir la fecha en cada fila
    });

    // Crear el libro de trabajo y la hoja de cálculo
    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.aoa_to_sheet(data);
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Operadores_Sin_Trabajar');

    // Guardar el archivo
    XLSX.writeFile(workbook, nombreArchivo);
};

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

const reportes = [
    { titulo: 'Operadores Sin Trabajar', periodo: 'semana', periodoSeleccionado: 'semana' },
];
</script>

<template>
    <div v-for="reporte in reportes" :key="reporte.titulo" class="mb-4 bg-zinc-100 rounded-lg p-4">

        <h3 class="text-lg font-bold ">{{ reporte.titulo }}</h3>
        <div class="bg-gradient-to-r from-cyan-500 to-cyan-500 h-px mb-2"></div>
        <div class="flex flex-wrap gap-4 mb-3">
            <div class="flex flex-wrap space-x-3 mb-2">
                <h2 class="font-semibold text-l pt-0">Periodo: </h2>
                <select v-model="reporte.periodoSeleccionado"
                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="semana">Semanal</option>
                    <!-- <option value="mes">Mensual</option>
                    <option value="anio">Anual</option> -->
                </select>
                <template v-if="reporte.periodoSeleccionado === 'semana'">
                    <select v-model="semanaSeleccionada"
                        class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option v-for="(week, index) in weeks" :key="index" :value="week">Semana {{ week }}</option>
                    </select>
                </template>
            </div>
        </div>
        <div class="flex flex-wrap space-x-3">
            <button v-for="formato in formatos" :key="formato.tipo" :class="formato.clase"
                @click="generarArchivo(reporte, formato.tipo, form.operador, { tipo: reporte.periodoSeleccionado, valor: reporte.periodoSeleccionado === 'semana' ? semanaSeleccionada /* : reporte.periodoSeleccionado === 'mes' ? mesSeleccionado : reporte.periodoSeleccionado === 'anio' ? anioSeleccionado  */ : '' })">
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