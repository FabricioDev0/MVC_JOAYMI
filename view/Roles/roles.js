/**
 * JavaScript para gestión de roles - Sistema JOAYMI
 * Maneja consulta y visualización de roles predefinidos
 *
 */

let tablaRoles
let datosUsuarios = [] // Para calcular estadísticas
const $ = window.$ 
const Swal = window.Swal 

// Inicialización cuando el documento esté listo
$(document).ready(() => {
  console.log("Inicializando módulo de roles...")
  inicializarModuloRoles()
})

/**
 * Inicializa todos los componentes del módulo de roles
 */
function inicializarModuloRoles() {
  inicializarTablaRoles()
  inicializarEventos()
  cargarDatosUsuarios() // Para estadísticas

  console.log("Módulo de roles inicializado correctamente")
}

/**
 * Inicializa la tabla de roles con DataTables
 */
function inicializarTablaRoles() {
  tablaRoles = $("#tablaRoles").DataTable({
    responsive: true,
    processing: true,
    serverSide: false,
    destroy: true,
    language: {
      processing: "Procesando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron roles",
      emptyTable: "No hay roles registrados",
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
    order: [[0, "asc"]],
    columnDefs: [
      {
        targets: [5], // Columna de acciones
        orderable: false,
        searchable: false,
      },
    ],
  })

  // Cargar datos iniciales
  cargarRoles()
}

/**
 * Inicializa todos los eventos del módulo
 */
function inicializarEventos() {
  // Evento para búsqueda personalizada
  $("#buscarRol").on("keyup", function () {
    tablaRoles.search(this.value).draw()
  })

  // Limpiar modal al cerrar
  $("#modalInformacionRol").on("hidden.bs.modal", limpiarModalInformacion)
}

/**
 * Carga la lista de roles desde el API
 */
function cargarRoles() {
  console.log("Cargando roles...")

  // Mostrar indicador de carga
  $("#tablaRoles tbody").html(`
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="mt-2">Cargando roles...</div>
            </td>
        </tr>
    `)

  $.ajax({
    url: "../../public/roles_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      console.log("Respuesta roles:", respuesta)

      if (respuesta.exito && respuesta.datos) {
        procesarDatosRoles(respuesta.datos)
        console.log(`Roles cargados: ${respuesta.datos.length}`)
      } else {
        mostrarError("Error al cargar roles: " + (respuesta.mensaje || "Respuesta inválida"))
        mostrarTablaVacia()
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error cargando roles:", error, xhr)
      manejarErrorAjax(xhr, estado, error, "cargar roles")
      mostrarTablaVacia()
    },
  })
}

/**
 * Procesa los datos de roles y los muestra en la tabla
 */
function procesarDatosRoles(roles) {
  // Limpiar tabla
  tablaRoles.clear()

  // Procesar cada rol
  roles.forEach((rol) => {
    const fila = [
      rol.cod_rol,
      generarNombreRolConIcono(rol.str_nombre, rol.cod_rol),
      generarBadgeEstado(rol.est_activo),
      formatearFecha(rol.fec_creacion),
      generarDescripcionRol(rol.cod_rol),
      generarBotonesAccion(rol),
    ]

    tablaRoles.row.add(fila)
  })

  // Redibujar tabla
  tablaRoles.draw()
}

/**
 * Genera el nombre del rol con icono
 */
function generarNombreRolConIcono(nombreRol, codigoRol) {
  const iconos = {
    1: '<i class="mdi mdi-shield-crown text-warning me-2"></i>',
    2: '<i class="mdi mdi-account text-primary me-2"></i>',
  }

  const icono = iconos[codigoRol] || '<i class="mdi mdi-help-circle text-muted me-2"></i>'
  return `${icono}<span class="fw-medium">${nombreRol}</span>`
}

/**
 * Genera descripción breve del rol
 */
function generarDescripcionRol(codigoRol) {
  const descripciones = {
    1: '<small class="text-muted">Acceso completo al sistema</small>',
    2: '<small class="text-muted">Acceso limitado a funciones básicas</small>',
  }

  return descripciones[codigoRol] || '<small class="text-muted">Rol personalizado</small>'
}

/**
 * Genera los botones de acción para cada rol
 */
function generarBotonesAccion(rol) {
  return `
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-info btn-sm" 
                    onclick="verInformacionRol(${rol.cod_rol})"
                    data-bs-toggle="tooltip" title="Ver información detallada">
                <i class="mdi mdi-information"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" 
                    onclick="mostrarAyudaRoles()"
                    data-bs-toggle="tooltip" title="Ayuda sobre roles">
                <i class="mdi mdi-help-circle"></i>
            </button>
        </div>
    `
}

/**
 * Muestra información detallada de un rol
 */
function verInformacionRol(codigoRol) {
  console.log("Viendo información del rol:", codigoRol)

  $.ajax({
    url: `../../public/roles_api.php?accion=buscar&codigo=${codigoRol}`,
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        llenarModalInformacion(respuesta.datos)
        calcularEstadisticasRol(codigoRol)
        $("#modalInformacionRol").modal("show")
      } else {
        mostrarError("Error al cargar información del rol")
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error:", error)
      mostrarError("Error al cargar información del rol")
    },
  })
}

/**
 * Llena el modal con la información del rol
 */
