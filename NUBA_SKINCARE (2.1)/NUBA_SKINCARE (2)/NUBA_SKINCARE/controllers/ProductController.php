<?php
require_once '../models/Product.php';
require_once '../models/Database.php';

class ProductController {
    private $db;
    private $product;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
    }

    public function index() {
        $stmt = $this->product->read();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('products/index', ['products' => $products]);
    }

    public function category($category) {
        $stmt = $this->product->readByCategory($category);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('products/index', ['products' => $products, 'category' => $category]);
    }

    private function loadView($view, $data = []) {
        extract($data);
        $view_path = dirname(__DIR__) . '/views/' . $view . '.php';
        if (file_exists($view_path)) {
            require_once $view_path;
        } else {
            $this->showTempProducts($data);
        }
    }

    private function showTempProducts($data) {
        $products = $data['products'] ?? [];
        $category = $data['category'] ?? 'Todos los productos';
        
        // Obtener categorías dinámicamente
        $database = new Database();
        $db = $database->getConnection();
        $productModel = new Product($db);
        $categories = $productModel->getCategories();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Productos - NUBA Skincare</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .product-card {
                    transition: transform 0.3s;
                    border: none;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                }
                .product-card:hover {
                    transform: translateY(-5px);
                }
                .product-image {
                    height: 200px;
                    object-fit: cover;
                }
                .price {
                    color: #9b3876;
                    font-weight: bold;
                    font-size: 1.2em;
                }
                .category-badge {
                    background: linear-gradient(135deg, #9b3876, #c54b8c);
                    color: white;
                }
                .stock-low {
                    color: #dc3545;
                    font-weight: bold;
                }
                .navbar-brand {
                    font-weight: bold;
                }
                .cart-badge {
                    font-size: 0.7em;
                    margin-left: 5px;
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
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <div class="navbar-nav ms-auto">
                            <a class="nav-link" href="/NUBA_SKINCARE/public/products">
                                <i class="fas fa-store"></i> Productos
                            </a>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a class="nav-link" href="/NUBA_SKINCARE/public/client/dashboard">
                                    <i class="fas fa-user"></i> Mi Cuenta
                                </a>
                            <?php else: ?>
                                <a class="nav-link" href="/NUBA_SKINCARE/public/auth/login">
                                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                </a>
                            <?php endif; ?>
                            <a class="nav-link" href="/NUBA_SKINCARE/public/cart">
                                <i class="fas fa-shopping-cart"></i> Carrito
                                <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                    <span class="badge bg-light text-dark cart-badge"><?php echo array_sum($_SESSION['cart']); ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <div class="row">
                    <div class="col-12">
                        <!-- Header con título y breadcrumb -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="mb-0"><?php echo htmlspecialchars($category); ?></h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/NUBA_SKINCARE/public/">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="/NUBA_SKINCARE/public/products">Productos</a></li>
                                    <?php if(isset($data['category'])): ?>
                                        <li class="breadcrumb-item active"><?php echo ucfirst(htmlspecialchars($category)); ?></li>
                                    <?php endif; ?>
                                </ol>
                            </nav>
                        </div>
                        
                        <!-- Filtros de Categoría -->
                        <div class="mb-4">
                            <h5 class="text-muted mb-3">Filtrar por categoría:</h5>
                            <div class="d-flex flex-wrap">
                                <a href="/NUBA_SKINCARE/public/products" 
                                   class="btn <?php echo !isset($data['category']) ? 'btn-primary' : 'btn-outline-primary'; ?> me-2 mb-2">
                                    <i class="fas fa-th-large"></i> Todos
                                </a>
                                <?php foreach($categories as $cat): ?>
                                    <a href="/NUBA_SKINCARE/public/products/category/<?php echo urlencode($cat); ?>" 
                                       class="btn <?php echo (isset($data['category']) && $data['category'] === $cat) ? 'btn-primary' : 'btn-outline-primary'; ?> me-2 mb-2">
                                        <i class="fas fa-tag"></i> <?php echo ucfirst(htmlspecialchars($cat)); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Contador de productos -->
                        <div class="alert alert-light mb-4">
                            <i class="fas fa-info-circle"></i> 
                            Mostrando <strong><?php echo count($products); ?></strong> 
                            producto<?php echo count($products) !== 1 ? 's' : ''; ?>
                            <?php if(isset($data['category'])): ?>
                                en la categoría <strong><?php echo ucfirst(htmlspecialchars($category)); ?></strong>
                            <?php endif; ?>
                        </div>

                        <!-- Grid de Productos -->
                        <div class="row">
                            <?php if (empty($products)): ?>
                                <div class="col-12">
                                    <div class="alert alert-info text-center py-5">
                                        <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                                        <h4>No hay productos disponibles</h4>
                                        <p class="mb-3">No encontramos productos <?php echo isset($data['category']) ? 'en esta categoría' : 'disponibles'; ?>.</p>
                                        <a href="/NUBA_SKINCARE/public/products" class="btn btn-primary">
                                            <i class="fas fa-th-large"></i> Ver Todos los Productos
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card product-card h-100">
                                            <div class="position-relative">
                                                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                                     class="card-img-top product-image" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                     onerror="this.src='https://via.placeholder.com/300x200/9b3876/ffffff?text=NUBA'">
                                                <?php if($product['stock'] == 0): ?>
                                                    <div class="position-absolute top-0 start-0 m-2">
                                                        <span class="badge bg-danger">Agotado</span>
                                                    </div>
                                                <?php elseif($product['stock'] < 10): ?>
                                                    <div class="position-absolute top-0 start-0 m-2">
                                                        <span class="badge bg-warning text-dark">Últimas unidades</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <span class="badge category-badge">
                                                        <?php echo ucfirst(htmlspecialchars($product['category'])); ?>
                                                    </span>
                                                    <?php if($product['stock'] > 0): ?>
                                                        <small class="text-muted">
                                                            <i class="fas fa-box"></i> <?php echo $product['stock']; ?> disponibles
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                                <p class="card-text flex-grow-1 text-muted">
                                                    <?php echo htmlspecialchars($product['description']); ?>
                                                </p>
                                                
                                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                                    <span class="price">Bs. <?php echo number_format($product['price'], 2); ?></span>
                                                    <?php if($product['stock'] < 10 && $product['stock'] > 0): ?>
                                                        <small class="stock-low">
                                                            <i class="fas fa-exclamation-triangle"></i> Stock bajo
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <form action="/NUBA_SKINCARE/public/cart/add" method="POST" class="mt-3">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <div class="input-group">
                                                        <input type="number" 
                                                               name="quantity" 
                                                               class="form-control" 
                                                               value="1" 
                                                               min="1" 
                                                               max="<?php echo $product['stock']; ?>"
                                                               <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                                                        <button type="submit" 
                                                                class="btn <?php echo $product['stock'] == 0 ? 'btn-secondary' : 'btn-primary'; ?>" 
                                                                <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                                                            <i class="fas fa-<?php echo $product['stock'] == 0 ? 'times' : 'cart-plus'; ?>"></i> 
                                                            <?php echo $product['stock'] == 0 ? 'Agotado' : 'Añadir'; ?>
                                                        </button>
                                                    </div>
                                                    <?php if($product['stock'] == 0): ?>
                                                        <small class="text-muted d-block mt-1 text-center">
                                                            Producto temporalmente no disponible
                                                        </small>
                                                    <?php endif; ?>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
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

            <!-- Chatbot -->
            <script src="/NUBA_SKINCARE/public/assets/js/chatbot.js"></script>
            <link rel="stylesheet" href="/NUBA_SKINCARE/public/assets/css/chatbot.css">
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }
}
?>