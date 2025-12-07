document.addEventListener('DOMContentLoaded', function() {
    
    // --- NAVEGACIÓN SPA ---
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.content-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // 1. Gestionar clases 'active' en links
            navLinks.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');

            // 2. Gestionar visibilidad de secciones
            const targetId = this.getAttribute('href').substring(1);
            sections.forEach(section => section.classList.remove('active'));
            
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.classList.add('active');
            }
        });
    });

    // --- ALERTAS DE FORMULARIOS (Simulación UX) ---
    
    // Formulario de Equipo
    const formEquipo = document.getElementById('formNuevoEquipo');
    if(formEquipo) {
        formEquipo.addEventListener('submit', function(e) {
            // Nota: Permitimos el submit real si tienes backend conectado.
            // Si es solo frontend demo, descomenta:
            // e.preventDefault();
            // alert('Equipo registrado exitosamente (Simulación)');
            // formEquipo.reset();
        });
    }

    // Formulario de Participante
    const formParticipante = document.getElementById('formParticipante');
    if(formParticipante) {
        formParticipante.addEventListener('submit', function(e) {
            // e.preventDefault();
            // alert('Participante agregado al equipo (Simulación)');
            // formParticipante.reset();
        });
    }

    // --- INTERACTIVIDAD TARJETAS ---
    const detailButtons = document.querySelectorAll('.btn-solid');
    detailButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Aquí podrías abrir un modal con detalles del equipo
            alert('Abriendo detalles del equipo...');
        });
    });
});