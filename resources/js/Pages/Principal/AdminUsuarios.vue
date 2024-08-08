<script setup>
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import Mensaje from '../../Components/Mensaje.vue';
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import { ref, onMounted, nextTick } from 'vue';
import Swal from 'sweetalert2';
import FormularioUsuarios from '../../Components/Principal/FormularioUsuarios.vue';
import FormularioActualizarUsuario from '../../Components/Principal/FormularioActualizarUsuario.vue';
import { jsPDF } from 'jspdf';
import * as XLSX from 'xlsx';
import 'jspdf-autotable';

DataTable.use(DataTablesLib);
DataTable.use(Select);

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    usuario: { type: Object },//obtenerInfo
    usuarios: { type: Object },//all();
    tipoUsuario: { type: Object },
});

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;
const usuariosSeleccionados = ref([]);

const formEliminar = useForm({
    _method: 'DELETE',
});

const formRestaurar = useForm({
    _method: 'PUT',
});

const descripcionCrear = "Rellene todos los campos para poder registrar un nuevo usuario";
const descripcionEditar = "Rellene todos los campos para poder actualizar la información de un usuario";

var usuarioE = ({});
const abrirUsuarios = ($usuarioss) => {
    usuarioE = $usuarioss;
    mostrarModalE.value = true;
}

const cerrarModal = () => {
    mostrarModal.value = false;
};

const cerrarModalE = () => {
    mostrarModalE.value = false;
};

const toggleUsuarioSelection = (user) => {
    if (usuariosSeleccionados.value.includes(user)) {
        // Si el usuario ya está seleccionado, la eliminamos del array
        usuariosSeleccionados.value = usuariosSeleccionados.value.filter((r) => r !== user);
    } else {
        // Si el usuario no está seleccionado, la agregamos al array
        usuariosSeleccionados.value.push(user);
    }
    // Llamado del botón de eliminar para cambiar su estado deshabilitado
    const botonEliminar = document.getElementById("eliminar-button");
    if (usuariosSeleccionados.value.length > 0) {
        botonEliminar.removeAttribute("disabled");
    } else {
        botonEliminar.setAttribute("disabled", "");
    }
};

const eliminarUsuario = (idUsuario, user) => {
    const swal = Swal.mixin({
        buttonsStyling: true
    })
    swal.fire({
        title: `¿Estas seguro que deseas eliminar los datos de ` + user + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            formEliminar.post(route('principal.eliminarUsuario', idUsuario));
        }

    })
};

const restaurarUsuario = (user) => {
    const idUsuario = user.idUsuario;
    const swal = Swal.mixin({
        buttonsStyling: true
    })

    swal.fire({
        title: '¿Estas seguro de restaurar los intentos y/o tiempo de cambio de contraseña?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await formRestaurar.post(route('principal.restUsuario', idUsuario));
            } catch (error) {
                console.log('El error se origina aquí');
                console.log(error);
            }
        }
    });
};

onMounted(() => {
    // Agrega un escuchador de eventos fuera de la lógica de Vue
    document.getElementById('usuariosTablaId').addEventListener('click', (event) => {
        const checkbox = event.target;
        //console.log(checkbox);
        if (checkbox.classList.contains('usuario-checkbox')) {
            const usuarioId = parseInt(checkbox.getAttribute('data-id'));
            // Se asegura que props.materias.data esté definido antes de usar find
            if (props.usuarios) {
                const user = props.usuarios.find(user => user.idUsuario === usuarioId);
                if (user) {
                    toggleUsuarioSelection(user);
                } else {
                    console.log("No se tiene usuario");
                }
            }
        }
    });

    // Manejar clic en el botón de editar
    $('#usuariosTablaId').on('click', '.editar-button', function () {
        const usuarioId = $(this).data('id');
        const user = props.usuarios.find(u => u.idUsuario === usuarioId);
        abrirUsuarios(user);
    });

    // Manejar clic en el botón de eliminar
    $('#usuariosTablaId').on('click', '.eliminar-button', function () {
        const usuarioId = $(this).data('id');
        const user = props.usuarios.find(u => u.idUsuario === usuarioId);
        eliminarUsuario(usuarioId, user.usuario);
    });

    $('#usuariosTablaId').on('click', '.mostrar-password-button', function () {
        const usuarioId = $(this).data('id');
        const user = props.usuarios.find(u => u.idUsuario === usuarioId);
        const passwordCell = $(this).closest('td').find('.ph');

        if (passwordCell.hasClass('password-hidden')) {
            // Muestra la contraseña
            passwordCell.removeClass('password-hidden').text(user.contrasenia);
        } else {
            // Oculta la contraseña
            passwordCell.addClass('password-hidden').text('*'.repeat(user.contrasenia.length));
        }
    });

    $('#usuariosTablaId').on('click', '.restaurar-usuario', function () {
        const usuarioId = $(this).data('id');
        const user = props.usuarios.find(u => u.idUsuario === usuarioId);
        restaurarUsuario(user);
    })

});

