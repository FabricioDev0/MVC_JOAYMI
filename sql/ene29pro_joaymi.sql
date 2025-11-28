-- Active: 1759627627509@@127.0.0.1@3306@mysql
-- =============================================
-- 1. CREACIÓN DE BASE DE DATOS
-- =============================================
CREATE DATABASE ene29pro_joaymi
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Usamos la base de datos creada
USE ene29pro_joaymi;

-- =============================================
-- 2. TABLAS CON AUDITORÍA
-- =============================================

-- Tabla de roles de usuario
CREATE TABLE tm_roles (
    cod_rol INT AUTO_INCREMENT PRIMARY KEY,
    str_nombre VARCHAR(50) NOT NULL UNIQUE,
    fec_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    est_activo TINYINT(1) DEFAULT 1
);

-- Tabla de usuarios con referencias a roles y auditoría
CREATE TABLE tm_usuarios (
    cod_usuario INT AUTO_INCREMENT PRIMARY KEY,
    str_correo VARCHAR(100) NOT NULL UNIQUE,
    str_clave VARCHAR(100) NOT NULL,
    cod_rol INT NOT NULL,
    est_activo TINYINT(1) DEFAULT 1,
    fec_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fec_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fec_eliminacion DATETIME NULL DEFAULT NULL,
    cod_usuario_creador INT NULL,
    cod_usuario_modificador INT NULL,
    FOREIGN KEY (cod_rol) REFERENCES tm_roles(cod_rol),
    FOREIGN KEY (cod_usuario_creador) REFERENCES tm_usuarios(cod_usuario),
    FOREIGN KEY (cod_usuario_modificador) REFERENCES tm_usuarios(cod_usuario)
);

-- Tabla de proveedores
CREATE TABLE tm_proveedores (
    cod_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    str_nombre VARCHAR(100) NOT NULL,
    str_contacto VARCHAR(100),
    str_telefono VARCHAR(20),
    est_activo TINYINT(1) DEFAULT 1,
    fec_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fec_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fec_eliminacion DATETIME NULL DEFAULT NULL,
    cod_usuario_creador INT NULL,
    cod_usuario_modificador INT NULL,
    FOREIGN KEY (cod_usuario_creador) REFERENCES tm_usuarios(cod_usuario),
    FOREIGN KEY (cod_usuario_modificador) REFERENCES tm_usuarios(cod_usuario)
);

-- Tabla de categorías
CREATE TABLE tm_categorias (
    cod_categoria INT AUTO_INCREMENT PRIMARY KEY,
    str_nombre VARCHAR(100) NOT NULL,
    str_descripcion TEXT,
    est_activo TINYINT(1) DEFAULT 1,
    fec_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fec_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fec_eliminacion DATETIME NULL DEFAULT NULL,
    cod_usuario_creador INT NULL,
    cod_usuario_modificador INT NULL,
    FOREIGN KEY (cod_usuario_creador) REFERENCES tm_usuarios(cod_usuario),
    FOREIGN KEY (cod_usuario_modificador) REFERENCES tm_usuarios(cod_usuario)
);

-- Tabla de productos
CREATE TABLE tm_productos (
    cod_producto INT AUTO_INCREMENT PRIMARY KEY,
    str_nombre VARCHAR(100) NOT NULL,
    str_descripcion TEXT,
    int_stock INT NOT NULL DEFAULT 0,
    dec_precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    cod_categoria INT NOT NULL,
    cod_proveedor INT NOT NULL,
    est_activo TINYINT(1) DEFAULT 1,
    fec_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fec_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fec_eliminacion DATETIME NULL DEFAULT NULL,
    cod_usuario_creador INT NULL,
    cod_usuario_modificador INT NULL,
    FOREIGN KEY (cod_categoria) REFERENCES tm_categorias(cod_categoria),
    FOREIGN KEY (cod_proveedor) REFERENCES tm_proveedores(cod_proveedor),
    FOREIGN KEY (cod_usuario_creador) REFERENCES tm_usuarios(cod_usuario),
    FOREIGN KEY (cod_usuario_modificador) REFERENCES tm_usuarios(cod_usuario)
);

