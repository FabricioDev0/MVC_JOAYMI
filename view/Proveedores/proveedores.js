/**
 * JavaScript para gestión de proveedores - Sistema JOAYMI
 * Maneja CRUD de proveedores con validaciones
 *
 */

let tablaProveedores
let proveedorIdEliminar = null
let proveedorIdVista = null
const $ = window.$ 

// Inicialización cuando el documento esté listo
$(document).ready(() => {
  inicializarModuloProveedores()
})

/**
 * Inicializa todos los componentes del módulo de proveedores
 */
function inicializarModuloProveedores() {
  inicializarTablaProveedores()
  inicializarEventos()

  console.log("Módulo de proveedores inicializado correctamente")
}

/**
 * Inicializa la tabla de proveedores con DataTables
 */
function inicializarTablaProveedores() {
  tablaProveedores = $("#tablaProveedores").DataTable({
    responsive: true,
    processing: true,
    language: {
      processing: "Procesando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron proveedores",
      emptyTable: "No hay proveedores registrados",
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      search: "Buscar:",
      loadingRecords: "Cargando...",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior",
      },
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "copy",
        text: '<i class="mdi mdi-content-copy"></i> Copiar',
        className: "btn btn-secondary btn-sm",
        exportOptions: {
          columns: ":not(:last-child)",
        },
      },
      {
        extend: "excel",
        text: '<i class="mdi mdi-file-excel"></i> Excel',
        className: "btn btn-success btn-sm",
        exportOptions: {
          columns: ":not(:last-child)",
        },
      },
      {
        extend: "csv",
        text: '<i class="mdi mdi-file-delimited"></i> CSV',
        className: "btn btn-info btn-sm",
        exportOptions: {
          columns: ":not(:last-child)",
        },
      },
    ],
    order: [[0, "desc"]],
    columnDefs: [
      {
        targets: [6], // Columna de acciones
        orderable: false,
        searchable: false,
      },
    ],
  })

  // Cargar datos iniciales
  cargarProveedores()
}

/**
 * Inicializa todos los eventos del módulo
 */
function inicializarEventos() {
  // Evento para nuevo proveedor
  $("#btnNuevoProveedor").on("click", abrirModalNuevoProveedor)

  // Evento para envío del formulario
  $("#formularioProveedor").on("submit", guardarProveedor)

  // Evento para confirmar eliminación
  $("#btnConfirmarEliminarProveedor").on("click", confirmarEliminacionProveedor)

  // Limpiar formulario al cerrar modal
  $("#modalMantenimientoProveedor").on("hidden.bs.modal", limpiarFormularioProveedor)
}

/**
 * Carga la lista de proveedores desde el API
 */
function cargarProveedores() {
  console.log("Cargando proveedores...")

  // Mostrar indicador de carga
  $("#tablaProveedores tbody").html(`
        <tr>
            <td colspan="7" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="mt-2">Cargando proveedores...</div>
            </td>
        </tr>
    `)

  $.ajax({
    url: "../../public/proveedores_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      console.log("Respuesta proveedores:", respuesta)

      if (respuesta.exito && respuesta.datos) {
        procesarDatosProveedores(respuesta.datos)
        console.log(`Proveedores cargados: ${respuesta.datos.length}`)
      } else {
        mostrarError("Error al cargar proveedores: " + (respuesta.mensaje || "Respuesta inválida"))
        mostrarTablaVacia()
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error cargando proveedores:", error, xhr)
      manejarErrorAjax(xhr, estado, error, "cargar proveedores")
      mostrarTablaVacia()
    },
  })
}

/**
 * Procesa los datos de proveedores y los muestra en la tabla
 */
function procesarDatosProveedores(proveedores) {
  tablaProveedores.clear()

  proveedores.forEach((proveedor) => {
    const fila = [
      `<span class="d-none d-md-inline">${proveedor.cod_proveedor}</span>`,
      `<span class="fw-medium">${proveedor.str_nombre}</span>`,
      `<span class="d-none d-md-inline">${proveedor.str_contacto}</span>`,
      mostrarTelefono(proveedor.str_telefono),
      generarBadgeEstado(proveedor.est_activo),
      `<span class="d-none d-md-inline">${formatearFecha(proveedor.fec_creacion)}</span>`,
      generarBotonesAccion(proveedor),
    ]

    tablaProveedores.row.add(fila)
  })

  tablaProveedores.draw()
}

