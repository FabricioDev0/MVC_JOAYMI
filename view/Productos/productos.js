/**
 * JavaScript para gestión de productos - Sistema JOAYMI
 * Maneja CRUD de productos con validaciones y control de inventario
 */

let tablaProductos
let productoIdEliminar = null
let productoIdVista = null
const $ = window.$
const Swal = window.Swal

$(document).ready(() => {
  inicializarModuloProductos()
})

function inicializarModuloProductos() {
  inicializarTablaProductos()
  inicializarEventos()
  cargarCategoriasYProveedores()
  console.log("Módulo de productos inicializado correctamente")
}

function inicializarTablaProductos() {
  tablaProductos = $("#tablaProductos").DataTable({
    responsive: true,
    processing: true,
    destroy: true,
    language: {
      processing: "Procesando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron productos",
      emptyTable: "No hay productos registrados",
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
        targets: [9],
        orderable: false,
        searchable: false,
      },
    ],
    responsive: true,
  })

  cargarProductos()
}

function inicializarEventos() {
  $("#btnNuevoProducto").on("click", abrirModalNuevoProducto)
  $("#formularioProducto").on("submit", guardarProducto)
  $("#btnConfirmarEliminarProducto").on("click", confirmarEliminacionProducto)
  $("#stockProducto").on("input", validarStockTiempoReal)
  $("#precioProducto").on("input", validarPrecioTiempoReal)
  $("#modalMantenimientoProducto").on("hidden.bs.modal", limpiarFormularioProducto)
}

function cargarCategoriasYProveedores() {
  $.ajax({
    url: "../../public/categorias_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        llenarSelectCategorias(respuesta.datos)
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error cargando categorías:", error)
    },
  })

  $.ajax({
    url: "../../public/proveedores_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        llenarSelectProveedores(respuesta.datos)
      }
    },
    error: (xhr, estado, error) => {
      console.error("Error cargando proveedores:", error)
    },
  })
}

function llenarSelectCategorias(categorias) {
  const select = $("#categoriaProducto")
  select.empty().append('<option value="">Seleccionar categoría...</option>')

  categorias.forEach((categoria) => {
    if (categoria.est_activo) {
      select.append(`<option value="${categoria.cod_categoria}">${categoria.str_nombre}</option>`)
    }
  })
}

function llenarSelectProveedores(proveedores) {
  const select = $("#proveedorProducto")
  select.empty().append('<option value="">Seleccionar proveedor...</option>')

  proveedores.forEach((proveedor) => {
    if (proveedor.est_activo) {
      select.append(`<option value="${proveedor.cod_proveedor}">${proveedor.str_nombre}</option>`)
    }
  })
}

function cargarProductos() {
  console.log("Cargando productos...")

  $("#tablaProductos tbody").html(`
    <tr>
      <td colspan="10" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <div class="mt-2">Cargando productos...</div>
      </td>
    </tr>
  `)

  $.ajax({
    url: "../../public/productos_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        procesarDatosProductos(respuesta.datos)
      } else {
        mostrarError("Error al cargar productos: " + (respuesta.mensaje || "Respuesta inválida"))
        mostrarTablaVacia()
      }
    },
    error: (xhr, estado, error) => {
      manejarErrorAjax(xhr, estado, error, "cargar productos")
      mostrarTablaVacia()
    },
  })
}

function procesarDatosProductos(productos) {
  tablaProductos.clear()

  productos.forEach((producto) => {
    const fila = [
      producto.cod_producto,
      `<span class="fw-medium">${producto.str_nombre || "Sin nombre"}</span>`,
      producto.str_descripcion || "-",
      generarBadgeStock(producto.int_stock || 0),
      formatearPrecio(producto.dec_precio || 0),
      `<span class="text-info">${producto.categoria || "Sin categoría"}</span>`,
      `<span class="text-success">${producto.proveedor || "Sin proveedor"}</span>`,
      generarBadgeEstado(producto.est_activo),
      formatearFecha(producto.fec_creacion) || "-",
      generarBotonesAccion(producto),
    ]

    tablaProductos.row.add(fila)
  })

  tablaProductos.draw()
}

