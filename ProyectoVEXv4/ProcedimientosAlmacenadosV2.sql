USE BaseDatosVex;

DELIMITER //

-- =================================================================
-- 1. AUTENTICACIÓN Y USUARIOS
-- =================================================================
DROP PROCEDURE IF EXISTS AltaAsistente //
CREATE PROCEDURE AltaAsistente(
    IN p_nombre VARCHAR(50),
    IN p_apellidoPat VARCHAR(50),
    IN p_apellidoMat VARCHAR(50),
    IN p_sexo ENUM('Hombre','Mujer'),
    IN p_email VARCHAR(100),
    IN p_password VARCHAR(255),
    OUT mensaje VARCHAR(100)
)
BEGIN
    IF EXISTS (SELECT 1 FROM Asistente WHERE email = p_email) THEN
        SET mensaje = 'El correo ya está registrado.';
    ELSE
        INSERT INTO Asistente (nombre, apellidoPat, apellidoMat, sexo, email, password)
        VALUES (p_nombre, p_apellidoPat, p_apellidoMat, p_sexo, p_email, p_password);
        SET mensaje = 'Registro exitoso';
    END IF;
END //

DROP PROCEDURE IF EXISTS SP_InicioSesion_Asistente //
CREATE PROCEDURE SP_InicioSesion_Asistente(IN p_email VARCHAR(100))
BEGIN
    SELECT idAsistente, nombre, password, email FROM Asistente WHERE email = p_email LIMIT 1;
END //

-- =================================================================
-- 2. GESTIÓN DE ROLES (Entrenador / Juez)
-- =================================================================
DROP PROCEDURE IF EXISTS AltaDetallesEntrenador //
CREATE PROCEDURE AltaDetallesEntrenador(
    IN p_idAsistente INT,
    IN p_codEscuela VARCHAR(50),
    OUT mensaje VARCHAR(100)
)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM EscuelaProcedencia WHERE codEscuela = p_codEscuela) THEN
        SET mensaje = 'La escuela no existe.';
    ELSE
        INSERT INTO Entrenador (idEntrenador, idAsistente_Asistente, codEscuela_EscuelaProcedencia)
        VALUES (p_idAsistente, p_idAsistente, p_codEscuela)
        ON DUPLICATE KEY UPDATE codEscuela_EscuelaProcedencia = p_codEscuela;
        SET mensaje = 'Entrenador guardado.';
    END IF;
END //

DROP PROCEDURE IF EXISTS AltaDetallesJuez //
CREATE PROCEDURE AltaDetallesJuez(
    IN p_idAsistente INT,
    IN p_gradoEstudios VARCHAR(50),
    IN p_codEscuela VARCHAR(50),
    OUT mensaje VARCHAR(100)
)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM EscuelaProcedencia WHERE codEscuela = p_codEscuela) THEN
        SET mensaje = 'La escuela no existe.';
    ELSE
        INSERT INTO Juez (idJuez, gradoEstudios, idAsistente_Asistente, codEscuela_EscuelaProcedencia)
        VALUES (p_idAsistente, p_gradoEstudios, p_idAsistente, p_codEscuela)
        ON DUPLICATE KEY UPDATE gradoEstudios = p_gradoEstudios, codEscuela_EscuelaProcedencia = p_codEscuela;
        SET mensaje = 'Juez guardado.';
    END IF;
END //

-- =================================================================
-- 3. REGISTROS (Eventos, Escuelas, Equipos, Participantes)
-- =================================================================
DROP PROCEDURE IF EXISTS AltaEvento //
CREATE PROCEDURE AltaEvento(IN p_nombre VARCHAR(100), IN p_lugar VARCHAR(100), IN p_fecha DATE, OUT mensaje VARCHAR(100))
BEGIN
    IF EXISTS (SELECT 1 FROM Evento WHERE nombre = p_nombre) THEN
        SET mensaje = 'El evento ya existe.';
    ELSE
        INSERT INTO Evento (nombre, lugar, fecha) VALUES (p_nombre, p_lugar, p_fecha);
        SET mensaje = 'Evento creado.';
    END IF;
END //

