/**
 * JavaScript para gestión de categorías - Sistema JOAYMI
 * Maneja CRUD de categorías con validaciones y previsualización
 */

let tablaCategorias
let categoriaIdEliminar = null
let categoriaIdVista = null
const $ = window.$ 
const Swal = window.Swal 

// Inicialización cuando el documento esté listo
$(document).ready(() => {
  inicializarModuloCategorias()
})

/**
 * Inicializa todos los componentes del módulo de categorías
 */
function inicializarModuloCategorias() {
  inicializarTablaCategorias()
  inicializarEventos()

  console.log("Módulo de categorías inicializado correctamente")
}

/**
 * Inicializa la tabla de categorías con DataTables
 */
function inicializarTablaCategorias() {
  tablaCategorias = $("#tablaCategorias").DataTable({
    responsive: true,
    processing: true,
    serverSide: false,
    destroy: true,
    language: {
      processing: "Procesando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron categorías",
      emptyTable: "No hay categorías registradas",
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
  cargarCategorias()
}

/**
 * Inicializa todos los eventos del módulo
 */
function inicializarEventos() {
  // Evento para nueva categoría
  $("#btnNuevaCategoria").on("click", abrirModalNuevaCategoria)

  // Evento para envío del formulario
  $("#formularioCategoria").on("submit", guardarCategoria)

  // Evento para confirmar eliminación
  $("#btnConfirmarEliminarCategoria").on("click", confirmarEliminacionCategoria)

  // Evento para búsqueda personalizada
  $("#buscarCategoria").on("keyup", function () {
    tablaCategorias.search(this.value).draw()
  })

  // Eventos para previsualización en tiempo real
  $("#nombreCategoria").on("input", actualizarPrevisualizacion)
  $("#descripcionCategoria").on("input", actualizarPrevisualizacion)

  // Limpiar formulario al cerrar modal
  $("#modalMantenimientoCategoria").on("hidden.bs.modal", limpiarFormularioCategoria)
}

/**
 * Carga la lista de categorías desde el API
 */
function cargarCategorias() {
  console.log("Cargando categorías...")

  // Mostrar indicador de carga
  $("#tablaCategorias tbody").html(`
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="mt-2">Cargando categorías...</div>
            </td>
        </tr>
    `)

  $.ajax({
    url: "../../public/categorias_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      console.log("Respuesta categorías:", respuesta)

      if (respuesta.exito && respuesta.datos) {
        procesarDatosCategorias(respuesta.datos)
        console.log(`Categorías cargadas: ${respuesta.datos.length}`)
      } else {
        mostrarError("Error al cargar categorías: " + (respuesta.mensaje || "Respuesta inválida"))
        mostrarTablaVacia()
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error cargando categorías:", error, xhr)
      manejarErrorAjax(xhr, estado, error, "cargar categorías")
      mostrarTablaVacia()
    },
  })
}

/**
 * Procesa los datos de categorías y los muestra en la tabla
 */
function procesarDatosCategorias(categorias) {
  // Limpiar tabla
  tablaCategorias.clear()

  // Procesar cada categoría
  categorias.forEach((categoria) => {
    const fila = [
      categoria.cod_categoria,
      `<span class="fw-medium">${categoria.str_nombre}</span>`,
      truncarTexto(categoria.str_descripcion, 50),
      generarBadgeEstado(categoria.est_activo),
      formatearFecha(categoria.fec_creacion),
      generarBotonesAccion(categoria),
    ]

    tablaCategorias.row.add(fila)
  })

  // Redibujar tabla
  tablaCategorias.draw()
}

/**
 * Genera los botones de acción para cada categoría
 */
function generarBotonesAccion(categoria) {
  const esAdministrador = true // Se puede obtener de la sesión si es necesario

  return `
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-info btn-sm" 
                    onclick="verDetalleCategoria(${categoria.cod_categoria})"
                    data-bs-toggle="tooltip" title="Ver detalles">
                <i class="mdi mdi-eye"></i>
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm" 
                    onclick="editarCategoria(${categoria.cod_categoria})"
                    data-bs-toggle="tooltip" title="Editar categoría">
                <i class="mdi mdi-pencil"></i>
            </button>
            ${
              esAdministrador
                ? `<button type="button" class="btn btn-outline-danger btn-sm" 
                    onclick="eliminarCategoria(${categoria.cod_categoria}, '${categoria.str_nombre}')"
                    data-bs-toggle="tooltip" title="Eliminar categoría">
                <i class="mdi mdi-trash-can"></i>
            </button>`
                : ""
            }
        </div>
    `
}

/**
 * Abre el modal para crear una nueva categoría
 */
function abrirModalNuevaCategoria() {
  console.log("Abriendo modal nueva categoría")
  limpiarFormularioCategoria()
  $("#tituloModalCategoria").text("Crear Nueva Categoría")
  $("#btnGuardarCategoria").html('<i class="mdi mdi-content-save me-1"></i> Crear Categoría')
  $("#informacionAdicionalCategoria").hide()
  $("#modalMantenimientoCategoria").modal("show")
}

/**
 * Ver detalle de una categoría
 */
function verDetalleCategoria(codigoCategoria) {
  console.log("👁️ Viendo detalle categoría:", codigoCategoria)
  categoriaIdVista = codigoCategoria

  $.ajax({
    url: `../../public/categorias_api.php?accion=buscar&codigo=${codigoCategoria}`,
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        llenarModalVista(respuesta.datos)
        cargarProductosCategoria(codigoCategoria)
        $("#modalVistaCategoria").modal("show")
      } else {
        mostrarError("Error al cargar datos de la categoría")
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error:", error)
      mostrarError("Error al cargar datos de la categoría")
    },
  })
}

/**
 * Editar categoría
 */
function editarCategoria(codigoCategoria) {
  console.log("Editando categoría:", codigoCategoria)

  $.ajax({
    url: `../../public/categorias_api.php?accion=buscar&codigo=${codigoCategoria}`,
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        llenarFormularioEdicion(respuesta.datos)
        $("#tituloModalCategoria").text("Editar Categoría")
        $("#btnGuardarCategoria").html('<i class="mdi mdi-content-save me-1"></i> Actualizar Categoría')
        $("#informacionAdicionalCategoria").show()
        $("#modalMantenimientoCategoria").modal("show")
      } else {
        mostrarError("Error al cargar datos de la categoría")
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error:", error)
      mostrarError("Error al cargar datos de la categoría")
    },
  })
}

/**
 * Llena el formulario con los datos de la categoría para edición
 */
function llenarFormularioEdicion(categoria) {
  $("#codigoCategoria").val(categoria.cod_categoria)
  $("#nombreCategoria").val(categoria.str_nombre)
  $("#descripcionCategoria").val(categoria.str_descripcion)
  $("#fechaCreacionCategoria").text(formatearFecha(categoria.fec_creacion))

  // Actualizar previsualización
  actualizarPrevisualizacion()

  // Simular contador de productos
  $("#contadorProductos").text("0 productos")
}

/**
 * Llena el modal de vista con los datos de la categoría
 */
function llenarModalVista(categoria) {
  $("#tituloVistaCategoria").text(`Detalles: ${categoria.str_nombre}`)
  $("#vistaCodigoCategoria").text(categoria.cod_categoria)
  $("#vistaNombreCategoria").text(categoria.str_nombre)
  $("#vistaDescripcionCategoria").text(categoria.str_descripcion)
  $("#vistaFechaCreacion").text(formatearFecha(categoria.fec_creacion))

  const estadoBadge = categoria.est_activo
    ? '<span class="badge bg-success">Activo</span>'
    : '<span class="badge bg-danger">Inactivo</span>'
  $("#vistaEstadoCategoria").html(estadoBadge)
}

/**
 * Carga los productos de una categoría específica
 */
function cargarProductosCategoria(codigoCategoria) {
  $("#listaProductosCategoria").html(`
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `)

  $.ajax({
    url: `../../public/productos_api.php?accion=porCategoria&codigoCategoria=${codigoCategoria}`,
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        mostrarProductosCategoria(respuesta.datos)
        $("#vistaContadorProductos").text(respuesta.datos.length)
      } else {
        $("#listaProductosCategoria").html('<p class="text-muted">No hay productos en esta categoría</p>')
        $("#vistaContadorProductos").text("0")
      }
    },
    error: () => {
      $("#listaProductosCategoria").html('<p class="text-danger">Error al cargar productos</p>')
      $("#vistaContadorProductos").text("-")
    },
  })
}

