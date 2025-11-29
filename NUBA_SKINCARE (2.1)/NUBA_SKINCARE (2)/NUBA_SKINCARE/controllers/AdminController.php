<?php
require_once '../models/User.php';
require_once '../models/Product.php';
require_once '../models/Database.php';

class AdminController {
    private $db;
    private $user;
    private $product;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->product = new Product($this->db);
    }

    public function dashboard() {
        $this->showDashboard();
    }

    public function users() {
        $users = $this->getAllUsers();
        $this->showUsersManagement($users);
    }

    public function products() {
        $products = $this->getAllProducts();
        $categories = $this->product->getCategories();
        $this->showProductsManagement($products, $categories);
    }

    public function reports() {
        $stats = $this->getDashboardStats();
        $this->showReports($stats);
    }

    public function createProduct() {
        if ($_POST) {
            $this->handleCreateProduct();
        } else {
            $categories = $this->product->getCategories();
            $this->showCreateProductForm($categories);
        }
    }

    public function editProduct($id) {
        if ($_POST) {
            $this->handleEditProduct($id);
        } else {
            $product = $this->product->getById($id);
            if (!$product) {
                header('Location: /NUBA_SKINCARE/public/admin/products');
                exit;
            }
            $categories = $this->product->getCategories();
            $this->showEditProductForm($product, $categories);
        }
    }

    public function deleteProduct($id) {
        $this->handleDeleteProduct($id);
    }

    public function toggleUserStatus($id) {
        $this->handleToggleUserStatus($id);
    }

    private function getAllUsers() {
        $query = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAllProducts() {
        $query = "SELECT * FROM products ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getDashboardStats() {
        // Estadísticas de usuarios
        $userStatsQuery = "SELECT 
            COUNT(*) as total_users,
            SUM(role = 'client') as total_clients,
            SUM(role = 'employee') as total_employees,
            SUM(role = 'admin') as total_admins
            FROM users";
        $userStmt = $this->db->prepare($userStatsQuery);
        $userStmt->execute();
        $userStats = $userStmt->fetch(PDO::FETCH_ASSOC);

        // Estadísticas de productos
        $productStatsQuery = "SELECT 
            COUNT(*) as total_products,
            SUM(stock) as total_stock,
            SUM(stock = 0) as out_of_stock
            FROM products";
        $productStmt = $this->db->prepare($productStatsQuery);
        $productStmt->execute();
        $productStats = $productStmt->fetch(PDO::FETCH_ASSOC);

        return [
            'users' => $userStats,
            'products' => $productStats
        ];
    }

    private function handleCreateProduct() {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category = $_POST['category'] ?? '';
        $stock = $_POST['stock'] ?? 0;
        $image = $_POST['image'] ?? 'https://via.placeholder.com/300x200/9b3876/ffffff?text=NUBA';

        $query = "INSERT INTO products (name, description, price, category, stock, image) 
                  VALUES (:name, :description, :price, :category, :stock, :image)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":image", $image);

        if ($stmt->execute()) {
            $_SESSION['admin_message'] = [
                'type' => 'success',
                'text' => 'Producto creado exitosamente'
            ];
            header('Location: /NUBA_SKINCARE/public/admin/products');
            exit;
        } else {
            $_SESSION['admin_message'] = [
                'type' => 'error',
                'text' => 'Error al crear el producto'
            ];
            header('Location: /NUBA_SKINCARE/public/admin/create-product');
            exit;
        }
    }

    private function handleEditProduct($id) {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category = $_POST['category'] ?? '';
        $stock = $_POST['stock'] ?? 0;
        $status = $_POST['status'] ?? 'active';
        $image = $_POST['image'] ?? '';

        $query = "UPDATE products SET 
                  name = :name, 
                  description = :description, 
                  price = :price, 
                  category = :category, 
                  stock = :stock, 
                  status = :status,
                  image = :image
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            $_SESSION['admin_message'] = [
                'type' => 'success',
                'text' => 'Producto actualizado exitosamente'
            ];
        } else {
            $_SESSION['admin_message'] = [
                'type' => 'error',
                'text' => 'Error al actualizar el producto'
            ];
        }
        header('Location: /NUBA_SKINCARE/public/admin/products');
        exit;
    }

    private function handleDeleteProduct($id) {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            $_SESSION['admin_message'] = [
                'type' => 'success',
                'text' => 'Producto eliminado exitosamente'
            ];
        } else {
            $_SESSION['admin_message'] = [
                'type' => 'error',
                'text' => 'Error al eliminar el producto'
            ];
        }
        header('Location: /NUBA_SKINCARE/public/admin/products');
        exit;
    }

    private function handleToggleUserStatus($id) {
        // Primero obtenemos el estado actual
        $query = "SELECT status FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['admin_message'] = [
                'type' => 'error',
                'text' => 'Usuario no encontrado'
            ];
            header('Location: /NUBA_SKINCARE/public/admin/users');
            exit;
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';

        $query = "UPDATE users SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":status", $newStatus);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            $_SESSION['admin_message'] = [
                'type' => 'success',
                'text' => 'Estado de usuario actualizado'
            ];
        } else {
            $_SESSION['admin_message'] = [
                'type' => 'error',
                'text' => 'Error al actualizar el usuario'
            ];
        }
        header('Location: /NUBA_SKINCARE/public/admin/users');
        exit;
    }

    private function showDashboard() {
        $stats = $this->getDashboardStats();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Panel Admin - NUBA Skincare</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .dashboard-card { 
                    background: white; 
                    border-radius: 15px; 
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
                    transition: transform 0.3s; 
                    border: none;
                }
                .dashboard-card:hover { 
                    transform: translateY(-5px); 
                }
                .welcome-section { 
                    background: linear-gradient(135deg, #d6108dff, #c50cf3ff); 
                    color: white; 
                    border-radius: 15px; 
                }
                .stat-card { 
                    text-align: center; 
                    padding: 30px 20px;
                    color: white;
                    border-radius: 15px;
                }
                .stat-number { 
                    font-size: 2.5rem; 
                    font-weight: bold; 
                    margin-bottom: 10px;
                }
                .bg-users { 
                    background: linear-gradient(135deg, #3557f0ff, #7a23d1ff); 
                }
                .bg-products { 
                    background: linear-gradient(135deg, #da33ecff, #f32f49ff); 
                }
                .bg-clients { 
                    background: linear-gradient(135deg, #297dc7ff, #00f2fe); 
                }
                .bg-employees { 
                    background: linear-gradient(135deg, #1ef365ff, #24e7c3ff); 
                }
                .nav-admin {
                    background: linear-gradient(135deg, #e90f99ff, #ce247fff) !important;
                }
            </style>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark nav-admin">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Admin
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/users">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/products">
                            <i class="fas fa-boxes"></i> Productos
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/reports">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                        <span class="navbar-text me-3">
                            <i class="fas fa-user-shield"></i> <?php echo $_SESSION['user_name']; ?>
                        </span>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/auth/logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <!-- Mensajes -->
                <?php if(isset($_SESSION['admin_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['admin_message']['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['admin_message']['text']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['admin_message']); ?>
                <?php endif; ?>

                <div class="welcome-section p-4 mb-4">
                    <h1 class="display-5 fw-bold">Panel de Administración </h1>
                    <p class="lead mb-0">Bienvenido, <?php echo $_SESSION['user_name']; ?></p>
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card bg-users">
                            <i class="fas fa-users fa-2x mb-3"></i>
                            <div class="stat-number"><?php echo $stats['users']['total_users']; ?></div>
                            <div>Total Usuarios</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card bg-clients">
                            <i class="fas fa-user-friends fa-2x mb-3"></i>
                            <div class="stat-number"><?php echo $stats['users']['total_clients']; ?></div>
                            <div>Clientes</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card bg-employees">
                            <i class="fas fa-user-tie fa-2x mb-3"></i>
                            <div class="stat-number"><?php echo $stats['users']['total_employees']; ?></div>
                            <div>Empleados</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card bg-products">
                            <i class="fas fa-boxes fa-2x mb-3"></i>
                            <div class="stat-number"><?php echo $stats['products']['total_products']; ?></div>
                            <div>Productos</div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card dashboard-card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                <h5>Gestión de Usuarios</h5>
                                <p class="text-muted">Administra clientes y empleados</p>
                                <a href="/NUBA_SKINCARE/public/admin/users" class="btn btn-primary">
                                    <i class="fas fa-cog"></i> Gestionar Usuarios
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card dashboard-card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-boxes fa-3x text-success mb-3"></i>
                                <h5>Gestión de Productos</h5>
                                <p class="text-muted">Administra inventario y categorías</p>
                                <a href="/NUBA_SKINCARE/public/admin/products" class="btn btn-success">
                                    <i class="fas fa-cog"></i> Gestionar Productos
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card dashboard-card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                                <h5>Reportes y Estadísticas</h5>
                                <p class="text-muted">Visualiza métricas del negocio</p>
                                <a href="/NUBA_SKINCARE/public/admin/reports" class="btn btn-warning">
                                    <i class="fas fa-chart-line"></i> Ver Reportes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="dashboard-card p-4">
                            <h5><i class="fas fa-exclamation-triangle text-warning"></i> Productos con Stock Bajo</h5>
                            <p class="text-muted"><?php echo $stats['products']['out_of_stock']; ?> productos agotados</p>
                            <a href="/NUBA_SKINCARE/public/admin/products" class="btn btn-outline-warning btn-sm">
                                Revisar Inventario
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dashboard-card p-4">
                            <h5><i class="fas fa-info-circle text-info"></i> Información del Sistema</h5>
                            <p class="text-muted">Stock total: <?php echo $stats['products']['total_stock']; ?> unidades</p>
                            <p class="text-muted">Administradores: <?php echo $stats['users']['total_admins']; ?> usuarios</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-dark text-light mt-5 py-4">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-spa"></i> NUBA Skincare</h5>
                            <p>Panel de Administración</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p>&copy; 2025 NUBA Skincare. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </div>
            </footer>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }

    private function showUsersManagement($users) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gestión de Usuarios - NUBA Admin</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .nav-admin {
                    background: linear-gradient(135deg, #9b3876, #c54b8c) !important;
                }
            </style>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark nav-admin">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Admin
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/users">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/products">
                            <i class="fas fa-boxes"></i> Productos
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/reports">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                        <span class="navbar-text me-3">
                            <i class="fas fa-user-shield"></i> <?php echo $_SESSION['user_name']; ?>
                        </span>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/auth/logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <!-- Mensajes -->
                <?php if(isset($_SESSION['admin_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['admin_message']['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['admin_message']['text']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['admin_message']); ?>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
                    <a href="/NUBA_SKINCARE/public/admin/dashboard" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Teléfono</th>
                                        <th>Estado</th>
                                        <th>Fecha Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($users)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="fas fa-users fa-2x mb-2"></i>
                                                <p>No hay usuarios registrados</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'employee' ? 'warning' : 'primary'); ?>">
                                                        <?php echo ucfirst($user['role']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($user['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <a href="/NUBA_SKINCARE/public/admin/toggle-user/<?php echo $user['id']; ?>" 
                                                       class="btn btn-sm btn-<?php echo $user['status'] === 'active' ? 'warning' : 'success'; ?>"
                                                       onclick="return confirm('¿Estás seguro de cambiar el estado de este usuario?')">
                                                        <i class="fas fa-<?php echo $user['status'] === 'active' ? 'pause' : 'play'; ?>"></i>
                                                        <?php echo $user['status'] === 'active' ? 'Desactivar' : 'Activar'; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }

    private function showProductsManagement($products, $categories) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gestión de Productos - NUBA Admin</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .nav-admin {
                    background: linear-gradient(135deg, #9b3876, #c54b8c) !important;
                }
                .product-img {
                    width: 50px; 
                    height: 50px; 
                    object-fit: cover; 
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark nav-admin">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Admin
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/users">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/products">
                            <i class="fas fa-boxes"></i> Productos
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/reports">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                        <span class="navbar-text me-3">
                            <i class="fas fa-user-shield"></i> <?php echo $_SESSION['user_name']; ?>
                        </span>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/auth/logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <!-- Mensajes -->
                <?php if(isset($_SESSION['admin_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['admin_message']['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['admin_message']['text']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['admin_message']); ?>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-boxes"></i> Gestión de Productos</h1>
                    <div>
                        <a href="/NUBA_SKINCARE/public/admin/dashboard" class="btn btn-outline-primary me-2">
                            <i class="fas fa-arrow-left"></i> Dashboard
                        </a>
                        <a href="/NUBA_SKINCARE/public/admin/create-product" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nuevo Producto
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if(empty($products)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <h4>No hay productos registrados</h4>
                                <p class="mb-3">Comienza agregando tu primer producto</p>
                                <a href="/NUBA_SKINCARE/public/admin/create-product" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Crear Primer Producto
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Imagen</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Precio</th>
                                            <th>Categoría</th>
                                            <th>Stock</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($products as $product): ?>
                                            <tr>
                                                <td><?php echo $product['id']; ?></td>
                                                <td>
                                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                         class="product-img"
                                                         onerror="this.src='https://via.placeholder.com/50x50/9b3876/ffffff?text=NUBA'">
                                                </td>
                                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></td>
                                                <td>Bs. <?php echo number_format($product['price'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo ucfirst($product['category']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $product['stock'] > 10 ? 'success' : ($product['stock'] > 0 ? 'warning' : 'danger'); ?>">
                                                        <?php echo $product['stock']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $product['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($product['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="/NUBA_SKINCARE/public/admin/edit-product/<?php echo $product['id']; ?>" 
                                                       class="btn btn-sm btn-primary mb-1">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <a href="/NUBA_SKINCARE/public/admin/delete-product/<?php echo $product['id']; ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }

    private function showReports($stats) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reportes - NUBA Admin</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .nav-admin {
                    background: linear-gradient(135deg, #9b3876, #c54b8c) !important;
                }
                .stat-card {
                    border: none;
                    border-radius: 15px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                }
            </style>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark nav-admin">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Admin
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/users">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/products">
                            <i class="fas fa-boxes"></i> Productos
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/reports">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                        <span class="navbar-text me-3">
                            <i class="fas fa-user-shield"></i> <?php echo $_SESSION['user_name']; ?>
                        </span>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/auth/logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-chart-bar"></i> Reportes y Estadísticas</h1>
                    <a href="/NUBA_SKINCARE/public/admin/dashboard" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card stat-card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-users"></i> Estadísticas de Usuarios</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Total de Usuarios
                                        <span class="badge bg-primary rounded-pill"><?php echo $stats['users']['total_users']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Clientes
                                        <span class="badge bg-success rounded-pill"><?php echo $stats['users']['total_clients']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Empleados
                                        <span class="badge bg-warning rounded-pill"><?php echo $stats['users']['total_employees']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Administradores
                                        <span class="badge bg-danger rounded-pill"><?php echo $stats['users']['total_admins']; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card stat-card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-boxes"></i> Estadísticas de Productos</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Total de Productos
                                        <span class="badge bg-primary rounded-pill"><?php echo $stats['products']['total_products']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Stock Total
                                        <span class="badge bg-success rounded-pill"><?php echo $stats['products']['total_stock']; ?> unidades</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Productos Agotados
                                        <span class="badge bg-danger rounded-pill"><?php echo $stats['products']['out_of_stock']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Productos Disponibles
                                        <span class="badge bg-info rounded-pill"><?php echo $stats['products']['total_products'] - $stats['products']['out_of_stock']; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos Placeholder -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card stat-card">
                            <div class="card-header bg-warning text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Distribución de Usuarios</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="bg-light p-5 rounded">
                                    <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Gráfico de distribución de usuarios por roles</p>
                                    <small class="text-muted">(Integración con librerías de gráficos próximamente)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card stat-card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Tendencia de Inventario</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="bg-light p-5 rounded">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Gráfico de tendencias de stock y ventas</p>
                                    <small class="text-muted">(Integración con librerías de gráficos próximamente)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }

    private function showCreateProductForm($categories) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Nuevo Producto - NUBA Admin</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .nav-admin {
                    background: linear-gradient(135deg, #9b3876, #c54b8c) !important;
                }
            </style>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark nav-admin">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Admin
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/products">
                            <i class="fas fa-boxes"></i> Productos
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-plus"></i> Crear Nuevo Producto</h1>
                    <a href="/NUBA_SKINCARE/public/admin/products" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Volver a Productos
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nombre del Producto *</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Descripción *</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Precio (Bs.) *</label>
                                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Categoría *</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Seleccionar categoría</option>
                                            <?php foreach($categories as $category): ?>
                                                <option value="<?php echo $category; ?>"><?php echo ucfirst($category); ?></option>
                                            <?php endforeach; ?>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Stock *</label>
                                        <input type="number" class="form-control" id="stock" name="stock" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image" class="form-label">URL de la Imagen</label>
                                        <input type="url" class="form-control" id="image" name="image" 
                                               placeholder="https://ejemplo.com/imagen.jpg">
                                        <div class="form-text">Dejar vacío para usar imagen por defecto</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Crear Producto
                                </button>
                                <a href="/NUBA_SKINCARE/public/admin/products" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }

    private function showEditProductForm($product, $categories) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Editar Producto - NUBA Admin</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .nav-admin {
                    background: linear-gradient(135deg, #9b3876, #c54b8c) !important;
                }
            </style>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark nav-admin">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Admin
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/admin/products">
                            <i class="fas fa-boxes"></i> Productos
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-edit"></i> Editar Producto</h1>
                    <a href="/NUBA_SKINCARE/public/admin/products" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Volver a Productos
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nombre del Producto *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Descripción *</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Precio (Bs.) *</label>
                                        <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                               value="<?php echo $product['price']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Categoría *</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Seleccionar categoría</option>
                                            <?php foreach($categories as $category): ?>
                                                <option value="<?php echo $category; ?>" <?php echo $product['category'] === $category ? 'selected' : ''; ?>>
                                                    <?php echo ucfirst($category); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Stock *</label>
                                        <input type="number" class="form-control" id="stock" name="stock" 
                                               value="<?php echo $product['stock']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Estado</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Activo</option>
                                            <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image" class="form-label">URL de la Imagen</label>
                                        <input type="url" class="form-control" id="image" name="image" 
                                               value="<?php echo htmlspecialchars($product['image']); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar Producto
                                </button>
                                <a href="/NUBA_SKINCARE/public/admin/products" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }
}
?>