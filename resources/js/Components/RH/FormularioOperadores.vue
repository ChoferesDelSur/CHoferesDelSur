<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, watch, onMounted } from 'vue';
import Modal from '../Modal.vue';
import axios from 'axios'; // Importar Axios

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    operador: {
        type: Object,
        default: () => ({}),
    },
    tipoOperador: {
        type: Object,
        default: () => ({}),
    },
    estado: {
        type: Object,
        default: () => ({}),
    },
    directivo: {
        type: Object,
        default: () => ({}),
    },
    empresa: {
        type: Object,
        default: () => ({}),
    },
    convenioPago: {
        type: Object,
        default: () => ({}),
    },
    incapacidad: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})

const entidades = ref([]);
const municipios = ref([]);
const asentamientos = ref([]);
const anios = ref([]);

const emit = defineEmits(['close']);

// Inicialización de `incapacidad`
const incapacidad = ref({
    motivo: '',
    numeroDias: '',
    fechaInicio: '',
    fechaFin: '',
});

const form = useForm({
    idOperador: props.operador.idOperador,
    nombre: props.operador.nombre,
    apellidoP: props.operador.apellidoP,
    apellidoM: props.operador.apellidoM,
    fechaNacimiento: props.operador.fechaNacimiento,
    edad: props.operador.edad,
    CURP: props.operador.CURP,
    RFC: props.operador.RFC,
    numTelefono: props.operador.numTelefono,
    NSS: props.operador.NSS,
    direccion: props.operador.idDireccion,
    tipoOperador: props.operador.idTipoOperador,
    estado: props.operador.idEstado,
    directivo: props.operador.idDirectivo,
    codigoPostal: props.operador.codigoPos,
    entidad: props.operador.idEntidad,
    municipio: props.operador.idMunicipio,
    asentamiento: props.operador.idAsentamiento,
    calle: props.operador.calle,
    numero: props.operador.numero,
    idDireccion: props.operador.idDireccion,
    numLicencia: props.operador.numLicencia,
    vigenciaLicencia: props.operador.vigenciaLicencia,
    numINE: props.operador.numINE,
    vigenciaINE: props.vigenciaINE,
    ultimoContrato: props.operador.ultimoContrato,
    antiguedad: props.operador.antiguedad,
    fechaAlta: props.operador.fechaAlta,
    fechaBaja: props.operadorfechaBaja,
    empresaa: props.operador.idEmpresa,
    constanciaSF: props.operador.constanciaSF,
    convenioPa: props.operador.idConvenioPago,
    cursoSemovi: props.operador.cursoSemovi,
    cursoPsicologico: props.operador.cursoPsicologico,
    constanciaSemovi: props.operador.constanciaSemovi,
    idIncapacidad: props.incapacidad.idIncapacidad,
    motivo: props.incapacidad.motivo,
    numeroDias: props.incapacidad.numeroDias,
    fechaInicio: props.incapacidad.fechaInicio,
    fechaFin: props.incapacidad.fechaFin,
});

watch(() => props.operador, async (newVal) => {
    form.idOperador = newVal.idOperador;
    form.nombre = newVal.nombre;
    form.apellidoP = newVal.apellidoP;
    form.apellidoM = newVal.apellidoM;
    form.fechaNacimiento = newVal.fechaNacimiento;
    form.edad = newVal.edad;
    form.CURP = newVal.CURP;
    form.telefono = newVal.telefono;
    form.NSS = newVal.NSS;
    form.domicilio = newVal.idDomicilio;
    form.tipoOperador = newVal.idTipoOperador;
    form.estado = newVal.idEstado;
    form.directivo = newVal.idDirectivo;

    // Ajusta los campos de incapacidad si el estado es Incapacidad
    if (newVal.idEstado === 3) { // idEstado para Incapacidad
        incapacidad.value.idOperador = newVal.idOperador;
    } else {
        incapacidad.value = {
            motivo: '',
            numeroDias: '',
            fechaInicio: '',
            fechaFin: '',
        };
    }
}, { deep: true }
);

// Watcher para numeroDias y fechaInicio
watch([() => form.numeroDias, () => form.fechaInicio], ([newNumeroDias, newFechaInicio]) => {
    if (newNumeroDias && newFechaInicio) {
        const startDate = new Date(newFechaInicio);
        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + parseInt(newNumeroDias, 10));
        form.fechaFin = endDate.toISOString().split('T')[0]; // Formato YYYY-MM-DD
    }
});

// Variables para los mensajes de validación
const nombreError = ref('');
const apellidoPError = ref('');
const apellidoMError = ref('');
const tipoOperadorError = ref('');
const estadoError = ref('');
const directivoError = ref('');
const fechaNError = ref('');
const edadError = ref('');
const CURPError = ref('');
const RFCError = ref('');
const telError = ref('');
const NSSError = ref('');
const NLicenciaError = ref('');
const VigenciaLError = ref('');
const NumINEError = ref('');
const vigenciaINEError = ref('');
const UltimoConError = ref('');
const AntiguedadError = ref('');
const FechaAltaError = ref('');
const EmpresaError = ref('');
const convenioPagoError = ref('');
const codigoPError = ref('');
const calleError = ref('');
const numeroCError = ref('');
const motivoError = ref('');
const numDiasError = ref('');
const fechaInicioError = ref('');
const fechaFinError = ref('');

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

