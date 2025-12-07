<?php
session_start();

// 1. Verificación de Seguridad
if (!isset($_SESSION['rol_activo']) || $_SESSION['rol_activo'] !== 'entrenador') {
    header("Location: ../login/login_unificado.php");
    exit();
}

require_once '../../models/ModeloProcesos.php';

// 2. Cargar Datos
$idEntrenador = $_SESSION['usuario_id'];
$nombre_entrenador = $_SESSION['usuario_nombre'] ?? "Entrenador";

$listaEscuelas = ModeloProcesos::listarEscuelas();
$listaEventos = ModeloProcesos::listarEventos();
$misEquipos = ModeloProcesos::listarEquiposPorEntrenador($idEntrenador);

// Conteo simple para las cards
$totalEquipos = count($misEquipos);
$totalAlumnos = 0;
foreach($misEquipos as $eq) { $totalAlumnos += $eq['numIntegrantes']; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Entrenador - VEX Robotics</title>
    <link rel="icon" type="image/x-icon" href="../../assets/img/fav-robot.ico">

    <link rel="stylesheet" href="../../assets/css/styles_asistenteDashboard.css">
    <link rel="stylesheet" href="../../assets/css/styles_entrenador.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="dashboard-container">

    <nav class="sidebar">
        <div class="sidebar-header">
            <div class="logo-coach">🧢</div>
            <h2>VEX Team</h2>
            <p>Panel de Entrenador</p>
        </div>

        <ul class="sidebar-menu">
            <li><a href="#resumen" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Resumen</a></li>
            <li><a href="#completar-perfil" class="nav-link"><i class="fas fa-id-card"></i> Mi Perfil</a></li>
            <li><a href="#nuevo-equipo" class="nav-link"><i class="fas fa-plus-circle"></i> Nuevo Equipo</a></li>
            <li><a href="#participantes" class="nav-link"><i class="fas fa-user-plus"></i> Participantes</a></li>
            <li><a href="#mis-equipos" class="nav-link"><i class="fas fa-users"></i> Mis Equipos</a></li>
            <li><a href="../login/terminarSesion_asistente.php" class="logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </nav>

    <main class="main-content">
        
        <header class="content-header">
            <h1>Gestión de Equipos</h1>
            <div class="user-info">
                <span>Coach: <?php echo htmlspecialchars($nombre_entrenador); ?></span>
                <i class="fas fa-id-badge fa-lg" style="margin-left: 10px; color: #40407A;"></i>
            </div>
        </header>

        <!-- SECCIÓN: RESUMEN -->
        <div id="resumen" class="content-section active">
            <div class="welcome-banner coach-banner">
                <h2>¡Hola de nuevo, Coach!</h2>
                <p>Prepara a tus equipos para la victoria. Aquí tienes el estado actual.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card indigo">
                    <div class="icon"><i class="fas fa-robot"></i></div>
                    <div class="info">
                        <h3>Mis Equipos</h3>
                        <p class="number"><?php echo $totalEquipos; ?></p>
                    </div>
                </div>
                <div class="stat-card teal">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="info">
                        <h3>Participantes</h3>
                        <p class="number"><?php echo $totalAlumnos; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN: COMPLETAR PERFIL -->
        <div id="completar-perfil" class="content-section">
            <div class="form-section">
                <h2><i class="fas fa-university"></i> Mi Escuela de Procedencia</h2>
                <form action="../../controllers/control_completarEntrenador.php" method="POST" autocomplete="off">
                    <input type="hidden" name="idAsistente" value="<?php echo $idEntrenador; ?>">
                    <div class="form-group">
                        <label for="codEscuela">Selecciona tu Escuela:</label>
                        <select name="codEscuela" id="codEscuela" required class="big-select" autocomplete="off">
                            <option value="">-- Seleccione --</option>
                            <?php foreach ($listaEscuelas as $escuela): ?>
                                <option value="<?php echo $escuela['codEscuela']; ?>">
                                    <?php echo htmlspecialchars($escuela['nombreEscuela']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Guardar Información</button>
                </form>
            </div>
        </div>

        <!-- SECCIÓN: NUEVO EQUIPO -->
        <div id="nuevo-equipo" class="content-section">
            <div class="form-section">
                <h2><i class="fas fa-plus"></i> Registrar Nuevo Equipo</h2>
                <form action="../../controllers/control_registrarEquipo.php" method="POST" autocomplete="off">
                    <input type="hidden" name="idAsistente" value="<?php echo $idEntrenador; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre del Equipo *</label>
                            <input type="text" name="nombreEquipo" required maxlength="30" autocomplete="off" >
                        </div>
                        <div class="form-group">
                            <label>Categoría *</label>
                            <select name="idCategoria" required autocomplete="off">
                                <option value="1">Primaria</option>
                                <option value="2">Secundaria</option>
                                <option value="3">Preparatoria</option>
                                <option value="4">Universidad</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Evento *</label>
                            <select name="evento" required autocomplete="off">
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($listaEventos as $ev): ?>
                                    <option value="<?php echo htmlspecialchars($ev['nombre']); ?>">
                                        <?php echo htmlspecialchars($ev['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Escuela del Equipo *</label>
                            <select name="codEscuela" required autocomplete="off">
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($listaEscuelas as $esc): ?>
                                    <option value="<?php echo $esc['codEscuela']; ?>">
                                        <?php echo htmlspecialchars($esc['nombreEscuela']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Registrar Equipo</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SECCIÓN: REGISTRAR PARTICIPANTES -->
        <div id="participantes" class="content-section">
            <div class="form-section">
                <h2><i class="fas fa-user-plus"></i> Agregar Integrantes</h2>
                <form action="../../controllers/control_registrarParticipante.php" method="POST" autocomplete="off">
                    
                    <div class="form-group highlight-group">
                        <label>Selecciona el Equipo *</label>
                        <select name="idEquipo" required class="big-select" autocomplete="off">
                            <option value="">-- Mis Equipos --</option>
                            <?php foreach ($misEquipos as $eq): ?>
                                <option value="<?php echo $eq['idEquipo']; ?>">
                                    <?php echo htmlspecialchars($eq['nombreEquipo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Número de Control *</label>
                            <input type="number" name="numControl" required autocomplete="off" step="1">
                        </div>
                        <div class="form-group">
                            <label>Nombre *</label>
                            <input type="text" name="nombre" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]+" maxlength="30" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Apellido Paterno *</label>
                            <input type="text" name="apellidoPat" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]+" maxlength="30" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Apellido Materno *</label>
                            <input type="text" name="apellidoMat" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]+" maxlength="30" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Edad *</label>
                            <input type="number" name="edad" required step="1" min="1" max="99" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Sexo *</label>
                            <select name="sexo" required autocomplete="off">
                                <option value="Hombre">Hombre</option>
                                <option value="Mujer">Mujer</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Guardar Participante</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SECCIÓN: MIS EQUIPOS -->
        <div id="mis-equipos" class="content-section">
            <div class="teams-container">
                <?php if(empty($misEquipos)): ?>
                    <p>No tienes equipos registrados.</p>
                <?php else: ?>
                    <?php foreach ($misEquipos as $eq): ?>
                    <div class="coach-team-card">
                        <div class="card-header-img placeholder-img">
                            <i class="fas fa-robot fa-3x"></i>
                            <span class="category-badge"><?php echo htmlspecialchars($eq['categoria']); ?></span>
                        </div>
                        <div class="card-body">
                            <h3><?php echo htmlspecialchars($eq['nombreEquipo']); ?></h3>
                            <p class="event-name"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($eq['nombreEvento']); ?></p>
                            
                            <div class="members-preview">
                                <span>Integrantes: <?php echo $eq['numIntegrantes']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </main>
</div>
<script src="../../assets/js/entrenador_script.js"></script>
</body>
</html>