function generarBadgeStock(stock) {
  let clase = "bg-success"
  if (stock <= 5) {
    clase = "bg-danger"
  } else if (stock <= 10) {
    clase = "bg-warning"
  }
  return `<span class="badge ${clase}">${stock} unidades</span>`
}

function formatearPrecio(precio) {
  return `<span class="fw-bold text-success">S/${Number.parseFloat(precio).toFixed(2)}</span>`
}

function generarBotonesAccion(producto) {
  const esAdministrador = true

  return `
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-outline-info btn-sm" 
              onclick="verDetalleProducto(${producto.cod_producto})"
              data-bs-toggle="tooltip" title="Ver detalles">
        <i class="mdi mdi-eye"></i>
      </button>
      <button type="button" class="btn btn-outline-primary btn-sm" 
              onclick="editarProducto(${producto.cod_producto})"
              data-bs-toggle="tooltip" title="Editar producto">
        <i class="mdi mdi-pencil"></i>
      </button>
      ${
        esAdministrador
          ? `<button type="button" class="btn btn-outline-danger btn-sm" 
              onclick="eliminarProducto(${producto.cod_producto}, '${producto.str_nombre}')"
              data-bs-toggle="tooltip" title="Eliminar producto">
          <i class="mdi mdi-trash-can"></i>
        </button>`
          : ""
      }
    </div>
  `
}

function abrirModalNuevoProducto() {
  limpiarFormularioProducto()
  $("#tituloModalProducto").text("Crear Nuevo Producto")
  $("#btnGuardarProducto").html('<i class="mdi mdi-content-save me-1"></i> Crear Producto')
  $("#informacionAdicionalProducto").hide()
  $("#modalMantenimientoProducto").modal("show")
}

function verDetalleProducto(codigoProducto) {
  productoIdVista = codigoProducto

  $.ajax({
    url: `../../public/productos_api.php?accion=buscar&codigo=${codigoProducto}`,
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        llenarModalVista(respuesta.datos)
        $("#modalVistaProducto").modal("show")
      } else {
        mostrarError("Error al cargar datos del producto: " + (respuesta.mensaje || "Producto no encontrado"))
      }
    },
    error: (xhr, estado, error) => {
      mostrarError("Error de conexión al cargar datos del producto")
    },
  })
}

function editarProducto(codigoProducto) {
  $.ajax({
    url: `../../public/productos_api.php?accion=buscar&codigo=${codigoProducto}`,
    type: "GET",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        llenarFormularioEdicion(respuesta.datos)
        $("#tituloModalProducto").text("Editar Producto")
        $("#btnGuardarProducto").html('<i class="mdi mdi-content-save me-1"></i> Actualizar Producto')
        $("#informacionAdicionalProducto").show()
        $("#modalMantenimientoProducto").modal("show")
      } else {
        mostrarError("Error al cargar datos del producto: " + (respuesta.mensaje || "Producto no encontrado"))
      }
    },
    error: (xhr, estado, error) => {
      mostrarError("Error de conexión al cargar datos del producto")
    },
  })
}

function llenarFormularioEdicion(producto) {
  $("#codigoProducto").val(producto.cod_producto || "")
  $("#nombreProducto").val(producto.str_nombre || "")
  $("#descripcionProducto").val(producto.str_descripcion || "")
  $("#stockProducto").val(producto.int_stock || 0)
  $("#precioProducto").val(producto.dec_precio || 0)
  $("#categoriaProducto").val(producto.cod_categoria || "")
  $("#proveedorProducto").val(producto.cod_proveedor || "")

  if (producto.fec_creacion) {
    $("#fechaCreacionProducto").text(formatearFecha(producto.fec_creacion))
  }
}

