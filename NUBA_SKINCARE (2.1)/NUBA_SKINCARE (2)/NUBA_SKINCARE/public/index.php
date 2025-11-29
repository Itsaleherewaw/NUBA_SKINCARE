<?php

session_start();


require_once '../config/database.php';
require_once '../models/Database.php';

// Autoload para controladores
spl_autoload_register(function ($class_name) {
    $file = '../controllers/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/NUBA_SKINCARE/public';
$request_uri = str_replace($base_path, '', $request_uri);
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');
$segments = $path ? explode('/', $path) : [];

$controller = $segments[0] ?? 'home';
$action = $segments[1] ?? 'index';
$id = $segments[2] ?? null;

// Mapeo de controladores ACTUALIZADO
$controllers = [
    '' => 'HomeController',
    'home' => 'HomeController',
    'auth' => 'AuthController',
    'admin' => 'AdminController',
    'employee' => 'EmployeeController',
    'client' => 'ClientController',
    'products' => 'ProductController',
    'cart' => 'CartController',
    'chat' => 'ChatController'
];



// Manejo de rutas para categorías de productos
if ($controller === 'products' && $action === 'category' && $id) {
    $controller_class = 'ProductController';
    if (class_exists($controller_class)) {
        $instance = new $controller_class();
        $instance->category($id);
        exit;
    }
}

// Manejo de rutas para acciones del carrito
if ($controller === 'cart') {
    $controller_class = 'CartController';
    if (class_exists($controller_class)) {
        $instance = new $controller_class();
        
        // Mapeo de acciones específicas del carrito
        $cart_actions = [
            'add' => 'add',
            'update' => 'update',
            'remove' => 'remove',
            'checkout' => 'checkout'
        ];
        
        if (isset($cart_actions[$action])) {
            $method = $cart_actions[$action];
            if ($id) {
                $instance->$method($id);
            } else {
                $instance->$method();
            }
            exit;
        } elseif ($action === 'index' || empty($action)) {
            $instance->index();
            exit;
        }
    }
}

if ($controller === 'admin') {
    $controller_class = 'AdminController';
    if (class_exists($controller_class)) {
        $instance = new $controller_class();

        $admin_actions = [
            'users' => 'users',
            'products' => 'products',
            'reports' => 'reports',
            'create-product' => 'createProduct',
            'edit-product' => 'editProduct',
            'delete-product' => 'deleteProduct',
            'toggle-user' => 'toggleUserStatus'
        ];
        
        if (isset($admin_actions[$action])) {
            $method = $admin_actions[$action];
            if ($id) {
                $instance->$method($id);
            } else {
                $instance->$method();
            }
            exit;
        } elseif ($action === 'dashboard' || empty($action)) {
            $instance->dashboard();
            exit;
        }
    }
}


if ($controller === 'employee') {
    $controller_class = 'EmployeeController';
    if (class_exists($controller_class)) {
        $instance = new $controller_class();
        
        $employee_actions = [
            'orders' => 'orders',
            'order' => 'orderDetail',
            'update-order-status' => 'updateOrderStatus',
            'customer-support' => 'customerSupport',
            'customers' => 'getCustomers'
        ];
        
        if (isset($employee_actions[$action])) {
            $method = $employee_actions[$action];
            if ($id) {
                $instance->$method($id);
            } else {
                $instance->$method();
            }
            exit;
        } elseif ($action === 'dashboard' || empty($action)) {
            $instance->dashboard();
            exit;
        }
    }
}

// ==================== MANEJO DE RUTAS PARA CHAT ====================

if ($controller === 'chat') {
    $controller_class = 'ChatController';
    if (class_exists($controller_class)) {
        $instance = new $controller_class();
        
        if ($action === 'send') {
            $instance->send();
            exit;
        }
    }
}

// ==================== MANEJO DE RUTAS PRINCIPALES ====================

if (isset($controllers[$controller])) {
    $controller_class = $controllers[$controller];
    if (class_exists($controller_class)) {
        $instance = new $controller_class();
        if (method_exists($instance, $action)) {
            if ($id) {
                $instance->$action($id);
            } else {
                $instance->$action();
            }
        } else {
            showError("Método <strong>$action</strong> no existe en <strong>$controller_class</strong>");
        }
    } else {
        showError("Controlador <strong>$controller_class</strong> no existe");
    }
} else {
    showError("Página no encontrada: <strong>$controller</strong>");
}

function showError($message) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - NUBA Skincare</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            .error-container {
                background: linear-gradient(135deg, #f8e8f3, #e0c8dc);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .error-card {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(155,56,118,0.2);
                padding: 40px;
                text-align: center;
                max-width: 500px;
            }
            .error-icon {
                font-size: 4rem;
                color: #9b3876;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-card">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 class="text-danger">Error 404</h1>
                <p class="lead"><?php echo $message; ?></p>
                <div class="mt-4">
                    <a href="/NUBA_SKINCARE/public/" class="btn btn-primary me-2">
                        <i class="fas fa-home"></i> Volver al Inicio
                    </a>
                    <a href="/NUBA_SKINCARE/public/products" class="btn btn-outline-primary">
                        <i class="fas fa-store"></i> Ver Productos
                    </a>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        ¿Necesitas ayuda? Usa nuestro <strong>chatbot</strong> en la esquina inferior derecha.
                    </small>
                </div>
            </div>
        </div>

        <!-- Chatbot -->
        <script src="/NUBA_SKINCARE/public/assets/js/chatbot.js"></script>
        <link rel="stylesheet" href="/NUBA_SKINCARE/public/assets/css/chatbot.css">
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit;
}
?>