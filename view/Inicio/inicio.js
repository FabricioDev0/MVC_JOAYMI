/**
 * JavaScript para Dashboard - Sistema JOAYMI
 * Maneja la carga y visualización de datos del dashboard principal
 *
 */

$(document).ready(() => {
  // Verificar autenticación antes de cargar datos
  verificarAutenticacionUsuario()

  // Cargar todos los componentes del dashboard
  inicializarDashboard()
})

/**
 * Inicializa todos los componentes del dashboard
 * Carga datos de forma secuencial para mejor rendimiento
 */
function inicializarDashboard() {
  mostrarMensajeCarga("Cargando dashboard...")

  // Cargar datos en paralelo para mejor rendimiento
  Promise.all([cargarEstadisticasGenerales(), cargarProductosRecientes(), cargarCategoriasPrincipales()])
    .then(() => {
      ocultarMensajeCarga()
      registrarLog("Dashboard cargado completamente")
    })
    .catch((error) => {
      ocultarMensajeCarga()
      mostrarMensajeError("Error al cargar el dashboard")
      registrarLog("Error cargando dashboard", error)
    })

  // Configurar actualización automática cada 5 minutos
  configurarActualizacionAutomatica()
}

/**
 * Verifica si el usuario está autenticado
 * Redirige al login si no hay sesión válida
 */
function verificarAutenticacionUsuario() {
  $.ajax({
    url: "../../public/auth_api.php?accion=verificar",
    type: "GET",
    dataType: "json",
    timeout: 5000,
    success: (respuesta) => {
      if (!respuesta.exito || !respuesta.datos || !respuesta.datos.autenticado) {
        // No autenticado, redirigir al login
        mostrarMensajeError("Sesión expirada. Redirigiendo al login...")
        setTimeout(() => {
          window.location.href = "../Login/index.php"
        }, 2000)
      } else {
        registrarLog("Usuario autenticado correctamente", respuesta.datos)
      }
    },
    error: () => {
      // Error de conexión, redirigir al login por seguridad
      mostrarMensajeError("Error de conexión. Redirigiendo al login...")
      setTimeout(() => {
        window.location.href = "../Login/index.php"
      }, 2000)
    },
  })
}

/**
 * Carga las estadísticas generales del sistema
 * Obtiene totales de usuarios, productos, categorías y proveedores
 * Promise que resuelve cuando se cargan todas las estadísticas
 */
function cargarEstadisticasGenerales() {
  return new Promise((resolve, reject) => {
    let contadorCompletados = 0
    const totalPeticiones = 4
    let hayError = false

    // Función para verificar si todas las peticiones están completas
    const verificarCompletado = () => {
      contadorCompletados++
      if (contadorCompletados === totalPeticiones) {
        if (hayError) {
          reject("Error en una o más peticiones")
        } else {
          resolve()
        }
      }
    }

    // Cargar total de usuarios
    cargarTotalUsuarios()
      .then(() => verificarCompletado())
      .catch(() => {
        hayError = true
        verificarCompletado()
      })

    // Cargar total de productos
    cargarTotalProductos()
      .then(() => verificarCompletado())
      .catch(() => {
        hayError = true
        verificarCompletado()
      })

    // Cargar total de categorías
    cargarTotalCategorias()
      .then(() => verificarCompletado())
      .catch(() => {
        hayError = true
        verificarCompletado()
      })

    // Cargar total de proveedores
    cargarTotalProveedores()
      .then(() => verificarCompletado())
      .catch(() => {
        hayError = true
        verificarCompletado()
      })
  })
}

/**
 * Carga el total de usuarios del sistema
 * @returns {Promise} - Promise de la petición AJAX
 */
function cargarTotalUsuarios() {
  return $.ajax({
    url: "../../public/usuarios_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        const totalUsuarios = respuesta.datos.length
        $("#totalUsuarios").text(totalUsuarios)
        registrarLog(`Total usuarios cargado: ${totalUsuarios}`)
      } else {
        throw new Error("Respuesta inválida de usuarios")
      }
    },
    error: () => {
      $("#totalUsuarios").text("Error")
      throw new Error("Error cargando usuarios")
    },
  })
}

/**
 * Carga el total de productos y calcula estadísticas de inventario
 * @returns {Promise} - Promise de la petición AJAX
 */
