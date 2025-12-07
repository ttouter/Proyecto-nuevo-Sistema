<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_activo'] != 'juez') {
    header("Location: ../login/login_unificado.php");
    exit;
}

require_once '../../models/ModeloEvaluacion.php';

// Obtener los equipos asignados a este juez
$misEquipos = ModeloEvaluacion::obtenerEquiposAsignados($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Juez - VEX Control</title>
    <link rel="icon" type="image/x-icon" href="../../assets/img/fav-robot.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar-bg: #2C2C54; --header-bg: #FDF5A3; --bg-body: #f4f4f9; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg-body); margin: 0; display: flex; height: 100vh; overflow: hidden; }
        
        /* SIDEBAR */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; display: flex; flex-direction: column; padding: 20px; }
        .brand { font-size: 1.5rem; font-weight: bold; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; justify-content: center; margin-top:10px; }
        .menu-item { padding: 15px; color: rgba(255,255,255,0.8); text-decoration: none; display: flex; align-items: center; gap: 15px; border-radius: 8px; transition: 0.3s; margin-bottom: 5px; cursor: pointer; }
        .menu-item.active, .menu-item:hover { background: rgba(255,255,255,0.15); color: white; border-left: 4px solid var(--header-bg); }
        .logout { margin-top: auto; color: #ff6b6b; }
        
        /* MAIN */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .header-bar { background: var(--header-bg); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .header-title { font-size: 1.2rem; font-weight: bold; color: var(--sidebar-bg); }
        .work-area { flex: 1; padding: 30px; overflow-y: auto; }

        /* TARJETAS DE EQUIPO A EVALUAR */
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .team-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 5px solid #3498db; transition: 0.3s; }
        .team-card:hover { transform: translateY(-5px); }
        
        .card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .team-name { font-size: 1.3rem; font-weight: bold; color: #333; }
        .school-name { font-size: 0.9rem; color: #666; margin-bottom: 20px; display: block; }
        
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: bold; }
        .status-pending { background: #ffeaa7; color: #d35400; }
        .status-done { background: #d4edda; color: #155724; }

        .btn-eval { display: block; width: 100%; padding: 10px; background: var(--sidebar-bg); color: white; text-align: center; border-radius: 6px; text-decoration: none; font-weight: bold; transition: 0.2s; }
        .btn-eval:hover { background: #40407a; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="brand"><i class="fas fa-gavel"></i> VEX Juez</div>
        <div class="menu-item active"><i class="fas fa-clipboard-check"></i> Evaluaciones</div>
        <a href="../login/terminarSesion_asistente.php" class="menu-item logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </aside>

    <div class="main-content">
        <header class="header-bar">
            <div class="header-title">Equipos Asignados</div>
            <div>Juez: <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></div>
        </header>

        <div class="work-area">
            <?php if (empty($misEquipos)): ?>
                <div style="text-align: center; margin-top: 50px; color: #777;">
                    <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 20px;"></i>
                    <p>No tienes equipos asignados para evaluar por el momento.</p>
                </div>
            <?php else: ?>
                <div class="cards-grid">
                    <?php foreach ($misEquipos as $eq): ?>
                        <div class="team-card">
                            <div class="card-header">
                                <span class="team-name"><?php echo htmlspecialchars($eq['nombreEquipo']); ?></span>
                                <?php if($eq['estado'] == 'Evaluado'): ?>
                                    <span class="status-badge status-done"><i class="fas fa-check"></i> Evaluado</span>
                                <?php else: ?>
                                    <span class="status-badge status-pending"><i class="fas fa-clock"></i> Pendiente</span>
                                <?php endif; ?>
                            </div>
                            
                            <span class="school-name"><i class="fas fa-university"></i> <?php echo htmlspecialchars($eq['nombreEscuela']); ?></span>
                            <div style="margin-bottom: 15px; font-size: 0.9rem; color: #555;">
                                <i class="fas fa-tag"></i> Categoría: <?php echo htmlspecialchars($eq['categoria']); ?>
                            </div>

                            <a href="evaluacion.php?idEquipo=<?php echo $eq['idEquipo']; ?>" class="btn-eval">
                                <i class="fas fa-pen-alt"></i> Evaluar Equipo
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>