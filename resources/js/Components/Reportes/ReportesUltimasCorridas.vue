<script setup>
import Swal from 'sweetalert2';
import { ref, reactive } from 'vue';
import * as XLSX from 'xlsx';
import axios from 'axios';
import jsPDF from 'jspdf';
/* import 'jspdf-autotable'; */

const ultimasCorridas = ref([]);

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
    tipoUltimaCorrida: {
        type: Object,
        default: () => ({}),
    },
    ultimaCorrida: {
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
        url = route('reportes.UCSemana', { idUnidad: idUnidad, semana: periodo.valor });
    } else if (periodo.tipo === 'mes' || typeof periodo === 'number') {
        url = route('reportes.UCMes', { idUnidad: idUnidad, mes: periodo.valor });
    } else if (periodo.tipo === 'anio') {
        url = route('reportes.UCAnio', { idUnidad: idUnidad, anio: periodo.valor });
    }

    try {
        const response = await axios.get(url);
        ultimasCorridas.value = response.data;
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
        if (reporte.titulo === 'Últimas Corridas') {
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

    const doc = new jsPDF({
        orientation: 'landscape',
    });

    // Configurar el título y el encabezado
    doc.setFontSize(12);
    doc.text(`Reporte de ${tipo} - Período: ${periodoTexto}`, 14, 20);

    const bodyContent = ultimasCorridas.value.map(entry => {
        const ruta = entry.ruta ? entry.ruta.nombreRuta : 'N/A';
        const fecha = entry.created_at ? new Date(entry.created_at).toLocaleDateString() : 'N/A';
        const numeroUnidad = entry.unidad?.numeroUnidad || 'N/A';
        const directivo = entry.directivo ? `${entry.directivo.nombre_completo}` : 'N/A';
        const tipoUltimaCorrida = entry.tipo_ultima_corrida ? entry.tipo_ultima_corrida.tipoUltimaCorrida : 'N/A';
        const horaInicioUC = entry.horaInicioUC ? entry.horaInicioUC.substring(0, 5) : 'N/A';
        const horaFinUC = entry.horaFinUC ? entry.horaFinUC.substring(0, 5) : 'N/A';
        const operador = entry.operador ? `${entry.operador.nombre_completo}` : 'N/A';

        return [ruta, fecha, numeroUnidad, directivo, tipoUltimaCorrida, horaInicioUC, horaFinUC, operador];
    });

    // Configurar la tabla
    doc.autoTable({
        startY: 24,
        head: [['Ruta', 'Fecha', 'Numero Unidad', 'Socio/Prestador', 'Tipo Última Corrida', 'Hora Inicio', 'Hora Fin', 'Operador']],
        body: bodyContent,
    });

    // Obtener la fecha actual
    const fechaCreacion = new Date().toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    // Agregar el pie de página
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
        periodoTexto = periodoSeleccionado.valor; // Asumiendo que `anioSeleccionado` ya está en `periodoSeleccionado.valor`
    } else {
        periodoTexto = periodoSeleccionado.valor; // Default case
    }

    const nombreArchivo = `${tipo}-${periodoTexto}.xlsx`;
    // Crear datos para el archivo Excel
    const data = [['Ruta', 'Fecha', 'Numero Unidad', 'Socio/Prestador', 'Tipo Última Corrida', 'Hora Inicio', 'Hora Fin', 'Operador']];
    ultimasCorridas.value.forEach(entry => {
        const ruta = entry.ruta ? entry.ruta.nombreRuta : 'N/A';
        const fecha = entry.created_at ? new Date(entry.created_at).toLocaleDateString() : 'N/A';
        const numeroUnidad = entry.unidad?.numeroUnidad || 'N/A';
        const directivo = entry.directivo ? `${entry.directivo.nombre_completo}` : 'N/A';
        const tipoUltimaCorrida = entry.tipo_ultima_corrida ? entry.tipo_ultima_corrida.tipoUltimaCorrida : 'N/A';
        const horaInicioUC = entry.horaInicioUC ? entry.horaInicioUC.substring(0, 5) : 'N/A';
        const horaFinUC = entry.horaFinUC ? entry.horaFinUC.substring(0, 5) : 'N/A';
        const operador = entry.operador ? `${entry.operador.nombre_completo}` : 'N/A';
        data.push([ruta, fecha, numeroUnidad, directivo, tipoUltimaCorrida, horaInicioUC, horaFinUC, operador]);
    });

    // Crear libro de Excel
    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.aoa_to_sheet(data);

    XLSX.utils.book_append_sheet(workbook, worksheet, 'Reporte_Últimas_Corridas');
    // Guardar el archivo Excel
    XLSX.writeFile(workbook, nombreArchivo);
};

const reportes = [
    { titulo: 'Últimas Corridas', periodo: 'semana', periodoSeleccionado: 'semana' },
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