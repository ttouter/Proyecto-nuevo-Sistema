-- 1. crear esquema y usarlo
create schema BaseDatosVex;

use BaseDatosVex;

-- Creacion de tablas independientes
create table Evento(
    nombre varchar(100) primary key,
    lugar varchar(100),
    fecha date
);

create table EscuelaProcedencia(
    codEscuela varchar(50) primary key,
    nombreEscuela varchar(100)
);

create table Categoria(
    idCategoria int primary key,
    nombre enum ('Primaria','Secundaria','Preparatoria','Universidad'),
    rangoMin int,
    rangoMax int
);

-- Se actualizo la tabla para que contenga contraseña y correo electronico para el login
create table Asistente(
    idAsistente int auto_increment primary key,
    nombre varchar(50),
    apellidoPat varchar(50),
    apellidoMat varchar(50),
    sexo enum('Hombre', 'Mujer'),
    email varchar(100) unique,
    password varchar(255)
);

-- Tabla equipo (depende de categoria, escuela, evento, asistente)
create table Equipo(
    idEquipo int auto_increment primary key,
    nombreEquipo varchar(50),
    idCategoria_Categoria int,
    codEscuela_EscuelaProcedencia varchar(50),
    nombre_Evento varchar(100),
    idAsistente int,
    -- LLaves Foraneas
    foreign key (idCategoria_Categoria) references Categoria(idCategoria),
    foreign key (codEscuela_EscuelaProcedencia) references EscuelaProcedencia(codEscuela),
    foreign key (nombre_Evento) references Evento(nombre),
    foreign key (idAsistente) references Asistente(idAsistente)
);

-- Tabla participante (depende de equipo)
create table Participante(
    numControl int primary key,
    nombre varchar(50),
    apellidoPat varchar(50),
    apellidoMat varchar(50),
    edad int,
    sexo enum('Hombre', 'Mujer'),
    idEquipo_Equipo int,
    -- Llave Foranea
    foreign key (idEquipo_Equipo) references Equipo(idEquipo)
);

-- Tablas de roles (dependen de asistente y escuela)
create table Entrenador(
    idEntrenador int primary key,
    idAsistente_Asistente int,
    codEscuela_EscuelaProcedencia varchar(50), -- Columna añadida
    -- Llave Foranea
    foreign key (idAsistente_Asistente) references Asistente(idAsistente),
    foreign key (codEscuela_EscuelaProcedencia) references EscuelaProcedencia(codEscuela)
);

create table Juez(
    idJuez int primary key,
    gradoEstudios varchar(50),
    idAsistente_Asistente int,
    codEscuela_EscuelaProcedencia varchar(50),
    -- Llave Foranea
    foreign key (idAsistente_Asistente) references Asistente(idAsistente),
    foreign key (codEscuela_EscuelaProcedencia) references EscuelaProcedencia(codEscuela)
);

-- Tabla de asignación juez-equipo (muchos-a-muchos) #pense que era 1 a 1
create table Juez_Evaluacion_Equipo (
    idAsignacion int auto_increment primary key,
    idJuez int,
    idEquipo int,
    -- Llave Foranea
    foreign key (idJuez) references Juez(idJuez),
    foreign key (idEquipo) references Equipo(idEquipo),
    
    -- Un juez solo puede ser asignado una vez al mismo equipo!!!
    unique key (idJuez, idEquipo) 
);

-- EVALUACION
-- Tablas de evaluación (actualizadas a rubros int)
-- evaluación diseño (12 rubros)
create table EvaluacionDiseño (
    idJuez int,
    idEquipo int,
    
    -- Bitácora (5 rubros)
    registroDeFechas int,
    justificacionDeCambios int,
    diagramasEImagenes int,
    ortografiaYRedaccion int,
    presentacion int,
    
    -- Medio Digital (7 rubros)
    videoYAnimacion int,
    disenoYModeladoEnAutodesk int,
    analisisDeElementos int,
    ensambleDelPrototipo int,
    elModeloEsAcorde int,
    estaAcordeLaSimulacion int,
    restriccionesDeMovimientos int,
    -- Llave Foranea
    foreign key (idJuez) references Juez(idJuez),
    foreign key (idEquipo) references Equipo(idEquipo)
);

-- Evaluación programación (18 rubros)
create table if not exists EvaluacionProgramacion (
    idJuez int,
    idEquipo int,
    
    -- Inspección General (7 rubros)
    softwareDeProgramacionRobotC int,
    usoDeFunciones int,
    complejidadDelPrograma int,
    justificacionDeLasSecuencias int,
    conocimientoDeLasEstructuras int,
    rutinasDeDepuracion int,
    creacionDeCodigoModular int,
    
    -- Sistema Autónomo (3 rubros)
    documentacionAutonomo int,
    vinculacionAutonomo int,
    declaracionSensores int,
    
    -- Sistema Manipulado (4 rubros)
    vinculoJoystick int,
    habilidadJoystick int,
    respuestaDispositivo int,
    documentacionDriver int,
    
    -- Demostración (4 rubros)
    demo15Seg int,
    noInconvenientes int,
    cumpleObjetivoDriver int,
    explicacionRutina int,
    -- Llave Foranea
    foreign key (idJuez) references Juez(idJuez),
    foreign key (idEquipo) references Equipo(idEquipo)
);

-- Evaluación construcción (17 rubros)
create table if not exists EvaluacionConstruccion (
    idJuez int,
    idEquipo int,
    
    -- Inspección General (9 rubros)
    prototipoEstetico int,
    estructurasEstables int,
    usoSistemasTransmision int,
    usoSensores int,
    cableadoAdecuado int,
    calculoNeumatico int,
    conocimientoAlcance int,
    implementacionVex int,
    usoUnProcesador int,
    
    -- Cálculos (8 rubros)
    analisisEstructuras int,
    relacionVelocidades int,
    trenEngranes int,
    centroGravedad int,
    sistemasTransmisionCalculos int,
    potencia int,
    torque int,
    velocidad int,
    -- Llave Foranea
    foreign key (idJuez) references Juez(idJuez),
    foreign key (idEquipo) references Equipo(idEquipo)
);


insert into Categoria (idCategoria, nombre, rangoMin, rangoMax) values (1, 'Primaria', 7, 13),
																	   (2, 'Secundaria', 14, 16),
                                                                       (3, 'Preparatoria', 17, 18),
																		(4, 'Universidad', 19, 99);
               
               
INSERT INTO EscuelaProcedencia (codEscuela, nombreEscuela) VALUES
('UNAM', 'Universidad Nacional Autónoma de México'),
('IPN', 'Instituto Politécnico Nacional'),
('TEC_MTY', 'Tecnológico de Monterrey (ITESM)'),
('UDLAP', 'Universidad de las Américas Puebla'),
('ITAM', 'Instituto Tecnológico Autónomo de México'),
('UDG', 'Universidad de Guadalajara'),
('UANL', 'Universidad Autónoma de Nuevo León'),
('IPN_ESCOM', 'IPN - Escuela Superior de Cómputo (ESCOM)'),
('UVM', 'Universidad del Valle de México'),
('UP', 'Universidad Panamericana'),
('BUAP', 'Benemérita Universidad Autónoma de Puebla'),
('UASLP', 'Universidad Autónoma de San Luis Potosí'),
('UPN', 'Universidad Pedagógica Nacional'),
('ITCM', 'Instituto Tecnológico de Ciudad Madero'),
('UTT', 'Universidad Tecnológica de Tamaulipas');

                                                                        