function llenarModalInformacion(rol) {
  $("#tituloModalRol").text(`Información del Rol: ${rol.str_nombre}`)
  $("#codigoRolInfo").text(rol.cod_rol)
  $("#nombreRolInfo").text(rol.str_nombre)
  $("#fechaCreacionRolInfo").text(formatearFecha(rol.fec_creacion))

  // Estado
  const estadoBadge = rol.est_activo
    ? '<span class="badge bg-success">Activo</span>'
    : '<span class="badge bg-danger">Inactivo</span>'
  $("#estadoRolInfo").html(estadoBadge)

  // Permisos específicos del rol
  const permisosHtml = generarPermisosRol(rol.cod_rol)
  $("#permisosRolInfo").html(permisosHtml)
}

/**
 * Genera la lista de permisos según el rol
 */
function generarPermisosRol(codigoRol) {
  const permisos = {
    1: [
      // Administrador
      { permiso: "Gestión completa de usuarios", activo: true },
      { permiso: "Crear, editar y eliminar productos", activo: true },
      { permiso: "Gestión de categorías y proveedores", activo: true },
      { permiso: "Acceso a reportes y estadísticas", activo: true },
      { permiso: "Configuración del sistema", activo: true },
      { permiso: "Gestión de roles y permisos", activo: true },
    ],
    2: [
      // Usuario
      { permiso: "Consulta de productos y categorías", activo: true },
      { permiso: "Creación de registros básicos", activo: true },
      { permiso: "Edición de registros propios", activo: true },
      { permiso: "Gestión de usuarios", activo: false },
      { permiso: "Eliminación de registros", activo: false },
      { permiso: "Configuración del sistema", activo: false },
    ],
  }

  const permisosRol = permisos[codigoRol] || []
  let html = '<div class="row">'

  permisosRol.forEach((item, index) => {
    const icono = item.activo ? "mdi-check text-success" : "mdi-close text-danger"
    const clase = index % 2 === 0 ? "col-md-6" : "col-md-6"

    html += `
            <div class="${clase}">
                <div class="d-flex align-items-center mb-2">
                    <i class="mdi ${icono} me-2"></i>
                    <span class="${item.activo ? "" : "text-muted"}">${item.permiso}</span>
                </div>
            </div>
        `
  })

  html += "</div>"
  return html
}

/**
 * Calcula estadísticas de uso del rol
 */
function calcularEstadisticasRol(codigoRol) {
  // Contar usuarios con este rol
  const usuariosConRol = datosUsuarios.filter((usuario) => usuario.cod_rol == codigoRol).length
  const totalUsuarios = datosUsuarios.length
  const porcentaje = totalUsuarios > 0 ? Math.round((usuariosConRol / totalUsuarios) * 100) : 0

  $("#usuariosConRol").text(usuariosConRol)
  $("#porcentajeUso").text(`${porcentaje}%`)
  $("#ultimoAcceso").text("Hoy") // Placeholder
}

/**
 * Carga datos de usuarios para estadísticas
 */
function cargarDatosUsuarios() {
  $.ajax({
    url: "../../public/usuarios_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        datosUsuarios = respuesta.datos
        console.log("Datos de usuarios cargados para estadísticas")
      }
    },
    error: () => {
      console.warn("No se pudieron cargar datos de usuarios para estadísticas")
    },
  })
}

/**
 * Muestra modal de ayuda
 */
function mostrarAyudaRoles() {
  $("#modalAyudaRoles").modal("show")
}

/**
 * Exporta información del rol actual
 */
function exportarInformacionRol() {
  // Implementar exportación si es necesario
  mostrarExito("Función de exportación disponible próximamente")
}

/**
 * Actualiza la tabla de roles
 */
function actualizarTablaRoles() {
  console.log("Actualizando tabla de roles...")
  cargarRoles()
  mostrarExito("Tabla de roles actualizada correctamente")
}

// === FUNCIONES DE UTILIDAD ===

function generarBadgeEstado(estado) {
  return estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'
}

function formatearFecha(fecha) {
  if (!fecha) return "N/A"
  const options = { year: "numeric", month: "long", day: "numeric" }
  return new Date(fecha).toLocaleDateString("es-ES", options)
}

function mostrarTablaVacia() {
  tablaRoles.clear().draw()
}

function limpiarModalInformacion() {
  $("#codigoRolInfo").text("-")
  $("#nombreRolInfo").text("-")
  $("#fechaCreacionRolInfo").text("-")
  $("#permisosRolInfo").html("")
  $("#usuariosConRol").text("0")
  $("#porcentajeUso").text("0%")
  $("#ultimoAcceso").text("-")
}

function mostrarError(mensaje) {
  if (typeof Swal !== "undefined") {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: mensaje,
      timer: 3000,
      showConfirmButton: false,
    })
  } else {
    alert(mensaje)
  }
  console.error("Error: ", mensaje)
}

function mostrarExito(mensaje) {
  if (typeof Swal !== "undefined") {
    Swal.fire({
      icon: "success",
      title: "¡Éxito!",
      text: mensaje,
      timer: 2000,
      showConfirmButton: false,
    })
  } else {
    alert(mensaje)
  }
  console.log("Exito: ", mensaje)
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

// Funciones adicionales para exportación
function exportarRoles() {
  $(".buttons-excel").trigger("click")
}
