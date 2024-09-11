<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, watch, onMounted } from 'vue';
import axios from 'axios'; // Importar Axios
import Modal from '../Modal.vue';

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
    personal: {
        type: Object,
        default: () => ({}),
    },
    escolaridad: {
        type: Object,
        default: () => ({}),
    },
    empresa: {
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

const form = useForm({
    _method: 'PUT',
    idPersonal: props.personal.idPersonal,
    nombre: props.personal.nombre,
    apellidoP: props.personal.apellidoP,
    apellidoM: props.personal.apellidoM,
    fechaNacimiento: props.personal.fechaNacimiento,
    edad: props.personal.edad,
    CURP: props.personal.CURP,
    RFC: props.personal.RFC,
    numTelefono: props.personal.numTelefono,
    telEmergencia: props.personal.telEmergencia,
    NSS: props.personal.NSS,
    escolaridadd: props.personal.idEscolaridad,
    direccion: props.personal.idDireccion,
    codigoPostal: props.personal.codigoPos,
    entidad: props.personal.idEntidad,
    municipio: props.personal.idMunicipio,
    asentamiento: props.personal.idAsentamiento,
    calle: props.personal.calle,
    numero: props.personal.numero,
    idDireccion: props.personal.idDireccion,
    numINE: props.personal.numINE,
    vigenciaINE: props.personal.vigenciaINE,
    antiguedad: props.personal.antiguedad,
    fechaAlta: props.personal.fechaAlta,
    fechaBaja: props.personal.fechaBaja,
    empresaa: props.personal.idEmpresa,
    constanciaSF: props.personal.constanciaSF,
    totalDiasVac: props.personal.totalDiasVac,
    diasVacRestantes: props.personal.diasVacRestantes,
    
});

watch(() => props.personal, async (newVal) => {
    form.idPersonal = newVal.idPersonal;
    form.nombre = newVal.nombre;
    form.apellidoP = newVal.apellidoP;
    form.apellidoM = newVal.apellidoM;
    form.fechaNacimiento = newVal.fechaNacimiento;
    form.edad = newVal.edad;
    form.CURP = newVal.CURP;
    form.RFC = newVal.RFC;
    form.numTelefono = newVal.numTelefono;
    form.telEmergencia = newVal.telEmergencia;
    form.NSS = newVal.NSS;
    form.escolaridadd = newVal.idEscolaridad;
    form.calle = newVal.calle;
    form.numero = newVal.numero;
    form.codigoPostal = await newVal.codigoPostal;
    await buscarDatosXCodigoPostal();
    form.entidad = await newVal.idEntidad;
    await cargarMunicipios();
    form.municipio = await newVal.idMunicipio;
    await cargarAsentamientos();
    form.asentamiento = await newVal.idAsentamiento;
    form.numINE = newVal.numINE;
    form.vigenciaINE = newVal.vigenciaINE;
    form.antiguedad = newVal.antiguedad;
    form.fechaAlta = newVal.fechaAlta;
    form.fechaBaja = newVal.fechaBaja;
    form.empresaa = newVal.idEmpresa;
    form.constanciaSF = newVal.constanciaSF === 1;
    form.totalDiasVac = newVal.totalDiasVac;
    form.diasVacRestantes = newVal.diasVacRestantes;
    form.idDireccion = newVal.idDireccion;
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
const fechaNError = ref('');
const edadError = ref('');
const CURPError = ref('');
const RFCError = ref('');
const telError = ref('');
const telEmerError = ref('');
const NSSError = ref('');
const escolaridadError = ref('');
const NumINEError = ref('');
const vigenciaINEError = ref('');
const AntiguedadError = ref('');
const FechaAltaError = ref('');
const EmpresaError = ref('');
const codigoPError = ref('');
const calleError = ref('');
const numeroCError = ref('');
const totalDiasVacError = ref('');
const diasRestantesError = ref('');
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

// Validación de número de teléfono de emergencia a través de expresión regular
const validateTelefonoEmergencia = (telEmergencia) => {
    const telefonoEmergenciaExpReg = /^(\d{10})?$/;
    // Asegúrate de limpiar el valor y comprobar si es vacío o válido
    const cleanedValue = telEmergencia ? telEmergencia.trim() : "";
    return cleanedValue === "" || telefonoEmergenciaExpReg.test(cleanedValue);
};


// Validación de codigo postal a traves de expresion regular
const validateCodigoPostal = (codigoPostal) => {
    const codigoPostalRegex = /^\d{5}$/;
    return codigoPostalRegex.test(codigoPostal);
};
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
const validateINE = (vigenciaINE) => {
    // Permitir que el campo sea nulo o vacío
    if (vigenciaINE === null || vigenciaINE === undefined) {
        return true; // O falso, dependiendo de si quieres permitir nulos o no
    }

    // Obtener el año actual
    const currentYear = new Date().getFullYear();

    // Verificar que el año de vigencia no esté en el pasado
    const vigenciaYear = parseInt(vigenciaINE);
    if (isNaN(vigenciaYear) || vigenciaYear < currentYear) {
        return false;
    }
    return true;
};

// Validación del número de INE
const validateNumINE = (numINE) => {
    // Permitir que el campo sea nulo
    if (numINE === null || numINE === undefined) {
        return true; // O falso, dependiendo de si quieres permitir nulos o no
    }

    // Verificar que el campo sea una cadena y tenga la longitud correcta
    return typeof numINE === 'string' && numINE.trim() !== '' && numINE.length === 13;
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

const validateDiasVacRestantes = (totalDiasVac, diasVacRestantes) => {
    // Verificar que ambos valores sean números válidos
    if (typeof totalDiasVac !== 'number' || typeof diasVacRestantes !== 'number') {
        return false;
    }
    // Verificar que ambos valores sean mayores o iguales a cero
    if (totalDiasVac < 0 || diasVacRestantes < 0) {
        return false;
    }
    // Verificar que los días restantes no sean mayores a los días totales
    return diasVacRestantes <= totalDiasVac;
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
    for (let i = anioActual; i <= anioActual + 30; i++) {
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

const calcularAntiguedad = (fechaAlta) => {
    if (!fechaAlta) {
        form.antiguedad = '';
        return;
    }
    const fechaAltaDate = new Date(fechaAlta);
    const hoy = new Date();
    let anios = hoy.getFullYear() - fechaAltaDate.getFullYear();
    const meses = hoy.getMonth() - fechaAltaDate.getMonth();

    if (meses < 0 || (meses === 0 && hoy.getDate() < fechaAltaDate.getDate())) {
        anios--;
    }

    form.antiguedad = anios;
};

watch(() => form.fechaAlta, (newFechaAlta) => {
    calcularAntiguedad(newFechaAlta);
});

const calcularTotalDiasVac = () => {
    const antiguedad = parseInt(form.antiguedad, 10);

    if (isNaN(antiguedad) || antiguedad < 0) {
        AntiguedadError.value = 'Por favor ingrese una antigüedad válida.';
        form.totalDiasVac = '';
        return;
    }

    AntiguedadError.value = '';
    let diasVac = 0;

    if (antiguedad === 1) {
        diasVac = 12;
    } else if (antiguedad === 2) {
        diasVac = 14;
    } else if (antiguedad === 3) {
        diasVac = 16;
    } else if (antiguedad === 4) {
        diasVac = 18;
    } else if (antiguedad === 5) {
        diasVac = 20;
    } else if (antiguedad >= 6 && antiguedad <= 10) {
        diasVac = 22;
    } else if (antiguedad >= 11 && antiguedad <= 15) {
        diasVac = 24;
    } else if (antiguedad >= 16 && antiguedad <= 20) {
        diasVac = 26;
    } else if (antiguedad >= 21 && antiguedad <= 25) {
        diasVac = 28;
    } else if (antiguedad >= 26 && antiguedad <= 30) {
        diasVac = 30;
    } else if (antiguedad >= 31 && antiguedad <= 35) {
        diasVac = 32;
    }

    form.totalDiasVac = diasVac;
};

watch(() => form.antiguedad, calcularTotalDiasVac);

const update = async () => {
    // Validación de los campos del formulario principal
    nombreError.value = validateStringNotEmpty(form.nombre) ? '' : 'Ingrese el nombre(s)';
    apellidoPError.value = validateStringNotEmpty(form.apellidoP) ? '' : 'Ingrese el apellido paterno';
    apellidoMError.value = validateStringNotEmpty(form.apellidoM) ? '' : 'Ingrese el apellido materno';
    fechaNError.value = validateFechaNacimiento(form.fechaNacimiento) ? '' : 'Fecha de nacimiento no válido';
    edadError.value = validateIntegerField(form.edad) ? '' : 'Edad no válido';
    CURPError.value = validateCURP(form.CURP) ? '' : 'CURP no válido';
    RFCError.value = validateRFC(form.RFC) ? '' : 'RFC no válido';
    telError.value = validateNumeroTelefono(form.numTelefono) ? '' : 'Número de telefono no válido';
    telEmerError.value = validateTelefonoEmergencia(form.telEmergencia) ? '' : 'Número de telefono no válido';
    NSSError.value = validateNSS(form.NSS) ? '' : 'NSS no válido';
    escolaridadError.value = validateSelect(form.escolaridadd) ? '' : 'Seleccione la escolaridad';
    FechaAltaError.value = validateDateField(form.fechaAlta) ? '' : 'Fecha de alta no válido';
    EmpresaError.value = validateSelect(form.empresaa) ? '' : 'Seleccione la empresa';
    AntiguedadError.value = validateIntegerField(form.antiguedad) ? '' : 'Ingrese los años de antiguedad';
    codigoPError.value = validateCodigoPostal(form.codigoPostal) ? '' : 'Ingrese el codigo postal';
    calleError.value = validateStringNotEmpty(form.calle) ? '' : 'Ingrese el nombre de la calle';
    /* numeroCError.value = validateIntegerField(form.numero) ? '' : 'Número de casa no válido'; */
    NumINEError.value = validateNumINE(form.numINE) ? '' : 'Número de INE no válido';
    vigenciaINEError.value = validateINE(form.vigenciaINE) ? '' : 'Vigencia de INE no válido';
    totalDiasVacError.value = validateIntegerField(form.totalDiasVac) ? '' : 'Número de días no válido';
    diasRestantesError.value = validateDiasVacRestantes(form.totalDiasVac, form.diasVacRestantes) ? '' : 'Número de días no válido';
    // Verificar si hay algún error antes de enviar el formulario
    if (
        nombreError.value || apellidoPError.value || apellidoMError.value || fechaNError.value || edadError.value || CURPError.value || RFCError.value || telError.value ||
        telEmerError.value || escolaridadError.value || NSSError.value || NumINEError.value || AntiguedadError.value || FechaAltaError.value || EmpresaError.value |
        codigoPError.value || calleError.value /* || numeroCError.value  */|| vigenciaINEError.value || totalDiasVacError.value || diasRestantesError.value
    ) {
        return;
    }
    // Si no hay errores, enviar el formulario
    form.post(route('rh.actualizarPersonal',form.idPersonal), {
        onSuccess: () => {
            close();
            // Limpia los errores y campos si es necesario
            nombreError.value = '';
            apellidoPError.value = '';
            apellidoMError.value = '';
            fechaNError.value = '';
            edadError.value = '';
            CURPError.value = '';
            RFCError.value = '';
            telError.value = '';
            telEmerError.value = '';
            escolaridadError.value = '';
            NSSError.value = '';
            NumINEError.value = '';
            AntiguedadError.value = '';
            FechaAltaError.value = '';
            EmpresaError.value = '';
            codigoPError.value = '';
            calleError.value = '';
            /* numeroCError.value = ''; */
            vigenciaINEError.value = '';
            totalDiasVacError.value = '';
            diasRestantesError.value = '';
        },
        onError: (errors) => {
            console.log('Errores de envío:', errors);
        }
    });
};
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="update">
                <div class="pb-0">
                    <h1 class="text-2xl font-semibold leading-7 text-gray-900">{{ title }}</h1>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Modifique el dato que desea y presione Guardar para
                        aplicar los cambios.
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
                                    Paterno</label>
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
                                    Materno</label>
                                <div class="mt-2">
                                    <input type="text" name="apellidoM" :id="'apellidoM' + op" v-model="form.apellidoM"
                                        placeholder="Ingrese el apellido materno"
                                        class="block w-56 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="apellidoMError != ''" class="text-red-500 text-xs mt-1">{{ apellidoMError }}
                                </div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="nombre" class="block text-sm font-medium leading-6 text-gray-900">Nombre(s)</label>
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
                                    de Nacimiento</label>
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
                                <label for="edad" class="block text-sm font-medium leading-6 text-gray-900">Edad</label>
                                <div class="mt-2">
                                    <input type="number" name="edad" :id="'edad' + op" v-model="form.edad"
                                        placeholder="Años "
                                        class="block w-20 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="edadError != ''" class="text-red-500 text-xs mt-1">{{ edadError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="CURP" class="block text-sm font-medium leading-6 text-gray-900">CURP</label>
                                <div class="mt-2">
                                    <input type="text" name="CURP" :id="'CURP' + op" v-model="form.CURP"
                                        placeholder="Ingrese la CURP"
                                        class="block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="CURPError != ''" class="text-red-500 text-xs mt-1">{{ CURPError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="RFC" class="block text-sm font-medium leading-6 text-gray-900">RFC</label>
                                <div class="mt-2">
                                    <input type="text" name="RFC" :id="'RFC' + op" v-model="form.RFC"
                                        placeholder="Ingrese la RFC"
                                        class="block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="RFCError != ''" class="text-red-500 text-xs mt-1">{{ RFCError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="numTelefono"
                                    class="block text-sm font-medium leading-6 text-gray-900">Teléfono</label>
                                <div class="mt-2">
                                    <input type="text" name="numTelefono" :id="'numTelefono' + op"
                                        v-model="form.numTelefono" placeholder="Ingrese número de telefono"
                                        class="block w-28 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="telError != ''" class="text-red-500 text-xs mt-1">{{ telError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="telEmergencia" class="block text-sm font-medium leading-6 text-gray-900">Tel.
                                    de Emergencia
                                </label>
                                <div class="mt-2">
                                    <input type="text" name="telEmergencia" :id="'telEmergencia' + op"
                                        v-model="form.telEmergencia" placeholder="Ingrese número de telefono"
                                        class="block w-28 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="telEmerError != ''" class="text-red-500 text-xs mt-1">{{ telEmerError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="NSS" class="block text-sm font-medium leading-6 text-gray-900">NSS</label>
                                <div class="mt-2">
                                    <input type="text" name="NSS" :id="'NSS' + op" v-model="form.NSS"
                                        placeholder="Ingrese el NSS"
                                        class="block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="NSSError != ''" class="text-red-500 text-xs mt-1">{{ NSSError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="escolaridad"
                                    class="block text-sm font-medium leading-6 text-gray-900">Escolaridad</label>
                                <div class="mt-2">
                                    <select name="escolaridad" :id="'escolaridad' + op" v-model="form.escolaridadd"
                                        placeholder="Seleccione la escolaridad"
                                        class="block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="" disabled selected>Seleccione la escolaridad</option>
                                        <option v-for="escuela in escolaridad" :key="escuela.idEscolaridad"
                                            :value="escuela.idEscolaridad">
                                            {{ escuela.escolaridad }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="escolaridadError != ''" class="text-red-500 text-xs mt-1">{{ escolaridadError}}
                                </div>
                            </div>
                        </div><!-- donde termina el div para ordenar campos -->
                    </div><!-- Donde termina el div de la seccion informacion personal -->

                    <div class="p-3 border-b border-gray-300"><!-- Div que divide la seccion Informacion Laboral-->
                        <h2 class="text-md font-semibold mb-1">Información Laboral</h2>
                        <div class="flex flex-wrap"><!-- Para ordenar los campos -->
                            <div class="sm:col-span-2 px-2">
                                <label for="fechaAlta" class="block text-sm font-medium leading-6 text-gray-900">Fecha
                                    de
                                    Alta</label>
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
                                <label for="empresa" class="block text-sm font-medium leading-6 text-gray-900">Empresa</label>
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
                                <label for="antiguedad"
                                    class="block text-sm font-medium leading-6 text-gray-900">Antiguedad</label>
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
                                    Postal</label>
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
                                <label for="entidad" class="block text-sm font-medium leading-6 text-gray-900">Estado</label>
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
                                    class="block text-sm font-medium leading-6 text-gray-900">Municipio</label>
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
                                    / Localidad</label>
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
                                <label for="calle" class="block text-sm font-medium leading-6 text-gray-900">Calle</label>
                                <div class="mt-2">
                                    <input type="text" name="calle" :id="'calle' + op" v-model="form.calle"
                                        placeholder="Ingrese el nombre de la calle"
                                        class="block w-72 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="calleError != ''" class="text-red-500 text-xs mt-1">{{ calleError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="numero" class="block text-sm font-medium leading-6 text-gray-900">Número de
                                    Casa</label>
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
                                <label for="numINE" class="block text-sm font-medium leading-6 text-gray-900">Número de
                                    INE</label>
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
                                    del INE </label>
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
                        </div><!-- Div que temrina para ordenar -->
                    </div><!-- Div donde termina la seccion documentacion -->

                    <div class="p-3 border-b border-gray-300"><!-- Div de la seccion informacion adicional -->
                        <h2 class="text-md font-semibold mb-1">Información Vacacional</h2>
                        <div class="flex flex-wrap"><!-- Div que ordena los campos -->
                            <div class="sm:col-span-2 px-2">
                                <label for="totalDiasVac"
                                    class="block text-sm font-medium leading-6 text-gray-900">Total de
                                    Días </label>
                                <div class="mt-2">
                                    <input type="number" name="totalDiasVac" :id="'totalDiasVac' + op"
                                        v-model="form.totalDiasVac" placeholder="Días "
                                        class="block w-20 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="totalDiasVacError != ''" class="text-red-500 text-xs mt-1">{{ totalDiasVacError }}</div>
                            </div>
                            <div class="sm:col-span-2 px-2">
                                <label for="diasVacRestantes"
                                    class="block text-sm font-medium leading-6 text-gray-900">Días
                                    Restantes</label>
                                <div class="mt-2">
                                    <input type="number" name="diasVacRestantes" :id="'diasVacRestantes' + op"
                                        v-model="form.diasVacRestantes" placeholder="Días "
                                        class="block w-20 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div v-if="diasRestantesError != ''" class="text-red-500 text-xs mt-1">{{ diasRestantesError }}</div>
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