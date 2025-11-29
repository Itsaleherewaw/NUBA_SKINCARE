<?php
require_once '../models/Chat.php';
require_once '../models/Database.php';

class ChatController {
    private $db;
    private $chat;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->chat = new Chat($this->db);
    }

    public function send() {
        header('Content-Type: application/json');
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $message = $input['message'] ?? '';
            
            if(!empty($message)) {
                $response = $this->chat->getBotResponse($message);
                
                // Guardar en base de datos si hay usuario logueado
                if(isset($_SESSION['user_id'])) {
                    $this->chat->user_id = $_SESSION['user_id'];
                    $this->chat->message = $message;
                    $this->chat->response = $response;
                    $this->chat->is_bot = 0;
                    $this->chat->create();
                }
                
                echo json_encode(['response' => $response]);
            } else {
                echo json_encode(['response' => 'Por favor envía un mensaje válido.']);
            }
        } else {
            echo json_encode(['response' => 'Método no permitido.']);
        }
    }
}
?>