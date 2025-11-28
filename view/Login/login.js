/**
 * JavaScript para manejo de Login - Sistema JOAYMI
 */

const $ = window.$
const Swal = window.Swal

$(document).ready(() => {
  console.log("Inicializando módulo de login...")

  // Verificar dependencias
  if (typeof $ === "undefined") {
    console.error("jQuery no está cargado")
    return
  }

  if (typeof Swal === "undefined") {
    console.warn("SweetAlert2 no está cargado, usando alerts básicos")
  }

  // Verificar sesión existente
  verificarSesionExistente()

  // Cargar email recordado
  cargarEmailRecordado()

  console.log("Módulo de login inicializado correctamente")
})

/**
 * Evento click del botón ingresar
 */
$(document).on("click", "#btningresar", (event) => {
  event.preventDefault()
  iniciarSesionUsuario()
})

/**
 * Evento para manejar Enter en los campos
 */
$(document).on("keypress", "#usuario, #clave", (evento) => {
  if (evento.which === 13) {
    iniciarSesionUsuario()
  }
})

/**
 * Función principal para iniciar sesión
 */
function iniciarSesionUsuario() {
  console.log("Iniciando proceso de login...")

  // Obtener valores
  const correoElectronico = $("#usuario").val().trim()
  const claveAcceso = $("#clave").val().trim()

  console.log("Email:", correoElectronico)
  console.log("Contraseña:", claveAcceso ? "***" : "vacía")

  // Validaciones básicas
  if (correoElectronico === "" || claveAcceso === "") {
    console.warn("Campos vacíos")
    mostrarMensaje("warning", "Campos requeridos", "Por favor complete todos los campos")
    return
  }

  if (!validarFormatoEmail(correoElectronico)) {
    console.warn("Email inválido:", correoElectronico)
    mostrarMensaje("warning", "Email inválido", "Por favor ingrese un correo electrónico válido")
    return
  }

  // Mostrar estado de carga
  mostrarEstadoCarga(true)

  // Preparar datos
  const datosLogin = {
    correoElectronico: correoElectronico,
    claveAcceso: claveAcceso,
  }

  console.log("Enviando datos al servidor...")

  // Petición AJAX
  $.ajax({
    url: "../../public/auth_api.php?accion=login",
    type: "POST",
    data: JSON.stringify(datosLogin),
    contentType: "application/json",
    dataType: "json",
    timeout: 15000,
    beforeSend: function (xhr) {
      console.log("Enviando petición a:", this.url)
      console.log("Datos:", datosLogin)
    },
    success: (respuesta) => {
      console.log("Respuesta recibida:", respuesta)
      manejarRespuestaLogin(respuesta, correoElectronico)
    },
    error: (xhr, estado, error) => {
      console.error("Error en petición AJAX:")
      console.error("Estado:", estado)
      console.error("Error:", error)
      console.error("Status:", xhr.status)
      console.error("Response Text:", xhr.responseText)

      manejarErrorLogin(xhr, estado, error)
    },
    complete: () => {
      mostrarEstadoCarga(false)
      console.log("Petición completada")
    },
  })
}

/**
 * Maneja la respuesta del login
 */
function manejarRespuestaLogin(respuesta, correoElectronico) {
  console.log("Procesando respuesta login:", respuesta)

  if (respuesta && respuesta.exito) {
    console.log("Login exitoso!")

    // Guardar datos
    guardarDatosRecordatorio(correoElectronico)

    // Mostrar mensaje de éxito
    mostrarMensaje("success", "¡Bienvenido!", "Acceso concedido al Sistema JOAYMI")

    // Redirigir
    console.log("Redirigiendo al dashboard...")
    setTimeout(() => {
      window.location.href = "../Inicio/index.php"
    }, 1500)
  } else {
    console.warn("Login fallido:", respuesta?.mensaje)
    mostrarMensaje("error", "Acceso denegado", respuesta?.mensaje || "Credenciales incorrectas")
    limpiarCampoContrasena()
  }
}

/**
 * Maneja errores de conexión
 */
function manejarErrorLogin(xhr, estado, error) {
  console.error("Error detallado en login:")
  console.error("XHR:", xhr)
  console.error("Estado:", estado)
  console.error("Error:", error)

  let mensajeError = "Error de conexión con el servidor"

  if (xhr.status === 0) {
    mensajeError = "No se pudo conectar con el servidor. Verifique su conexión."
  } else if (xhr.status === 404) {
    mensajeError = "Archivo de autenticación no encontrado (404)"
  } else if (xhr.status === 500) {
    mensajeError = "Error interno del servidor (500)"
  } else if (estado === "timeout") {
    mensajeError = "La petición tardó demasiado tiempo"
  } else if (estado === "parsererror") {
    mensajeError = "Error al procesar la respuesta del servidor"
    console.error("Respuesta del servidor:", xhr.responseText)
  }

  mostrarMensaje("error", "Error de conexión", mensajeError)
  limpiarCampoContrasena()
}

/**
 * Verifica si ya existe una sesión activa
 */
function verificarSesionExistente() {
  console.log("Verificando sesión existente...")

  $.ajax({
    url: "../../public/auth_api.php?accion=verificar",
    type: "GET",
    dataType: "json",
    timeout: 5000,
    success: (respuesta) => {
      console.log("Estado de sesión:", respuesta)

      if (respuesta && respuesta.exito && respuesta.datos && respuesta.datos.autenticado) {
        console.log("Usuario ya autenticado, redirigiendo...")
        window.location.href = "../Inicio/index.php"
      } else {
        console.log("No hay sesión activa")
      }
    },
    error: (xhr, estado, error) => {
      console.log("No se pudo verificar sesión:", estado)
    },
  })
}

/**
 * Carga email recordado
 */
function cargarEmailRecordado() {
  const emailRecordado = localStorage.getItem("joaymi_email_recordado")
  if (emailRecordado) {
    console.log("Cargando email recordado:", emailRecordado)
    $("#usuario").val(emailRecordado)
    $("#clave").focus()
  }
}

/**
 * Guarda datos de recordatorio
 */
function guardarDatosRecordatorio(correoElectronico) {
  localStorage.setItem("joaymi_email_recordado", correoElectronico)
  localStorage.setItem("joaymi_ultimo_acceso", new Date().toISOString())
  console.log("Datos guardados en localStorage")
}

/**
 * Valida formato de email
 */
function validarFormatoEmail(email) {
  const patronEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return patronEmail.test(email)
}

/**
 * Muestra mensajes
 */
function mostrarMensaje(tipo, titulo, texto) {
  if (typeof Swal !== "undefined") {
    return Swal.fire({
      position: "center",
      icon: tipo,
      title: titulo,
      text: texto,
      showConfirmButton: false,
      timer: tipo === "success" ? 2000 : 4000,
      timerProgressBar: true,
    })
  } else {
    alert(titulo + ": " + texto)
    return Promise.resolve()
  }
}

/**
 * Controla el estado de carga del botón
 */
function mostrarEstadoCarga(mostrarCarga) {
  const botonIngresar = $("#btningresar")

  if (mostrarCarga) {
    botonIngresar.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin"></i> Verificando...')
  } else {
    botonIngresar.prop("disabled", false).html("Iniciar Sesión")
  }
}

/**
 * Limpia el campo de contraseña
 */
function limpiarCampoContrasena() {
  $("#clave").val("").focus()
}
