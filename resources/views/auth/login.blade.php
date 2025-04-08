<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - OCE</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://source.unsplash.com/random/1920x1080/?office,workspace') 
                        no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .auth-container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(3px);
        }
        
        .auth-header {
            padding: 25px;
            background: #2c3e50;
            color: white;
            text-align: center;
            border-bottom: 4px solid #3498db;
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 15px;
        }

        .logo-main {
            font-family: 'Arial', sans-serif;
            font-size: 2.5rem;
            font-weight: bold;
            color: #fff;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .engrane {
            font-size: 2.5rem;
            color: #f39c12;
            animation: girar 5s linear infinite;
            margin-right: 2px;
        }

        .logo-e {
            position: relative;
            display: inline-block;
        }

        .llave {
            position: absolute;
            font-size: 1rem;
            color: #f39c12;
            top: 32px;
            right: -5px;
            transform: rotate(5deg);
        }

        .logo-sub {
            font-size: 1.2rem;
            color: #f39c12;
            margin-top: 5px;
        }

        @keyframes girar {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .auth-body {
            padding: 30px;
        }
        
        .form-control {
            border-radius: 5px;
            padding: 12px 20px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }
        
        .btn-auth {
            border-radius: 5px;
            padding: 12px;
            font-weight: 600;
            background: #2c3e50;
            border: none;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s;
        }
        
        .btn-auth:hover {
            background: #34495e;
        }
        
        .auth-switch {
            text-align: center;
            margin-top: 20px;
            color: #555;
        }
        
        .auth-switch a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }
        
        #registerForm {
            display: none;
        }

        .text-danger {
            font-size: 0.875rem;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .alert-danger {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-container">
                    <div class="auth-header">
                        <div class="logo-container">
                            <div class="logo-main">
                                <i class="fas fa-cog engrane"></i>
                                <span>C</span>
                                <span class="logo-e">E
                                    <i class="fas fa-wrench llave"></i>
                                </span>
                            </div>
                            <div class="logo-sub">Ingeniería & Mantenimiento</div>
                            <div style="margin-top: 10px; font-size: 1rem;">Bienvenido</div>
                        </div>
                    </div>
                    <div class="auth-body">
                        <!-- Formulario de Login - Funcional con Laravel -->
                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf
                            
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i> Credenciales incorrectas
                                </div>
                            @endif

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="Correo">
                                <label for="email"><i class="fas fa-envelope me-2"></i>Correo</label>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Contraseña">
                                <label for="password"><i class="fas fa-key me-2"></i>Contraseña</label>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">Recordar sesión</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                            </div>
                            
                            <button type="submit" class="btn btn-auth text-white">
                                <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                            </button>
                            
                            <div class="auth-switch">
                                ¿Necesitas acceso? <a id="showRegister">Solicitar credenciales</a>
                            </div>
                        </form>
                        
                        <!-- Formulario de Registro (Frontend) -->
                        <form id="registerForm" method="POST" action="#">
                            @csrf
                            <div class="mb-3 text-center">
                                <p>Complete el formulario para solicitar acceso al panel administrativo.</p>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Nombre Completo" required>
                                <label for="fullName"><i class="fas fa-id-card me-2"></i>Nombre Completo</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="registerEmail" name="email" placeholder="Correo Corporativo" required>
                                <label for="registerEmail"><i class="fas fa-envelope me-2"></i>Correo Corporativo</label>
                            </div>
                            <button type="submit" class="btn btn-auth text-white">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                            </button>
                            
                            <div class="auth-switch">
                                ¿Ya tienes acceso? <a id="showLogin">Iniciar sesión</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Alternar formularios
        document.getElementById('showRegister')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        });
        
        document.getElementById('showLogin')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
        });

        // Manejo del formulario de registro (solo frontend)
        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            alert("Solicitud enviada. Un administrador revisará tu petición.");
            document.getElementById('registerForm').reset();
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
        });
    </script>
</body>
</html>