/**
 * Muestra el teléfono como texto normal
 */
function mostrarTelefono(telefono) {
  if (!telefono) return "-"
  return `<span class="text-dark">${telefono}</span>`
}

/**
 * Genera los botones de acción para cada proveedor
 */
function generarBotonesAccion(proveedor) {
  const esAdministrador = true // Se puede obtener de la sesión si es necesario

  return `
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-info btn-sm" 
                    onclick="verDetalleProveedor(${proveedor.cod_proveedor})"
                    data-bs-toggle="tooltip" title="Ver detalles">
                <i class="mdi mdi-eye"></i>
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm" 
                    onclick="editarProveedor(${proveedor.cod_proveedor})"
                    data-bs-toggle="tooltip" title="Editar proveedor">
                <i class="mdi mdi-pencil"></i>
            </button>
            ${
              esAdministrador
                ? `<button type="button" class="btn btn-outline-danger btn-sm" 
                    onclick="eliminarProveedor(${proveedor.cod_proveedor}, '${proveedor.str_nombre}')"
                    data-bs-toggle="tooltip" title="Eliminar proveedor">
                <i class="mdi mdi-trash-can"></i>
            </button>`
                : ""
            }
        </div>
    `
}

/**
 * Abre el modal para crear un nuevo proveedor
 */
function abrirModalNuevoProveedor() {
  console.log("Abriendo modal nuevo proveedor")
  limpiarFormularioProveedor()
  $("#tituloModalProveedor").text("Crear Nuevo Proveedor")
  $("#btnGuardarProveedor").html('<i class="mdi mdi-content-save me-1"></i> Crear Proveedor')
  $("#informacionAdicionalProveedor").hide()
  $("#modalMantenimientoProveedor").modal("show")
}

/**
 * Ver detalle de un proveedor
 */
function verDetalleProveedor(codigoProveedor) {
  console.log("Viendo detalle proveedor:", codigoProveedor)
  proveedorIdVista = codigoProveedor

  $.ajax({
    url: `../../public/proveedores_api.php?accion=buscar&codigo=${codigoProveedor}`,
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        llenarModalVista(respuesta.datos)
        $("#modalVistaProveedor").modal("show")
      } else {
        mostrarError("Error al cargar datos del proveedor")
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error:", error)
      mostrarError("Error al cargar datos del proveedor")
    },
  })
}

/**
 * Editar proveedor
 */
function editarProveedor(codigoProveedor) {
  console.log("Editando proveedor:", codigoProveedor)

  $.ajax({
    url: `../../public/proveedores_api.php?accion=buscar&codigo=${codigoProveedor}`,
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        llenarFormularioEdicion(respuesta.datos)
        $("#tituloModalProveedor").text("Editar Proveedor")
        $("#btnGuardarProveedor").html('<i class="mdi mdi-content-save me-1"></i> Actualizar Proveedor')
        $("#informacionAdicionalProveedor").show()
        $("#modalMantenimientoProveedor").modal("show")
      } else {
        mostrarError("Error al cargar datos del proveedor")
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error:", error)
      mostrarError("Error al cargar datos del proveedor")
    },
  })
}

/**
 * Llena el formulario con los datos del proveedor para edición
 */
function llenarFormularioEdicion(proveedor) {
  $("#codigoProveedor").val(proveedor.cod_proveedor)
  $("#nombreProveedor").val(proveedor.str_nombre)
  $("#contactoProveedor").val(proveedor.str_contacto)
  $("#telefonoProveedor").val(proveedor.str_telefono)
  $("#fechaCreacionProveedor").text(formatearFecha(proveedor.fec_creacion))
}

/**
 * Llena el modal de vista con los datos del proveedor
 */