/**
 * Muestra la lista de productos de la categoría
 */
function mostrarProductosCategoria(productos) {
  if (productos.length === 0) {
    $("#listaProductosCategoria").html('<p class="text-muted">No hay productos en esta categoría</p>')
    return
  }

  let html = '<div class="row">'
  productos.slice(0, 6).forEach((producto) => {
    // Mostrar solo los primeros 6
    html += `
            <div class="col-md-6 mb-2">
                <div class="border rounded p-2">
                    <h6 class="mb-1">${producto.str_nombre}</h6>
                    <small class="text-muted">Stock: ${producto.int_stock} | Precio: $${producto.dec_precio}</small>
                </div>
            </div>
        `
  })

  if (productos.length > 6) {
    html += `<div class="col-12"><small class="text-muted">Y ${productos.length - 6} productos más...</small></div>`
  }

  html += "</div>"
  $("#listaProductosCategoria").html(html)
}

/**
 * Editar categoría desde el modal de vista
 */
function editarCategoriaDesdeVista() {
  $("#modalVistaCategoria").modal("hide")
  setTimeout(() => {
    editarCategoria(categoriaIdVista)
  }, 300)
}

/**
 * Guarda o actualiza una categoría
 */
function guardarCategoria(evento) {
  evento.preventDefault()

  if (!validarFormularioCategoria()) {
    return
  }

  const esEdicion = $("#codigoCategoria").val() !== ""
  const datosFormulario = obtenerDatosFormulario()

  console.log("Guardando categoría:", datosFormulario)

  const btnGuardar = $("#btnGuardarCategoria")
  const textoOriginal = btnGuardar.html()
  btnGuardar.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Guardando...')

  const url = esEdicion
    ? "../../public/categorias_api.php?accion=actualizar"
    : "../../public/categorias_api.php?accion=crear"

  const metodo = esEdicion ? "PUT" : "POST"

  $.ajax({
    url: url,
    type: metodo,
    data: JSON.stringify(datosFormulario),
    contentType: "application/json",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito) {
        $("#modalMantenimientoCategoria").modal("hide")
        cargarCategorias()

        const mensaje = esEdicion ? "Categoría actualizada correctamente" : "Categoría creada correctamente"
        mostrarExito(mensaje)
      } else {
        mostrarError("Error al guardar categoría: " + respuesta.mensaje)
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error guardando:", error)
      mostrarError("Error al guardar categoría")
    },
    complete: () => {
      btnGuardar.prop("disabled", false).html(textoOriginal)
    },
  })
}