const exportarPDF = (titulo = 'Documento') => {
    const doc = new jsPDF('landscape');
    // Título del documento
    doc.setFontSize(12);
    doc.text(titulo, 14, 22); // Posiciona el título en la parte superior izquierda
    // Fecha de generación del documento
    const fecha = new Date().toLocaleDateString();
    doc.setFontSize(8);
    doc.text(`Fecha: ${fecha}`, 260, 22); // Posiciona la fecha en la parte superior derecha
    // Definir las columnas de la tabla
    const columnas = [
        "ID",
        "Nombre",
        "Apellido Paterno",
        "Apellido Materno",
        "Usuario",
        "Tipo De Usuario",
        "Contraseña"
    ];
    // Extraer los datos filtrados y visibles de la tabla
    const filas = [];
    nextTick(() => {
        const table = $('#usuariosTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados
        data.each((row) => {
            filas.push([
                row.idUsuario,
                row.nombre,
                row.apellidoP,
                row.apellidoM,
                row.usuario,
                props.tipoUsuario.find(tUser => tUser.idTipoUsuario === row.idTipoUsuario)?.tipoUsuario || '',
                row.contrasenia
            ]);
        });
        // Generar la tabla en el PDF
        doc.autoTable({
            head: [columnas],
            body: filas,
            startY: 24 // Ajusta el inicio de la tabla debajo del título y la fecha
        });
        // Guardar el documento con el título como nombre del archivo
        doc.save(`${titulo}.pdf`);
    });
};
const exportarExcel = () => {
    nextTick(() => {
        // Obtener la instancia de DataTable
        const table = $('#usuariosTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados

        // Convertir los datos a formato JSON
        const jsonData = data.toArray().map(row => ({
            ID: row.idUsuario,
            'Nombre': row.nombre,
            'Apellido Paterno': row.apellidoP,
            'Apellido Materno': row.apellidoM,
            'Usuario': row.usuario,
            'Tipo De Usuario': props.tipoUsuario.find(tUser => tUser.idTipoUsuario === row.idTipoUsuario)?.tipoUsuario || '',
            'Contraseña': row.contrasenia
        }));

        // Crear la hoja de Excel
        const ws = XLSX.utils.json_to_sheet(jsonData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Usuarios Registrados');

        // Guardar el archivo Excel
        XLSX.writeFile(wb, 'Usuarios Registrados.xlsx');
    });
};

const botonesPersonalizados = [
    {
        extend: 'copyHtml5',
        text: '<i class="fa-solid fa-copy"></i> Copiar', // Texto del botón
        className: 'bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [0, 2] // Indica qué columnas deben ser copiadas (por ejemplo, aquí se copiarían las columnas 0 y 2)
        },
        button: true
    },
    {
        title: 'Usuarios registrados',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon',
        action: () => exportarExcel() // Usa la función de exportar a Excel
    },
    {
        title: 'Usuarios registrados',
        text: '<i class="fa-solid fa-file-pdf"></i> PDF', // Texto del botón
        className: 'bg-red-500 hover:bg-red-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        orientation: 'landscape', // Configurar la orientación horizontal
        action: () => exportarPDF(props.title || 'Usuarios Registrados')
    },
    {
        title: 'Usuarios registrados',
        extend: 'print',
        text: '<i class="fa-solid fa-print"></i> Imprimir', // Texto del botón
        className: 'bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8] // Índices de las columnas que deseas imprimir 
        }
    }
];

const columnsUsuario = [
    {
        data: null,
        render: function (data, type, row, meta) {
            return "";
        }
    },
    {
        data: null,
        render: function (data, type, row, meta) {
            return `<input type="checkbox" class="usuario-checkbox" data-id="${row.idUsuario}" ">`;
        }
    },
    {
        data: null, render: function (data, type, row, meta) { return meta.row + 1 }
    },
    { data: 'nombre' },
    { data: 'apellidoP' },
    { data: 'apellidoM' },
    { data: 'usuario' },
    {
        data: 'idTipoUsuario',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const tipoUser = props.tipoUsuario.find(tipoUser => tipoUser.idTipoUsuario === data);
            return tipoUser ? tipoUser.tipoUsuario : '';
        }
    },
    {
        data: 'contrasenia', render: function (data, type, row, meta) {
            return `<div class="password-container">
                        <span class="ph password-hidden">${'*'.repeat(data.length)}</span>
                        <button class="mostrar-password-button" data-id="${row.idUsuario}"><i class="fa fa-eye"></i></button>
                    </div>`;
        }
    },
    {
        data: null, render: function (data, type, row, meta) {
            return `<button class="restaurar-usuario" data-id="${row.idUsuario}"><i class="fa fa-arrows-rotate"></i></button>`;
        }
    },
    {
        data: null, render: function (data, type, row, meta) {
            return `<button class="editar-button" data-id="${row.idUsuario}"><i class="fa fa-pencil"></i></button>`;
        }
    },
    {
        data: null, render: function (data, type, row, meta) {
            return `<button class="eliminar-button" data-id="${row.idUsuario}"><i class="fa fa-trash"></i></button>`;
        }

    }
];