DROP PROCEDURE IF EXISTS AltaEscuela //
CREATE PROCEDURE AltaEscuela(IN p_codEscuela VARCHAR(50), IN p_nombreEscuela VARCHAR(100), OUT mensaje VARCHAR(100))
BEGIN
    IF EXISTS (SELECT 1 FROM EscuelaProcedencia WHERE codEscuela = p_codEscuela) THEN
        SET mensaje = 'La escuela ya existe.';
    ELSE
        INSERT INTO EscuelaProcedencia (codEscuela, nombreEscuela) VALUES (p_codEscuela, p_nombreEscuela);
        SET mensaje = 'Escuela creada.';
    END IF;
END //

DROP PROCEDURE IF EXISTS AltaEquipo //
CREATE PROCEDURE AltaEquipo(
    IN p_nombreEquipo VARCHAR(50),
    IN p_idCategoria INT,
    IN p_codEscuela VARCHAR(50),
    IN p_nombreEvento VARCHAR(100),
    IN p_idAsistente INT,
    OUT mensaje VARCHAR(100),
    OUT p_idEquipoGenerado INT
)
BEGIN
    INSERT INTO Equipo (nombreEquipo, idCategoria_Categoria, codEscuela_EscuelaProcedencia, nombre_Evento, idAsistente)
    VALUES (p_nombreEquipo, p_idCategoria, p_codEscuela, p_nombreEvento, p_idAsistente);
    SET p_idEquipoGenerado = LAST_INSERT_ID();
    SET mensaje = 'Equipo registrado.';
END //

DROP PROCEDURE IF EXISTS AltaParticipante //
CREATE PROCEDURE AltaParticipante(
    IN p_numControl INT,
    IN p_nombre VARCHAR(50),
    IN p_apellidoPat VARCHAR(50),
    IN p_apellidoMat VARCHAR(50),
    IN p_edad INT,
    IN p_sexo ENUM('Hombre','Mujer'),
    IN p_idEquipo INT,
    OUT mensaje VARCHAR(100)
)
BEGIN
    INSERT INTO Participante (numControl, nombre, apellidoPat, apellidoMat, edad, sexo, idEquipo_Equipo)
    VALUES (p_numControl, p_nombre, p_apellidoPat, p_apellidoMat, p_edad, p_sexo, p_idEquipo);
    SET mensaje = 'Participante registrado.';
END //

-- =================================================================
-- 4. LISTADOS (Dropdowns y Tablas)
-- =================================================================
DROP PROCEDURE IF EXISTS ListarEscuelas //
CREATE PROCEDURE ListarEscuelas()
BEGIN
    SELECT codEscuela, nombreEscuela FROM EscuelaProcedencia ORDER BY nombreEscuela ASC;
END //

DROP PROCEDURE IF EXISTS ListarEventos //
CREATE PROCEDURE ListarEventos()
BEGIN
    SELECT nombre, fecha FROM Evento ORDER BY fecha DESC;
END //

DROP PROCEDURE IF EXISTS ListarCategorias //
CREATE PROCEDURE ListarCategorias()
BEGIN
    SELECT idCategoria, nombre FROM Categoria;
END //

-- =================================================================
-- 5. ASIGNACIÓN Y GESTIÓN DE COMPETENCIA
-- =================================================================
DROP PROCEDURE IF EXISTS AsignarJuezEquipo //
CREATE PROCEDURE AsignarJuezEquipo(IN p_idJuez INT, IN p_idEquipo INT, OUT mensaje VARCHAR(100))
BEGIN
    DECLARE CONTINUE HANDLER FOR 1062 SET mensaje = 'Ya asignado.';
    INSERT INTO Juez_Evaluacion_Equipo (idJuez, idEquipo) VALUES (p_idJuez, p_idEquipo);
    IF mensaje IS NULL THEN SET mensaje = 'Asignación correcta.'; END IF;
END //

DROP PROCEDURE IF EXISTS ObtenerEquiposPorJuez //
CREATE PROCEDURE ObtenerEquiposPorJuez(IN p_idJuez INT)
BEGIN
    SELECT e.idEquipo, e.nombreEquipo, ep.nombreEscuela, c.nombre as categoria,
    CASE WHEN EXISTS (SELECT 1 FROM EvaluacionDiseño ed WHERE ed.idEquipo = e.idEquipo AND ed.idJuez = p_idJuez) 
         THEN 'Evaluado' ELSE 'Pendiente' END as estado
    FROM Juez_Evaluacion_Equipo jee
    JOIN Equipo e ON jee.idEquipo = e.idEquipo
    JOIN EscuelaProcedencia ep ON e.codEscuela_EscuelaProcedencia = ep.codEscuela
    JOIN Categoria c ON e.idCategoria_Categoria = c.idCategoria
    WHERE jee.idJuez = p_idJuez;
