<?php
require_once '../models/Order.php';
require_once '../models/User.php';
require_once '../models/Database.php';

class EmployeeController {
    private $db;
    private $order;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->order = new Order($this->db);
        $this->user = new User($this->db);
    }

    public function dashboard() {
        $this->showDashboard();
    }

    public function orders() {
        $orders = $this->order->getAllOrders();
        $this->showOrdersManagement($orders);
    }

    public function orderDetail($id) {
        $order = $this->order->getOrderById($id);
        $orderItems = $this->order->getOrderItems($id);
        $this->showOrderDetail($order, $orderItems);
    }

    public function updateOrderStatus() {
        if ($_POST) {
            $orderId = $_POST['order_id'];
            $status = $_POST['status'];
            
            if ($this->order->updateStatus($orderId, $status)) {
                $_SESSION['employee_message'] = [
                    'type' => 'success',
                    'text' => 'Estado del pedido actualizado correctamente'
                ];
            } else {
                $_SESSION['employee_message'] = [
                    'type' => 'error',
                    'text' => 'Error al actualizar el pedido'
                ];
            }
            
            header('Location: /NUBA_SKINCARE/public/employee/orders');
            exit;
        }
    }

    public function customerSupport() {
        $this->showCustomerSupport();
    }

    public function getCustomers() {
        $customers = $this->user->getAllClients();
        $this->showCustomersList($customers);
    }

    private function showDashboard() {
        $stats = $this->getEmployeeStats();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Panel Empleado - NUBA Skincare</title>
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
                    background: linear-gradient(135deg, #e20f95ff, #c54b8c);
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
                .bg-orders { background: linear-gradient(135deg, #3f60f3ff, #7e28d4ff); }
                .bg-pending { background: linear-gradient(135deg, #d236e4ff, #f5576c); }
                .bg-customers { background: linear-gradient(135deg, #4facfe, #00f2fe); }
                .bg-completed { background: linear-gradient(135deg, #1de961ff, #38f9d7); }
            </style>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #9b3876, #c54b8c);">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Empleado
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/orders">
                            <i class="fas fa-shopping-cart"></i> Pedidos
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/customer-support">
                            <i class="fas fa-headset"></i> Soporte
                        </a>
                        <span class="navbar-text me-3">
                            <i class="fas fa-user-tie"></i> <?php echo $_SESSION['user_name']; ?>
                        </span>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/auth/logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <!-- Mensajes -->
                <?php if(isset($_SESSION['employee_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['employee_message']['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['employee_message']['text']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['employee_message']); ?>
                <?php endif; ?>

                <div class="welcome-section p-4 mb-4">
                    <h1 class="display-5 fw-bold">Panel de Empleado</h1>
                    <p class="lead mb-0">Bienvenido, <?php echo $_SESSION['user_name']; ?></p>
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card bg-orders">
                            <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                            <div class="stat-number"><?php echo $stats['total_orders']; ?></div> 
                            <div>Total Pedidos</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card bg-pending">
                            <i class="fas fa-clock fa-2x mb-3"></i>
                            <div class="stat-number"><?php echo $stats['pending_orders']; ?></div>
                            <div>Pendientes</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card bg-customers">
                            <i class="fas fa-users fa-2x mb-3"></i>
                            <div class="stat-number"><?php echo $stats['total_customers']; ?></div>
                            <div>Clientes</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card bg-completed">
                            <i class="fas fa-check-circle fa-2x mb-3"></i>
                            <div class="stat-number"><?php echo $stats['completed_orders']; ?></div>
                            <div>Completados</div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card dashboard-card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                                <h5>Gestión de Pedidos</h5>
                                <p class="text-muted">Procesa y gestiona pedidos de clientes</p>
                                <a href="/NUBA_SKINCARE/public/employee/orders" class="btn btn-primary">
                                    <i class="fas fa-cog"></i> Gestionar Pedidos
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card dashboard-card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-headset fa-3x text-success mb-3"></i>
                                <h5>Atención al Cliente</h5>
                                <p class="text-muted">Chat y soporte a clientes</p>
                                <a href="/NUBA_SKINCARE/public/employee/customer-support" class="btn btn-success">
                                    <i class="fas fa-comments"></i> Soporte Clientes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pedidos Recientes -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Pedidos Recientes</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $recentOrders = $this->order->getRecentOrders(5);
                        if(empty($recentOrders)): ?>
                            <p class="text-muted text-center">No hay pedidos recientes</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($recentOrders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                <td>Bs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $this->getStatusBadge($order['status']); ?>">
                                                        <?php echo $this->getStatusText($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <a href="/NUBA_SKINCARE/public/employee/order/<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Ver
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

    private function showOrdersManagement($orders) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gestión de Pedidos - NUBA Empleado</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #9b3876, #c54b8c);">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Empleado
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/orders">
                            <i class="fas fa-shopping-cart"></i> Pedidos
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/customer-support">
                            <i class="fas fa-headset"></i> Soporte
                        </a>
                        <span class="navbar-text me-3">
                            <i class="fas fa-user-tie"></i> <?php echo $_SESSION['user_name']; ?>
                        </span>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/auth/logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-shopping-cart"></i> Gestión de Pedidos</h1>
                    <a href="/NUBA_SKINCARE/public/employee/dashboard" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if(empty($orders)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <h4>No hay pedidos registrados</h4>
                                <p>Los pedidos aparecerán aquí cuando los clientes realicen compras.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                <td>Bs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $this->getStatusBadge($order['status']); ?>">
                                                        <?php echo $this->getStatusText($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <a href="/NUBA_SKINCARE/public/employee/order/<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $order['id']; ?>">
                                                        <i class="fas fa-edit"></i> Estado
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Modal para cambiar estado -->
                                            <div class="modal fade" id="statusModal<?php echo $order['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cambiar Estado del Pedido #<?php echo $order['id']; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="/NUBA_SKINCARE/public/employee/update-order-status">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nuevo Estado</label>
                                                                    <select class="form-select" name="status" required>
                                                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                                                                        <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmado</option>
                                                                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>En proceso</option>
                                                                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                                                                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Entregado</option>
                                                                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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

    private function showOrderDetail($order, $orderItems) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Detalle Pedido #<?php echo $order['id']; ?> - NUBA</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #9b3876, #c54b8c);">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Empleado
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/orders">
                            <i class="fas fa-arrow-left"></i> Volver a Pedidos
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Pedido #<?php echo $order['id']; ?></h4>
                                <span class="badge bg-<?php echo $this->getStatusBadge($order['status']); ?> fs-6">
                                    <?php echo $this->getStatusText($order['status']); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <h5>Productos del Pedido</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($orderItems as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                                    <td>Bs. <?php echo number_format($item['unit_price'], 2); ?></td>
                                                    <td><?php echo $item['quantity']; ?></td>
                                                    <td>Bs. <?php echo number_format($item['subtotal'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                <td><strong>Bs. <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Información del Cliente</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($order['customer_phone'] ?? 'No proporcionado'); ?></p>
                                <p><strong>Fecha del pedido:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Cambiar Estado</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="/NUBA_SKINCARE/public/employee/update-order-status">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <div class="mb-3">
                                        <select class="form-select" name="status" required>
                                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                                            <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmado</option>
                                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>En proceso</option>
                                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Entregado</option>
                                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save"></i> Actualizar Estado
                                    </button>
                                </form>
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

    private function showCustomerSupport() {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Soporte al Cliente - NUBA Empleado</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #9b3876, #c54b8c);">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Empleado
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/orders">
                            <i class="fas fa-shopping-cart"></i> Pedidos
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/customer-support">
                            <i class="fas fa-headset"></i> Soporte
                        </a>
                        <span class="navbar-text me-3">
                            <i class="fas fa-user-tie"></i> <?php echo $_SESSION['user_name']; ?>
                        </span>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/auth/logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-headset"></i> Atención al Cliente</h1>
                    <a href="/NUBA_SKINCARE/public/employee/dashboard" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-users"></i> Lista de Clientes</h5>
                            </div>
                            <div class="card-body">
                                <p>Gestiona la información de los clientes y su historial.</p>
                                <a href="/NUBA_SKINCARE/public/employee/customers" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Ver Clientes
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-comments"></i> Chat de Soporte</h5>
                            </div>
                            <div class="card-body">
                                <p>Atiende consultas y brinda soporte en tiempo real.</p>
                                <button class="btn btn-success" onclick="openSupportChat()">
                                    <i class="fas fa-comment-dots"></i> Iniciar Chat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consultas Recientes -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Consultas Recientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Carlos López - Problema con pedido</h6>
                                    <small class="text-muted">Hace 2 horas</small>
                                </div>
                                <p class="mb-1">El cliente no recibió su pedido #12345</p>
                                <small class="text-muted">Estado: <span class="badge bg-warning">Pendiente</span></small>
                            </div>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">María García - Consulta sobre producto</h6>
                                    <small class="text-muted">Hace 5 horas</small>
                                </div>
                                <p class="mb-1">Consulta sobre ingredientes del serum revitalizante</p>
                                <small class="text-muted">Estado: <span class="badge bg-success">Resuelto</span></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function openSupportChat() {
                    alert('Sistema de chat en tiempo real - Próximamente');
                    // Aquí integrarías un sistema de chat real como Socket.io
                }
            </script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }

    private function showCustomersList($customers) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Lista de Clientes - NUBA Empleado</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #9b3876, #c54b8c);">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare - Empleado
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/employee/customer-support">
                            <i class="fas fa-headset"></i> Soporte
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-users"></i> Lista de Clientes</h1>
                    <a href="/NUBA_SKINCARE/public/employee/customer-support" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Volver al Soporte
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if(empty($customers)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h4>No hay clientes registrados</h4>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Fecha Registro</th>
                                            <th>Total Pedidos</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($customers as $customer): ?>
                                            <tr>
                                                <td><?php echo $customer['id']; ?></td>
                                                <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                                <td><?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($customer['created_at'])); ?></td>
                                                <td>
                                                    <?php 
                                                    $orderCount = $this->order->getCustomerOrderCount($customer['id']);
                                                    echo $orderCount; 
                                                    ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="contactCustomer('<?php echo $customer['email']; ?>', '<?php echo $customer['phone']; ?>')">
                                                        <i class="fas fa-envelope"></i> Contactar
                                                    </button>
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

            <script>
                function contactCustomer(email, phone) {
                    let message = `Información de contacto:\n`;
                    if (email) message += `Email: ${email}\n`;
                    if (phone && phone !== 'N/A') message += `Teléfono: ${phone}\n`;
                    
                    alert(message);
                    
                    // Aquí podrías abrir un modal o redirigir a un sistema de mensajería
                    if (email) {
                        window.open(`mailto:${email}?subject=NUBA Skincare - Soporte`, '_blank');
                    }
                }
            </script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }

    private function getEmployeeStats() {
        // Obtener estadísticas para el dashboard
        $totalOrders = $this->order->getTotalOrders();
        $pendingOrders = $this->order->getOrdersByStatus('pending');
        $completedOrders = $this->order->getOrdersByStatus('delivered');
        $totalCustomers = $this->user->getTotalClients();

        return [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'total_customers' => $totalCustomers
        ];
    }

    private function getStatusBadge($status) {
        switch($status) {
            case 'pending': return 'warning';
            case 'confirmed': return 'info';
            case 'processing': return 'primary';
            case 'shipped': return 'success';
            case 'delivered': return 'success';
            case 'cancelled': return 'danger';
            default: return 'secondary';
        }
    }

    private function getStatusText($status) {
        switch($status) {
            case 'pending': return 'Pendiente';
            case 'confirmed': return 'Confirmado';
            case 'processing': return 'En proceso';
            case 'shipped': return 'Enviado';
            case 'delivered': return 'Entregado';
            case 'cancelled': return 'Cancelado';
            default: return $status;
        }
    }
}
?>