function llenarModalVista(proveedor) {
  $("#tituloVistaProveedor").text(`Detalles: ${proveedor.str_nombre}`)
  $("#vistaCodigoProveedor").text(proveedor.cod_proveedor)
  $("#vistaNombreProveedor").text(proveedor.str_nombre)
  $("#vistaContactoProveedor").text(proveedor.str_contacto)
  $("#vistaTelefonoProveedor").text(proveedor.str_telefono)
  $("#vistaFechaCreacionProveedor").text(formatearFecha(proveedor.fec_creacion))

  // Información adicional
  $("#vistaEmpresaProveedor").text(proveedor.str_nombre)
  $("#vistaPersonaContacto").text(proveedor.str_contacto)

  // Teléfono como texto normal
  $("#vistaTextoTelefono").text(proveedor.str_telefono)

  // Estado
  const estadoBadge = proveedor.est_activo
    ? '<span class="badge bg-success">Activo</span>'
    : '<span class="badge bg-danger">Inactivo</span>'
  $("#vistaEstadoProveedor").html(estadoBadge)
  $("#vistaEstadoTexto").text(proveedor.est_activo ? "Activo" : "Inactivo")
}

/**
 * Editar proveedor desde el modal de vista
 */
function editarProveedorDesdeVista() {
  $("#modalVistaProveedor").modal("hide")
  setTimeout(() => {
    editarProveedor(proveedorIdVista)
  }, 300)
}

/**
 * Guarda o actualiza un proveedor
 */
function guardarProveedor(evento) {
  evento.preventDefault()

  if (!validarFormularioProveedor()) {
    return
  }

  const esEdicion = $("#codigoProveedor").val() !== ""
  const datosFormulario = obtenerDatosFormulario()

  console.log("Guardando proveedor:", datosFormulario)

  const btnGuardar = $("#btnGuardarProveedor")
  const textoOriginal = btnGuardar.html()
  btnGuardar.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Guardando...')

  const url = esEdicion
    ? "../../public/proveedores_api.php?accion=actualizar"
    : "../../public/proveedores_api.php?accion=crear"

  const metodo = esEdicion ? "PUT" : "POST"

  $.ajax({
    url: url,
    type: metodo,
    data: JSON.stringify(datosFormulario),
    contentType: "application/json",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito) {
        $("#modalMantenimientoProveedor").modal("hide")
        cargarProveedores()

        const mensaje = esEdicion ? "Proveedor actualizado correctamente" : "Proveedor creado correctamente"
        mostrarExito(mensaje)
      } else {
        mostrarError("Error al guardar proveedor: " + respuesta.mensaje)
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error guardando:", error)
      mostrarError("Error al guardar proveedor")
    },
    complete: () => {
      btnGuardar.prop("disabled", false).html(textoOriginal)
    },
  })
}

/**
 * Eliminar proveedor
 */
function eliminarProveedor(codigoProveedor, nombreProveedor) {
  proveedorIdEliminar = codigoProveedor
  $("#nombreProveedorEliminar").text(nombreProveedor)
  $("#modalEliminarProveedor").modal("show")
}

/**
 * Confirma la eliminación del proveedor
 */
function confirmarEliminacionProveedor() {
  if (!proveedorIdEliminar) return

  const btnConfirmar = $("#btnConfirmarEliminarProveedor")
  const textoOriginal = btnConfirmar.html()
  btnConfirmar.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Eliminando...')

  $.ajax({
    url: "../../public/proveedores_api.php?accion=eliminar",
    type: "DELETE",
    data: JSON.stringify({ codigoProveedor: proveedorIdEliminar }),
    contentType: "application/json",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito) {
        $("#modalEliminarProveedor").modal("hide")
        cargarProveedores()
        mostrarExito("Proveedor eliminado correctamente")
      } else {
        mostrarError("Error al eliminar proveedor: " + respuesta.mensaje)
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error eliminando:", error)
      mostrarError("Error al eliminar proveedor")
    },
    complete: () => {
      btnConfirmar.prop("disabled", false).html(textoOriginal)
      proveedorIdEliminar = null
    },
  })
}

// === FUNCIONES DE UTILIDAD ===

function obtenerDatosFormulario() {
  const datos = {
    nombreProveedor: $("#nombreProveedor").val().trim(),
    contactoProveedor: $("#contactoProveedor").val().trim(),
    telefonoProveedor: $("#telefonoProveedor").val().trim(),
  }

  const codigoProveedor = $("#codigoProveedor").val()
  if (codigoProveedor) {
    datos.codigoProveedor = Number.parseInt(codigoProveedor)
  }

  return datos
}