/**
 * Eliminar categoría
 */
function eliminarCategoria(codigoCategoria, nombreCategoria) {
  categoriaIdEliminar = codigoCategoria
  $("#nombreCategoriaEliminar").text(nombreCategoria)
  $("#modalEliminarCategoria").modal("show")
}

/**
 * Confirma la eliminación de la categoría
 */
function confirmarEliminacionCategoria() {
  if (!categoriaIdEliminar) return

  const btnConfirmar = $("#btnConfirmarEliminarCategoria")
  const textoOriginal = btnConfirmar.html()
  btnConfirmar.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Eliminando...')

  $.ajax({
    url: "../../public/categorias_api.php?accion=eliminar",
    type: "DELETE",
    data: JSON.stringify({ codigoCategoria: categoriaIdEliminar }),
    contentType: "application/json",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito) {
        $("#modalEliminarCategoria").modal("hide")
        cargarCategorias()
        mostrarExito("Categoría eliminada correctamente")
      } else {
        mostrarError("Error al eliminar categoría: " + respuesta.mensaje)
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error eliminando:", error)
      mostrarError("Error al eliminar categoría")
    },
    complete: () => {
      btnConfirmar.prop("disabled", false).html(textoOriginal)
      categoriaIdEliminar = null
    },
  })
}

/**
 * Actualiza la previsualización en tiempo real
 */
function actualizarPrevisualizacion() {
  const nombre = $("#nombreCategoria").val().trim() || "-"
  const descripcion = $("#descripcionCategoria").val().trim() || "-"

  $("#previewNombre").text(nombre)
  $("#previewDescripcion").text(descripcion)
}

// === FUNCIONES DE UTILIDAD ===

function obtenerDatosFormulario() {
  const datos = {
    nombreCategoria: $("#nombreCategoria").val().trim(),
    descripcionCategoria: $("#descripcionCategoria").val().trim(),
  }

  const codigoCategoria = $("#codigoCategoria").val()
  if (codigoCategoria) {
    datos.codigoCategoria = Number.parseInt(codigoCategoria)
  }

  return datos
}

function validarFormularioCategoria() {
  let esValido = true

  // Validar nombre
  const nombre = $("#nombreCategoria").val().trim()
  if (!nombre || nombre.length < 3) {
    marcarCampoInvalido("#nombreCategoria", "El nombre debe tener al menos 3 caracteres")
    esValido = false
  } else if (nombre.length > 100) {
    marcarCampoInvalido("#nombreCategoria", "El nombre no puede exceder 100 caracteres")
    esValido = false
  } else {
    marcarCampoValido("#nombreCategoria")
  }

  // Validar descripción
  const descripcion = $("#descripcionCategoria").val().trim()
  if (!descripcion || descripcion.length < 10) {
    marcarCampoInvalido("#descripcionCategoria", "La descripción debe tener al menos 10 caracteres")
    esValido = false
  } else if (descripcion.length > 500) {
    marcarCampoInvalido("#descripcionCategoria", "La descripción no puede exceder 500 caracteres")
    esValido = false
  } else {
    marcarCampoValido("#descripcionCategoria")
  }

  return esValido
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

function limpiarFormularioCategoria() {
  $("#formularioCategoria")[0].reset()
  $("#formularioCategoria .form-control").removeClass("is-valid is-invalid")
  $("#codigoCategoria").val("")
  actualizarPrevisualizacion()
}

function truncarTexto(texto, longitud) {
  if (!texto) return "-"
  return texto.length > longitud ? texto.substring(0, longitud) + "..." : texto
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
  tablaCategorias.clear().draw()
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
  console.error("error: ", mensaje)
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
  console.log("exito: ", mensaje)
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

// Funciones adicionales
function actualizarTablaCategorias() {
  cargarCategorias()
  mostrarExito("Tabla actualizada correctamente")
}

function exportarCategorias() {
  $(".buttons-excel").trigger("click")
}
