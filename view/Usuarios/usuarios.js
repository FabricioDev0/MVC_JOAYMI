/**
 * JavaScript para gestión de usuarios - Sistema JOAYMI
 * Maneja CRUD de usuarios con validaciones y control de permisos
 */

let tablaUsuarios
let usuarioIdEliminar = null

// Inicialización cuando el documento esté listo
$(document).ready(() => {
  inicializarModuloUsuarios()
})

/**
 * Inicializa todos los componentes del módulo de usuarios
 */
function inicializarModuloUsuarios() {
  inicializarTablaUsuarios()
  inicializarEventos()
  cargarRolesDisponibles()

  console.log("Módulo de usuarios inicializado correctamente")
}

/**
 * Inicializa la tabla de usuarios con DataTables
 */
function inicializarTablaUsuarios() {
  tablaUsuarios = $("#tablaUsuarios").DataTable({
    responsive: true,
    processing: true,
    serverSide: false,
    destroy: true,
    language: {
      processing: "Procesando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron usuarios",
      emptyTable: "No hay usuarios registrados",
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
      },
      {
        extend: "excel",
        text: '<i class="mdi mdi-file-excel"></i> Excel',
        className: "btn btn-success btn-sm",
      },
      {
        extend: "csv",
        text: '<i class="mdi mdi-file-delimited"></i> CSV',
        className: "btn btn-info btn-sm",
      },
    ],
    order: [[0, "desc"]],
    columnDefs: [
      {
        targets: [5], // Columna de acciones
        orderable: false,
        searchable: false,
      },
    ],
  })

  // Cargar datos iniciales
  cargarUsuarios()
}

/**
 * Inicializa todos los eventos del módulo
 */
function inicializarEventos() {
  // Evento para nuevo usuario
  $("#btnNuevoUsuario").on("click", abrirModalNuevoUsuario)

  // Evento para envío del formulario
  $("#formularioUsuario").on("submit", guardarUsuario)

  // Evento para mostrar/ocultar contraseña
  $("#mostrarContrasena").on("click", alternarVisibilidadContrasena)

  // Evento para confirmar eliminación
  $("#btnConfirmarEliminar").on("click", confirmarEliminacionUsuario)

  // Evento para búsqueda personalizada
  $("#buscarUsuario").on("keyup", function () {
    tablaUsuarios.search(this.value).draw()
  })

  // Limpiar formulario al cerrar modal
  $("#modalMantenimientoUsuario").on("hidden.bs.modal", limpiarFormularioUsuario)
}

/**
 * Carga la lista de usuarios desde el API
 */
function cargarUsuarios() {
  console.log("Cargando usuarios...")

  // Mostrar indicador de carga
  $("#tablaUsuarios tbody").html(`
    <tr>
      <td colspan="6" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <div class="mt-2">Cargando usuarios...</div>
      </td>
    </tr>
  `)

  $.ajax({
    url: "../../public/usuarios_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      console.log("Respuesta usuarios:", respuesta)

      if (respuesta.exito && respuesta.datos) {
        procesarDatosUsuarios(respuesta.datos)
        console.log(`Usuarios cargados: ${respuesta.datos.length}`)
      } else {
        mostrarError("Error al cargar usuarios: " + (respuesta.mensaje || "Respuesta inválida"))
        mostrarTablaVacia()
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error cargando usuarios:", error, xhr)
      manejarErrorAjax(xhr, estado, error, "cargar usuarios")
      mostrarTablaVacia()
    },
  })
}

/**
 * Procesa los datos de usuarios y los muestra en la tabla
 */
function procesarDatosUsuarios(usuarios) {
  // Limpiar tabla
  tablaUsuarios.clear()

  // Procesar cada usuario
  usuarios.forEach((usuario) => {
    const fila = [
      usuario.cod_usuario,
      usuario.str_correo,
      obtenerNombreRol(usuario.cod_rol),
      generarBadgeEstado(usuario.est_activo),
      formatearFecha(usuario.fec_creacion),
      generarBotonesAccion(usuario),
    ]

    tablaUsuarios.row.add(fila)
  })

  // Redibujar tabla
  tablaUsuarios.draw()
}

/**
 * Genera los botones de acción para cada usuario
 */
