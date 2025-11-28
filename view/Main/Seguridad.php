<!-- Script de Seguridad -->
<script>
(function() {
    'use strict';
    
    // Función para mostrar página de seguridad
    function mostrarSeguridad() {
        // Crear overlay de seguridad
        const overlay = document.createElement('div');
        overlay.innerHTML = `
            <div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:99999;display:flex;align-items:center;justify-content:center;">
                <div style="background:white;padding:40px;border-radius:10px;text-align:center;max-width:500px;">
                    <div style="color:#dc3545;font-size:48px;margin-bottom:20px;">🔒</div>
                    <h2 style="color:#dc3545;margin-bottom:15px;">Acceso Restringido</h2>
                    <p style="color:#666;margin-bottom:20px;">Esta acción está bloqueada por políticas de seguridad</p>
                    <button onclick="window.location.href='../Inicio/'" style="background:#007bff;color:white;border:none;padding:10px 20px;border-radius:5px;cursor:pointer;">Volver al Dashboard</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
        
        // Redireccionar después de 3 segundos
        setTimeout(() => {
            window.location.href = 'Seguridad.php';
        }, 3000);
    }
    
    // Bloquear clic derecho
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        mostrarSeguridad();
        return false;
    });
    
    // Bloquear teclas de desarrollador
    document.addEventListener('keydown', function(e) {
        // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, Ctrl+Shift+C
        if (e.key === 'F12' || 
            (e.ctrlKey && e.shiftKey && ['I', 'J', 'C'].includes(e.key)) || 
            (e.ctrlKey && e.key === 'U')) {
            e.preventDefault();
            mostrarSeguridad();
            return false;
        }
    });
    
    // Detectar DevTools (método básico)
    let devtools = {open: false, orientation: null};
    const threshold = 160;
    
    setInterval(() => {
        if (window.outerHeight - window.innerHeight > threshold || 
            window.outerWidth - window.innerWidth > threshold) {
            if (!devtools.open) {
                devtools.open = true;
                mostrarSeguridad();
            }
        } else {
            devtools.open = false;
        }
    }, 500);
    
    // Deshabilitar selección de texto
    document.addEventListener('selectstart', function(e) {
        e.preventDefault();
    });
    
    // Bloquear arrastrar elementos
    document.addEventListener('dragstart', function(e) {
        e.preventDefault();
    });
    
})();
</script>