function validarFormularioProveedor() {
  let esValido = true

  // Validar nombre
  const nombre = $("#nombreProveedor").val().trim()
  if (!nombre || nombre.length < 3) {
    marcarCampoInvalido("#nombreProveedor", "El nombre debe tener al menos 3 caracteres")
    esValido = false
  } else if (nombre.length > 100) {
    marcarCampoInvalido("#nombreProveedor", "El nombre no puede exceder 100 caracteres")
    esValido = false
  } else {
    marcarCampoValido("#nombreProveedor")
  }

  // Validar contacto
  const contacto = $("#contactoProveedor").val().trim()
  if (!contacto || contacto.length < 3) {
    marcarCampoInvalido("#contactoProveedor", "El contacto debe tener al menos 3 caracteres")
    esValido = false
  } else if (contacto.length > 100) {
    marcarCampoInvalido("#contactoProveedor", "El contacto no puede exceder 100 caracteres")
    esValido = false
  } else {
    marcarCampoValido("#contactoProveedor")
  }

  // Validar teléfono
  const telefono = $("#telefonoProveedor").val().trim()
  if (!telefono) {
    marcarCampoInvalido("#telefonoProveedor", "El teléfono es obligatorio")
    esValido = false
  } else if (!validarFormatoTelefono(telefono)) {
    marcarCampoInvalido("#telefonoProveedor", "Formato de teléfono inválido")
    esValido = false
  } else if (telefono.length > 20) {
    marcarCampoInvalido("#telefonoProveedor", "El teléfono no puede exceder 20 caracteres")
    esValido = false
  } else {
    marcarCampoValido("#telefonoProveedor")
  }

  return esValido
}

function validarFormatoTelefono(telefono) {
  // Permitir números, espacios, guiones, paréntesis y el signo +
  const patron = /^[\d\s\-$$$$+]+$/
  return patron.test(telefono) && telefono.replace(/\D/g, "").length >= 7
}

function marcarCampoInvalido(selector, mensaje) {
  const campo = $(selector)
  campo.addClass("is-invalid").removeClass("is-valid")
  campo.siblings(".invalid-feedback").text(mensaje)
}

function marcarCampoValido(selector) {
  const campo = $(selector)
  campo.addClass("is-valid").removeClass("is-invalid")
}

function limpiarFormularioProveedor() {
  $("#formularioProveedor")[0].reset()
  $("#formularioProveedor .form-control").removeClass("is-valid is-invalid")
  $("#codigoProveedor").val("")
}

function generarBadgeEstado(estado) {
  return estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'
}

function formatearFecha(fecha) {
  if (!fecha) return "N/A"
  const options = { year: "numeric", month: "long", day: "numeric" }
  return new Date(fecha).toLocaleDateString("es-ES", options)
}

function mostrarTablaVacia() {
  tablaProveedores.clear().draw()
}

function mostrarError(mensaje) {
  const toastEl = document.getElementById('liveToast');
  const toastBody = toastEl.querySelector('.toast-body');
  
  toastEl.className = 'toast align-items-center text-white border-0 bg-danger';
  toastBody.innerHTML = `
    <i class="mdi mdi-alert-circle me-2"></i>
    ${mensaje}
  `;
  
  const toast = new bootstrap.Toast(toastEl, {
    animation: true,
    autohide: true,
    delay: 5000
  });
  
  toast.show();
  console.error("Error: ", mensaje);
}

function mostrarExito(mensaje) {
  const toastEl = document.getElementById('liveToast');
  const toastBody = toastEl.querySelector('.toast-body');
  
  toastEl.className = 'toast align-items-center text-white border-0 bg-success';
  toastBody.innerHTML = `
    <i class="mdi mdi-check-circle me-2"></i>
    ${mensaje}
  `;
  
  const toast = new bootstrap.Toast(toastEl, {
    animation: true,
    autohide: true,
    delay: 1000
  });
  
  toast.show();
  console.log("Exito: ", mensaje);
}

function manejarErrorAjax(xhr, estado, error, accion) {
  let mensaje = `Error al ${accion}`

  if (xhr.status === 401) {
    mensaje = "Sesión expirada. Redirigiendo al login..."
    setTimeout(() => {
      window.location.href = "../Login/"
    }, 2000)
  } else if (xhr.status === 403) {
    mensaje = "No tiene permisos para realizar esta acción"
  } else if (xhr.status === 0) {
    mensaje = "Error de conexión. Verifique su conexión a internet."
  }

  mostrarError(mensaje)
  console.error(`Error AJAX en ${accion}:`, error, xhr)
}