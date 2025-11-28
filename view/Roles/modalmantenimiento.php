<!-- Modal de información detallada del rol -->
<div id="modalInformacionRol" class="modal fade" tabindex="-1" aria-labelledby="modalInformacionRolLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalRol">Información del Rol</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-info" role="alert">
                            <i class="mdi mdi-information me-2"></i>
                            <strong>Nota:</strong> Los roles son predefinidos del sistema y no pueden ser modificados.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID del Rol:</label>
                            <p class="form-control-static" id="codigoRolInfo">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del Rol:</label>
                            <p class="form-control-static" id="nombreRolInfo">-</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Estado:</label>
                            <p class="form-control-static">
                                <span class="badge bg-success" id="estadoRolInfo">Activo</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de Creación:</label>
                            <p class="form-control-static" id="fechaCreacionRolInfo">-</p>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Permisos del rol -->
                <div class="row">
                    <div class="col-lg-12">
                        <h5 class="mb-3">
                            <i class="mdi mdi-shield-check me-2"></i>
                            Permisos y Características
                        </h5>
                        <div id="permisosRolInfo">
                            <!-- Se llena dinámicamente según el rol -->
                        </div>
                    </div>
                </div>

                <!-- Estadísticas del rol -->
                <hr>
                <div class="row">
                    <div class="col-lg-12">
                        <h5 class="mb-3">
                            <i class="mdi mdi-chart-bar me-2"></i>
                            Estadísticas de Uso
                        </h5>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h4 class="text-primary mb-1" id="usuariosConRol">0</h4>
                                    <p class="text-muted mb-0">Usuarios con este rol</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h4 class="text-success mb-1" id="porcentajeUso">0%</h4>
                                    <p class="text-muted mb-0">Porcentaje de uso</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h4 class="text-info mb-1" id="ultimoAcceso">-</h4>
                                    <p class="text-muted mb-0">Último acceso</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de ayuda sobre roles -->
<div id="modalAyudaRoles" class="modal fade" tabindex="-1" aria-labelledby="modalAyudaRolesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-help-circle me-2"></i>
                    Ayuda - Gestión de Roles
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="accordionAyuda">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                ¿Qué son los roles?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionAyuda">
                            <div class="accordion-body">
                                Los roles definen los permisos y niveles de acceso que tienen los usuarios en el sistema JOAYMI.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                ¿Puedo crear nuevos roles?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionAyuda">
                            <div class="accordion-body">
                                No, los roles son predefinidos del sistema y no pueden ser modificados para mantener la seguridad e integridad.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                ¿Cómo asigno roles a usuarios?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionAyuda">
                            <div class="accordion-body">
                                Los roles se asignan desde el módulo de <strong>Gestión de Usuarios</strong> al crear o editar un usuario.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
