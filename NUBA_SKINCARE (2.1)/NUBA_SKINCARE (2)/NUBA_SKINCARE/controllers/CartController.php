<?php
require_once '../models/Product.php';
require_once '../models/Database.php';

class CartController {
    private $db;
    private $product;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
        session_start();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function index() {
        $cartItems = [];
        $total = 0;

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $productData = $this->product->getById($productId);
            if ($productData) {
                $productData['quantity'] = $quantity;
                $productData['subtotal'] = $productData['price'] * $quantity;
                $cartItems[] = $productData;
                $total += $productData['subtotal'];
            }
        }

        $this->loadView('cart/index', ['cartItems' => $cartItems, 'total' => $total]);
    }

    public function add() {
        if ($_POST) {
            $productId = $_POST['product_id'] ?? '';
            $quantity = $_POST['quantity'] ?? 1;

            if ($productId) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] += $quantity;
                } else {
                    $_SESSION['cart'][$productId] = $quantity;
                }
                
                // Verificar stock
                $productData = $this->product->getById($productId);
                if ($_SESSION['cart'][$productId] > $productData['stock']) {
                    $_SESSION['cart'][$productId] = $productData['stock'];
                    $_SESSION['cart_message'] = [
                        'type' => 'warning',
                        'text' => 'No hay suficiente stock. Se añadió la cantidad máxima disponible.'
                    ];
                } else {
                    $_SESSION['cart_message'] = [
                        'type' => 'success',
                        'text' => 'Producto añadido al carrito correctamente.'
                    ];
                }
            }
            
            header('Location: /NUBA_SKINCARE/public/products');
            exit;
        }
    }

    public function update() {
        if ($_POST) {
            foreach ($_POST['quantity'] as $productId => $quantity) {
                if ($quantity <= 0) {
                    unset($_SESSION['cart'][$productId]);
                } else {
                    $_SESSION['cart'][$productId] = $quantity;
                }
            }
            
            $_SESSION['cart_message'] = [
                'type' => 'success',
                'text' => 'Carrito actualizado correctamente.'
            ];
            
            header('Location: /NUBA_SKINCARE/public/cart');
            exit;
        }
    }

    public function remove($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $_SESSION['cart_message'] = [
                'type' => 'success',
                'text' => 'Producto eliminado del carrito.'
            ];
        }
        
        header('Location: /NUBA_SKINCARE/public/cart');
        exit;
    }

    public function checkout() {
        // Aquí puedes implementar el proceso de checkout
        // Por ahora solo limpiaremos el carrito
        $_SESSION['cart'] = [];
        $_SESSION['cart_message'] = [
            'type' => 'success',
            'text' => '¡Compra realizada con éxito! Gracias por tu compra.'
        ];
        
        header('Location: /NUBA_SKINCARE/public/products');
        exit;
    }

    private function loadView($view, $data = []) {
        extract($data);
        $view_path = dirname(__DIR__) . '/views/' . $view . '.php';
        if (file_exists($view_path)) {
            require_once $view_path;
        } else {
            $this->showTempCart($data);
        }
    }

    private function showTempCart($data) {
        $cartItems = $data['cartItems'] ?? [];
        $total = $data['total'] ?? 0;
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Carrito - NUBA Skincare</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #9b3876, #c54b8c);">
                <div class="container">
                    <a class="navbar-brand" href="/NUBA_SKINCARE/public/">
                        <i class="fas fa-spa"></i> NUBA Skincare
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/NUBA_SKINCARE/public/client/dashboard">
                            <i class="fas fa-user"></i> Mi Cuenta
                        </a>
                        <a class="nav-link" href="/NUBA_SKINCARE/public/products">
                            <i class="fas fa-store"></i> Productos
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <h1 class="mb-4">Mi Carrito de Compras</h1>

                <?php if (isset($_SESSION['cart_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['cart_message']['type']; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['cart_message']['text']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['cart_message']); ?>
                <?php endif; ?>

                <?php if (empty($cartItems)): ?>
                    <div class="alert alert-info">
                        <h4>Tu carrito está vacío</h4>
                        <p>Descubre nuestros productos y añade algunos a tu carrito.</p>
                        <a href="/NUBA_SKINCARE/public/products" class="btn btn-primary">Ver Productos</a>
                    </div>
                <?php else: ?>
                    <form action="/NUBA_SKINCARE/public/cart/update" method="POST">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                         style="width: 60px; height: 60px; object-fit: cover;"
                                                         onerror="this.src='https://via.placeholder.com/60x60/9b3876/ffffff?text=NUBA'"
                                                         class="me-3">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                        <small class="text-muted"><?php echo htmlspecialchars($item['category']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">Bs. <?php echo number_format($item['price'], 2); ?></td>
                                            <td class="align-middle">
                                                <input type="number" name="quantity[<?php echo $item['id']; ?>]" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="1" max="<?php echo $item['stock']; ?>"
                                                       class="form-control" style="width: 80px;">
                                            </td>
                                            <td class="align-middle">Bs. <?php echo number_format($item['subtotal'], 2); ?></td>
                                            <td class="align-middle">
                                                <a href="/NUBA_SKINCARE/public/cart/remove/<?php echo $item['id']; ?>" 
                                                   class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td colspan="2"><strong>Bs. <?php echo number_format($total, 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/NUBA_SKINCARE/public/products" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> Seguir Comprando
                            </a>
                            <div>
                                <button type="submit" class="btn btn-warning me-2">
                                    <i class="fas fa-sync"></i> Actualizar Carrito
                                </button>
                                <a href="/NUBA_SKINCARE/public/cart/checkout" class="btn btn-success">
                                    <i class="fas fa-credit-card"></i> Finalizar Compra
                                </a>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

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