// Validación de cadenas no vacias
const validateStringNotEmpty = (value) => {
    return typeof value === 'string' && value.trim() !== '';
}

// Validación de los select 
const validateSelect = (selectedValue) => {
    if (selectedValue == undefined) {
        return false;
    }
    return true;
};

// Validación de fecha de nacimiento
const validateFechaNacimiento = (fechaNacimiento) => {
    const fechaNac = new Date(fechaNacimiento);
    const fechaActual = new Date();
    return fechaNac < fechaActual;
}

// Validación de cadenas no vacías y solo números enteros
const validateIntegerField = (value) => {
    // Verificar que el valor no sea null o undefined
    if (value == null) {
        return false;
    }

    // Verificar que el valor sea un número entero
    return Number.isInteger(value) && value >= 0;
}

const validateEdad = (edad) => {
    // Primero, verificar que el valor no esté vacío
    if (typeof edad !== 'string' || edad.trim() === '') {
        return false;
    }
    // Luego, verificar que el valor sea un número entero
    const integerRegex = /^\d+$/; // Regex para números enteros positivos
    return integerRegex.test(edad.trim());
}

// Función para validar CURP
const validateCURP = (CURP) => {
    // Validación con expresión regular
    // Devuelve true si la CURP es válida, de lo contrario, devuelve false
    return /^[A-Z]{4}\d{6}[HM]{1}[A-Z]{5}[A-Z0-9]{1}\d{1}$/.test(CURP);
}

// Función para validar que el campo no esté vacío
const validateRFC = (RFC) => {
    // Verificar que el campo no esté vacío
    return RFC.trim() !== '';
}

// Validación de numero telefonico a traves de espresion regular
const validateNumeroTelefono = (numTelefono) => {
    const numeroTExpReg = /^\d{10}$/;
    return numeroTExpReg.test(numTelefono);
};

// Validación de codigo postal a traves de expresion regular
const validateCodigoPostal = (codigoPostal) => {
    const codigoPostalRegex = /^\d{5}$/;
    return codigoPostalRegex.test(codigoPostal);
};

// Validacion de codigo postal coincida con el asentamiento
const validatePostal = async (asentamiento) => {
    const infoAsentamiento = await axios.get(route('infoAsentamiento', asentamiento));
    const codPosAsentamiento = infoAsentamiento.data.codPos;
    return codPosAsentamiento != form.codigoPostal ? false : true;
}
// Validación del NSS
const validateNSS = (NSS) => {
    // Verificar que el NSS no esté vacío y sea una cadena
    if (typeof NSS !== 'string' || NSS.trim() === '') {
        return false;
    }
    // El NSS debe tener como máximo 11 caracteres
    if (NSS.length > 11) {
        return false;
    }
    // El NSS debe estar compuesto solo por números
    const nssRegex = /^\d{1,11}$/;
    if (!nssRegex.test(NSS)) {
        return false;
    }
    return true;
};

// Validación de la fecha de vigencia de la licencia
const validateLicenseExpiryDate = (vigenciaLicencia) => {
    // Verificar que el campo no sea null o undefined
    if (vigenciaLicencia === null || vigenciaLicencia === undefined) {
        return false;
    }
    // Convertir cadenas de texto a Date si es necesario
    if (typeof vigenciaLicencia === 'string' || vigenciaLicencia instanceof String) {
        vigenciaLicencia = new Date(vigenciaLicencia);
    }
    // Verificar que el valor sea una instancia de Date
    if (!(vigenciaLicencia instanceof Date)) {
        return false;
    }
    // Verificar que la fecha sea válida
    if (isNaN(vigenciaLicencia.getTime())) {
        return false;
    }
    // Verificar que la fecha de vigencia no esté en el pasado
    const currentDate = new Date();
    if (vigenciaLicencia < currentDate) {
        return false;
    }
    return true;
};

// Validación de la fecha de vigencia de la licencia
const validateINE = (vigenciaINE) => {
    // Verificar que el campo no esté vacío
    if (!vigenciaINE) {
        return false;
    }

    // Obtener el año actual
    const currentYear = new Date().getFullYear();

    // Verificar que el año de vigencia no esté en el pasado
    if (parseInt(vigenciaINE) < currentYear) {
        return false;
    }
    return true;
};

// Validación del número de INE
const validateNumINE = (numINE) => {
    // Verificar que el campo no esté vacío y sea una cadena
    if (typeof numINE !== 'string' || numINE.trim() === '') {
        return false;
    }

    // El número de INE debe tener exactamente 13 caracteres
    return numINE.length === 13;
};

