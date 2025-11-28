<div id="modalMantenimientoCategoria" class="modal fade" tabindex="-1" aria-labelledby="modalMantenimientoCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalCategoria">Crear Nueva Categoría</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Formulario de mantenimiento -->
            <form method="post" id="formularioCategoria">
                <div class="modal-body">
                    <!-- Campo oculto para ID de la categoría (para edición) -->
                    <input type="hidden" name="codigoCategoria" id="codigoCategoria"/>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="nombreCategoria" class="form-label">
                                    Nombre de la Categoría <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombreCategoria" name="nombreCategoria" 
                                       placeholder="Ej: Electrónicos, Ropa, Hogar..." required maxlength="100"/>
                                <div class="form-text">
                                    Ingrese un nombre descriptivo para la categoría (máximo 100 caracteres).
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingrese un nombre válido para la categoría.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="descripcionCategoria" class="form-label">
                                    Descripción <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="descripcionCategoria" name="descripcionCategoria" 
                                          rows="4" placeholder="Describe qué tipo de productos incluye esta categoría..." 
                                          required maxlength="500"></textarea>
                                <div class="form-text">
                                    Proporcione una descripción detallada de la categoría (máximo 500 caracteres).
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingrese una descripción para la categoría.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional para edición -->
                    <div id="informacionAdicionalCategoria" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Creación:</label>
                                    <p class="form-control-static" id="fechaCreacionCategoria">-</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado:</label>
                                    <p class="form-control-static">
                                        <span class="badge bg-success" id="estadoCategoria">Activo</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="btnGuardarCategoria">
                        <i class="mdi mdi-content-save me-1"></i> Guardar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div id="modalEliminarCategoria" class="modal fade" tabindex="-1" aria-labelledby="modalEliminarCategoriaLabel" aria-hidden="true">
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
                        Esta acción eliminará permanentemente la categoría <strong id="nombreCategoriaEliminar"></strong>.
                    </p>
                    <div class="alert alert-warning" role="alert">
                        <i class="mdi mdi-alert me-2"></i>
                        <strong>Advertencia:</strong> Si esta categoría tiene productos asociados, 
                        no podrá ser eliminada hasta que se reasignen a otra categoría.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger waves-effect waves-light" id="btnConfirmarEliminarCategoria">
                    <i class="mdi mdi-trash-can me-1"></i> Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de vista detallada -->
<div id="modalVistaCategoria" class="modal fade" tabindex="-1" aria-labelledby="modalVistaCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloVistaCategoria">Detalles de la Categoría</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 id="vistaNombreCategoria">-</h5>
                        <p class="text-muted" id="vistaDescripcionCategoria">-</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-success" id="vistaEstadoCategoria">Activo</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center border rounded p-3">
                            <h4 class="text-primary mb-1" id="vistaContadorProductos">0</h4>
                            <p class="text-muted mb-0">Productos</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center border rounded p-3">
                            <h4 class="text-success mb-1" id="vistaFechaCreacion">-</h4>
                            <p class="text-muted mb-0">Fecha Creación</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center border rounded p-3">
                            <h4 class="text-info mb-1" id="vistaCodigoCategoria">-</h4>
                            <p class="text-muted mb-0">ID Categoría</p>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de productos en esta categoría -->
                <div class="mt-4">
                    <h6>Productos en esta categoría:</h6>
                    <div id="listaProductosCategoria">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="editarCategoriaDesdeVista()">
                    <i class="mdi mdi-pencil me-1"></i> Editar Categoría
                </button>
            </div>
        </div>
    </div>
</div>