function generarBotonesAccion(usuario) {
  return `
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-outline-primary btn-sm" 
              onclick="editarUsuario(${usuario.cod_usuario})"
              data-bs-toggle="tooltip" title="Editar usuario">
        <i class="mdi mdi-pencil"></i>
      </button>
      <button type="button" class="btn btn-outline-danger btn-sm" 
              onclick="eliminarUsuario(${usuario.cod_usuario}, '${usuario.str_correo}')"
              data-bs-toggle="tooltip" title="Eliminar usuario">
        <i class="mdi mdi-trash-can"></i>
      </button>
    </div>
  `
}

/**
 * Abre el modal para crear un nuevo usuario
 */
function abrirModalNuevoUsuario() {
  console.log("Abriendo modal nuevo usuario")
  limpiarFormularioUsuario()
  $("#tituloModalUsuario").text("Crear Nuevo Usuario")
  $("#btnGuardarUsuario").html('<i class="mdi mdi-content-save me-1"></i> Crear Usuario')
  $("#informacionAdicional").hide()
  $("#modalMantenimientoUsuario").modal("show")
}

/**
 * Editar usuario
 */
function editarUsuario(codigoUsuario) {
  console.log("Editando usuario:", codigoUsuario)

  $.ajax({
    url: `../../public/usuarios_api.php?accion=buscar&codigo=${codigoUsuario}`,
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        llenarFormularioEdicion(respuesta.datos)
        $("#tituloModalUsuario").text("Editar Usuario")
        $("#btnGuardarUsuario").html('<i class="mdi mdi-content-save me-1"></i> Actualizar Usuario')
        $("#informacionAdicional").show()
        $("#modalMantenimientoUsuario").modal("show")
      } else {
        mostrarError("Error al cargar datos del usuario")
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error:", error)
      mostrarError("Error al cargar datos del usuario")
    },
  })
}

/**
 * Llena el formulario con los datos del usuario para edición
 */
function llenarFormularioEdicion(usuario) {
  $("#codigoUsuario").val(usuario.cod_usuario)
  $("#correoElectronico").val(usuario.str_correo)
  $("#claveAcceso").val("")
  $("#codigoRol").val(usuario.cod_rol)
  $("#fechaCreacionUsuario").text(formatearFecha(usuario.fec_creacion))

  // Hacer opcional la contraseña en edición
  $("#claveAcceso").removeAttr("required")
  $("#claveAcceso").attr("placeholder", "Dejar vacío para mantener contraseña actual")
}

/**
 * Guarda o actualiza un usuario
 */
function guardarUsuario(evento) {
  evento.preventDefault()

  if (!validarFormularioUsuario()) {
    return
  }

  const esEdicion = $("#codigoUsuario").val() !== ""
  const datosFormulario = obtenerDatosFormulario()

  console.log("Guardando usuario:", datosFormulario)

  const btnGuardar = $("#btnGuardarUsuario")
  const textoOriginal = btnGuardar.html()
  btnGuardar.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Guardando...')

  const url = esEdicion
    ? "../../public/usuarios_api.php?accion=actualizar"
    : "../../public/usuarios_api.php?accion=crear"

  const metodo = esEdicion ? "PUT" : "POST"

  $.ajax({
    url: url,
    type: metodo,
    data: JSON.stringify(datosFormulario),
    contentType: "application/json",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito) {
        $("#modalMantenimientoUsuario").modal("hide")
        cargarUsuarios()

        const mensaje = esEdicion ? "Usuario actualizado correctamente" : "Usuario creado correctamente"
        mostrarExito(mensaje)
      } else {
        mostrarError("Error al guardar usuario: " + respuesta.mensaje)
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error guardando:", error)
      mostrarError("Error al guardar usuario")
    },
    complete: () => {
      btnGuardar.prop("disabled", false).html(textoOriginal)
    },
  })
}

/**
 * Eliminar usuario
 */
function eliminarUsuario(codigoUsuario, correoUsuario) {
  usuarioIdEliminar = codigoUsuario
  $("#nombreUsuarioEliminar").text(correoUsuario)
  $("#modalEliminarUsuario").modal("show")
}

/**
 * Confirma la eliminación del usuario
 */