const validateDateField = (dateValue) => {
    // Verificar que el campo no sea null o undefined
    if (dateValue === null || dateValue === undefined) {
        return false;
    }
    // Convertir cadenas de texto a Date si es necesario
    if (typeof dateValue === 'string' || dateValue instanceof String) {
        dateValue = new Date(dateValue);
    }
    // Verificar que el valor sea una instancia de Date
    if (!(dateValue instanceof Date)) {
        return false;
    }
    // Verificar que la fecha sea válida
    if (isNaN(dateValue.getTime())) {
        return false;
    }
    return true;
};

const save = async () => {
    // Validación de los campos del formulario principal
    nombreError.value = validateStringNotEmpty(form.nombre) ? '' : 'Ingrese el nombre(s)';
    apellidoPError.value = validateStringNotEmpty(form.apellidoP) ? '' : 'Ingrese el apellido paterno';
    apellidoMError.value = validateStringNotEmpty(form.apellidoM) ? '' : 'Ingrese el apellido materno';
    tipoOperadorError.value = validateSelect(form.tipoOperador) ? '' : 'Seleccione el tipo de operador';
    fechaNError.value = validateFechaNacimiento(form.fechaNacimiento) ? '' : 'Fecha de nacimiento no válido';
    edadError.value = validateIntegerField(form.edad) ? '' : 'Edad no válido';
    CURPError.value = validateCURP(form.CURP) ? '' : 'CURP no válido';
    RFCError.value = validateRFC(form.RFC) ? '' : 'RFC no válido';
    telError.value = validateNumeroTelefono(form.numTelefono) ? '' : 'Número de telefono no válido';
    NSSError.value = validateNSS(form.NSS) ? '' : 'NSS no válido';
    NLicenciaError.value = validateStringNotEmpty(form.numLicencia) ? '' : 'Número de licencia no válido';
    VigenciaLError.value = validateLicenseExpiryDate(form.vigenciaLicencia) ? '' : 'Vigencia no válido';
    NumINEError.value = validateNumINE(form.numINE) ? '' : 'Número de INE no válido';
    UltimoConError.value = validateDateField(form.ultimoContrato) ? '' : 'Fecha de último contrato no válido';
    AntiguedadError.value = validateIntegerField(form.antiguedad) ? '' : 'Ingrese los años de antiguedad';
    FechaAltaError.value = validateDateField(form.fechaAlta) ? '' : 'Fecha de alta no válido';
    EmpresaError.value = validateSelect(form.empresaa) ? '' : 'Seleccione la empresa';
    convenioPagoError.value = validateSelect(form.convenioPa) ? '' : 'Seleccione el convenio de pago';
    codigoPError.value = validateCodigoPostal(form.codigoPostal) ? '' : 'Ingrese el codigo postal';
    calleError.value = validateStringNotEmpty(form.calle) ? '' : 'Ingrese el nombre de la calle';
    estadoError.value = validateSelect(form.estado) ? '' : 'Seleccione el estado';
    numeroCError.value = validateIntegerField(form.numero) ? '' : 'Número de casa no válido';
    directivoError.value = validateSelect(form.directivo) ? '' : 'Seleccione para que socio trabaja';
    vigenciaINEError.value = validateINE(form.vigenciaINE) ? '' : 'Vigencia de INE no válido';

    // Validación adicional para los campos de incapacidad si el estado es Incapacidad
    if (form.estado === 3) { // idEstado para Incapacidad
        motivoError.value = validateStringNotEmpty(form.motivo) ? '' : 'Ingrese el motivo de la incapacidad';
        numDiasError.value = validateIntegerField(form.numeroDias) ? '' : 'Ingrese el número de días de incapacidad';
        fechaInicioError.value = validateDateField(form.fechaInicio) ? '' : 'Ingrese la fecha de inicio de incapacidad';
        fechaFinError.value = validateDateField(form.fechaFin) ? '' : 'Ingrese la fecha de fin de incapacidad';
    }
    // Verificar si hay algún error antes de enviar el formulario
    if (
        nombreError.value || apellidoPError.value || apellidoMError.value || tipoOperadorError.value ||
        fechaNError.value || edadError.value || CURPError.value || RFCError.value || telError.value ||
        NSSError.value || NLicenciaError.value || VigenciaLError.value || NumINEError.value || UltimoConError.value ||
        AntiguedadError.value || FechaAltaError.value || EmpresaError.value || convenioPagoError.value ||
        codigoPError.value || calleError.value || estadoError.value || numeroCError.value || directivoError.value ||
        vigenciaINEError.value || (form.estado === 3 && (motivoError.value || numDiasError.value || fechaInicioError.value || fechaFinError.value))
    ) {
        return;
    }

    // Si no hay errores, enviar el formulario
    form.post(route('rh.addOperador'), {
        data: {
            ...form.data,
            ...(form.estado === 3 ? { incapacidad: incapacidad.value } : {}), // Incluye datos de incapacidad si aplica
        },
        onSuccess: () => {
            close();
            // Limpia los errores y campos si es necesario
            nombreError.value = '';
            apellidoPError.value = '';
            apellidoMError.value = '';
            tipoOperadorError.value = '';
            estadoError.value = '';
            directivoError.value = '';
            motivoError.value = '';
            numDiasError.value = '';
            fechaInicioError.value = '';
            fechaFinError.value = '';
            /*           form.nombre = '';
                      form.apellidoP = '';
                      form.apellidoM = ''; */
            //form.tipoOperador = '';
            fechaNError.value = '';
            edadError.value = '';
            CURPError.value = '';
            RFCError.value = '';
            telError.value = '';
            NSSError.value = '';
            NLicenciaError.value = '';
            VigenciaLError.value = '';
            NumINEError.value = '';
            UltimoConError.value = '';
            AntiguedadError.value = '';
            FechaAltaError.value = '';
            EmpresaError.value = '';
            convenioPagoError.value = '';
            codigoPError.value = '';
            calleError.value = '';
            /* estadoError.value = ''; */
            numeroCError.value = '';
            directivoError.value = '';
            vigenciaINEError.value = '';
            /* form.estado = ''; */
            motivoError.value = '';
            numDiasError.value = '';
            fechaInicioError.value = '';
            fechaFinError.value = '';
            incapacidad.value = {
                motivo: '',
                numeroDias: '',
                fechaInicio: '',
                fechaFin: '',
            }; // Limpia los campos de incapacidad
        }
    });
};

