<?php
/**
 * Modelo del chat: operaciones de mensajes en BD, seguro contra SQL Injection.
 * Escalable para integración con IA en reply().
 */

class Chat {
    private $db;

    public function __construct() {
        // Adaptar a tu sistema de conexión
        $this->db = new PDO('mysql:host=localhost;dbname=tu_bd', 'usuario', 'clave', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    /**
     * Guarda un mensaje en la BD, previene inyección.
     */
    public function saveMessage($userId, $userRole, $message, $sender) {
        $sql = "INSERT INTO chat_messages (user_id, role, message, sender, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $userId,
            $userRole,
            $message,
            $sender
        ]);
    }

    /**
     * Obtiene historial de usuario (privado).
     * Los admins pueden pedir historial de cualquier usuario.
     */
    public function getUserMessages($userId, $role) {
        $sql = "SELECT * FROM chat_messages WHERE user_id = ? ORDER BY created_at ASC LIMIT 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los historiales o de usuario (solo admin).
     */
    public function getAllMessages($filterId = null) {
        if ($filterId !== null) {
            $sql = "SELECT * FROM chat_messages WHERE user_id = ? ORDER BY created_at ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$filterId]);
        } else {
            $sql = "SELECT * FROM chat_messages ORDER BY created_at ASC";
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para responder el chatbot. Aquí se integra IA en el futuro.
     * Ahora responde por rol.
     */
    public function reply($userId, $role, $message) {
        // Futuro: aquí integrar OpenAI, Gemini, etc.
        switch ($role) {
            case 'admin':
                return "Hola administrador, ¿en qué puedo ayudarte?";
            case 'empleado':
                return "Hola empleado, ¿necesitas asistencia interna?";
            case 'cliente':
                return "Hola cliente, ¿en qué podemos ayudarte hoy?";
            default:
                return "Hola, ¿cómo puedo ayudar?";
        }
    }
}