function cargarTotalProductos() {
  return $.ajax({
    url: "../../public/productos_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        const totalProductos = respuesta.datos.length
        $("#totalProductos").text(totalProductos)
        $("#totalProductosActivos").text(totalProductos)

        // Calcular estadísticas de stock
        let stockTotal = 0
        let productosConStock = 0

        respuesta.datos.forEach((producto) => {
          const stock = Number.parseInt(producto.int_stock || 0)
          stockTotal += stock
          if (stock > 0) productosConStock++
        })

        // Actualizar información de inventario
        const porcentajeConStock = totalProductos > 0 ? Math.round((productosConStock / totalProductos) * 100) : 0

        $("#estadoInventario").html(
          `${stockTotal} <span class="font-size-14 text-muted ms-1">unidades (${porcentajeConStock}% con stock)</span>`,
        )

        registrarLog(`Productos cargados: ${totalProductos}, Stock total: ${stockTotal}`)
      } else {
        throw new Error("Respuesta inválida de productos")
      }
    },
    error: () => {
      $("#totalProductos").text("Error")
      $("#totalProductosActivos").text("Error")
      $("#estadoInventario").text("Error al cargar")
      throw new Error("Error cargando productos")
    },
  })
}

/**
 * Carga el total de categorías del sistema
 * @returns {Promise} - Promise de la petición AJAX
 */
function cargarTotalCategorias() {
  return $.ajax({
    url: "../../public/categorias_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        const totalCategorias = respuesta.datos.length
        $("#totalCategorias").text(totalCategorias)
        registrarLog(`Total categorías cargado: ${totalCategorias}`)
      } else {
        throw new Error("Respuesta inválida de categorías")
      }
    },
    error: () => {
      $("#totalCategorias").text("Error")
      throw new Error("Error cargando categorías")
    },
  })
}

/**
 * Carga el total de proveedores del sistema
 * @returns {Promise} - Promise de la petición AJAX
 */
function cargarTotalProveedores() {
  return $.ajax({
    url: "../../public/proveedores_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        const totalProveedores = respuesta.datos.length
        $("#totalProveedores").text(totalProveedores)
        registrarLog(`Total proveedores cargado: ${totalProveedores}`)
      } else {
        throw new Error("Respuesta inválida de proveedores")
      }
    },
    error: () => {
      $("#totalProveedores").text("Error")
      throw new Error("Error cargando proveedores")
    },
  })
}

/**
 * Carga los productos más recientes en la tabla del dashboard
 * Muestra los primeros 5 productos con información relevante
 * Promise de la petición AJAX
 */
function cargarProductosRecientes() {
  return $.ajax({
    url: "../../public/productos_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        generarTablaProductosRecientes(respuesta.datos)
        registrarLog(`Productos recientes cargados: ${respuesta.datos.length}`)
      } else {
        throw new Error("Respuesta inválida de productos recientes")
      }
    },
    error: () => {
      mostrarErrorTablaProductos()
      throw new Error("Error cargando productos recientes")
    },
  })
}

/**
 * Genera el HTML de la tabla de productos recientes
 * productos - Array de productos del backend
 */
function generarTablaProductosRecientes(productos) {
  let htmlTabla = ""

  // Mostrar solo los primeros 5 productos
  const productosRecientes = productos.slice(0, 3)

  if (productosRecientes.length === 0) {
    htmlTabla = generarFilaVaciaProductos()
  } else {
    productosRecientes.forEach((producto) => {
      htmlTabla += generarFilaProducto(producto)
    })
  }

  $("#tablaProductosRecientes").html(htmlTabla)

  // Inicializar tooltips para los botones de acción
  inicializarTooltips()
}

/**
 * Genera una fila HTML para un producto específico
 * @param {Object} producto - Objeto producto del backend
 *  - HTML de la fila del producto
 */
function generarFilaProducto(producto) {
  const badgeStock = generarBadgeStock(producto.int_stock)
  const precioFormateado = formatearPrecio(producto.dec_precio)
  const categoriaTexto = producto.categoria || "Sin categoría"

  return `
    <tr>
      <th scope="row">
        <a href="#" class="text-primary">#${producto.cod_producto}</a>
      </th>
      <td>
        <span class="fw-medium">${producto.str_nombre}</span>
      </td>
      <td class="d-none d-md-table-cell">
        <span class="badge bg-light text-dark">${categoriaTexto}</span>
      </td>
      <td>${badgeStock}</td>
      <td>
        <span class="fw-bold text-success">S/${precioFormateado}</span>
      </td>
      <td class="d-none d-sm-table-cell">
        ${generarBotonesAccionProducto(producto.cod_producto)}
      </td>
    </tr>
  `
}