function llenarModalVista(producto) {
  $("#tituloVistaProducto").text(`Detalles: ${producto.str_nombre || "Producto"}`)
  $("#vistaCodigoProducto").text(producto.cod_producto || "N/A")
  $("#vistaNombreProducto").text(producto.str_nombre || "Sin nombre")
  $("#vistaDescripcionProducto").text(truncarTexto(producto.str_descripcion || "Sin descripción", 100))
  $("#vistaDescripcionCompleta").text(producto.str_descripcion || "Sin descripción")
  $("#vistaStockProducto").text(producto.int_stock || 0)
  $("#vistaPrecioProducto").text(`S/${Number.parseFloat(producto.dec_precio || 0).toFixed(2)}`)
  $("#vistaCategoriaProducto").text(producto.categoria || "Sin categoría")
  $("#vistaProveedorProducto").text(producto.proveedor || "Sin proveedor")
  $("#vistaFechaCreacionProducto").text(formatearFecha(producto.fec_creacion) || "N/A")

  const estadoBadge = producto.est_activo
    ? '<span class="badge bg-success">Activo</span>'
    : '<span class="badge bg-danger">Inactivo</span>'
  $("#vistaEstadoProducto").html(estadoBadge)
  $("#vistaEstadoTexto").text(producto.est_activo ? "Activo" : "Inactivo")
}

function editarProductoDesdeVista() {
  $("#modalVistaProducto").modal("hide")
  setTimeout(() => {
    editarProducto(productoIdVista)
  }, 300)
}

function guardarProducto(evento) {
  evento.preventDefault()

  if (!validarFormularioProducto()) {
    return
  }

  const esEdicion = $("#codigoProducto").val() !== ""
  const datosFormulario = obtenerDatosFormulario()

  const btnGuardar = $("#btnGuardarProducto")
  const textoOriginal = btnGuardar.html()
  btnGuardar.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Guardando...')

  const url = esEdicion
    ? "../../public/productos_api.php?accion=actualizar"
    : "../../public/productos_api.php?accion=crear"

  const metodo = esEdicion ? "PUT" : "POST"

  $.ajax({
    url: url,
    type: metodo,
    data: JSON.stringify(datosFormulario),
    contentType: "application/json",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito) {
        $("#modalMantenimientoProducto").modal("hide")
        cargarProductos()

        const mensaje = esEdicion 
          ? "Producto actualizado correctamente" 
          : "Producto creado correctamente"
        mostrarExito(mensaje)
      } else {
        mostrarError("Error al guardar producto: " + respuesta.mensaje)
      }
    },
    error: (xhr, estado, error) => {
      mostrarError("Error al guardar producto")
    },
    complete: () => {
      btnGuardar.prop("disabled", false).html(textoOriginal)
    },
  })
}

function eliminarProducto(codigoProducto, nombreProducto) {
  productoIdEliminar = codigoProducto
  $("#nombreProductoEliminar").text(nombreProducto)
  $("#modalEliminarProducto").modal("show")
}

function confirmarEliminacionProducto() {
  if (!productoIdEliminar) return

  const btnConfirmar = $("#btnConfirmarEliminarProducto")
  const textoOriginal = btnConfirmar.html()
  btnConfirmar.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Eliminando...')

  $.ajax({
    url: "../../public/productos_api.php?accion=eliminar",
    type: "DELETE",
    data: JSON.stringify({ codigoProducto: productoIdEliminar }),
    contentType: "application/json",
    dataType: "json",
    success: (respuesta) => {
      if (respuesta.exito) {
        $("#modalEliminarProducto").modal("hide")
        cargarProductos()
        mostrarExito("Producto eliminado correctamente")
      } else {
        mostrarError("Error al eliminar producto: " + respuesta.mensaje)
      }
    },
    error: (xhr, estado, error) => {
      mostrarError("Error al eliminar producto")
    },
    complete: () => {
      btnConfirmar.prop("disabled", false).html(textoOriginal)
      productoIdEliminar = null
    },
  })
}

function validarStockTiempoReal() {
  const stock = Number.parseInt($("#stockProducto").val())
  const campo = $("#stockProducto")

  if (isNaN(stock) || stock < 0) {
    campo.addClass("is-invalid").removeClass("is-valid")
  } else {
    campo.addClass("is-valid").removeClass("is-invalid")
  }
}

