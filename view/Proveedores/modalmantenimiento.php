<div id="modalMantenimientoProveedor" class="modal fade" tabindex="-1" aria-labelledby="modalMantenimientoProveedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalProveedor">Crear Nuevo Proveedor</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Formulario de mantenimiento -->
            <form method="post" id="formularioProveedor">
                <div class="modal-body">
                    <!-- Campo oculto para ID del proveedor (para edición) -->
                    <input type="hidden" name="codigoProveedor" id="codigoProveedor"/>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="nombreProveedor" class="form-label">
                                    Nombre del Proveedor <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombreProveedor" name="nombreProveedor" 
                                       placeholder="Ej: Distribuidora ABC, Comercial XYZ..." required maxlength="100"/>
                                <div class="form-text">
                                    Ingrese el nombre completo de la empresa proveedora (máximo 100 caracteres).
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingrese un nombre válido para el proveedor.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="contactoProveedor" class="form-label">
                                    Persona de Contacto <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="contactoProveedor" name="contactoProveedor" 
                                       placeholder="Ej: Juan Pérez, María García..." required maxlength="100"/>
                                <div class="form-text">
                                    Nombre de la persona responsable o contacto principal.
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingrese el nombre del contacto.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="telefonoProveedor" class="form-label">
                                    Teléfono de Contacto <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control" id="telefonoProveedor" name="telefonoProveedor" 
                                           placeholder="Ej: +1234567890, 987-654-321..." required maxlength="20"/>
                                </div>
                                <div class="form-text">
                                    Número de teléfono principal para contactar al proveedor.
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingrese un número de teléfono válido.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional para edición -->
                    <div id="informacionAdicionalProveedor" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Registro:</label>
                                    <p class="form-control-static" id="fechaCreacionProveedor">-</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Estado:</label>
                                    <p class="form-control-static">
                                        <span class="badge bg-success" id="estadoProveedor">Activo</span>
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
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="btnGuardarProveedor">
                        <i class="mdi mdi-content-save me-1"></i> Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div id="modalEliminarProveedor" class="modal fade" tabindex="-1" aria-labelledby="modalEliminarProveedorLabel" aria-hidden="true">
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
                        Esta acción eliminará permanentemente al proveedor <strong id="nombreProveedorEliminar"></strong>.
                    </p>
                    <div class="alert alert-warning" role="alert">
                        <i class="mdi mdi-alert me-2"></i>
                        <strong>Advertencia:</strong> Si este proveedor tiene productos asociados, 
                        no podrá ser eliminado hasta que se reasignen a otro proveedor.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger waves-effect waves-light" id="btnConfirmarEliminarProveedor">
                    <i class="mdi mdi-trash-can me-1"></i> Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de vista detallada -->
<div id="modalVistaProveedor" class="modal fade" tabindex="-1" aria-labelledby="modalVistaProveedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloVistaProveedor">Detalles del Proveedor</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información principal -->
                <div class="row">
                    <div class="col-md-8 col-12">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-lg">
                                    <div class="avatar-title bg-primary rounded-circle">
                                        <i class="mdi mdi-domain font-size-24"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 id="vistaNombreProveedor">-</h5>
                                <p class="text-muted mb-1">
                                    <i class="mdi mdi-account me-1"></i><span id="vistaContactoProveedor">-</span>
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="mdi mdi-phone me-1"></i><span id="vistaTelefonoProveedor">-</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 text-md-end text-center">
                        <span class="badge bg-success" id="vistaEstadoProveedor">Activo</span>
                    </div>
                </div>
                
                <hr>
                
                <!-- Estadísticas -->
                <div class="row">
                    <div class="col-6 col-md-4">
                        <div class="text-center border rounded p-3">
                            <h4 class="text-primary mb-1" id="vistaContadorProductosProveedor">0</h4>
                            <p class="text-muted mb-0">Productos</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="text-center border rounded p-3">
                            <h4 class="text-success mb-1" id="vistaFechaCreacionProveedor">-</h4>
                            <p class="text-muted mb-0">Fecha Registro</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="text-center border rounded p-3">
                            <h4 class="text-info mb-1" id="vistaCodigoProveedor">-</h4>
                            <p class="text-muted mb-0">ID Proveedor</p>
                        </div>
                    </div>
                </div>
                
                <!-- Información de contacto adicional -->
                <div class="mt-4">
                    <h6>Información de Contacto:</h6>
                    <div class="card border">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <p class="mb-2">
                                        <strong>Empresa:</strong><br>
                                        <span id="vistaEmpresaProveedor">-</span>
                                    </p>
                                </div>
                                <div class="col-md-6 col-12">
                                    <p class="mb-2">
                                        <strong>Contacto Principal:</strong><br>
                                        <span id="vistaPersonaContacto">-</span>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <p class="mb-0">
                                        <strong>Teléfono:</strong><br>
                                        <span id="vistaTextoTelefono">-</span>
                                    </p>
                                </div>
                                <div class="col-md-6 col-12">
                                    <p class="mb-0">
                                        <strong>Estado:</strong><br>
                                        <span id="vistaEstadoTexto">Activo</span>
                                    </p>
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
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="editarProveedorDesdeVista()">
                    <i class="mdi mdi-pencil me-1"></i> Editar Proveedor
                </button>
            </div>
        </div>
    </div>
</div>
