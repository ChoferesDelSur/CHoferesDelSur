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

// Función para calcular el domingo más reciente
const obtenerDomingoMasReciente = () => {
    const hoy = new Date();
    const diaSemana = hoy.getDay(); // 0 = Domingo, 6 = Sábado
    const diferencia = diaSemana === 0 ? 0 : diaSemana; // Si es domingo hoy, diferencia = 0
    const domingo = new Date(hoy);
    domingo.setDate(hoy.getDate() - diferencia); // Restamos los días necesarios para llegar al domingo
    return domingo.toLocaleDateString(); // Retornamos la fecha en formato legible
};

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

const generarArchivo = async (reporte, formato) => {
    console.log("Estoy en generar Archivo:");
    const domingoMasReciente = obtenerDomingoMasReciente(); // Obtener la fecha del domingo más reciente
    const tituloConFecha = `${reporte.titulo} - ${domingoMasReciente}`; // Incluir la fecha en el título
    
    try {
        const response = await axios.get(route('reporte.multasDominicales')); // Llama a tu endpoint
        entradas.value = response.data; // Asigna los datos a la variable
        console.log("Datos obtenidos de la consulta:", entradas.value);
        if (formato === 'pdf') {
            generarPDF(tituloConFecha); // Pasar el nuevo título con la fecha
        } else if (formato === 'excel') {
            generarExcel(tituloConFecha); // Pasar el nuevo título con la fecha
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
        const rutaLunes = entry.entradaLunesTemprana && entry.entradaLunesTemprana.ruta ? entry.entradaLunesTemprana.ruta.nombreRuta: 'Sin registro';

        return [numeroUnidad, directivo, sabadoEntrada, operadorSabado,rutaSabado, lunesEntrada,operadorLunes, rutaLunes];
    });

    // Añadimos las columnas "Sábado" y "Lunes"
    const headers = [['Numero Unidad', 'Socio/Prestador','Sábado (horaEntrada)', 'Operador (Sábado)','Ruta (Sábado)', 'Lunes (horaEntrada)','Operador (Lunes)','Ruta (Lunes)']];
    doc.autoTable({ head: headers, body: bodyContent, startY: 24 });

    doc.save(`${tipo}.pdf`);
};

const generarExcel = (tipo) => {
    const nombreArchivo = `${tipo}.xlsx`;
    const data = [['Numero Unidad', 'Socio/Prestador','Sábado (horaEntrada)', 'Operador (Sábado)','Ruta (Sábado)', 'Lunes (horaEntrada)','Operador (Lunes)','Ruta (Lunes)','Justificado']];

    const formatHora = (hora) => {
        if (!hora) return 'Sin registro';
        const date = new Date(hora);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };

    entradas.value.forEach(entry => {
        const numeroUnidad = entry.numeroUnidad || 'N/A';
        const directivo = entry.directivo ? `${entry.directivo.nombre_completo}` : 'N/A';
        const sabadoEntrada = entry.entradaSabado ? formatearHora(entry.entradaSabado.horaEntrada) : 'Sin registro';
        const operadorSabado = entry.entradaSabado ? formatearOperador(entry.entradaSabado.operador) : 'Sin registro';
        const rutaSabado = entry.entradaSabado && entry.entradaSabado.ruta ? entry.entradaSabado.ruta.nombreRuta : 'Sin registro';
        const lunesEntrada = entry.entradaLunesTemprana ? formatearHora(entry.entradaLunesTemprana.horaEntrada) : 'Sin registro';
        const operadorLunes = entry.entradaLunesTemprana ? formatearOperador(entry.entradaLunesTemprana.operador) : 'Sin registro';
        const rutaLunes = entry.entradaLunesTemprana && entry.entradaLunesTemprana.ruta ? entry.entradaLunesTemprana.ruta.nombreRuta: 'Sin registro';
        data.push([numeroUnidad, directivo, sabadoEntrada,operadorSabado,rutaSabado, lunesEntrada,operadorLunes,rutaLunes,'']);
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