<?php
echo "<h1>✅ PHP está funcionando</h1>";
echo "<p>Si ves este mensaje, PHP está funcionando correctamente.</p>";

// Verificar sesión
session_start();
echo "<p>Sesión: " . (isset($_SESSION) ? "ACTIVA" : "INACTIVA") . "</p>";

// Verificar rutas
echo "<h2>Información del Servidor:</h2>";
echo "<p>REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>PHP_SELF: " . $_SERVER['PHP_SELF'] . "</p>";

// Probar includes
echo "<h2>Probando includes:</h2>";
$files_to_test = [
    '../config/database.php',
    '../models/Database.php',
    '../controllers/AdminController.php'
];

foreach ($files_to_test as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file - EXISTE</p>";
    } else {
        echo "<p style='color: red;'>❌ $file - NO EXISTE</p>";
    }
}

// Probar base de datos
try {
    require_once '../config/database.php';
    require_once '../models/Database.php';
    
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<p style='color: green;'>✅ Conexión a BD - EXITOSA</p>";
    } else {
        echo "<p style='color: red;'>❌ Conexión a BD - FALLIDA</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error BD: " . $e->getMessage() . "</p>";
}
?>
