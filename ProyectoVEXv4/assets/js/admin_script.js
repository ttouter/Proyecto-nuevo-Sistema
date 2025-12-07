document.addEventListener('DOMContentLoaded', function() {
    
    // 1. OBTENER TODOS LOS ENLACES DEL MENÚ LATERAL
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.content-section');

    // 2. AGREGAR EL EVENTO DE CLIC A CADA ENLACE
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // EVITA QUE LA PÁGINA SALTE O SE RECARGUE

            // A. Remover la clase 'active' de todos los enlaces y secciones
            navLinks.forEach(nav => nav.classList.remove('active'));
            sections.forEach(section => section.classList.remove('active'));

            // B. Agregar clase 'active' al enlace clickeado
            this.classList.add('active');

            // C. Mostrar la sección correspondiente
            // Usamos el atributo 'href' (ej: #eventos) para saber qué ID mostrar
            const targetId = this.getAttribute('href').substring(1); // Quita el #
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                targetSection.classList.add('active');
            }
        });
    });

    // 3. LÓGICA PARA BOTONES DE ELIMINAR (Simulación)
    const deleteButtons = document.querySelectorAll('.delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if(confirm('¿Seguro que deseas eliminar este registro?')) {
                const row = this.closest('tr');
                row.style.transition = "all 0.5s";
                row.style.opacity = "0";
                setTimeout(() => row.remove(), 500);
            }
        });
    });
});