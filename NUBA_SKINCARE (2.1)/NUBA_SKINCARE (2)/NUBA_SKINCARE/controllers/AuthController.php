<?php
require_once '../models/User.php';
require_once '../models/Database.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function login() {
        if($_POST) {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->user->login($email, $password);
            
            if($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                switch($user['role']) {
                    case 'admin':
                        header('Location: /NUBA_SKINCARE/public/admin/dashboard');
                        break;
                    case 'employee':
                        header('Location: /NUBA_SKINCARE/public/employee/dashboard');
                        break;
                    case 'client':
                        header('Location: /NUBA_SKINCARE/public/client/dashboard');
                        break;
                    default:
                        header('Location: /NUBA_SKINCARE/public/');
                }
                exit;
            } else {
                $error = "Credenciales incorrectas";
                $this->loadView('auth/login', ['error' => $error]);
            }
        } else {
            $this->loadView('auth/login');
        }
    }

    public function register() {
        if($_POST) {
            $this->user->first_name = $_POST['first_name'];
            $this->user->last_name = $_POST['last_name'];
            $this->user->email = $_POST['email'];
            $this->user->password = $_POST['password'];
            $this->user->phone = $_POST['phone'];
            $this->user->role = 'client';

            if($this->user->create()) {
                header('Location: /NUBA_SKINCARE/public/auth/login');
                exit;
            } else {
                $error = "Error al crear la cuenta";
                $this->loadView('auth/register', ['error' => $error]);
            }
        } else {
            $this->loadView('auth/register');
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /NUBA_SKINCARE/public/');
        exit;
    }

    private function loadView($view, $data = []) {
        extract($data);
        $view_path = dirname(__DIR__) . '/views/' . $view . '.php';
        if (file_exists($view_path)) {
            require_once $view_path;
        } else {
            echo "<h2>Vista no encontrada: $view</h2>";
            echo "<p>Creando vista temporal...</p>";
            if ($view == 'auth/login') {
                $this->showTempLogin($data);
            } elseif ($view == 'auth/register') {
                $this->showTempRegister($data);
            }
        }
    }

    private function showTempLogin($data) {
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>Login - NUBA</title></head>
        <body style="font-family: Arial; margin: 50px;">
            <h2>Iniciar Sesión - NUBA</h2>
            <?php if(isset($data['error'])) echo "<p style='color:red;'>{$data['error']}</p>"; ?>
            <form method="POST" style="max-width: 400px;">
                <input type="email" name="email" placeholder="Email" required style="width:100%; padding:10px; margin:5px 0;"><br>
                <input type="password" name="password" placeholder="Password" required style="width:100%; padding:10px; margin:5px 0;"><br>
                <button type="submit" style="background:#9b3876; color:white; padding:10px 20px; border:none; cursor:pointer;">Entrar</button>
            </form>
            <p><a href="/NUBA_SKINCARE/public/">← Volver al inicio</a></p>
        </body>
        </html>
        <?php
    }

    private function showTempRegister($data) {
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>Registro - NUBA</title></head>
        <body style="font-family: Arial; margin: 50px;">
            <h2>Registrarse - NUBA</h2>
            <?php if(isset($data['error'])) echo "<p style='color:red;'>{$data['error']}</p>"; ?>
            <form method="POST" style="max-width: 400px;">
                <input type="text" name="first_name" placeholder="Nombre" required style="width:100%; padding:10px; margin:5px 0;"><br>
                <input type="text" name="last_name" placeholder="Apellido" required style="width:100%; padding:10px; margin:5px 0;"><br>
                <input type="email" name="email" placeholder="Email" required style="width:100%; padding:10px; margin:5px 0;"><br>
                <input type="password" name="password" placeholder="Password" required style="width:100%; padding:10px; margin:5px 0;"><br>
                <input type="tel" name="phone" placeholder="Teléfono" style="width:100%; padding:10px; margin:5px 0;"><br>
                <button type="submit" style="background:#9b3876; color:white; padding:10px 20px; border:none; cursor:pointer;">Registrarse</button>
            </form>
            <p><a href="/NUBA_SKINCARE/public/auth/login">← Ya tengo cuenta</a></p>
        </body>
        </html>
        <?php
    }
}
?>