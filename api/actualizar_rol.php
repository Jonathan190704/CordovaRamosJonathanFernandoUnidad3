<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../config/auth.php';
verificarAcceso(['admin']);

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id_usuario']) || !isset($input['nuevo_rol_id'])) {
    echo json_encode(['ok' => false, 'error' => 'Faltan datos requeridos.']);
    exit;
}

$id_usuario = intval($input['id_usuario']);
$nuevo_rol_id = intval($input['nuevo_rol_id']);

if ($id_usuario === $_SESSION['usuario_id']) {
    echo json_encode(['ok' => false, 'error' => 'No puedes cambiar tu propio rol desde aquí.']);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE usuarios SET rol_id = ? WHERE id = ?');
    $stmt->execute([$nuevo_rol_id, $id_usuario]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['ok' => true, 'msg' => 'Rol actualizado correctamente.']);
    } else {
        echo json_encode(['ok' => false, 'error' => 'No se realizaron cambios.']);
    }

} catch (PDOException $e) {
    error_log('Error al actualizar rol: ' . $e->getMessage());
    echo json_encode(['ok' => false, 'error' => 'Error interno del servidor.']);
}
?>