// Constante con función para cargar los municipios dependiendo de
// la selección del select estado
const cargarMunicipios = async () => {
    try {
        var idEntidad = form.entidad;
        const response = await axios.get(route('consMunicipiosXIdEstado', idEntidad));
        municipios.value = response.data;
    } catch (error) {
        console.error('Error al obtener municipios:', error);
    }
};

// Constante con función para cargar los asentamientos dependiendo de
// la selección del select municipio
const cargarAsentamientos = async () => {
    try {
        var idMunicipio = form.municipio;
        const response = await axios.get(route('consAsentamientosXIdMunicipio', idMunicipio));
        asentamientos.value = await response.data;
    } catch (error) {
        console.error('Error al obtener asentamientos:', error);
    }
};

// Constante con función para cargar los datos de estado, municipio
// y asentamientos dependiendo del codigo postal que se coloque en
// el formulario
const buscarDatosXCodigoPostal = async () => {
    try {
        var codigoPostal = form.codigoPostal;
        if (validateCodigoPostal(codigoPostal)) {
            const response = await axios.get(route('consDatosXCodigoPostal', codigoPostal));
            const datos = response.data;
            if (datos.length <= 0) {
                codigoPError.value = 'Codigo postal no existente';
                return;
            }
            if (datos.entidad) {
                form.entidad = await datos.entidad.idEntidad;
                await cargarMunicipios();

            }
            if (datos.municipio) {
                form.municipio = await datos.municipio.idMunicipio;
                await cargarAsentamientos();
                form.asentamiento = asentamientos.value[0]?.idAsentamiento;
            }
            codigoPError.value = '';
        } else {
            codigoPError.value = 'Ingrese el codigo postal completo';
        }
    } catch (error) {
        console.error('Error al obtener datos por código postal:', error);
    }
};

const generarAnios = () => {
    const anioActual = new Date().getFullYear();
    const aniosArray = [];
    for (let i = anioActual; i <= anioActual + 20; i++) {
        aniosArray.push(i);
    }
    anios.value = aniosArray;
};

// Funcion onMounted para al rellenar los datos del select estado
onMounted(async () => {
    generarAnios();
    try {
        const response = await axios.get(route('consEstados'));
        entidades.value = response.data;
        form.entidad = entidades.value[19]?.idEntidad;
        await cargarMunicipios();
        form.municipio = municipios.value[0]?.idMunicipio;
        await cargarAsentamientos();
        form.asentamiento = asentamientos.value[0]?.idAsentamiento;
    } catch (error) {
        console.log("Error generado en onMounted: " + error);
    }
});