-- =============================================
-- 3. DATOS INICIALES
-- =============================================
-- Insertamos roles predeterminados
INSERT INTO tm_roles (str_nombre) VALUES ('Administrador'), ('Usuario');

-- Usuario administrador inicial
INSERT INTO tm_usuarios (
    str_correo, str_clave, cod_rol, cod_usuario_creador
) VALUES (
    'admin@joaymi.com', 'admin123', 1, NULL
);

-- =============================================
-- 4. PROCEDIMIENTOS ALMACENADOS
-- =============================================

--  Usuarios 
DELIMITER $$

CREATE PROCEDURE InsertarUsuario(IN p_correo VARCHAR(100), IN p_clave VARCHAR(100), IN p_rol INT, IN p_usuario_creador INT)
BEGIN
    INSERT INTO tm_usuarios (str_correo, str_clave, cod_rol, cod_usuario_creador)
    VALUES (p_correo, p_clave, p_rol, p_usuario_creador);
END $$

CREATE PROCEDURE ListarUsuarios()
BEGIN
    SELECT * FROM tm_usuarios WHERE est_activo = 1;
END $$

CREATE PROCEDURE BuscarUsuarioPorId(IN p_id INT)
BEGIN
    SELECT * FROM tm_usuarios WHERE cod_usuario = p_id;
END $$

CREATE PROCEDURE ActualizarUsuario(IN p_id INT, IN p_correo VARCHAR(100), IN p_rol INT, IN p_usuario_modificador INT)
BEGIN
    UPDATE tm_usuarios
    SET str_correo = p_correo, cod_rol = p_rol, cod_usuario_modificador = p_usuario_modificador
    WHERE cod_usuario = p_id;
END $$

CREATE PROCEDURE EliminarUsuario(IN p_id INT, IN p_usuario_modificador INT)
BEGIN
    UPDATE tm_usuarios
    SET est_activo = 0, fec_eliminacion = CURRENT_TIMESTAMP, cod_usuario_modificador = p_usuario_modificador
    WHERE cod_usuario = p_id;
END $$

CREATE PROCEDURE LoginUsuario(IN p_correo VARCHAR(100))
BEGIN
    SELECT * FROM tm_usuarios
    WHERE str_correo = p_correo AND est_activo = 1;
END $$

--  Proveedores 
CREATE PROCEDURE InsertarProveedor(IN p_nombre VARCHAR(100), IN p_contacto VARCHAR(100), IN p_telefono VARCHAR(20), IN p_usuario_creador INT)
BEGIN
    INSERT INTO tm_proveedores (str_nombre, str_contacto, str_telefono, cod_usuario_creador)
    VALUES (p_nombre, p_contacto, p_telefono, p_usuario_creador);
END $$

CREATE PROCEDURE ListarProveedores()
BEGIN
    SELECT * FROM tm_proveedores WHERE est_activo = 1;
END $$

CREATE PROCEDURE BuscarProveedorPorId(IN p_id INT)
BEGIN
    SELECT * FROM tm_proveedores WHERE cod_proveedor = p_id;
END $$

CREATE PROCEDURE ActualizarProveedor(IN p_id INT, IN p_nombre VARCHAR(100), IN p_contacto VARCHAR(100), IN p_telefono VARCHAR(20), IN p_usuario_modificador INT)
BEGIN
    UPDATE tm_proveedores
    SET str_nombre = p_nombre, str_contacto = p_contacto, str_telefono = p_telefono, cod_usuario_modificador = p_usuario_modificador
    WHERE cod_proveedor = p_id;
END $$

CREATE PROCEDURE EliminarProveedor(IN p_id INT, IN p_usuario_modificador INT)
BEGIN
    UPDATE tm_proveedores
    SET est_activo = 0, fec_eliminacion = CURRENT_TIMESTAMP, cod_usuario_modificador = p_usuario_modificador
    WHERE cod_proveedor = p_id;
