<div id="modalMantenimientoUsuario" class="modal fade" tabindex="-1" aria-labelledby="modalMantenimientoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalUsuario">Crear Nuevo Usuario</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Formulario de mantenimiento -->
            <form method="post" id="formularioUsuario">
                <div class="modal-body">
                    <!-- Campo oculto para ID del usuario (para edición) -->
                    <input type="hidden" name="codigoUsuario" id="codigoUsuario"/>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="correoElectronico" class="form-label">
                                    Correo Electrónico <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="correoElectronico" name="correoElectronico" 
                                       placeholder="usuario@ejemplo.com" required/>
                                <div class="invalid-feedback">
                                    Por favor ingrese un correo electrónico válido.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="claveAcceso" class="form-label">
                                    Contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="claveAcceso" name="claveAcceso" 
                                           placeholder="Mínimo 6 caracteres" required minlength="6"/>
                                    <button class="btn btn-outline-secondary" type="button" id="mostrarContrasena">
                                        <i class="mdi mdi-eye" id="iconoOjo"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    La contraseña debe tener al menos 6 caracteres.
                                </div>
                                <div class="invalid-feedback">
                                    La contraseña debe tener al menos 6 caracteres.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="codigoRol" class="form-label">
                                    Rol del Usuario <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="codigoRol" name="codigoRol" required>
                                    <option value="">Seleccione un rol...</option>
                                    <!-- Los roles se cargan dinámicamente -->
                                </select>
                                <div class="form-text">
                                    <strong>Administrador:</strong> Acceso completo al sistema<br>
                                    <strong>Usuario:</strong> Acceso limitado a funciones básicas
                                </div>
                                <div class="invalid-feedback">
                                    Por favor seleccione un rol para el usuario.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional para edición -->
                    <div id="informacionAdicional" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Creación:</label>
                                    <p class="form-control-static" id="fechaCreacionUsuario">-</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado:</label>
                                    <p class="form-control-static">
                                        <span class="badge bg-success" id="estadoUsuario">Activo</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="btnGuardarUsuario">
                        <i class="mdi mdi-content-save me-1"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div id="modalEliminarUsuario" class="modal fade" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 48px;"></i>
                    <h4 class="mt-3">¿Está seguro?</h4>
                    <p class="text-muted">
                        Esta acción eliminará permanentemente al usuario <strong id="nombreUsuarioEliminar"></strong>.
                        Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger waves-effect waves-light" id="btnConfirmarEliminar">
                    <i class="mdi mdi-trash-can me-1"></i> Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