const optionsUsuario = {
    responsive: true,
    autoWidth: false,
    dom: 'Bfrtip',
    language: {
        search: 'Buscar',
        zeroRecords: 'No hay registros para mostrar',
        info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
        infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
        infoFiltered: '(filtrado de un total de _MAX_ registros)',
        lengthMenu: 'Mostrar _MENU_ registros',
        paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Ultimo' },
    },
    buttons: botonesPersonalizados,
};

</script>

<template>
    <PrincipalLayout title="Usuarios" :usuario="props.usuario">
        <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Usuarios</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-3"></div>

            <Mensaje />

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Agregar Usuario
                </button>
            </div>

            <div class="">
                <DataTable class="w-full table-auto text-sm display stripe compact cell-border order-column"
                    id="usuariosTablaId" :responsive="true" :columns="columnsUsuario" :data="usuarios"
                    :options="optionsUsuario">
                    <thead>
                        <tr class="text-sm leading-normal">
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                #
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Nombre(s)
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Apellido Paterno
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Apellido Materno
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Usuario
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Tipo Usuario
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Contraseña
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Re
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Ed
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                El
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <FormularioUsuarios :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir usuario'" :modal="'modalCreate'" :descripcion="descripcionCrear"
            :usuarios="props.usuarios" :tipoUsuario="props.tipoUsuario"></FormularioUsuarios>
        <FormularioActualizarUsuario :show="mostrarModalE" :max-width="maxWidth" :closeable="closeable"
            @close="cerrarModalE" :title="'Editar usuario'" :modal="'modalEdit'"
            :descripcion="descripcionEditar" :usuarios="usuarioE" :tipoUsuario="props.tipoUsuario">
        </FormularioActualizarUsuario>
    </PrincipalLayout>
</template>

<style>
.password-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.mostrar-password-button {
    margin-left: auto;
}
</style>