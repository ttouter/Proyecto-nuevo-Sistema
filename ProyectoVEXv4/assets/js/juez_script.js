document.addEventListener('DOMContentLoaded', function() {
    
    // --- VARIABLES ---
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.content-section');
    const btnEvaluarList = document.querySelectorAll('.btn-evaluar');
    const btnCancelarEval = document.getElementById('btn-cancelar-eval');
    const navEvaluacion = document.getElementById('nav-evaluacion');
    
    // --- NAVEGACIÓN SPA ---
    function navigateTo(targetId) {
        // Actualizar Menú
        navLinks.forEach(link => {
            link.classList.remove('active');
            if(link.getAttribute('href') === '#' + targetId) {
                link.classList.add('active');
            }
        });

        // Actualizar Secciones
        sections.forEach(section => {
            section.classList.remove('active');
        });
        const targetSection = document.getElementById(targetId);
        if(targetSection) targetSection.classList.add('active');
    }

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            navigateTo(targetId);
        });
    });

    // --- LÓGICA DE EVALUACIÓN (Abrir Formulario) ---
    btnEvaluarList.forEach(btn => {
        btn.addEventListener('click', function() {
            const teamId = this.getAttribute('data-id');
            const teamName = this.getAttribute('data-name');

            // 1. Llenar datos en el formulario
            document.getElementById('input-id-equipo').value = teamId;
            document.getElementById('eval-team-title').textContent = `Evaluando a: ${teamName} (#${teamId})`;

            // 2. Mostrar pestaña "Evaluando" en sidebar y navegar ahí
            navEvaluacion.classList.remove('hidden-nav'); // Mostrar en sidebar
            navEvaluacion.classList.add('active-link'); // Resaltar
            
            navigateTo('evaluacion');
        });
    });

    // --- CANCELAR EVALUACIÓN ---
    btnCancelarEval.addEventListener('click', function() {
        if(confirm('¿Salir sin guardar? Se perderán los datos no guardados.')) {
            navigateTo('equipos'); // Volver a la lista
            navEvaluacion.classList.add('hidden-nav'); // Ocultar del menú
            document.getElementById('formEvaluacion').reset(); // Limpiar form
        }
    });

    // --- TABS INTERNOS DEL FORMULARIO (Diseño / Progra / Construcción) ---
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); // Evita submit del form

            // Quitar active de todo
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Activar clickeado
            this.classList.add('active');
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // --- CÁLCULO DE PUNTAJE EN TIEMPO REAL ---
    const inputs = document.querySelectorAll('.rubric-item input');
    const scoreDisplay = document.getElementById('score-total');

    inputs.forEach(input => {
        input.addEventListener('input', updateTotal);
    });

    function updateTotal() {
        let total = 0;
        inputs.forEach(input => {
            const val = parseInt(input.value) || 0;
            total += val;
        });
        scoreDisplay.textContent = total;
    }
});