/**
 * Genera los botones de acción para un producto (responsive)
 * codigoProducto - ID del producto
 *  - HTML de los botones de acción
 */
function generarBotonesAccionProducto(codigoProducto) {
  return `
    <div class="btn-group" role="group">
      <button type="button" 
              class="btn btn-outline-secondary btn-sm" 
              data-bs-toggle="tooltip" 
              data-bs-placement="top" 
              title="Editar producto"
              onclick="editarProducto(${codigoProducto})">
        <i class="mdi mdi-pencil"></i>
      </button>
    </div>
  `
}

/**
 * Carga las categorías principales del sistema
 * @returns {Promise} - Promise de la petición AJAX
 */
function cargarCategoriasPrincipales() {
  return $.ajax({
    url: "../../public/categorias_api.php?accion=listar",
    type: "GET",
    dataType: "json",
    timeout: 10000,
    success: (respuesta) => {
      if (respuesta.exito && respuesta.datos) {
        generarTablaCategoriasPrincipales(respuesta.datos)
        registrarLog(`Categorías principales cargadas: ${respuesta.datos.length}`)
      } else {
        throw new Error("Respuesta inválida de categorías principales")
      }
    },
    error: () => {
      mostrarErrorTablaCategorias()
      throw new Error("Error cargando categorías principales")
    },
  })
}

/**
 * Genera el HTML de la tabla de categorías principales
 * @param {Array} categorias - Array de categorías del backend
 */
function generarTablaCategoriasPrincipales(categorias) {
  let htmlTabla = ""

  // Mostrar solo las primeras 3 categorías
  const categoriasPrincipales = categorias.slice(0, 4)

  if (categoriasPrincipales.length === 0) {
    htmlTabla = generarFilaVaciaCategorias()
  } else {
    categoriasPrincipales.forEach((categoria, indice) => {
      htmlTabla += generarFilaCategoria(categoria, indice)
    })
  }

  $("#tablaCategoriasPrincipales").html(htmlTabla)

  // Reinicializar los gráficos knob después de cargar los datos
  setTimeout(() => {
    inicializarGraficosKnob()
  }, 100)
}

/**
 * Genera una fila HTML para una categoría específica
 * categoria - Objeto categoría del backend
 * indice - Índice de la categoría para calcular porcentaje
 * HTML de la fila de la categoría
 */
function generarFilaCategoria(categoria, indice) {
  const porcentajeSimulado = calcularPorcentajeCategoria(indice)
  const descripcionCorta = truncarTexto(categoria.str_descripcion, 25)

  return `
    <tr>
      <th scope="row">
        <span class="fw-medium">${categoria.str_nombre}</span>
      </th>
      <td>
        <small class="text-muted">${descripcionCorta}</small>
      </td>
      <td>
        <div dir="ltr" class="ms-2">
          <input data-plugin="knob" 
                 data-width="36" 
                 data-height="36" 
                 data-linecap="round" 
                 data-displayInput="false"
                 data-fgColor="#3051d3" 
                 value="${porcentajeSimulado}" 
                 data-skin="tron" 
                 data-angleOffset="36"
                 data-readOnly="true" 
                 data-thickness=".2" />
        </div>
      </td>
    </tr>
  `
}

// === FUNCIONES DE UTILIDAD ===

/**
 * Genera badge de stock según la cantidad disponible
 * stock - Cantidad en stock
 * HTML del badge de stock
 */
function generarBadgeStock(stock) {
  const cantidad = Number.parseInt(stock || 0)

  if (cantidad === 0) {
    return '<div class="badge bg-danger">Sin Stock</div>'
  } else if (cantidad <= 5) {
    return `<div class="badge bg-danger">Bajo (${cantidad})</div>`
  } else if (cantidad <= 20) {
    return `<div class="badge bg-warning">Medio (${cantidad})</div>`
  } else {
    return `<div class="badge bg-success">Alto (${cantidad})</div>`
  }
}

/**
 * Formatea un precio para mostrar con 2 decimales
 * precio - Precio a formatear
 * Precio formateado
 */
function formatearPrecio(precio) {
  return Number.parseFloat(precio || 0).toFixed(2)
}

/**
 * Trunca un texto a una longitud específica
 * texto - Texto a truncar
 * longitud - Longitud máxima
 * Texto truncado
 */
