<?php
header('Content-Type: application/json');

require_once '../config/config.php';
require_once __DIR__ . '/../config/database.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../libs/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer.php';
require_once __DIR__ . '/../libs/SMTP.php';

$recaptchaSecret = RECAPTCHA_SECRET;

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$action) {
    echo json_encode(['ok' => false, 'error' => 'bad_request', 'msg' => 'Método o petición no permitida.']);
    exit;
}

function verifyCaptcha($token) {
    if (!$token) return false;
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = ['secret' => RECAPTCHA_SECRET, 'response' => $token];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) return false;
    
    $res = json_decode($result, true);
    return $res['success'] ?? false;
}

if ($action === 'request') {
    $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $captchaToken = $input['captcha_token'] ?? '';

    if (!$email) {
        echo json_encode(['ok' => false, 'error' => 'invalid_email', 'msg' => 'El formato del correo electrónico no es válido.']);
        exit;
    }

    if (!verifyCaptcha($captchaToken)) {
        echo json_encode(['ok' => false, 'error' => 'captcha_failed', 'msg' => 'Por favor, completa la verificación humana.']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, nombre, email FROM usuarios WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['ok' => true, 'msg' => 'Si el correo está registrado, recibirás un código de recuperación pronto.']);
        exit;
    }

    $code = sprintf("%06d", random_int(0, 999999));
    
    $tokenHash = hash('sha256', $code);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
    $stmt->execute(['email' => $email]);

    $stmt = $pdo->prepare("INSERT INTO password_resets (email, token_hash, expires_at) VALUES (:email, :token_hash, :expires_at)");
    $stmt->execute([
        'email'      => $email,
        'token_hash' => $tokenHash,
        'expires_at' => $expiresAt
    ]);


    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_USER, 'PortalCore Seguridad');
        $mail->addAddress($email, $user['nombre'] ?? 'Usuario'); 

        $mail->isHTML(true);
        $mail->Subject = 'Código de verificación – PortalCore';
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; background-color: #f6f7fb; padding: 40px; color: #1e2230;'>
                <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #2563eb;'>
                    <h2 style='color: #1e3a8a; margin-top: 0;'>Hola, " . htmlspecialchars($user['nombre'] ?? 'Usuario') . "</h2>
                    <p style='font-size: 14px; line-height: 1.6; color: #4b5563;'>Recibimos una solicitud para restablecer la contraseña de tu cuenta en PortalCore.</p>
                    <p style='font-size: 14px; line-height: 1.6; color: #4b5563;'>Introduce el siguiente código de seguridad en la página web. Este código tiene una <strong>validez de 15 minutos</strong>:</p>
                    
                    <div style='text-align: center; margin: 30px 0; background-color: #f3f4f6; padding: 20px; border-radius: 8px;'>
                        <span style='font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #1e3a8a;'>{$code}</span>
                    </div>
                    
                    <p style='font-size: 12px; color: #9ca3af; line-height: 1.5;'>Si tú no realizaste esta solicitud, puedes ignorar este correo de forma segura.</p>
                    <hr style='border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                    <p style='font-size: 11px; color: #9ca3af; text-align: center;'>&copy; " . date('Y') . " PortalCore. Sistema de identidad.</p>
                </div>
            </div>
        ";

        $mail->send();

        echo json_encode(['ok' => true, 'msg' => 'Si el correo está registrado, recibirás un código de recuperación pronto.']);
        exit;

    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'error' => 'mail_failed', 'msg' => 'El código se generó pero el correo no pudo ser enviado: ' . $mail->ErrorInfo]);
        exit;
    }
}

if ($action === 'reset') {
    $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $code = $input['code'] ?? '';
    $password = $input['password'] ?? '';

    if (!$email || strlen($code) !== 6 || strlen($password) < 8) {
        echo json_encode(['ok' => false, 'error' => 'invalid_data', 'msg' => 'Datos incompletos o contraseña muy corta.']);
        exit;
    }

    $tokenHash = hash('sha256', $code);

    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE email = :email AND token_hash = :token_hash AND expires_at > NOW()");
    $stmt->execute([
        'email'      => $email,
        'token_hash' => $tokenHash
    ]);
    $resetRequest = $stmt->fetch();

    if (!$resetRequest) {
        echo json_encode(['ok' => false, 'error' => 'expired_token', 'msg' => 'El código de verificación es incorrecto o ha expirado.']);
        exit;
    }

    $passwordHashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = :password_hash WHERE email = :email");
    $stmt->execute([
        'password_hash' => $passwordHashed,
        'email'         => $email
    ]);

    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
    $stmt->execute(['email' => $email]);

    echo json_encode(['ok' => true, 'msg' => 'Contraseña actualizada con éxito. Redirigiendo al inicio de sesión.']);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'unknown_action', 'msg' => 'Acción no reconocida.']);
?>