function validarPrecioTiempoReal() {
  const precio = Number.parseFloat($("#precioProducto").val())
  const campo = $("#precioProducto")

  if (isNaN(precio) || precio <= 0) {
    campo.addClass("is-invalid").removeClass("is-valid")
  } else {
    campo.addClass("is-valid").removeClass("is-invalid")
  }
}

function obtenerDatosFormulario() {
  const datos = {
    nombreProducto: $("#nombreProducto").val().trim(),
    descripcionProducto: $("#descripcionProducto").val().trim(),
    stockProducto: Number.parseInt($("#stockProducto").val()) || 0,
    precioProducto: Number.parseFloat($("#precioProducto").val()) || 0,
    codigoCategoria: Number.parseInt($("#categoriaProducto").val()) || null,
    codigoProveedor: Number.parseInt($("#proveedorProducto").val()) || null,
  }

  const codigoProducto = $("#codigoProducto").val()
  if (codigoProducto) {
    datos.codigoProducto = Number.parseInt(codigoProducto)
  }

  return datos
}

function generarBadgeEstado(estado) {
  return estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'
}

function formatearFecha(fecha) {
  if (!fecha) return "N/A"
  const options = { year: "numeric", month: "long", day: "numeric" }
  return new Date(fecha).toLocaleDateString("es-ES", options)
}

function truncarTexto(texto, longitud) {
  if (!texto) return "-"
  return texto.length > longitud ? texto.substring(0, longitud) + "..." : texto
}

function mostrarTablaVacia() {
  tablaProductos.clear().draw()
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

function marcarCampoInvalido(selector, mensaje) {
  const campo = $(selector)
  campo.addClass("is-invalid").removeClass("is-valid")
  campo.siblings(".invalid-feedback").text(mensaje)
}

function marcarCampoValido(selector) {
  const campo = $(selector)
  campo.addClass("is-valid").removeClass("is-invalid")
}

function limpiarFormularioProducto() {
  $("#formularioProducto")[0].reset()
  $("#formularioProducto .form-control, #formularioProducto .form-select").removeClass("is-valid is-invalid")
  $("#codigoProducto").val("")
}

function validarFormularioProducto() {
  let isValid = true

  const nombreProducto = $("#nombreProducto").val().trim()
  if (!nombreProducto) {
    marcarCampoInvalido("#nombreProducto", "El nombre del producto es requerido")
    isValid = false
  } else {
    marcarCampoValido("#nombreProducto")
  }

  const descripcionProducto = $("#descripcionProducto").val().trim()
  if (!descripcionProducto) {
    marcarCampoInvalido("#descripcionProducto", "La descripción del producto es requerida")
    isValid = false
  } else {
    marcarCampoValido("#descripcionProducto")
  }

  const stockProducto = Number.parseInt($("#stockProducto").val())
  if (isNaN(stockProducto) || stockProducto < 0) {
    marcarCampoInvalido("#stockProducto", "El stock debe ser un número no negativo")
    isValid = false
  } else {
    marcarCampoValido("#stockProducto")
  }

  const precioProducto = Number.parseFloat($("#precioProducto").val())
  if (isNaN(precioProducto) || precioProducto <= 0) {
    marcarCampoInvalido("#precioProducto", "El precio debe ser un número positivo")
    isValid = false
  } else {
    marcarCampoValido("#precioProducto")
  }

  const categoriaProducto = $("#categoriaProducto").val()
  if (!categoriaProducto) {
    marcarCampoInvalido("#categoriaProducto", "Debe seleccionar una categoría")
    isValid = false
  } else {
    marcarCampoValido("#categoriaProducto")
  }

  const proveedorProducto = $("#proveedorProducto").val()
  if (!proveedorProducto) {
    marcarCampoInvalido("#proveedorProducto", "Debe seleccionar un proveedor")
    isValid = false
  } else {
    marcarCampoValido("#proveedorProducto")
  }

  return isValid
}