const calcularEdad = () => {
    const fechaNac = new Date(form.fechaNacimiento);
    const hoy = new Date();
    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mesDiferencia = hoy.getMonth() - fechaNac.getMonth();

    if (mesDiferencia < 0 || (mesDiferencia === 0 && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }

    form.edad = edad;
};

</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="save">
                <div class="pb-0">
                    <h1 class="text-2xl font-semibold leading-7 text-gray-900">{{ title }}</h1>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Rellene el formulario para poder registrar a un
                        nuevo operador. Los campos con <span class="text-red-500">*</span> son obligatorios.
                    </p>
                    <div class="p-3 border-b border-gray-300">
                        <!-- Linea que divide la seccion de informacion personal -->
                        <h2 class="text-md font-semibold mb-1">Información Personal</h2>
                        <div class="flex flex-wrap"> <!-- Para que se ordenen los campos -->
                            <div class="md:col-span-2">
                                <div class="sm:col-span-2" hidden> <!-- Definir el tamaño del cuadro de texto -->
                                    <label for="idOperador"
                                        class="block text-sm font-medium leading-6 text-gray-900">idOperador</label>
                                    <div class="mt-1">
                                        <input type="number" name="idOperador" v-model="form.idOperador"
                                            placeholder="Ingrese id del operador" :id="'idOperador' + op"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-2 px-2"> <!-- Definir el tamaño del cuadro de texto -->
                                <label for="apellidoP"
                                    class="block text-sm font-medium leading-6 text-gray-900">Apellido
                                    Paterno <span class="text-red-500">*</span></label>
                                <div class="mt-2"><!-- Espacio entre titulo y cuadro de texto -->
                                    <input type="text" name="apellidoP" :id="'apellidoP' + op" v-model="form.apellidoP"
                                        placeholder="Ingrese el apellido paterno"
                                        class="block w-56 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="apellidoPError != ''" class="text-red-500 text-xs mt-1">{{ apellidoPError }}
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="apellidoM"
                                    class="block text-sm font-medium leading-6 text-gray-900">Apellido
                                    Materno <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="text" name="apellidoM" :id="'apellidoM' + op" v-model="form.apellidoM"
                                        placeholder="Ingrese el apellido materno"
                                        class="block w-56 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="apellidoMError != ''" class="text-red-500 text-xs mt-1">{{ apellidoMError }}
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="nombre" class="block text-sm font-medium leading-6 text-gray-900">Nombre(s)
                                    <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="text" name="nombre" :id="'nombre' + op" v-model="form.nombre"
                                        placeholder="Ingrese el nombre"
                                        class="block w-64 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="nombreError != ''" class="text-red-500 text-xs mt-1">{{ nombreError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="fechaNacimiento"
                                    class="block text-sm font-medium leading-6 text-gray-900">Fecha
                                    de Nacimiento <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="date" name="fechaNacimiento" :id="'fechaNacimiento' + op"
                                        v-model="form.fechaNacimiento" @change="calcularEdad"
                                        placeholder="Ingrese la fecha de nacimiento"
                                        class="block w-38 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="fechaNError != ''" class="text-red-500 text-xs mt-1">{{
                                    fechaNError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="edad" class="block text-sm font-medium leading-6 text-gray-900">Edad <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="number" name="edad" :id="'edad' + op" v-model="form.edad"
                                        placeholder="Años "
                                        class="block w-20 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="edadError != ''" class="text-red-500 text-xs mt-1">{{ edadError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="CURP" class="block text-sm font-medium leading-6 text-gray-900">CURP <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="text" name="CURP" :id="'CURP' + op" v-model="form.CURP"
                                        placeholder="Ingrese la CURP"
                                        class="block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="CURPError != ''" class="text-red-500 text-xs mt-1">{{ CURPError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="RFC" class="block text-sm font-medium leading-6 text-gray-900">RFC <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="text" name="RFC" :id="'RFC' + op" v-model="form.RFC"
                                        placeholder="Ingrese la RFC"
                                        class="block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="RFCError != ''" class="text-red-500 text-xs mt-1">{{ RFCError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="numTelefono"
                                    class="block text-sm font-medium leading-6 text-gray-900">Teléfono
                                    <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="text" name="numTelefono" :id="'numTelefono' + op"
                                        v-model="form.numTelefono" placeholder="Ingrese número de telefono"
                                        class="block w-28 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="telError != ''" class="text-red-500 text-xs mt-1">{{ telError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="NSS" class="block text-sm font-medium leading-6 text-gray-900">NSS <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="text" name="NSS" :id="'NSS' + op" v-model="form.NSS"
                                        placeholder="Ingrese el NSS"
                                        class="block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="NSSError != ''" class="text-red-500 text-xs mt-1">{{ NSSError }}</div>
                            </div>
                        </div><!-- donde termina el div para ordenar campos -->
                    </div><!-- Donde termina el div de la seccion informacion personal -->

                    <div class="p-3 border-b border-gray-300"><!-- Div que divide la seccion Informacion Laboral-->
                        <h2 class="text-md font-semibold mb-1">Información Laboral</h2>
                        <div class="flex flex-wrap"><!-- Para ordenar los campos -->
                            <div class="sm:col-span-2 px-2">
                                <label for="tipoOperador" class="block text-sm font-medium leading-6 text-gray-900">Tipo
                                    de
                                    operador <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <select name="tipoOperador" :id="'tipoOperador' + op" v-model="form.tipoOperador"
                                        placeholder="Seleccione el tipo de operador"
                                        class="block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="" disabled selected>Seleccione tipo de operador</option>
                                        <option v-for="tOperador in tipoOperador" :key="tOperador.idTipoOperador"
                                            :value="tOperador.idTipoOperador">
                                            {{ tOperador.tipOperador }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="tipoOperadorError != ''" class="text-red-500 text-xs mt-1">{{
                                    tipoOperadorError
                                    }}
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="estado" class="block text-sm font-medium leading-6 text-gray-900">Estado del
                                    Operador <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <select name="estado" :id="'estado' + op" v-model="form.estado"
                                        placeholder="Seleccione el tipo de estado"
                                        class="block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="" disabled selected>Seleccione estado</option>
                                        <option v-for="est in estado" :key="est.idEstado" :value="est.idEstado">
                                            {{ est.estado }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="estadoError != ''" class="text-red-500 text-xs mt-1">{{ estadoError }}</div>
                            </div>
                            <!-- Campos adicionales para Incapacidad -->
                            <div v-if="form.estado == 3" class="mb-2">
                                <div class="p-3 border-b border-gray-300">
                                    <h3 class="text-md font-semibold mb-1">Información de Incapacidad</h3>
                                    <div class="flex flex-wrap mt-4">
                                        <div class="md:col-span-2">
                                            <div class="sm:col-span-2" hidden>
                                                <!-- Definir el tamaño del cuadro de texto -->
                                                <label for="idIncapacidad"
                                                    class="block text-sm font-medium leading-6 text-gray-900">idIncapacidad</label>
                                                <div class="mt-1">
                                                    <input type="number" name="idIncapacidad" v-model="form.idIncapacidad"
                                                        placeholder="Ingrese id de la incapacidad" :id="'idIncapacidad' + op"
                                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="sm:col-span-2 px-2">
                                            <label for="motivo"
                                                class="block text-sm font-medium leading-6 text-gray-900">Motivo
                                                <span class="text-red-500">*</span></label>
                                            <div class="mt-2">
                                                <input type="text" name="motivo" v-model="form.motivo"
                                                    placeholder="Ingrese el motivo de la incapacidad"
                                                    class="block w-64 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            </div>
                                            <div v-if="motivoError != ''" class="text-red-500 text-xs mt-1">{{
                                                motivoError
                                            }}
                                            </div>
                                        </div>
                                        <div class="sm:col-span-2 px-2">
                                            <label for="numeroDias"
                                                class="block text-sm font-medium leading-6 text-gray-900">Número
                                                de Días <span class="text-red-500">*</span></label>
                                            <div class="mt-2">
                                                <input type="number" name="numeroDias" v-model="form.numeroDias"
                                                    placeholder="Ingrese el número de días"
                                                    class="block w-32 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            </div>
                                            <div v-if="numDiasError != ''" class="text-red-500 text-xs mt-1">{{
                                                numDiasError
                                            }}
                                            </div>
                                        </div>
                                        <div class="sm:col-span-2 px-2">
                                            <label for="fechaInicio"
                                                class="block text-sm font-medium leading-6 text-gray-900">Fecha
                                                de Inicio <span class="text-red-500">*</span></label>
                                            <div class="mt-2">
                                                <input type="date" name="fechaInicio" v-model="form.fechaInicio"
                                                    placeholder="Ingrese la fecha de inicio"
                                                    class="block w-48 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            </div>
                                            <div v-if="fechaInicioError != ''" class="text-red-500 text-xs mt-1">{{
                                                fechaInicioError }}</div>
                                        </div>
                                        <div class="sm:col-span-2 px-2">
                                            <label for="fechaFin"
                                                class="block text-sm font-medium leading-6 text-gray-900">Fecha de
                                                Fin <span class="text-red-500">*</span></label>
                                            <div class="mt-2">
                                                <input type="date" name="fechaFin" v-model="form.fechaFin"
                                                    placeholder="Ingrese la fecha de fin"
                                                    class="block w-48 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            </div>
                                            <div v-if="fechaFinError != ''" class="text-red-500 text-xs mt-1">{{
                                                fechaFinError
                                            }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- Div de incapacidad -->
                            <div class="sm:col-span-2 px-2">
                                <label for="fechaAlta" class="block text-sm font-medium leading-6 text-gray-900">Fecha
                                    de
                                    Alta <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="date" name="fechaAlta" :id="'fechaAlta' + op" v-model="form.fechaAlta"
                                        placeholder=" "
                                        class="block w-38 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="FechaAltaError != ''" class="text-red-500 text-xs mt-1">{{
                                    FechaAltaError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="fechaBaja" class="block text-sm font-medium leading-6 text-gray-900">Fecha
                                    de
                                    Baja</label>
                                <div class="mt-2">
                                    <input type="date" name="fechaBaja" :id="'fechaBaja' + op" v-model="form.fechaBaja"
                                        placeholder=" "
                                        class="block w-38 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="empresa" class="block text-sm font-medium leading-6 text-gray-900">Empresa
                                    <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <select name="empresa" :id="'empresa' + op" v-model="form.empresaa"
                                        placeholder="Seleccione la empresa a la que pertenece"
                                        class="block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="" disabled selected>Seleccione la empresa</option>
                                        <option v-for="emp in empresa" :key="emp.idEmpresa" :value="emp.idEmpresa">
                                            {{ emp.empresa }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="EmpresaError != ''" class="text-red-500 text-xs mt-1">{{ EmpresaError
                                    }}
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="convenioPago"
                                    class="block text-sm font-medium leading-6 text-gray-900">Convenio
                                    de
                                    Pago <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <select name="convenioPago" :id="'convenioPago' + op" v-model="form.convenioPa"
                                        placeholder="Seleccione el convenio de pago"
                                        class="block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="" disabled selected>Seleccione el convenio</option>
                                        <option v-for="convenio in convenioPago" :key="convenio.idConvenioPago"
                                            :value="convenio.idConvenioPago">
                                            {{ convenio.convenioPago }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="convenioPagoError != ''" class="text-red-500 text-xs mt-1">{{
                                    convenioPagoError
                                }}
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="antiguedad"
                                    class="block text-sm font-medium leading-6 text-gray-900">Antiguedad
                                    <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="number" name="antiguedad" :id="'antiguedad' + op"
                                        v-model="form.antiguedad" placeholder="Años "
                                        class="block w-20 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="AntiguedadError != ''" class="text-red-500 text-xs mt-1">{{ AntiguedadError
                                    }}
                                </div>
                            </div>
                        </div><!-- Del div que ordena los campos -->
                    </div><!-- Div donde termina la seccion Informacion Laboral -->

                    <div class="p-3 border-b border-gray-300">
                        <!-- Div que divide la seccion de Información domiciliaria -->
                        <h2 class="text-md font-semibold mb-1">Información de Domicilio</h2>
                        <div class="flex flex-wrap"><!-- Div para ordenar campos -->
                            <div class="sm:col-span-2 px-2">
                                <label for="codigoPostal"
                                    class="block text-sm font-medium leading-6 text-gray-900">Código
                                    Postal <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="number" name="codigoPostal" :id="'codigoPostal' + op"
                                        v-model="form.codigoPostal" placeholder="Ingrese el CP"
                                        class="block w-28 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        maxlength="5" minlength="5" @blur="buscarDatosXCodigoPostal">
                                </div>
                                <div v-if="codigoPError != ''" class="text-red-500 text-xs mt-1">{{ codigoPError }}
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="entidad" class="block text-sm font-medium leading-6 text-gray-900">Estado
                                    <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <select name="entidad" :id="'entidad' + op" v-model="form.entidad"
                                        @change="cargarMunicipios"
                                        class="block w-full rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="" disabled selected>Seleccione el estado</option>
                                        <option v-for="entidad in entidades" :key="entidad.idEntidad"
                                            :value="entidad.idEntidad">
                                            {{ entidad.entidad }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="municipio"
                                    class="block text-sm font-medium leading-6 text-gray-900">Municipio
                                    <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <select name="municipio" :id="'municipio' + op" v-model="form.municipio"
                                        @change="cargarAsentamientos"
                                        class="block w-full rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="" disabled selected>Seleccione el municipio</option>
                                        <option v-for="municipio in municipios" :key="municipio.idMunicipio"
                                            :value="municipio.idMunicipio">
                                            {{ municipio.municipio }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="asentamiento"
                                    class="block text-sm font-medium leading-6 text-gray-900">Asentamiento
                                    / Localidad <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <select name="asentamiento" :id="'asentamiento' + op" v-model="form.asentamiento"
                                        class="block w-full rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="" disabled selected>Seleccione el asentamiento</option>
                                        <option v-for="asentamiento in asentamientos" :key="asentamiento.idAsentamiento"
                                            :value="asentamiento.idAsentamiento">
                                            {{ asentamiento.asentamiento }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="calle" class="block text-sm font-medium leading-6 text-gray-900">Calle <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="text" name="calle" :id="'calle' + op" v-model="form.calle"
                                        placeholder="Ingrese el nombre de la calle"
                                        class="block w-72 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="calleError != ''" class="text-red-500 text-xs mt-1">{{ calleError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="numero" class="block text-sm font-medium leading-6 text-gray-900">Número de
                                    Casa
                                    <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="number" name="numero" :id="'numero' + op" v-model="form.numero"
                                        placeholder=" Núm. casa"
                                        class="block w-28 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="numeroCError != ''" class="text-red-500 text-xs mt-1">{{ numeroCError }}
                                </div>
                            </div>
                            <div class="sm:col-span-3" hidden>
                                <label for="idDireccion"
                                    class="block text-sm font-medium leading-6 text-gray-900">idDomicilio</label>
                                <div class="mt-2">
                                    <input type="text" name="idDireccion" :id="'idDireccion' + op"
                                        v-model="form.idDireccion" placeholder="Ingrese la Direccion"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div><!-- Div con el que termina para ordenar campos -->
                    </div><!-- Div que termina la seccion de Informacion de domicilio -->

                    <div class="p-3 border-b border-gray-300"><!-- Div de inicio de seccion documentacion -->
                        <h2 class="text-md font-semibold mb-1">Información de Documentación</h2>
                        <div class="flex flex-wrap"><!-- Div para ordenar -->
                            <div class="sm:col-span-2 px-2">
                                <label for="numLicencia"
                                    class="block text-sm font-medium leading-6 text-gray-900">Número de
                                    Licencia <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="text" name="numLicencia" :id="'numLicencia' + op"
                                        v-model="form.numLicencia" placeholder="Número de licencia"
                                        class="block w-34 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="NLicenciaError != ''" class="text-red-500 text-xs mt-1">{{ NLicenciaError }}
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="vigenciaLicencia"
                                    class="block text-sm font-medium leading-6 text-gray-900">Vigencia de Licencia <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="date" name="vigenciaLicencia" :id="'vigenciaLicencia' + op"
                                        v-model="form.vigenciaLicencia" placeholder="Seleccione la vigencia"
                                        class="block w-38 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="VigenciaLError != ''" class="text-red-500 text-xs mt-1">{{
                                    VigenciaLError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="numINE" class="block text-sm font-medium leading-6 text-gray-900">Número de
                                    INE
                                    <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="text" name="numINE" :id="'numINE' + op" v-model="form.numINE"
                                        placeholder="Ingrese el número del INE"
                                        class="block w-72 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="NumINEError != ''" class="text-red-500 text-xs mt-1">{{ NumINEError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="vigenciaINE"
                                    class="block text-sm font-medium leading-6 text-gray-900">Vigencia
                                    del INE <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <select name="vigenciaINE" :id="'vigenciaINE' + op" v-model="form.vigenciaINE"
                                        class="block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="">Seleccione el año</option>
                                        <option v-for="anio in anios" :key="anio" :value="anio">{{ anio }}</option>
                                    </select>
                                </div>
                                <div v-if="vigenciaINEError != ''" class="text-red-500 text-xs mt-1">{{ vigenciaINEError
                                    }}
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="constanciaSF"
                                    class="block text-sm font-medium leading-6 text-gray-900">Constancia de Situación
                                    Fiscal</label>
                                <div class="mt-2 flex items-center">
                                    <input type="checkbox" name="constanciaSF" :id="'constanciaSF' + op"
                                        v-model="form.constanciaSF"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    <span class="ml-2 text-sm text-gray-600">Cuenta con la Constancia de
                                        Situación Fiscal</span>
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="cursoSemovi" class="block text-sm font-medium leading-6 text-gray-900">Curso
                                    SEMOVI</label>
                                <div class="mt-2 flex items-center">
                                    <input type="checkbox" name="cursoSEMOVI" :id="'cursoSEMOVI' + op"
                                        v-model="form.cursoSemovi"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    <span class="ml-2 text-sm text-gray-600">Cuenta con Curso SEMOVI</span>
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="constanciaSEMOVI"
                                    class="block text-sm font-medium leading-6 text-gray-900">Constancia SEMOVI</label>
                                <div class="mt-2 flex items-center">
                                    <input type="checkbox" name="constanciaSEMOVI" :id="'constanciaSEMOVI' + op"
                                        v-model="form.constanciaSemovi"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    <span class="ml-2 text-sm text-gray-600">Cuenta con Constancia de SEMOVI</span>
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="cursoPsicologioc"
                                    class="block text-sm font-medium leading-6 text-gray-900">Curso Psicológico</label>
                                <div class="mt-2 flex items-center">
                                    <input type="checkbox" name="cursoPsicologico" :id="'cursoPsicologioc' + op"
                                        v-model="form.cursoPsicologico"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    <span class="ml-2 text-sm text-gray-600">Cuenta con Curso Psicológico</span>
                                </div>
                            </div>
                        </div><!-- Div que temrina para ordenar -->
                    </div><!-- Div donde termina la seccion documentacion -->

                    <div class="p-3 border-b border-gray-300"><!-- Div de la seccion informacion adicional -->
                        <h2 class="text-md font-semibold mb-1">Información Adicional</h2>
                        <div class="flex flex-wrap"><!-- Div que ordena los campos -->
                            <div class="sm:col-span-2 px-2">
                                <label for="ultimoContrato"
                                    class="block text-sm font-medium leading-6 text-gray-900">Fecha de Último
                                    Contrato <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="date" name="ultimoContrato" :id="'ultimoContrato' + op"
                                        v-model="form.ultimoContrato" placeholder=" "
                                        class="block w-48 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="UltimoConError != ''" class="text-red-500 text-xs mt-1">{{
                                    UltimoConError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="directivo"
                                    class="block text-sm font-medium leading-6 text-gray-900">Jefe<span
                                        class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <select name="directivo" :id="'directivo' + op" v-model="form.directivo"
                                        placeholder="Seleccione a un socio/prestador"
                                        class="block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="" disabled selected>Seleccione a un socio/prestador</option>
                                        <option v-for="jefe in directivo" :key="jefe.idDirectivo"
                                            :value="jefe.idDirectivo">
                                            {{ jefe.nombre_completo }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="directivoError != ''" class="text-red-500 text-xs mt-1">{{ directivoError }}
                                </div>
                            </div>
                        </div><!-- Div donde termina para ordenar los campos -->
                    </div><!-- Div que termina la seccion de informacion adicional -->
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" :id="'cerrar' + op"
                        class="text-sm font-semibold leading-6 bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-white py-2 px-2 rounded"
                        data-bs.dismiss="modal" @click="close"><i class="fa-solid fa-ban"></i> Cancelar</button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-2 rounded"
                        :disabled="form.processing"> <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </Modal>
</template>
<style>
@import "vue-select/dist/vue-select.css";

hr {
    border: 1px solid #e5e7eb;
    /* Ajuste del color de la línea divisoria */
}
</style>