END $$

--  Categorías 
CREATE PROCEDURE InsertarCategoria(IN p_nombre VARCHAR(100), IN p_descripcion TEXT, IN p_usuario_creador INT)
BEGIN
    INSERT INTO tm_categorias (str_nombre, str_descripcion, cod_usuario_creador)
    VALUES (p_nombre, p_descripcion, p_usuario_creador);
END $$

CREATE PROCEDURE ListarCategorias()
BEGIN
    SELECT * FROM tm_categorias WHERE est_activo = 1;
END $$

CREATE PROCEDURE BuscarCategoriaPorId(IN p_id INT)
BEGIN
    SELECT * FROM tm_categorias WHERE cod_categoria = p_id;
END $$

CREATE PROCEDURE ActualizarCategoria(IN p_id INT, IN p_nombre VARCHAR(100), IN p_descripcion TEXT, IN p_usuario_modificador INT)
BEGIN
    UPDATE tm_categorias
    SET str_nombre = p_nombre, str_descripcion = p_descripcion, cod_usuario_modificador = p_usuario_modificador
    WHERE cod_categoria = p_id;
END $$

CREATE PROCEDURE EliminarCategoria(IN p_id INT, IN p_usuario_modificador INT)
BEGIN
    UPDATE tm_categorias
    SET est_activo = 0, fec_eliminacion = CURRENT_TIMESTAMP, cod_usuario_modificador = p_usuario_modificador
    WHERE cod_categoria = p_id;
END $$

--  Productos 
CREATE PROCEDURE InsertarProducto(
    IN p_nombre VARCHAR(100), IN p_descripcion TEXT,
    IN p_stock INT, IN p_precio DECIMAL(10,2),
    IN p_categoria INT, IN p_proveedor INT,
    IN p_usuario_creador INT
)
BEGIN
    INSERT INTO tm_productos (
        str_nombre, str_descripcion, int_stock, dec_precio,
        cod_categoria, cod_proveedor, cod_usuario_creador
    ) VALUES (
        p_nombre, p_descripcion, p_stock, p_precio,
        p_categoria, p_proveedor, p_usuario_creador
    );
END $$

CREATE PROCEDURE ListarProductos()
BEGIN
    SELECT 
        p.cod_producto, p.str_nombre, p.int_stock, p.dec_precio,
        c.str_nombre AS categoria, pr.str_nombre AS proveedor
    FROM tm_productos p
    JOIN tm_categorias c ON p.cod_categoria = c.cod_categoria
    JOIN tm_proveedores pr ON p.cod_proveedor = pr.cod_proveedor
    WHERE p.est_activo = 1;
END $$

CREATE PROCEDURE BuscarProductoPorId(IN p_id INT)
BEGIN
    SELECT * FROM tm_productos WHERE cod_producto = p_id;
END $$

CREATE PROCEDURE ActualizarProducto(
    IN p_id INT, IN p_nombre VARCHAR(100), IN p_descripcion TEXT,
    IN p_stock INT, IN p_precio DECIMAL(10,2),
    IN p_categoria INT, IN p_proveedor INT,
    IN p_usuario_modificador INT
)
BEGIN
    UPDATE tm_productos
    SET str_nombre = p_nombre, str_descripcion = p_descripcion,
        int_stock = p_stock, dec_precio = p_precio,
        cod_categoria = p_categoria, cod_proveedor = p_proveedor,
        cod_usuario_modificador = p_usuario_modificador
    WHERE cod_producto = p_id;
END $$

CREATE PROCEDURE EliminarProducto(IN p_id INT, IN p_usuario_modificador INT)
BEGIN
    UPDATE tm_productos
    SET est_activo = 0, fec_eliminacion = CURRENT_TIMESTAMP, cod_usuario_modificador = p_usuario_modificador
    WHERE cod_producto = p_id;
END $$

DELIMITER ;

