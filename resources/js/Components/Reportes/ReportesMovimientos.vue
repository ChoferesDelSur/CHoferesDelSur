<script setup>
import Swal from 'sweetalert2';
import { ref, reactive, computed } from 'vue';
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
    directivo: {
        type: Object,
        default: () => ({}),
    },
    movimiento: {
        type: Object,
        default: () => ({}),
    },
    tipoMovimiento: {
        type: Object,
        default: () => ({}),
    },
});
const form = reactive({
    directivo: null,
    movimiento: null,
});

const fetchMovimientos = async (idDirectivo, periodo) => {
    let url = '';
    let valor = periodo.valor.value ? periodo.valor.value : periodo.valor;
    
    let formattedDate = valor;

    // Si el tipo de periodo es 'dia', convierte el valor del formato 'DD/MM/YYYY' a 'DD-MM-YYYY'
    if (periodo.tipo === 'dia' && valor.includes('/')) {
        const [day, month, year] = valor.split('/');
        formattedDate = `${day}-${month}-${year}`;
    }
    if (periodo.tipo === 'semana') {
        url = route('reportes.MovSemana', { idDirectivo: idDirectivo, semana: valor });
    } else if (periodo.tipo === 'mes' || typeof periodo === 'number') {
        url = route('reportes.MovMes', { idDirectivo: idDirectivo, mes: valor });
    } else if (periodo.tipo === 'anio') {
        url = route('reportes.MovAnio', { idDirectivo: idDirectivo, anio: valor });
    } else if (periodo.tipo === 'dia') {
        url = route('reportes.MovDia', { idDirectivo: idDirectivo, dia: formattedDate });
    }
    try {
        const response = await axios.get(url);
        entradas.value = response.data;
    } catch (error) {
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron obtener los datos',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const generarArchivo = async (reporte, formato, idDirectivo, periodoSeleccionado) => {
    // Validar que se haya seleccionado la unidad y el periodo
    if (!idDirectivo || !periodoSeleccionado.tipo || !periodoSeleccionado.valor) {
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
    } else if (reporte.periodoSeleccionado === 'dia') {
        periodo.valor = diaSeleccionado;
    }

    try {
        await fetchMovimientos(idDirectivo, periodo);

        if (reporte.titulo === 'Movimientos') {
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
        console.error("Error al obtener datos:", error);
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron obtener los datos',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const generarPDF = (tipo, periodoSeleccionado) => {
    const doc = new jsPDF({ orientation: 'landscape' });

    // Ajusta el texto del periodo según el tipo
    let periodoTexto;
    if (periodoSeleccionado.tipo === 'semana') {
        periodoTexto = `Semana ${periodoSeleccionado.valor}`;
    } else if (periodoSeleccionado.tipo === 'mes') {
        periodoTexto = months[periodoSeleccionado.valor - 1];
    } else if (periodoSeleccionado.tipo === 'anio') {
        periodoTexto = periodoSeleccionado.valor;
    } else if (periodoSeleccionado.tipo === 'dia') {
        periodoTexto = typeof periodoSeleccionado.valor === 'string'
            ? periodoSeleccionado.valor
            : periodoSeleccionado.valor._value || 'Fecha inválida';
    } else {
        periodoTexto = periodoSeleccionado.valor;
    }

    // Añadir título al PDF
    doc.setFontSize(12);
    doc.text(`Reporte de ${tipo} - Período: ${periodoTexto}`, 14, 20);

    // Filtrar los datos para `Alta` y `Baja`
    const movimientosAlta = entradas.value.filter(entry => entry.estado.idEstado === 1);
    const movimientosBaja = entradas.value.filter(entry => entry.estado.idEstado === 2);

    // Añadir título para la primera tabla (Alta)
    doc.setFontSize(12);
    doc.text('Movimientos de Alta', 14, 30);

    // Crear el contenido de la primera tabla (Alta)
    const tableColumnAlta = [
        'Fecha', 'Movimiento', 'Operador', 'Socio/Prestador', 'Tipo De Movimiento'
    ];

    const tableRowsAlta = movimientosAlta.map(entry => {
        const fecha = entry.fechaMovimiento || 'N/A';
        const movimiento = entry.estado ? entry.estado.estado : 'N/A';
        const operador = entry.operador ? `${entry.operador.nombre_completo}` : 'N/A';
        const directivo = entry.directivo?.nombre_completo || 'N/A';
        const tipoMovimiento = entry.tipo_movimiento?.tipoMovimiento || 'N/A';

        return [fecha, movimiento, operador, directivo, tipoMovimiento];
    });

    // Añadir la primera tabla al PDF
    doc.autoTable({
        head: [tableColumnAlta],
        body: tableRowsAlta,
        startY: 34, // Ajusta la posición inicial de la tabla para dejar espacio para el título
        margin: { top: 30 }
    });

    // Añadir título para la segunda tabla (Baja)
    doc.setFontSize(12);
    doc.text('Movimientos de Baja', 14, doc.autoTable.previous.finalY + 10);

    // Crear el contenido de la segunda tabla (Baja)
    const tableColumnBaja = [
        'Fecha', 'Movimiento', 'Operador', 'Socio/Prestador', 'Tipo De Movimiento'
    ];

    const tableRowsBaja = movimientosBaja.map(entry => {
        const fecha = entry.fechaMovimiento || 'N/A';
        const movimiento = entry.estado ? entry.estado.estado : 'N/A';
        const operador = entry.operador ? `${entry.operador.nombre_completo}` : 'N/A';
        const directivo = entry.directivo?.nombre_completo || 'N/A';
        const tipoMovimiento = entry.tipo_movimiento?.tipoMovimiento || 'N/A';

        return [fecha, movimiento, operador, directivo, tipoMovimiento];
    });

    // Añadir la segunda tabla al PDF
    doc.autoTable({
        head: [tableColumnBaja],
        body: tableRowsBaja,
        startY: doc.autoTable.previous.finalY + 14, // Ajusta la posición inicial de la tabla
        margin: { top: 30 }
    });

    // Añadir espacio para "Elaboró" y "Recibió" en la misma línea
    const posInicio = doc.autoTable.previous.finalY + 25;
    const posElaboroX = 14;
    const posRecibioX = 150; // Ajusta la posición X para "Recibió"

    doc.setFontSize(12);
    doc.text('Elaboró:', posElaboroX, posInicio);
    doc.line(posElaboroX + 20, posInicio + 2, posElaboroX + 130, posInicio + 2); // Línea horizontal para la firma

    doc.text('Recibió:', posRecibioX, posInicio);
    doc.line(posRecibioX + 20, posInicio + 2, posRecibioX + 100, posInicio + 2); // Línea horizontal para la firma

    // Añadir texto debajo de las líneas
    doc.text('Jefa de Departamento de Recursos Humanos', posElaboroX + 30, posInicio + 10); // Texto debajo de la línea "Elaboró"
    doc.text('Departamento de Servicio', posRecibioX + 30, posInicio + 10); // Texto debajo de la línea "Recibió"

    // Añadir footer al PDF
    const fechaCreacion = new Date().toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    doc.setFontSize(10);
    doc.text(`Fecha de creación: ${fechaCreacion}`, 14, doc.internal.pageSize.height - 10);

    // Guardar el PDF
    const nombreArchivo = `${tipo}-${periodoTexto}.pdf`;
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
        periodoTexto = periodoSeleccionado.valor;
    } else if (periodoSeleccionado.tipo === 'dia') {
        // Accede al valor real si es una referencia reactiva
        periodoTexto = typeof periodoSeleccionado.valor === 'string'
            ? periodoSeleccionado.valor
            : periodoSeleccionado.valor._value || 'Fecha inválida';
    } else {
        periodoTexto = periodoSeleccionado.valor;
    }

    const nombreArchivo = `${tipo}-${periodoTexto}.xlsx`;
    // Filtrar los datos para `Alta` y `Baja`
    const movimientosAlta = entradas.value.filter(entry => entry.estado.idEstado === 1);
    const movimientosBaja = entradas.value.filter(entry => entry.estado.idEstado === 2);

    // Crear datos para la primera hoja (Movimientos de Alta)
    const dataAlta = [['Fecha', 'Movimiento', 'Operador', 'Directivo', 'Tipo De Movimiento']];
    movimientosAlta.forEach(entry => {
        const fecha = entry.fechaMovimiento || 'N/A';
        const movimiento = entry.estado ? entry.estado.estado : 'N/A';
        const operador = entry.operador ? `${entry.operador.nombre_completo}` : 'N/A';
        const directivo = entry.directivo?.nombre_completo || 'N/A';
        const tipoMovimiento = entry.tipo_movimiento?.tipoMovimiento || 'N/A';
        dataAlta.push([fecha, movimiento, operador, directivo, tipoMovimiento]);
    });

    // Crear datos para la segunda hoja (Movimientos de Baja)
    const dataBaja = [['Fecha', 'Movimiento', 'Operador', 'Directivo', 'Tipo De Movimiento']];
    movimientosBaja.forEach(entry => {
        const fecha = entry.fechaMovimiento || 'N/A';
        const movimiento = entry.estado ? entry.estado.estado : 'N/A';
        const operador = entry.operador ? `${entry.operador.nombre_completo}` : 'N/A';
        const directivo = entry.directivo?.nombre_completo || 'N/A';
        const tipoMovimiento = entry.tipo_movimiento?.tipoMovimiento || 'N/A';
        dataBaja.push([fecha, movimiento, operador, directivo, tipoMovimiento]);
    });

    // Crear libro de Excel
    const workbook = XLSX.utils.book_new();
    const worksheetAlta = XLSX.utils.aoa_to_sheet(dataAlta);
    const worksheetBaja = XLSX.utils.aoa_to_sheet(dataBaja);

    // Añadir hojas al libro
    XLSX.utils.book_append_sheet(workbook, worksheetAlta, 'Movimientos de Alta');
    XLSX.utils.book_append_sheet(workbook, worksheetBaja, 'Movimientos de Baja');

    // Guardar el archivo Excel
    XLSX.writeFile(workbook, nombreArchivo);
};

const reportes = [
    { titulo: 'Movimientos', periodo: 'dia', periodoSeleccionado: 'dia' },
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
// Función para formatear fechas en formato DD-MM-YYYY
let formatDate = (date) => {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Intl.DateTimeFormat('es-ES', options).format(date);
};
// Fecha actual en formato DD-MM-YYYY
let diaSeleccionado = ref(formatDate(new Date()));
let semanaSeleccionada = 1; // Por defecto, la primera semana
let mesSeleccionado = 1; // Por defecto, enero
let anioSeleccionado = currentYear; // Por defecto, el año actual
// Función para obtener los días de la semana en formato DD-MM-YYYY
const diasSemana = Array.from({ length: 7 }, (_, i) => {
    const dia = new Date();
    // Calcular el lunes de la semana actual
    const dayOfWeek = dia.getDay() || 7;  // Si es domingo (0), lo tratamos como 7
    dia.setDate(dia.getDate() - dayOfWeek + 1 + i); // Ajustar para obtener los días de lunes a domingo
    return formatDate(dia);
});
// Función para obtener los días actuales en formato DD-MM-YYYY
const days = Array.from({ length: 7 }, (_, i) => {
    const dia = new Date();
    const dayOfWeek = dia.getDay() || 7;  // Si es domingo (0), lo tratamos como 7
    dia.setDate(dia.getDate() - dayOfWeek + 1 + i); // Ajustar para obtener los días de lunes a domingo
    return formatDate(dia);
});

const periodos = {
    semana: weeks,
    mes: months,
    anio: years,
    dia: days
};

const reporte = reactive({
    periodoSeleccionado: '',
    semana: null,
    mes: null,
    anio: null,
    dia: null
});
</script>

<template>
    <div v-for="reporte in reportes" :key="reporte.titulo" class="mb-4 bg-zinc-100 rounded-lg p-4">
        <h3 class="text-lg font-bold ">{{ reporte.titulo }}</h3>
        <div class="bg-gradient-to-r from-cyan-500 to-cyan-500 h-px mb-2"></div>
        <div class="flex flex-wrap gap-4 mb-3">
            <h2 class="font-semibold text-l pt-0">Buscar por: </h2>
            <div>
                <div>
                    <select name="directivo" id="directivo" v-model="form.directivo"
                        class="block w-72 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option disabled select value="">--------------- Socio/Prestador --------------- </option>
                        <option value="todas">Todos los socios y prestadores</option>
                        <option v-for="socio in directivo" :key="socio.idDirectivo" :value="socio.idDirectivo">
                            {{ socio.nombre_completo }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-4 mb-3">
            <div class="flex flex-wrap space-x-3 mb-2">
                <h2 class="font-semibold text-l pt-0">Periodo: </h2>
                <select v-model="reporte.periodoSeleccionado"
                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="dia">Día</option>
                    <option value="semana">Semanal</option>
                    <option value="mes">Mensual</option>
                    <option value="anio">Anual</option>
                </select>
                <template v-if="reporte.periodoSeleccionado === 'dia'">
                    <select v-model="diaSeleccionado"
                        class="block w-28 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option v-for="(day, index) in days" :key="index" :value="day">{{ day }}</option>
                    </select>
                </template>
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
                @click="generarArchivo(reporte, formato.tipo, form.directivo, { tipo: reporte.periodoSeleccionado, valor: reporte.periodoSeleccionado === 'dia' ? diaSeleccionado : reporte.periodoSeleccionado === 'semana' ? semanaSeleccionada : reporte.periodoSeleccionado === 'mes' ? mesSeleccionado : reporte.periodoSeleccionado === 'anio' ? anioSeleccionado : '' })">
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