<?php
class Order {
    private $conn;
    private $table_orders = 'orders';
    private $table_order_items = 'order_items';
    private $table_users = 'users';

    public $id;
    public $user_id;
    public $total_amount;
    public $status;
    public $customer_name;
    public $customer_email;
    public $customer_phone;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear nuevo pedido
    public function create($cartItems, $userId, $customerData) {
        try {
            $this->conn->beginTransaction();

            // Calcular total
            $total = 0;
            foreach($cartItems as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Insertar pedido
            $query = "INSERT INTO " . $this->table_orders . " 
                     (user_id, total_amount, customer_name, customer_email, customer_phone, status) 
                     VALUES (:user_id, :total_amount, :customer_name, :customer_email, :customer_phone, 'pending')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":total_amount", $total);
            $stmt->bindParam(":customer_name", $customerData['name']);
            $stmt->bindParam(":customer_email", $customerData['email']);
            $stmt->bindParam(":customer_phone", $customerData['phone']);
            $stmt->execute();

            $orderId = $this->conn->lastInsertId();

            // Insertar items del pedido
            foreach($cartItems as $item) {
                $query = "INSERT INTO " . $this->table_order_items . " 
                         (order_id, product_id, product_name, unit_price, quantity, subtotal) 
                         VALUES (:order_id, :product_id, :product_name, :unit_price, :quantity, :subtotal)";
                
                $subtotal = $item['price'] * $item['quantity'];
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":order_id", $orderId);
                $stmt->bindParam(":product_id", $item['id']);
                $stmt->bindParam(":product_name", $item['name']);
                $stmt->bindParam(":unit_price", $item['price']);
                $stmt->bindParam(":quantity", $item['quantity']);
                $stmt->bindParam(":subtotal", $subtotal);
                $stmt->execute();
            }

            $this->conn->commit();
            return $orderId;

        } catch(Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Obtener todos los pedidos
    public function getAllOrders() {
        $query = "SELECT o.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                         u.email as customer_email,
                         u.phone as customer_phone
                  FROM " . $this->table_orders . " o
                  LEFT JOIN " . $this->table_users . " u ON o.user_id = u.id
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener pedido por ID
    public function getOrderById($id) {
        $query = "SELECT o.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                         u.email as customer_email,
                         u.phone as customer_phone
                  FROM " . $this->table_orders . " o
                  LEFT JOIN " . $this->table_users . " u ON o.user_id = u.id
                  WHERE o.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener items del pedido
    public function getOrderItems($orderId) {
        $query = "SELECT * FROM " . $this->table_order_items . " WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar estado del pedido
    public function updateStatus($orderId, $status) {
        $query = "UPDATE " . $this->table_orders . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $orderId);
        return $stmt->execute();
    }

    // Obtener pedidos recientes
    public function getRecentOrders($limit = 5) {
        $query = "SELECT o.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as customer_name
                  FROM " . $this->table_orders . " o
                  LEFT JOIN " . $this->table_users . " u ON o.user_id = u.id
                  ORDER BY o.created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener total de pedidos
    public function getTotalOrders() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_orders;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Obtener pedidos por estado
    public function getOrdersByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_orders . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Obtener conteo de pedidos por cliente
    public function getCustomerOrderCount($customerId) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_orders . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $customerId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>