-- =============================================
-- 5. VISTAS PARA CONSULTAS RELACIONADAS
-- =============================================

-- Vista de usuarios con nombre del rol
CREATE VIEW vw_listar_usuarios AS
SELECT u.cod_usuario, u.str_correo, r.str_nombre AS rol, u.est_activo
FROM tm_usuarios u
JOIN tm_roles r ON u.cod_rol = r.cod_rol
WHERE u.est_activo = 1;

-- Vista de productos con categoría y proveedor
CREATE VIEW vw_listar_productos AS
SELECT 
    p.cod_producto, p.str_nombre, p.int_stock, p.dec_precio,
    c.str_nombre AS categoria, pr.str_nombre AS proveedor
FROM tm_productos p
JOIN tm_categorias c ON p.cod_categoria = c.cod_categoria
JOIN tm_proveedores pr ON p.cod_proveedor = pr.cod_proveedor
WHERE p.est_activo = 1;

-- Vista de proveedores
CREATE VIEW vw_listar_proveedores AS
SELECT cod_proveedor, str_nombre, str_contacto, str_telefono, est_activo
FROM tm_proveedores
WHERE est_activo = 1;

-- Vista de categorías
CREATE VIEW vw_listar_categorias AS
SELECT cod_categoria, str_nombre, str_descripcion, est_activo
FROM tm_categorias
WHERE est_activo = 1;

-- Vista de roles
CREATE VIEW vw_listar_roles AS
SELECT cod_rol, str_nombre, est_activo
FROM tm_roles
WHERE est_activo = 1;

-- =============================================
-- 6. DATOS DE EJEMPLO PARA EMPRESA DE MANUALIDADES
-- =============================================

--  USUARIOS ADICIONALES (6 usuarios) 
INSERT INTO tm_usuarios (str_correo, str_clave, cod_rol, cod_usuario_creador) VALUES
('user1@gmail.com', '123456', 2, 1),
('user2@gmail.com', '123456', 2, 1),
('user3@gmail.com', '123456', 1, 1),
('user4@gmail.com', '123456', 2, 1),
('user5@gmail.com', '123456', 2, 1),
('user6@gmail.com', '123456', 1, 1);

--  PROVEEDORES DE MATERIALES PARA MANUALIDADES (6 proveedores) 
INSERT INTO tm_proveedores (str_nombre, str_contacto, str_telefono, cod_usuario_creador) VALUES
('Artesanías del Perú S.A.C.', 'María Elena Vásquez', '+51-987-123-456', 1),
('Distribuidora Creativa', 'Carlos Mendoza', '+51-976-234-567', 1),
('Materiales y Más', 'Ana Lucía Torres', '+51-965-345-678', 1),
('Papelería Artística', 'Roberto Silva', '+51-954-456-789', 1),
('Textiles Andinos', 'Carmen Flores', '+51-943-567-890', 1),
('Suministros Craft', 'Diego Herrera', '+51-932-678-901', 1);

--  CATEGORÍAS DE PRODUCTOS PARA MANUALIDADES (6 categorías) 
INSERT INTO tm_categorias (str_nombre, str_descripcion, cod_usuario_creador) VALUES
('Papelería y Cartón', 'Papeles decorativos, cartulinas, cartón corrugado y materiales de papel', 1),
('Textiles y Hilos', 'Telas, hilos, lanas, cintas y materiales textiles para bordado y costura', 1),
('Pinturas y Pinceles', 'Pinturas acrílicas, acuarelas, pinceles y materiales para pintura artística', 1),
('Bisutería y Abalorios', 'Cuentas, dijes, alambres, cadenas y materiales para joyería artesanal', 1),
('Herramientas Craft', 'Tijeras especiales, pegamentos, pistolas de silicón y herramientas para manualidades', 1),
('Decoración y Adornos', 'Flores artificiales, listones, stickers y elementos decorativos', 1);

