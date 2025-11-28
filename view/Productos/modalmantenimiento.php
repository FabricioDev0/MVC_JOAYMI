<div id="modalMantenimientoProducto" class="modal fade" tabindex="-1" aria-labelledby="modalMantenimientoProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalProducto">Crear Nuevo Producto</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Formulario de mantenimiento -->
            <form method="post" id="formularioProducto">
                <div class="modal-body">
                    <!-- Campo oculto para ID del producto (para edición) -->
                    <input type="hidden" name="codigoProducto" id="codigoProducto"/>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="nombreProducto" class="form-label">
                                    Nombre del Producto <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombreProducto" name="nombreProducto" 
                                       placeholder="Ej: Laptop HP, Mouse Logitech..." required maxlength="100"/>
                                <div class="form-text">
                                    Ingrese el nombre completo del producto (máximo 100 caracteres).
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingrese un nombre válido para el producto.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="descripcionProducto" class="form-label">
                                    Descripción del Producto <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="descripcionProducto" name="descripcionProducto" 
                                          rows="3" placeholder="Descripción detallada del producto..." required maxlength="500"></textarea>
                                <div class="form-text">
                                    Descripción detallada del producto (máximo 500 caracteres).
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingrese una descripción válida.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label for="stockProducto" class="form-label">
                                    Stock Disponible <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-package-variant"></i>
                                    </span>
                                    <input type="number" class="form-control" id="stockProducto" name="stockProducto" 
                                           placeholder="0" required min="0" max="999999"/>
                                </div>
                                <div class="form-text">
                                    Cantidad disponible en inventario.
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingrese un stock válido (0 o mayor).
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label for="precioProducto" class="form-label">
                                    Precio Unitario <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" class="form-control" id="precioProducto" name="precioProducto" 
                                           placeholder="0.00" required min="0.01" max="999999.99" step="0.01"/>
                                </div>
                                <div class="form-text">
                                    Precio de venta del producto.
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingrese un precio válido (mayor a 0).
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label for="categoriaProducto" class="form-label">
                                    Categoría <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="categoriaProducto" name="codigoCategoria" required>
                                    <option value="">Seleccionar categoría...</option>
                                    <!-- Las opciones se cargan dinámicamente -->
                                </select>
                                <div class="form-text">
                                    Seleccione la categoría del producto.
                                </div>
                                <div class="invalid-feedback">
                                    Por favor seleccione una categoría.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label for="proveedorProducto" class="form-label">
                                    Proveedor <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="proveedorProducto" name="codigoProveedor" required>
                                    <option value="">Seleccionar proveedor...</option>
                                    <!-- Las opciones se cargan dinámicamente -->
                                </select>
                                <div class="form-text">
                                    Seleccione el proveedor del producto.
                                </div>
                                <div class="invalid-feedback">
                                    Por favor seleccione un proveedor.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional para edición -->
                    <div id="informacionAdicionalProducto" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Registro:</label>
                                    <p class="form-control-static" id="fechaCreacionProducto">-</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Estado:</label>
                                    <p class="form-control-static">
                                        <span class="badge bg-success" id="estadoProducto">Activo</span>
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
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="btnGuardarProducto">
                        <i class="mdi mdi-content-save me-1"></i> Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div id="modalEliminarProducto" class="modal fade" tabindex="-1" aria-labelledby="modalEliminarProductoLabel" aria-hidden="true">
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
                        Esta acción eliminará permanentemente el producto <strong id="nombreProductoEliminar"></strong>.
                    </p>
                    <div class="alert alert-warning" role="alert">
                        <i class="mdi mdi-alert me-2"></i>
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger waves-effect waves-light" id="btnConfirmarEliminarProducto">
                    <i class="mdi mdi-trash-can me-1"></i> Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de vista detallada -->
<div id="modalVistaProducto" class="modal fade" tabindex="-1" aria-labelledby="modalVistaProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloVistaProducto">Detalles del Producto</h4>
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
                                        <i class="mdi mdi-package-variant font-size-24"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 id="vistaNombreProducto">-</h5>
                                <p class="text-muted mb-1" id="vistaDescripcionProducto">-</p>
                                <p class="text-muted mb-0">
                                    <i class="mdi mdi-tag me-1"></i><span id="vistaCategoriaProducto">-</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 text-md-end text-center mt-3 mt-md-0">
                        <span class="badge bg-success" id="vistaEstadoProducto">Activo</span>
                    </div>
                </div>
                
                <hr>
                
                <!-- Estadísticas -->
                <div class="row">
                    <div class="col-6 col-md-3">
                        <div class="text-center border rounded p-3">
                            <h4 class="text-primary mb-1" id="vistaStockProducto">0</h4>
                            <p class="text-muted mb-0">Stock</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center border rounded p-3">
                            <h4 class="text-success mb-1" id="vistaPrecioProducto">S/0.00</h4>
                            <p class="text-muted mb-0">Precio</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center border rounded p-3">
                            <h4 class="text-info mb-1" id="vistaProveedorProducto">-</h4>
                            <p class="text-muted mb-0">Proveedor</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center border rounded p-3">
                            <h4 class="text-warning mb-1" id="vistaCodigoProducto">-</h4>
                            <p class="text-muted mb-0">ID Producto</p>
                        </div>
                    </div>
                </div>
                
                <!-- Información detallada -->
                <div class="mt-4">
                    <h6>Información Detallada:</h6>
                    <div class="card border">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-2">
                                        <strong>Descripción:</strong><br>
                                        <span id="vistaDescripcionCompleta">-</span>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <p class="mb-2">
                                        <strong>Fecha de Registro:</strong><br>
                                        <span id="vistaFechaCreacionProducto">-</span>
                                    </p>
                                </div>
                                <div class="col-md-6 col-12">
                                    <p class="mb-2">
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
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="editarProductoDesdeVista()">
                    <i class="mdi mdi-pencil me-1"></i> Editar Producto
                </button>
            </div>
        </div>
    </div>
</div>
