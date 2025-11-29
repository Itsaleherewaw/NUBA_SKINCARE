<?php
class Chat {
    private $conn;
    private $table = 'chat_messages';

    public $id;
    public $user_id;
    public $message;
    public $response;
    public $is_bot;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET user_id=:user_id, message=:message, response=:response, is_bot=:is_bot";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":message", $this->message);
        $stmt->bindParam(":response", $this->response);
        $stmt->bindParam(":is_bot", $this->is_bot);
        
        return $stmt->execute();
    }

    public function getBotResponse($message) {
        $message = strtolower(trim($message));
        
        $responses = [
            'hola' => '¡Hola! Soy el asistente virtual de NUBA. ¿En qué puedo ayudarte hoy?',
            'productos' => 'Tenemos una amplia variedad de productos: sérums, limpiadores, cremas, aceites y protectores solares. ¿Te interesa algún producto en particular?',
            'precios' => 'Todos nuestros precios están en Bolivianos (Bs.). Puedes ver los precios específicos en nuestra sección de productos.',
            'envío' => 'Realizamos envíos a todo Bolivia. El costo y tiempo de entrega dependen de tu ubicación.',
            'contacto' => 'Puedes contactarnos al +591 70514802 o escribirnos a info@nuba.com',
            'horario' => 'Nuestro horario de atención es de lunes a viernes de 9:00 a 18:00 y sábados de 9:00 a 14:00.',
            'gracias' => '¡De nada! Estoy aquí para ayudarte. ¿Necesitas algo más?',
            'default' => 'Entiendo que quieres saber sobre: "' . $message . '". Te recomiendo visitar nuestra sección de productos o contactar con nuestro equipo de atención al cliente para información más específica.'
        ];

        foreach($responses as $key => $response) {
            if(strpos($message, $key) !== false) {
                return $response;
            }
        }

        return $responses['default'];
    }

    public function getConversation($user_id, $limit = 10) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
const response = await fetch('/nuba_skincare/public/chat/send'), {
}
?>