END //

-- (NUEVO) Obtener información básica de un equipo (Para el encabezado de evaluación)
DROP PROCEDURE IF EXISTS ObtenerInfoEquipo //
CREATE PROCEDURE ObtenerInfoEquipo(IN p_idEquipo INT)
BEGIN
    SELECT e.nombreEquipo, c.nombre as categoria, ep.nombreEscuela
    FROM Equipo e
    JOIN Categoria c ON e.idCategoria_Categoria = c.idCategoria
    JOIN EscuelaProcedencia ep ON e.codEscuela_EscuelaProcedencia = ep.codEscuela
    WHERE e.idEquipo = p_idEquipo;
END //

-- =================================================================
-- 6. EVALUACIONES (GUARDAR Y LEER)
-- =================================================================
-- DISEÑO
DROP PROCEDURE IF EXISTS AltaEvaluacionDiseno //
CREATE PROCEDURE AltaEvaluacionDiseno(
    IN p_idJuez INT, IN p_idEquipo INT, IN p_reg INT, IN p_just INT, IN p_diag INT, IN p_vid INT, IN p_mod INT, OUT mensaje VARCHAR(100)
)
BEGIN
    INSERT INTO EvaluacionDiseño (idJuez, idEquipo, registroDeFechas, justificacionDeCambios, diagramasEImagenes, videoYAnimacion, disenoYModeladoEnAutodesk, ortografiaYRedaccion, presentacion, analisisDeElementos, ensambleDelPrototipo, elModeloEsAcorde, estaAcordeLaSimulacion, restriccionesDeMovimientos)
    VALUES (p_idJuez, p_idEquipo, p_reg, p_just, p_diag, p_vid, p_mod, 0,0,0,0,0,0,0)
    ON DUPLICATE KEY UPDATE registroDeFechas=p_reg, justificacionDeCambios=p_just, diagramasEImagenes=p_diag, videoYAnimacion=p_vid, disenoYModeladoEnAutodesk=p_mod;
    SET mensaje = 'Guardado.';
END //

DROP PROCEDURE IF EXISTS ObtenerEvaluacionDiseno //
CREATE PROCEDURE ObtenerEvaluacionDiseno(IN p_idJuez INT, IN p_idEquipo INT)
BEGIN
    SELECT * FROM EvaluacionDiseño WHERE idJuez = p_idJuez AND idEquipo = p_idEquipo;
END //

-- PROGRAMACIÓN
DROP PROCEDURE IF EXISTS AltaEvaluacionProgramacion //
CREATE PROCEDURE AltaEvaluacionProgramacion(
    IN p_idJuez INT, IN p_idEquipo INT, IN p_uso INT, IN p_comp INT, IN p_modul INT, IN p_auto INT, IN p_joy INT, OUT mensaje VARCHAR(100)
)
BEGIN
    INSERT INTO EvaluacionProgramacion (idJuez, idEquipo, usoDeFunciones, complejidadDelPrograma, creacionDeCodigoModular, vinculacionAutonomo, habilidadJoystick, softwareDeProgramacionRobotC, justificacionDeLasSecuencias, conocimientoDeLasEstructuras, rutinasDeDepuracion, documentacionAutonomo, declaracionSensores, vinculoJoystick, respuestaDispositivo, demo15Seg, noInconvenientes, cumpleObjetivoDriver, explicacionRutina)
    VALUES (p_idJuez, p_idEquipo, p_uso, p_comp, p_modul, p_auto, p_joy, 0,0,0,0,0,0,0,0,0,0,0,0)
    ON DUPLICATE KEY UPDATE usoDeFunciones=p_uso, complejidadDelPrograma=p_comp, creacionDeCodigoModular=p_modul, vinculacionAutonomo=p_auto, habilidadJoystick=p_joy;
    SET mensaje = 'Guardado.';
END //

DROP PROCEDURE IF EXISTS ObtenerEvaluacionProgramacion //
CREATE PROCEDURE ObtenerEvaluacionProgramacion(IN p_idJuez INT, IN p_idEquipo INT)
BEGIN
    SELECT * FROM EvaluacionProgramacion WHERE idJuez = p_idJuez AND idEquipo = p_idEquipo;
END //

