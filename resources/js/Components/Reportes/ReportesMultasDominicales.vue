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
});

const entradas = ref([]); // Aquí guardamos los datos del reporte

const reportes = [
    { titulo: 'Multas Dominicales' },
];

const formatos = [
    { tipo: 'pdf', texto: 'Generar PDF', clase: 'bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded', icono: 'fa-solid fa-file-pdf' },
    { tipo: 'excel', texto: 'Generar Excel', clase: 'bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded', icono: 'fa-solid fa-file-excel' },
];

const generarArchivo = async (reporte, formato) => {
    console.log("Estoy en generar Archivo:");
    try {
        const response = await axios.get(route('reporte.multasDominicales')); // Llama a tu endpoint
        entradas.value = response.data; // Asigna los datos a la variable
        console.log("Datos obtenidos de la consulta:",entradas.value);
        if (formato === 'pdf') {
            generarPDF(reporte.titulo);
        } else if (formato === 'excel') {
            generarExcel(reporte.titulo);
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

const generarPDF = (tipo) => {
    const doc = new jsPDF('landscape'); // Configuración del documento en modo apaisado
    doc.setFontSize(12);
    doc.text(`Reporte de ${tipo}`, 14, 20);

    const bodyContent = entradas.value.map(entry => {
        const ruta = entry.ruta ? entry.ruta.nombreRuta : 'N/A';
        const fecha = entry.created_at ? new Date(entry.created_at).toLocaleDateString() : 'N/A';
        const numeroUnidad = entry.numeroUnidad || 'N/A';
        const directivo = entry.directivo ? `${entry.directivo.nombre_completo}` : 'N/A';
/*         const horaCorte = entry.horaCorte ? entry.horaCorte.substring(0, 5) : 'N/A';
        const causa = entry.causa || 'N/A';
        const horaRegreso = entry.horaRegreso ? entry.horaRegreso.substring(0, 5) : 'Sin Regreso'; */
        const operador = entry.operador ? `${entry.operador.nombre_completo}` : 'N/A';

        return [ruta, fecha, numeroUnidad, directivo/* , horaCorte, causa, horaRegreso */, operador];
    });

    const headers = [['Ruta', 'Fecha', 'Numero Unidad', 'Socio/Prestador', /* 'Hora Corte', 'Causa', 'Hora Regreso', */ 'Operador']];
    doc.autoTable({ head: headers, body: bodyContent, startY: 24 });

    doc.save(`${tipo}.pdf`);
};

const generarExcel = (tipo) => {
    const nombreArchivo = `${tipo}.xlsx`;
    const data = [['Ruta', 'Fecha', 'Numero Unidad', 'Socio/Prestador', 'Hora Corte', 'Causa', 'Hora Regreso', 'Operador']];

    entradas.value.forEach(entry => {
        const ruta = entry.ruta ? entry.ruta.nombreRuta : 'N/A';
        const fecha = entry.created_at ? new Date(entry.created_at).toLocaleDateString() : 'N/A';
        const numeroUnidad = entry.unidad?.numeroUnidad || 'N/A';
        const directivo = entry.directivo ? `${entry.directivo.nombre_completo}` : 'N/A';
        const horaCorte = entry.horaCorte ? entry.horaCorte.substring(0, 5) : 'N/A';
        const causa = entry.causa || 'N/A';
        const horaRegreso = entry.horaRegreso ? entry.horaRegreso.substring(0, 5) : 'Sin Regreso';
        const operador = entry.operador ? `${entry.operador.nombre_completo}` : 'N/A';
        data.push([ruta, fecha, numeroUnidad, directivo, horaCorte, causa, horaRegreso, operador]);
    });

    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.aoa_to_sheet(data);
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Reporte_Multas');
    XLSX.writeFile(workbook, nombreArchivo);
};


</script>

<template>
    <div v-for="reporte in reportes" :key="reporte.titulo" class="mb-4 bg-zinc-100 rounded-lg p-4">
        <h3 class="text-lg font-bold ">{{ reporte.titulo }}</h3>
        <div class="bg-gradient-to-r from-cyan-500 to-cyan-500 h-px mb-2"></div>
        <div class="flex flex-wrap space-x-3">
            <button v-for="formato in formatos" :key="formato.tipo" :class="formato.clase"
                @click="generarArchivo(reporte, formato.tipo)">
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