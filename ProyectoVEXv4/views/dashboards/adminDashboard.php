<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_activo'] != 'entrenador') {
    header("Location: ../login/login_unificado.php");
    exit;
}

require_once '../../models/ModeloEntrenador.php';
require_once '../../models/ModeloProcesos.php';

// Cargar datos
$datos = ModeloEntrenador::obtenerDatosDashboard($_SESSION['usuario_id']);
$miEscuela = $datos['escuela'];
$misEquipos = $datos['equipos'];

// Listas para los modales
$categorias = ModeloProcesos::listarCategorias();
$eventos = ModeloProcesos::listarEventos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Entrenador - VEX Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #2C2C54; --accent: #FDF5A3; --text: #333; --bg: #f4f4f9; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; display: flex; height: 100vh; overflow: hidden; }
        
        /* SIDEBAR */
        .sidebar { width: 260px; background: var(--primary); color: white; display: flex; flex-direction: column; padding: 20px; }
        .brand { font-size: 1.5rem; font-weight: bold; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
        .menu-item { padding: 15px; color: rgba(255,255,255,0.8); text-decoration: none; display: flex; align-items: center; gap: 15px; border-radius: 10px; transition: 0.3s; margin-bottom: 5px; }
        .menu-item:hover, .menu-item.active { background: rgba(255,255,255,0.1); color: white; transform: translateX(5px); }
        .logout { margin-top: auto; color: #ff6b6b; }

        /* MAIN CONTENT */
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: var(--primary); margin: 0; }
        
        .btn-main { background: var(--primary); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; transition: 0.3s; }
        .btn-main:hover { background: #40407A; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

        /* GRID DE TARJETAS DE EQUIPO */
        .teams-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        
        .team-card {
            background: white; border-radius: 15px; padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative; border-left: 5px solid var(--accent);
            display: flex; flex-direction: column;
        }
        .team-card:hover { transform: translateY(-10px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }

        .team-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .team-name { font-size: 1.3rem; font-weight: bold; color: var(--primary); }
        .team-cat { background: #e0e0ff; color: var(--primary); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        
        .team-stats { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 0.9rem; color: #666; }
        .stat-item i { margin-right: 5px; color: var(--primary); }
        
        .judges-box { background: #f9f9f9; padding: 10px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 20px; border: 1px dashed #ccc; }
        .judges-box strong { display: block; margin-bottom: 5px; color: var(--primary); }

        .card-actions { margin-top: auto; display: flex; gap: 10px; }
        .btn-small { flex: 1; padding: 8px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.9rem; font-weight: bold; transition: 0.2s; }
        .btn-add { background: #d4edda; color: #155724; }
        .btn-view { background: #e2e3e5; color: #383d41; }
        .btn-add:hover { background: #c3e6cb; }
        .btn-view:hover { background: #d6d8db; }

        /* MODALES */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; }
        .modal-content { background: white; padding: 30px; border-radius: 15px; width: 500px; max-width: 90%; animation: slideIn 0.3s; max-height: 90vh; overflow-y: auto; }
        @keyframes slideIn { from {transform: translateY(-50px); opacity: 0;} to {transform: translateY(0); opacity: 1;} }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        
        .age-warning { color: #dc3545; font-size: 0.85rem; display: none; margin-top: 5px; font-weight: bold; }
        
        /* ESTILOS TABLA DETALLES */
        .details-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.9rem; }
        .details-table th { background: #eee; padding: 8px; text-align: left; }
        .details-table td { border-bottom: 1px solid #eee; padding: 8px; }
        .btn-delete { color: red; cursor: pointer; background: none; border: none; }
        
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 0.75rem; margin-right: 5px; }
        .status-ok { background: #d4edda; color: #155724; }
        .status-no { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="brand"><i class="fas fa-robot"></i> VEX Coach</div>
        <a href="#" class="menu-item active"><i class="fas fa-columns"></i> Mis Equipos</a>
        <a href="#" class="menu-item"><i class="fas fa-chart-line"></i> Estadísticas</a>
        <a href="../login/terminarSesion_asistente.php" class="menu-item logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </aside>

    <main class="main-content">
        <div class="header">
            <div>
                <h1>Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
                <p style="color: #666;">Institución: <strong><?php echo htmlspecialchars($miEscuela['nombre_escuela']); ?></strong></p>
            </div>
            <button onclick="openModal('modalEquipo')" class="btn-main"><i class="fas fa-plus"></i> Nuevo Equipo</button>
        </div>

        <div class="teams-grid">
            <?php if (empty($misEquipos)): ?>
                <div style="grid-column: 1/-1; text-align: center; color: #999; padding: 50px;">
                    <i class="fas fa-robot" style="font-size: 3rem; margin-bottom: 20px;"></i>
                    <p>Aún no tienes equipos registrados. ¡Crea el primero!</p>
                </div>
            <?php else: ?>
                <?php foreach ($misEquipos as $eq): ?>
                    <div class="team-card">
                        <div class="team-header">
                            <span class="team-name"><?php echo htmlspecialchars($eq['nombreEquipo']); ?></span>
                            <span class="team-cat" 
                                  data-min="<?php echo $eq['rangoMin']; ?>" 
                                  data-max="<?php echo $eq['rangoMax']; ?>">
                                <?php echo htmlspecialchars($eq['categoria']); ?>
                            </span>
                        </div>
                        
                        <div class="team-stats">
                            <div class="stat-item"><i class="fas fa-users"></i> <?php echo $eq['num_integrantes']; ?> Integrantes</div>
                            <div class="stat-item"><i class="fas fa-calendar-alt"></i> <?php echo $eq['nombre_Evento']; ?></div>
                        </div>

                        <div class="judges-box">
                            <strong><i class="fas fa-gavel"></i> Jueces Asignados:</strong>
                            <?php echo $eq['jueces_asignados'] ? $eq['jueces_asignados'] : 'Pendiente de asignación'; ?>
                        </div>

                        <div class="card-actions">
                            <!-- AQUI ESTABA EL CAMBIO CLAVE: Cambiamos alert por verDetalles() -->
                            <button class="btn-small btn-view" onclick="verDetalles(<?php echo $eq['idEquipo']; ?>, '<?php echo $eq['nombreEquipo']; ?>')">
                                <i class="fas fa-eye"></i> Detalles
                            </button>
                            
                            <button class="btn-small btn-add" 
                                onclick="openAddMember(
                                    <?php echo $eq['idEquipo']; ?>, 
                                    '<?php echo $eq['nombreEquipo']; ?>',
                                    <?php echo $eq['rangoMin']; ?>,
                                    <?php echo $eq['rangoMax']; ?>
                                )">
                                <i class="fas fa-user-plus"></i> Integrante
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- MODAL NUEVO EQUIPO -->
    <div id="modalEquipo" class="modal">
        <div class="modal-content">
            <h2 style="margin-top:0;">Registrar Nuevo Equipo</h2>
            <form action="../../controllers/control_registrarEquipo.php" method="POST">
                <input type="hidden" name="codEscuela" value="<?php echo $miEscuela['escuela_id']; ?>">
                
                <div class="form-group">
                    <label>Nombre del Equipo</label>
                    <input type="text" name="nombreEquipo" required maxlength="30">
                </div>
                
                <div class="form-group">
                    <label>Categoría</label>
                    <select name="idCategoria" required>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?php echo $cat['idCategoria']; ?>"><?php echo $cat['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Evento</label>
                    <select name="nombreEvento" required>
                        <?php foreach($eventos as $ev): ?>
                            <option value="<?php echo $ev['nombre']; ?>"><?php echo $ev['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display:flex; justify-content:end; gap:10px;">
                    <button type="button" onclick="closeModal('modalEquipo')" style="background:none; border:none; cursor:pointer;">Cancelar</button>
                    <button type="submit" class="btn-main">Guardar Equipo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL NUEVO INTEGRANTE -->
    <div id="modalMember" class="modal">
        <div class="modal-content">
            <h2 style="margin-top:0;">Agregar a <span id="spanTeamName"></span></h2>
            <p style="font-size:0.9rem; color:#666;">Rango de edad permitido: <strong id="spanAgeRange"></strong> años.</p>
            
            <form action="../../controllers/control_registrarParticipante.php" method="POST">
                <input type="hidden" name="idEquipo" id="inputTeamId">
                <div class="form-group"><label>Número de Control</label><input type="number" name="numControl" required></div>
                <div class="form-group"><label>Nombre</label><input type="text" name="nombre" required></div>
                <div style="display:flex; gap:10px;">
                    <div style="flex:1;"><label>Ap. Paterno</label><input type="text" name="apPat" required></div>
                    <div style="flex:1;"><label>Ap. Materno</label><input type="text" name="apMat" required></div>
                </div>
                <div class="form-group">
                    <label>Edad</label><input type="number" name="edad" id="inputAge" required>
                    <div class="age-warning" id="ageWarning">Edad fuera de rango.</div>
                </div>
                <div class="form-group"><label>Sexo</label><select name="sexo"><option>Hombre</option><option>Mujer</option></select></div>
                <div style="display:flex; justify-content:end; gap:10px;">
                    <button type="button" onclick="closeModal('modalMember')" style="background:none; border:none; cursor:pointer;">Cancelar</button>
                    <button type="submit" class="btn-main" id="btnSaveMember">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL VER DETALLES (NUEVO) -->
    <div id="modalDetalles" class="modal">
        <div class="modal-content" style="width: 600px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2 style="margin:0;" id="titleDetalles">Detalles del Equipo</h2>
                <span onclick="closeModal('modalDetalles')" style="cursor:pointer; font-size:1.5rem;">&times;</span>
            </div>
            
            <div style="margin-top:20px; padding:15px; background:#f8f9fa; border-radius:8px;">
                <strong><i class="fas fa-tasks"></i> Progreso de Evaluación:</strong>
                <div style="margin-top:10px;" id="statusEvaluacion">
                    Cargando...
                </div>
            </div>

            <h3 style="margin-top:20px;">Roster de Alumnos</h3>
            <div id="listaIntegrantes">
                <p>Cargando integrantes...</p>
            </div>
        </div>
    </div>

    <script>
        let currentMinAge = 0; let currentMaxAge = 99;

        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }

        function openAddMember(id, name, min, max) {
            document.getElementById('inputTeamId').value = id;
            document.getElementById('spanTeamName').innerText = name;
            document.getElementById('spanAgeRange').innerText = min + ' - ' + max;
            currentMinAge = min; currentMaxAge = max;
            openModal('modalMember');
        }

        // --- LÓGICA DE VER DETALLES (AJAX) ---
        function verDetalles(idEquipo, nombre) {
            document.getElementById('titleDetalles').innerText = "Detalles: " + nombre;
            document.getElementById('statusEvaluacion').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Consultando jueces...';
            document.getElementById('listaIntegrantes').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando alumnos...';
            
            openModal('modalDetalles');

            fetch('../../controllers/api_detalles_equipo.php?id=' + idEquipo)
                .then(response => response.json())
                .then(data => {
                    // 1. Renderizar Estado Evaluación
                    const ev = data.evaluacion;
                    let htmlEval = '';
                    htmlEval += ev.eval_diseno > 0 ? '<span class="status-badge status-ok">Diseño OK</span>' : '<span class="status-badge status-no">Diseño Pendiente</span>';
                    htmlEval += ev.eval_prog > 0 ? '<span class="status-badge status-ok">Programación OK</span>' : '<span class="status-badge status-no">Progra. Pendiente</span>';
                    htmlEval += ev.eval_const > 0 ? '<span class="status-badge status-ok">Construcción OK</span>' : '<span class="status-badge status-no">Const. Pendiente</span>';
                    document.getElementById('statusEvaluacion').innerHTML = htmlEval;

                    // 2. Renderizar Tabla Integrantes
                    if(data.integrantes.length === 0) {
                        document.getElementById('listaIntegrantes').innerHTML = '<p>No hay integrantes registrados.</p>';
                    } else {
                        let htmlTable = '<table class="details-table"><thead><tr><th>Control</th><th>Nombre</th><th>Edad</th><th>Acción</th></tr></thead><tbody>';
                        data.integrantes.forEach(alumno => {
                            htmlTable += `<tr>
                                <td>${alumno.numControl}</td>
                                <td>${alumno.nombre} ${alumno.apellidoPat}</td>
                                <td>${alumno.edad} años</td>
                                <td style="text-align:center;">
                                    <a href="../../controllers/control_eliminar_participante.php?id=${alumno.numControl}" 
                                       onclick="return confirm('¿Eliminar a ${alumno.nombre}?')" class="btn-delete" title="Dar de baja">
                                       <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>`;
                        });
                        htmlTable += '</tbody></table>';
                        document.getElementById('listaIntegrantes').innerHTML = htmlTable;
                    }
                })
                .catch(err => {
                    document.getElementById('listaIntegrantes').innerHTML = '<p style="color:red">Error al cargar datos.</p>';
                    console.error(err);
                });
        }

        document.getElementById('inputAge').addEventListener('input', function() {
            const edad = parseInt(this.value);
            const btn = document.getElementById('btnSaveMember');
            if (edad < currentMinAge || edad > currentMaxAge) {
                document.getElementById('ageWarning').style.display = 'block';
                btn.disabled = true; btn.style.opacity = '0.5';
            } else {
                document.getElementById('ageWarning').style.display = 'none';
                btn.disabled = false; btn.style.opacity = '1';
            }
        });

        window.onclick = function(e) { if (e.target.classList.contains('modal')) e.target.style.display = "none"; }
    </script>
</body>
</html>