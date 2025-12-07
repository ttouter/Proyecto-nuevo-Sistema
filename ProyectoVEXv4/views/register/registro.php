<?php
// Incluimos el modelo para cargar la lista de escuelas
require_once '../../models/ModeloProcesos.php';
$listaEscuelas = ModeloProcesos::listarEscuelas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cuenta - VEX Robotics</title>
    <link rel="icon" type="image/x-icon" href="../../assets/img/fav-robot.ico">
    <link rel="stylesheet" href="../../assets/css/styles_registro_moderno.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .btn-back-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 20px;
            background-color: transparent; 
            border: 2px solid #2C2C54;
            color: #2C2C54;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            width: fit-content;
        }

        .btn-back-home:hover {
            background-color: #2C2C54;
            color: #ffffff;
            transform: translateX(-5px);
            box-shadow: 0 4px 10px rgba(44, 44, 84, 0.2);
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="split-screen">
        <div class="left-pane">
            <div class="overlay">
                <div class="brand-link">
                    <img src="../../assets/img/logo.png" alt="Logo VEX" class="logo">
                    <span>VEX Robotics</span>
                </div>
                <div class="welcome-content">
                    <h1>¡Bienvenido!</h1>
                    <p>Únete a la competencia de robótica más grande. Registra tu institución y gestiona tus equipos.</p>
                </div>
                <p class="copyright">&copy; 2025 VEX Robotics Organization</p>
            </div>
        </div>

        <div class="right-pane">
            <div class="form-container">
                
                <a href="../index/Index.html" class="btn-back-home">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio
                </a>

                <div class="form-header">
                    <h2>Crear una cuenta</h2>
                    <p>Completa tus datos para comenzar</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert error"><i class="fas fa-exclamation-triangle"></i> <span><?php echo htmlspecialchars($_GET['error']); ?></span></div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert success"><i class="fas fa-check-circle"></i> <span><?php echo htmlspecialchars($_GET['success']); ?></span></div>
                <?php endif; ?>

                <form action="../../controllers/control_registro.php" method="POST" class="register-form" autocomplete="off" id="formRegistro">
                    
                    <div class="form-row">
                        <div class="input-group">
                            <label>Nombre</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user"></i>
                                <input type="text" name="nombre" placeholder="Nombre" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]+" maxlength="30" autocomplete="off">
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Apellido Paterno</label>
                            <div class="input-wrapper">
                                <i class="far fa-user"></i>
                                <input type="text" name="ap_paterno" placeholder="Apellido Paterno" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]+" maxlength="30" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="input-group">
                            <label>Apellido Materno</label>
                            <div class="input-wrapper">
                                <i class="far fa-user"></i>
                                <input type="text" name="ap_materno" placeholder="Apellido Materno" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]+" maxlength="30" autocomplete="off">
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Sexo</label>
                            <div class="input-wrapper">
                                <i class="fas fa-venus-mars"></i>
                                <select name="sexo" required autocomplete="off">
                                    <option value="" disabled selected>Selecciona...</option>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Institución de Procedencia</label>
                        <div class="input-wrapper">
                            <i class="fas fa-university"></i>
                            <select name="codEscuela" required autocomplete="off">
                                <option value="" disabled selected>-- Selecciona tu Instituto --</option>
                                <?php if (!empty($listaEscuelas)): ?>
                                    <?php foreach ($listaEscuelas as $escuela): ?>
                                        <option value="<?php echo $escuela['codEscuela']; ?>">
                                            <?php echo htmlspecialchars($escuela['nombreEscuela']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No hay escuelas cargadas</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Correo Electrónico</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="ejemplo@correo.com" required autocomplete="off" value="">
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Contraseña</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="passwordInput" placeholder="Crea una contraseña" required autocomplete="new-password">
                            <i class="fas fa-eye toggle-password" id="togglePasswordBtn" style="left: auto; right: 15px; cursor: pointer;"></i>
                        </div>
                        
                        <div class="validation-box">
                            <p>La contraseña debe contener:</p>
                            <ul class="validation-list">
                                <li id="rule-length" class="validation-item"><i class="fas fa-circle"></i> Mínimo 8 caracteres</li>
                                <li id="rule-upper" class="validation-item"><i class="fas fa-circle"></i> Una letra Mayúscula</li>
                                <li id="rule-number" class="validation-item"><i class="fas fa-circle"></i> Un número</li>
                                <li id="rule-special" class="validation-item"><i class="fas fa-circle"></i> Un carácter especial (!@#$)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn-register" id="btnSubmit" disabled style="opacity: 0.6; cursor: not-allowed;">
                            Registrarme <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <p class="login-redirect">¿Ya tienes una cuenta? <a href="../login/login_unificado.php">Inicia sesión aquí</a></p>
                </form>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('passwordInput');
        const btnSubmit = document.getElementById('btnSubmit');
        const toggleBtn = document.getElementById('togglePasswordBtn');

        const ruleLength = document.getElementById('rule-length');
        const ruleUpper = document.getElementById('rule-upper');
        const ruleNumber = document.getElementById('rule-number');
        const ruleSpecial = document.getElementById('rule-special');

        passwordInput.addEventListener('input', function() {
            const val = passwordInput.value;
            let allValid = true;

            // Regla 1: Longitud
            if (val.length >= 8) setValid(ruleLength, true); 
            else { setValid(ruleLength, false); allValid = false; }

            // Regla 2: Mayúscula
            if (/[A-Z]/.test(val)) setValid(ruleUpper, true); 
            else { setValid(ruleUpper, false); allValid = false; }

            // Regla 3: Número
            if (/[0-9]/.test(val)) setValid(ruleNumber, true); 
            else { setValid(ruleNumber, false); allValid = false; }

            // Regla 4: Carácter especial
            if (/[!@#$%^&*(),.?":{}|<>]/.test(val)) setValid(ruleSpecial, true); 
            else { setValid(ruleSpecial, false); allValid = false; }

            // Activar botón si todo es válido
            if (allValid) {
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
                btnSubmit.style.cursor = 'pointer';
            } else {
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = '0.6';
                btnSubmit.style.cursor = 'not-allowed';
            }
        });

        function setValid(element, isValid) {
            const icon = element.querySelector('i');
            if (isValid) {
                element.classList.add('valid');
                icon.classList.remove('fa-circle');
                icon.classList.add('fa-check-circle');
            } else {
                element.classList.remove('valid');
                icon.classList.remove('fa-check-circle');
                icon.classList.add('fa-circle');
            }
        }

        toggleBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>