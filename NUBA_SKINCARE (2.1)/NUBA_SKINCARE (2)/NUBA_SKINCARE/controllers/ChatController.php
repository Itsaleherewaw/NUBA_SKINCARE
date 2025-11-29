<?php
/**
 * Controlador para gestión del Chat.
 * Maneja autenticación, historial, envío y filtrado de mensajes vía AJAX y vistas.
 * Escalable para integrar IA en responder().
 */
require_once __DIR__ . '/../models/Chat.php';

class ChatController {
    private $chatModel;

    public function __construct() {
        $this->chatModel = new Chat();
    }

    /**
     * Muestra la vista principal del chat (requiere login).
     */
    public function index() {
        if (!$this->isAuthenticated()) {
            header('Location: /login');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role']; // admin, empleado, cliente
        $history = $this->chatModel->getUserMessages($userId, $role);

        require_once __DIR__ . '/../views/chat.php';
    }

    /**
     * Obtiene historial del usuario (AJAX).
     */
    public function fetchHistory() {
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            exit('No authenticated');
        }
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];

        // Admin puede pedir historial de otro usuario
        $filterId = ($role === 'admin' && isset($_GET['user_id'])) ? (int)$_GET['user_id'] : $userId;

        $messages = $this->chatModel->getUserMessages($filterId, $role);

        header('Content-Type: application/json');
        echo json_encode($messages);
        exit();
    }

    /**
     * Envia un mensaje (AJAX), validando y previniendo spam/inyección.
     */
    public function sendMessage() {
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            exit('No authenticated');
        }
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];

        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        $msg = isset($data['message']) ? trim($data['message']) : '';
        if (strlen($msg) === 0 || strlen($msg) > 500) {
            http_response_code(400);
            exit('Message invalid');
        }

        // Sanitización extra
        $msg = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');

        // Anti-spam: podrías agregar aquí rate limiting simple.

        // Guardar mensaje usuario
        $this->chatModel->saveMessage($userId, $role, $msg, 'user');

        // Responder (IA futuro, ahora es static/bot)
        $botResponse = $this->chatModel->reply($userId, $role, $msg);

        // Guardar mensaje bot
        $this->chatModel->saveMessage($userId, $role, $botResponse, 'bot');

        header('Content-Type: application/json');
        echo json_encode(['user' => $msg, 'bot' => $botResponse]);
        exit();
    }

    /**
     * Solo admins pueden ver todos los historiales o filtrar.
     */
    public function fetchAll() {
        if (!$this->isAuthenticated() || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            exit('No permission');
        }
        $filterId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
        $all = $this->chatModel->getAllMessages($filterId);

        header('Content-Type: application/json');
        echo json_encode($all);
        exit();
    }

    /**
     * Autenticación básica (adaptar según tus métodos).
     */
    private function isAuthenticated() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }
}