--  PRODUCTOS PARA MANUALIDADES (6 productos) 
INSERT INTO tm_productos (str_nombre, str_descripcion, int_stock, dec_precio, cod_categoria, cod_proveedor, cod_usuario_creador) VALUES
('Papel Scrapbook Pack x12', 'Set de 12 hojas de papel decorativo para scrapbooking, diseños variados 30x30cm', 45, 25.50, 1, 1, 1),
('Hilo Mouliné DMC x50 colores', 'Pack de 50 madejas de hilo mouliné DMC para bordado, colores surtidos', 30, 89.90, 2, 2, 1),
('Pintura Acrílica Set x24', 'Set de 24 tubos de pintura acrílica de 12ml cada uno, colores básicos y metálicos', 25, 45.00, 3, 3, 1),
('Cuentas de Madera x500', 'Mix de 500 cuentas de madera natural en diferentes tamaños y formas', 60, 18.75, 4, 4, 1),
('Pistola de Silicón Profesional', 'Pistola de silicón caliente de 40W con interruptor y base de apoyo', 15, 32.00, 5, 5, 1),
('Flores de Tela Vintage x20', 'Pack de 20 flores de tela en tonos vintage para decoración y manualidades', 40, 22.50, 6, 6, 1);

-- =============================================
-- 7. CONSULTAS DE VERIFICACIÓN
-- =============================================

-- Verificar cantidad de registros por tabla
SELECT 'RESUMEN DEL SISTEMA JOAYMI - MANUALIDADES' as TITULO;

SELECT 'Roles' as Tabla, COUNT(*) as Total FROM tm_roles WHERE est_activo = 1
UNION ALL
SELECT 'Usuarios' as Tabla, COUNT(*) as Total FROM tm_usuarios WHERE est_activo = 1
UNION ALL
SELECT 'Proveedores' as Tabla, COUNT(*) as Total FROM tm_proveedores WHERE est_activo = 1
UNION ALL
SELECT 'Categorías' as Tabla, COUNT(*) as Total FROM tm_categorias WHERE est_activo = 1
UNION ALL
SELECT 'Productos' as Tabla, COUNT(*) as Total FROM tm_productos WHERE est_activo = 1;

-- =============================================
-- 8. VISTA DETALLADA DE DATOS INSERTADOS
-- =============================================

-- Mostrar usuarios creados
SELECT 'USUARIOS DEL SISTEMA:' as INFO;
SELECT cod_usuario, str_correo, 
       CASE WHEN cod_rol = 1 THEN 'Administrador' ELSE 'Usuario' END as rol,
       fec_creacion
FROM tm_usuarios 
WHERE est_activo = 1
ORDER BY cod_usuario;

-- Mostrar proveedores de manualidades
SELECT 'PROVEEDORES DE MATERIALES:' as INFO;
SELECT cod_proveedor, str_nombre, str_contacto, str_telefono
FROM tm_proveedores 
WHERE est_activo = 1
ORDER BY cod_proveedor;

-- Mostrar categorías de productos
SELECT 'CATEGORÍAS DE PRODUCTOS:' as INFO;
SELECT cod_categoria, str_nombre, str_descripcion
FROM tm_categorias 
WHERE est_activo = 1
ORDER BY cod_categoria;

-- Mostrar productos con detalles
SELECT 'PRODUCTOS PARA MANUALIDADES:' as INFO;
SELECT p.cod_producto, p.str_nombre, p.str_descripcion, 
       p.int_stock, CONCAT('S/ ', p.dec_precio) as precio,
       c.str_nombre as categoria, pr.str_nombre as proveedor
FROM tm_productos p
JOIN tm_categorias c ON p.cod_categoria = c.cod_categoria
JOIN tm_proveedores pr ON p.cod_proveedor = pr.cod_proveedor
WHERE p.est_activo = 1
ORDER BY p.cod_producto;

-- =============================================
-- 9. CONSULTA PARA VER PROCEDIMIENTOS DISPONIBLES
-- =============================================
SHOW PROCEDURE STATUS WHERE Db = "ene29pro_joaymi";