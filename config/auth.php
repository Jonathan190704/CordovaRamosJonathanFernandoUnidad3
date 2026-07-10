<?php
require_once __DIR__ . '/database.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificarAcceso($rolesPermitidos = []) {
    
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_nombre']) || !isset($_SESSION['session_token'])) {
        expulsarUsuario("No autorizado. Inicie sesión.");
    }

    try {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT id FROM sesiones_activas WHERE usuario_id = :usuario_id AND session_token = :token");
        $stmt->execute([
            ':usuario_id' => $_SESSION['usuario_id'],
            ':token' => $_SESSION['session_token']
        ]);
        
        if ($stmt->rowCount() === 0) {
            session_unset();
            session_destroy();
            expulsarUsuario("Su sesión ha caducado o se ha iniciado en otro dispositivo.");
        }
        
        $stmtUpdate = $pdo->prepare("UPDATE sesiones_activas SET ultimo_acceso = NOW() WHERE session_token = :token");
        $stmtUpdate->execute([':token' => $_SESSION['session_token']]);

    } catch (PDOException $e) {
        expulsarUsuario("Error de conexión al verificar credenciales.");
    }

    if (!empty($rolesPermitidos) && !in_array($_SESSION['rol_nombre'], $rolesPermitidos)) {
        
        if (esPeticionAPI()) {
            http_response_code(403);
            echo json_encode(["error" => "No tienes permisos suficientes para realizar esta acción."]);
            exit;
        }
        
        header("Location: /404.php?error=sin_permiso"); 
        exit;
    }
}

function expulsarUsuario($mensaje) {
    if (esPeticionAPI()) {
        http_response_code(401);
        echo json_encode(["error" => $mensaje]);
        exit;
    } else {
        header("Location: /index.html?mensaje=" . urlencode($mensaje));
        exit;
    }
}

function esPeticionAPI() {
    return (strpos($_SERVER['REQUEST_URI'], '/api/') !== false || 
            (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false));
}
?>