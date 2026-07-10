<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../libs/Exception.php';
require '../libs/PHPMailer.php';
require '../libs/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido."]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!empty($input['website'])) {
    echo json_encode(["ok" => true, "msg" => "¡Mensaje enviado con éxito!"]);
    exit;
}

$nombre  = htmlspecialchars(strip_tags(trim($input['nombre'] ?? '')));
$correo  = filter_var(trim($input['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
$mensaje = htmlspecialchars(strip_tags(trim($input['mensaje'] ?? '')));

if (empty($nombre) || empty($correo) || empty($mensaje)) {
    echo json_encode(["ok" => false, "msg" => "Todos los campos son obligatorios."]);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["ok" => false, "msg" => "El formato del correo es inválido."]);
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';                    
    $mail->SMTPAuth   = true;                                 
    $mail->Username   = SMTP_USER;              
    $mail->Password   = SMTP_PASS;                
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       
    $mail->Port       = 587;                                  
    $mail->CharSet    = 'UTF-8';


    $mail->setFrom('jonahost257@gmail.com', 'PortalCore Web'); 
    $mail->addAddress('jonahost257@gmail.com');               
    $mail->addReplyTo($correo, $nombre);                      

   
    $mail->isHTML(true);                                      
    $mail->Subject = "=?UTF-8?B?" . base64_encode("Nuevo mensaje: Buzón de PortalCore") . "?=";
    
    $cuerpoHTML = '
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Notificación PortalCore</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: \'Segoe UI\', Helvetica, Arial, sans-serif; background-color: #f6f7fb; color: #1e2230;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f6f7fb; padding: 40px 0;">
            <tr>
                <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06); border: 1px solid #e5e7eb;">
                        
                        <tr>
                            <td align="left" style="background: linear-gradient(135deg, #0c1455 0%, #1e2fa8 100%); padding: 32px; text-align: left;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td>
                                            <span style="font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                                Portal<span style="color: #4f6ef7;">Core</span>
                                            </span>
                                        </td>
                                        <td align="right">
                                            <span style="font-size: 11px; color: #dde8ff; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; background-color: rgba(255,255,255,0.1); padding: 6px 12px; border-radius: 20px;">
                                                Buzón Web
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding: 40px 32px;">
                                <h2 style="margin: 0 0 16px 0; font-size: 20px; font-weight: 700; color: #131f7a;">¡Has recibido un nuevo mensaje!</h2>
                                <p style="margin: 0 0 32px 0; font-size: 14px; line-height: 1.6; color: #6b7280;">Un usuario ha completado el formulario de contacto de tu portal perimetral seguro. A continuación los detalles de la transmisión:</p>
                                
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f0f4ff; border-radius: 12px; padding: 20px; margin-bottom: 28px;">
                                    <tr>
                                        <td style="padding-bottom: 12px; width: 30%; font-size: 13px; font-weight: 700; color: #131f7a; text-transform: uppercase; letter-spacing: 0.5px;">Remitente:</td>
                                        <td style="padding-bottom: 12px; font-size: 14px; color: #1e2230; font-weight: 600;">' . $nombre . '</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px; font-weight: 700; color: #131f7a; text-transform: uppercase; letter-spacing: 0.5px;">E-mail:</td>
                                        <td><a href="mailto:' . $correo . '" style="font-size: 14px; color: #3b55e6; text-decoration: none; font-weight: 600;">' . $correo . '</a></td>
                                    </tr>
                                </table>

                                <h3 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Contenido del Mensaje:</h3>
                                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-left: 4px solid #3b55e6; border-radius: 8px; padding: 20px; font-size: 14px; line-height: 1.6; color: #374151; font-style: italic; white-space: pre-line;">
                                    ' . $mensaje . '
                                </div>
                                
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 32px;">
                                    <tr>
                                        <td align="center">
                                            <a href="mailto:' . $correo . '" style="display: inline-block; background-color: #3b55e6; color: #ffffff; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 28px; border-radius: 10px; box-shadow: 0 4px 12px rgba(59, 85, 230, 0.2); transition: background-color 0.2s;">
                                                Responder Correo Directamente
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="background-color: #0c1455; padding: 24px 32px; text-align: center; border-top: 1px solid #e5e7eb;">
                                <p style="margin: 0; font-size: 11px; color: #a5b4fc; font-weight: 500;">
                                    &copy; 2026 PortalCore. Sistema automatizado de mensajería cifrada.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    $mail->Body = $cuerpoHTML;

    $cuerpoTexto  = "Nuevo mensaje desde el Buzón de tu Web:\n\n";
    $cuerpoTexto .= "Nombre: $nombre\n";
    $cuerpoTexto .= "Correo: $correo\n\n";
    $cuerpoTexto .= "Mensaje:\n$mensaje\n";
    $mail->AltBody = $cuerpoTexto;

    $mail->send();
    echo json_encode(["ok" => true, "msg" => "¡Mensaje enviado! Nos pondremos en contacto pronto."]);

} catch (Exception $e) {
    echo json_encode(["ok" => false, "msg" => "No se pudo enviar el correo. Error: {$mail->ErrorInfo}"]);
}
?>