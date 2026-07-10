<?php

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../config/database.php';
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed', 'msg' => 'Método no permitido.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['nombre'], $input['email'], $input['password'], $input['confirm_password'], $input['captcha_token'])) {
    echo json_encode(['ok' => false, 'error' => 'missing_fields', 'msg' => 'Campos incompletos en el servidor.']);
    exit;
}

$nombre          = trim($input['nombre']);
$email           = trim($input['email']);
$password        = $input['password'];
$confirmPassword = $input['confirm_password'];
$captchaToken    = trim($input['captcha_token']); 

if ($password !== $confirmPassword) {
    echo json_encode(['ok' => false, 'error' => 'password_mismatch', 'msg' => 'Las contraseñas no coinciden en el servidor.']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['ok' => false, 'error' => 'invalid_password', 'msg' => 'La contraseña debe tener mínimo 8 caracteres.']);
    exit;
}

$recaptchaSecret = RECAPTCHA_SECRET;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'secret' => $recaptchaSecret,
    'response' => $captchaToken
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$verifyResponse = curl_exec($ch);

$responseData = json_decode($verifyResponse, true);

if (!$responseData || !isset($responseData['success']) || $responseData['success'] !== true) {
    echo json_encode(['ok' => false, 'error' => 'captcha_failed', 'msg' => 'Validación de seguridad reCAPTCHA fallida. Inténtalo de nuevo.']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['ok' => false, 'error' => 'email_exists', 'msg' => 'El correo electrónico ya se encuentra registrado.']);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);


    $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)');
    $stmt->execute([$nombre, $email, $passwordHash]);

    echo json_encode([
        'ok' => true,
        'msg' => 'Usuario registrado exitosamente.'
    ]);

} catch (\PDOException $e) {
    error_log('Error crítico de registro de base de datos: ' . $e->getMessage());
    echo json_encode(['ok' => false, 'error' => 'db_error', 'msg' => 'Error interno al guardar los datos de cuenta.']);
}
?>