<?php
class ClientController {
    public function dashboard() {
        $this->showTempDashboard();
    }

    private function showTempDashboard() {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Panel Cliente - NUBA Skincare</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <!-- INCLUIR CHATBOT CSS -->
            <link rel="stylesheet" href="/NUBA_SKINCARE/public/assets/css/chatbot.css">
            <style>
                .dashboard-card {
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                    transition: transform 0.3s;
                }
                .dashboard-card:hover {
                    transform: translateY(-5px);
                }
                .welcome-section {
                    background: linear-gradient(135deg, #771091ff, #911255ff);
                    color: white;
                    border-radius: 15px;
                }
                .stat-card {
                    text-align: center;
                    padding: 20px;
                }
                .stat-number {
                    font-size: 2.5rem;
                    font-weight: bold;
                    color: #9b3876;
                }
            </style>
        </head>
        <body>
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #9b3876, #c54b8c);">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare
                    </a>
                    <div class="navbar-nav ms-auto">
                        <span class="navbar-text me-3">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                        </span>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/auth/logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <div class="row">
                    <div class="col-12">
                        <!-- Bienvenida -->
                        <div class="welcome-section p-4 mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h1 class="display-5 fw-bold">¡Hola, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>! </h1>
                                    <p class="lead mb-0">Bienvenido a tu panel de cliente en NUBA Skincare</p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="bg-white text-dark rounded p-3 d-inline-block">
                                        <small class="text-muted">ROL</small>
                                        <div class="fw-bold text-primary"><?php echo ucfirst($_SESSION['user_role']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estadísticas rápidas -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="dashboard-card stat-card">
                                    <i class="fas fa-shopping-cart fa-2x text-primary mb-3"></i>
                                    <div class="stat-number">
                                        <?php 
                                        $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                                        echo $cart_count;
                                        ?>
                                    </div>
                                    <div class="text-muted">Productos en carrito</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="dashboard-card stat-card">
                                    <i class="fas fa-heart fa-2x text-danger mb-3"></i>
                                    <div class="stat-number">0</div>
                                    <div class="text-muted">Favoritos</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="dashboard-card stat-card">
                                    <i class="fas fa-box-open fa-2x text-success mb-3"></i>
                                    <div class="stat-number">0</div>
                                    <div class="text-muted">Pedidos activos</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="dashboard-card stat-card">
                                    <i class="fas fa-star fa-2x text-warning mb-3"></i>
                                    <div class="stat-number">0</div>
                                    <div class="text-muted">Reseñas</div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones rápidas -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="dashboard-card p-4 h-100">
                                    <h4 class="mb-3"> Acciones de Compra</h4>
                                    <div class="d-grid gap-2">
                                        <a href="/NUBA_SKINCARE/public/products" class="btn btn-primary btn-lg">
                                            <i class="fas fa-store"></i> Ver Catálogo de Productos
                                        </a>
                                        <a href="/NUBA_SKINCARE/public/cart" class="btn btn-outline-primary">
                                            <i class="fas fa-shopping-cart"></i> Ver Mi Carrito
                                            <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                                <span class="badge bg-danger ms-2"><?php echo array_sum($_SESSION['cart']); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="dashboard-card p-4 h-100">
                                    <h4 class="mb-3">👤 Mi Cuenta</h4>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-secondary" disabled>
                                            <i class="fas fa-history"></i> Historial de Pedidos
                                        </button>
                                        <button class="btn btn-outline-secondary" disabled>
                                            <i class="fas fa-heart"></i> Lista de Deseos
                                        </button>
                                        <button class="btn btn-outline-secondary" disabled>
                                            <i class="fas fa-cog"></i> Configuración
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del usuario -->
                        <div class="dashboard-card p-4 mb-4">
                            <h4 class="mb-3"> Información de tu Cuenta</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Nombre completo:</strong></td>
                                            <td><?php echo $_SESSION['user_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td><?php echo $_SESSION['user_email']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Rol:</strong></td>
                                            <td><span class="badge bg-primary"><?php echo ucfirst($_SESSION['user_role']); ?></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle"></i> ¿Necesitas ayuda?</h6>
                                        <p class="mb-0">Usa nuestro chatbot para consultas sobre productos, envíos o cualquier duda que tengas.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enlaces de navegación -->
                        <div class="text-center mt-4">
                            <a href="/NUBA_SKINCARE/public/" class="btn btn-outline-primary me-2">
                                <i class="fas fa-home"></i> Volver al Inicio
                            </a>
                            <a href="/NUBA_SKINCARE/public/auth/logout" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
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
                            <p>Tu tienda de confianza para el cuidado de la piel</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p>&copy; 2025 NUBA Skincare. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </div>
            </footer>

            <!-- INCLUIR CHATBOT JS - CORREGIDO -->
            <script src="/NUBA_SKINCARE/public/assets/js/chatbot.js"></script>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }
}
?>