function truncarTexto(texto, longitud) {
  if (!texto) return "Sin descripción"
  return texto.length > longitud ? texto.substring(0, longitud) + "..." : texto
}

/**
 * Calcula un porcentaje simulado para las categorías
 * indice - Índice de la categoría
 * Porcentaje calculado
 */
function calcularPorcentajeCategoria(indice) {
  // Generar porcentajes diferentes para cada categoría
  const porcentajes = [75, 60, 45, 80, 55]
  return porcentajes[indice % porcentajes.length]
}

// === FUNCIONES DE INTERFAZ ===

/**
 * Muestra mensaje de carga en el dashboard
 * mensaje - Mensaje a mostrar
 */
function mostrarMensajeCarga(mensaje) {
  registrarLog(mensaje)
}

/**
 * Oculta el mensaje de carga
 */
function ocultarMensajeCarga() {
  registrarLog("Carga completada")
}

/**
 * Muestra mensaje de error
 * mensaje - Mensaje de error
 */
function mostrarMensajeError(mensaje) {
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
}

/**
 * Inicializa los tooltips de Bootstrap
 */
function inicializarTooltips() {
  if (typeof bootstrap !== "undefined") {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))
  }
}

/**
 * Inicializa los gráficos knob
 */
function inicializarGraficosKnob() {
  if (typeof jQuery !== "undefined" && jQuery.fn.knob) {
    $('[data-plugin="knob"]').knob()
  }
}

/**
 * Configura la actualización automática del dashboard
 */
function configurarActualizacionAutomatica() {
  // Actualizar estadísticas cada 5 minutos
  setInterval(() => {
    registrarLog("Actualizando estadísticas automáticamente...")
    cargarEstadisticasGenerales()
  }, 300000) // 5 minutos
}

// === FUNCIONES DE MANEJO DE ERRORES ===

/**
 * Genera fila vacía para tabla de productos
 *  - HTML de fila vacía
 */
function generarFilaVaciaProductos() {
  return `
    <tr>
      <td colspan="6" class="text-center text-muted py-4">
        <i class="mdi mdi-package-variant-closed mdi-48px mb-2"></i>
        <br>No hay productos registrados
      </td>
    </tr>
  `
}

/**
 * Muestra error en tabla de productos
 */
function mostrarErrorTablaProductos() {
  $("#tablaProductosRecientes").html(`
    <tr>
      <td colspan="6" class="text-center text-danger py-4">
        <i class="mdi mdi-alert-circle mdi-48px mb-2"></i>
        <br>Error al cargar productos
      </td>
    </tr>
  `)
}

/**
 * Genera fila vacía para tabla de categorías
 *  - HTML de fila vacía
 */
function generarFilaVaciaCategorias() {
  return `
    <tr>
      <td colspan="3" class="text-center text-muted py-4">
        <i class="mdi mdi-folder-outline mdi-48px mb-2"></i>
        <br>No hay categorías registradas
      </td>
    </tr>
  `
}

/**
 * Muestra error en tabla de categorías
 */
function mostrarErrorTablaCategorias() {
  $("#tablaCategoriasPrincipales").html(`
    <tr>
      <td colspan="3" class="text-center text-danger py-4">
        <i class="mdi mdi-alert-circle mdi-48px mb-2"></i>
        <br>Error al cargar categorías
      </td>
    </tr>
  `)
}

// === FUNCIONES DE ACCIONES (PLACEHOLDERS) ===

/**
 * Ver detalle de un producto - redirige al módulo de productos
 * codigoProducto - ID del producto
 */
function verDetalleProducto(codigoProducto) {
  registrarLog(`Ver detalle producto: ${codigoProducto}`)
  window.location.href = `../Productos/?ver=${codigoProducto}`
}

/**
 * Editar un producto - redirige al módulo de productos
 * codigoProducto - ID del producto
 */
function editarProducto(codigoProducto) {
  registrarLog(`Editar producto: ${codigoProducto}`)
  window.location.href = `../Productos/?editar=${codigoProducto}`
}

/**
 * Función de utilidad para logging en desarrollo
 * mensaje - Mensaje a registrar
 * Datos adicionales para el log
 */
function registrarLog(mensaje, datos = null) {
  if (console && console.log) {
    const timestamp = new Date().toLocaleTimeString()
    console.log(`[${timestamp}] [JOAYMI Dashboard] ${mensaje}`, datos || "")
  }
}
