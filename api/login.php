<?php

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../config/database.php';
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'ok' => false,
        'error' => 'method_not_allowed'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['email'], $input['password'], $input['captcha_token'])) {
    echo json_encode([
        'ok' => false,
        'error' => 'missing_fields',
        'msg' => 'Campos incompletos.'
    ]);
    exit;
}

$email = trim($input['email']);
$password = $input['password'];
$captchaToken = $input['captcha_token'];

if (!verificarTokenHumano($captchaToken)) {
    echo json_encode([
        'ok' => false,
        'error' => 'captcha_failed',
        'msg' => 'Validación humana fallida.'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        'SELECT u.id, u.nombre, u.email, u.password_hash, r.nombre_rol
         FROM usuarios u
         INNER JOIN roles r ON u.rol_id = r.id
         WHERE u.email = ?'
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $sessionToken = bin2hex(random_bytes(32));
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $stmtSession = $pdo->prepare(
            'INSERT INTO sesiones_activas (usuario_id, session_token, ip_address, user_agent) 
             VALUES (?, ?, ?, ?)'
        );
        $stmtSession->execute([$user['id'], $sessionToken, $ipAddress, $userAgent]);

        $_SESSION['usuario_id'] = $user['id']; 
        $_SESSION['user_name'] = htmlspecialchars($user['nombre'], ENT_QUOTES, 'UTF-8');
        $_SESSION['user_email'] = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
        
        $_SESSION['rol_nombre'] = $user['nombre_rol']; 
        $_SESSION['session_token'] = $sessionToken;    

        echo json_encode([
            'ok' => true,
            'msg' => 'Acceso concedido',
            'user' => [
                'id' => $_SESSION['usuario_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'rol' => $_SESSION['rol_nombre'] 
            ]
        ]);
    } else {
        echo json_encode([
            'ok' => false,
            'error' => 'invalid_credentials',
            'msg' => 'Correo o contraseña incorrectos.'
        ]);
    }

} catch (PDOException $e) {

    error_log('Error de login: ' . $e->getMessage());

    echo json_encode([
        'ok' => false,
        'error' => 'server_error',
        'msg' => 'Error interno de autenticación.'
    ]);
}
?>