-- CONSTRUCCIÓN
DROP PROCEDURE IF EXISTS AltaEvaluacionConstruccion //
CREATE PROCEDURE AltaEvaluacionConstruccion(
    IN p_idJuez INT, IN p_idEquipo INT, IN p_est INT, IN p_estab INT, IN p_cab INT, IN p_sens INT, IN p_engr INT, OUT mensaje VARCHAR(100)
)
BEGIN
    INSERT INTO EvaluacionConstruccion (idJuez, idEquipo, prototipoEstetico, estructurasEstables, cableadoAdecuado, usoSensores, trenEngranes, usoSistemasTransmision, calculoNeumatico, conocimientoAlcance, implementacionVex, usoUnProcesador, analisisEstructuras, relacionVelocidades, centroGravedad, sistemasTransmisionCalculos, potencia, torque, velocidad)
    VALUES (p_idJuez, p_idEquipo, p_est, p_estab, p_cab, p_sens, p_engr, 0,0,0,0,0,0,0,0,0,0,0,0)
    ON DUPLICATE KEY UPDATE prototipoEstetico=p_est, estructurasEstables=p_estab, cableadoAdecuado=p_cab, usoSensores=p_sens, trenEngranes=p_engr;
    SET mensaje = 'Guardado.';
END //

DROP PROCEDURE IF EXISTS ObtenerEvaluacionConstruccion //
CREATE PROCEDURE ObtenerEvaluacionConstruccion(IN p_idJuez INT, IN p_idEquipo INT)
BEGIN
    SELECT * FROM EvaluacionConstruccion WHERE idJuez = p_idJuez AND idEquipo = p_idEquipo;
END //

-- =================================================================
-- 7. DASHBOARD ADMIN
-- =================================================================
DROP PROCEDURE IF EXISTS ObtenerResumenAdmin //
CREATE PROCEDURE ObtenerResumenAdmin()
BEGIN
    SELECT (SELECT COUNT(*) FROM Equipo) as total_equipos, (SELECT COUNT(*) FROM Juez) as total_jueces, (SELECT COUNT(*) FROM Participante) as total_participantes;
END //

DROP PROCEDURE IF EXISTS ListarEquiposAdmin //
CREATE PROCEDURE ListarEquiposAdmin()
BEGIN
    SELECT e.idEquipo, e.nombreEquipo, c.nombre as categoria, ep.nombreEscuela, 'Activo' as estado
    FROM Equipo e JOIN Categoria c ON e.idCategoria_Categoria = c.idCategoria
    JOIN EscuelaProcedencia ep ON e.codEscuela_EscuelaProcedencia = ep.codEscuela LIMIT 10;
END //

DROP PROCEDURE IF EXISTS ListarUsuariosAdmin //
CREATE PROCEDURE ListarUsuariosAdmin()
BEGIN
    SELECT a.idAsistente, a.nombre, a.apellidoPat, a.email,
    CASE WHEN j.idJuez IS NOT NULL THEN 'Juez' WHEN e.idEntrenador IS NOT NULL THEN 'Entrenador' ELSE 'Asistente' END as rol_detectado
    FROM Asistente a LEFT JOIN Juez j ON a.idAsistente = j.idAsistente_Asistente
    LEFT JOIN Entrenador e ON a.idAsistente = e.idAsistente_Asistente;
END //

DROP PROCEDURE IF EXISTS AsignarRolJuezAdmin //
CREATE PROCEDURE AsignarRolJuezAdmin(IN p_id INT, IN p_esc VARCHAR(50), IN p_grado VARCHAR(50), OUT mensaje VARCHAR(100))
BEGIN
    INSERT INTO Juez (idJuez, gradoEstudios, idAsistente_Asistente, codEscuela_EscuelaProcedencia) VALUES (p_id, p_grado, p_id, p_esc)
    ON DUPLICATE KEY UPDATE gradoEstudios=p_grado, codEscuela_EscuelaProcedencia=p_esc;
    SET mensaje = 'Rol asignado.';
END //

DROP PROCEDURE IF EXISTS AsignarRolEntrenadorAdmin //
CREATE PROCEDURE AsignarRolEntrenadorAdmin(IN p_id INT, IN p_esc VARCHAR(50), OUT mensaje VARCHAR(100))
BEGIN
    INSERT INTO Entrenador (idEntrenador, idAsistente_Asistente, codEscuela_EscuelaProcedencia) VALUES (p_id, p_id, p_esc)
    ON DUPLICATE KEY UPDATE codEscuela_EscuelaProcedencia=p_esc;
    SET mensaje = 'Rol asignado.';
END //

DELIMITER ;