function confirmarEliminacionUsuario() {
  if (!usuarioIdEliminar) return

  const btnConfirmar = $("#btnConfirmarEliminar")
  const textoOriginal = btnConfirmar.html()
  btnConfirmar.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Eliminando...')

  $.ajax({
    url: "../../public/usuarios_api.php?accion=eliminar",
    type: "DELETE",
    data: JSON.stringify({ codigoUsuario: usuarioIdEliminar }),
    contentType: "application/json",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito) {
        $("#modalEliminarUsuario").modal("hide")
        cargarUsuarios()
        mostrarExito("Usuario eliminado correctamente")
      } else {
        mostrarError("Error al eliminar usuario: " + respuesta.mensaje)
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error eliminando:", error)
      mostrarError("Error al eliminar usuario")
    },
    complete: () => {
      btnConfirmar.prop("disabled", false).html(textoOriginal)
      usuarioIdEliminar = null
    },
  })
}

/**
 * Carga los roles disponibles
 */
function cargarRolesDisponibles() {
  $.ajax({
    url: "../../public/roles_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        const selectRol = $("#codigoRol")
        selectRol.empty().append('<option value="">Seleccione un rol...</option>')

        respuesta.datos.forEach((rol) => {
          selectRol.append(`<option value="${rol.cod_rol}">${rol.str_nombre}</option>`)
        })

        console.log(`Roles cargados: ${respuesta.datos.length}`)
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error cargando roles:", error)
    },
  })
}

// === FUNCIONES DE UTILIDAD ===

function obtenerDatosFormulario() {
  const datos = {
    correoElectronico: $("#correoElectronico").val().trim(),
    codigoRol: Number.parseInt($("#codigoRol").val()),
  }

  const claveAcceso = $("#claveAcceso").val().trim()
  if (claveAcceso) {
    datos.claveAcceso = claveAcceso
  }

  const codigoUsuario = $("#codigoUsuario").val()
  if (codigoUsuario) {
    datos.codigoUsuario = Number.parseInt(codigoUsuario)
  }

  return datos
}

function validarFormularioUsuario() {
  let esValido = true

  // Validar email
  const email = $("#correoElectronico").val().trim()
  if (!email || !validarFormatoEmail(email)) {
    marcarCampoInvalido("#correoElectronico", "Ingrese un correo electrónico válido")
    esValido = false
  } else {
    marcarCampoValido("#correoElectronico")
  }

  // Validar contraseña
  const esNuevoUsuario = !$("#codigoUsuario").val()
  const claveAcceso = $("#claveAcceso").val().trim()

  if (esNuevoUsuario && (!claveAcceso || claveAcceso.length < 6)) {
    marcarCampoInvalido("#claveAcceso", "La contraseña debe tener al menos 6 caracteres")
    esValido = false
  } else if (claveAcceso && claveAcceso.length < 6) {
    marcarCampoInvalido("#claveAcceso", "La contraseña debe tener al menos 6 caracteres")
    esValido = false
  } else {
    marcarCampoValido("#claveAcceso")
  }

  // Validar rol
  const rol = $("#codigoRol").val()
  if (!rol) {
    marcarCampoInvalido("#codigoRol", "Seleccione un rol para el usuario")
    esValido = false
  } else {
    marcarCampoValido("#codigoRol")
  }

  return esValido
}

function validarFormatoEmail(email) {
  const patron = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return patron.test(email)
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

function limpiarFormularioUsuario() {
  $("#formularioUsuario")[0].reset()
  $("#formularioUsuario .form-control").removeClass("is-valid is-invalid")
  $("#codigoUsuario").val("")
  $("#claveAcceso").attr("required", "required").attr("placeholder", "Mínimo 6 caracteres")
}

function alternarVisibilidadContrasena() {
  const campoContrasena = $("#claveAcceso")
  const icono = $("#iconoOjo")

  if (campoContrasena.attr("type") === "password") {
    campoContrasena.attr("type", "text")
    icono.removeClass("mdi-eye").addClass("mdi-eye-off")
  } else {
    campoContrasena.attr("type", "password")
    icono.removeClass("mdi-eye-off").addClass("mdi-eye")
  }
}

function obtenerNombreRol(codigoRol) {
  const roles = {
    1: "Administrador",
    2: "Usuario",
  }
  return roles[codigoRol] || "Desconocido"
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
  tablaUsuarios.clear().draw()
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

// Funciones de exportación
function exportarUsuarios() {
  $(".buttons-excel").trigger("click")
}

function actualizarTablaUsuarios() {
  cargarUsuarios()
  mostrarExito("Tabla actualizada correctamente")
}