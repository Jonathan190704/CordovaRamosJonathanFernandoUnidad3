<?php

try {
    $database_conectada = false; 
} catch (PDOException $e) {
    die("Error de conexión seguro.");
}

$busqueda_original = isset($_GET['q']) ? trim($_GET['q']) : '';

$resultados = [];

if (!empty($busqueda_original)) {
    
    if (isset($pdo) && $database_conectada) {
    
    
        $sql = "SELECT titulo, descripcion, url FROM paginas WHERE titulo LIKE :query OR descripcion LIKE :query LIMIT 10";
        $stmt = $pdo->prepare($sql);
        
        $termino_busqueda = "%" . $busqueda_original . "%";
        $stmt->execute(['query' => $termino_busqueda]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $datos_prueba = [
            ['titulo' => 'Panel de Control Principal', 'descripcion' => 'Accede al dashboard avanzado de administración de PortalCore.', 'url' => 'dashboard.php'],
            ['titulo' => 'Configuración de Seguridad', 'descripcion' => 'Ajustes de módulos perimetrales y administración de credenciales.', 'url' => 'config/seguridad.php'],
            ['titulo' => 'Manual de Usuario / FAQ', 'descripcion' => 'Preguntas frecuentes sobre el uso de la infraestructura inteligente.', 'url' => 'ayuda.html']
            
            ];
        
        foreach ($datos_prueba as $item) {
            if (stripos($item['titulo'], $busqueda_original) !== false || stripos($item['descripcion'], $busqueda_original) !== false) {
                $resultados[] = $item;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda - PortalCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col">

    <header class="bg-white border-b border-gray-200 py-3 px-6 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-2">
            <div class="bg-blue-500 text-white p-2 rounded-lg"><i class="fa-solid fa-cubes"></i></div>
            <a href="index.html" class="text-xl font-bold text-gray-800">Portal<span class="text-blue-500">Core</span></a>
        </div>
        <a href="index.html" class="text-sm text-gray-600 hover:text-blue-500 transition"><i class="fa-solid fa-arrow-left mr-1"></i> Volver</a>
    </header>

    <main class="flex-grow max-w-4xl w-full mx-auto py-12 px-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">
                Resultados para: 
                <span class="text-blue-600 font-extrabold">"<?php echo htmlspecialchars($busqueda_original, ENT_QUOTES, 'UTF-8'); ?>"</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">Hemos encontrado <?php echo count($resultados); ?> coincidencia(s).</p>
        </div>

<div class="space-y-4">
    <?php if (!empty($resultados)): ?>
        <?php foreach ($resultados as $resultado): ?>
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                <h2 class="text-lg font-bold text-gray-800 hover:text-blue-500 transition">
                    <a href="<?php echo htmlspecialchars($resultado['url'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($resultado['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    <?php echo htmlspecialchars($resultado['descripcion'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <a href="<?php echo htmlspecialchars($resultado['url'], ENT_QUOTES, 'UTF-8'); ?>" class="text-xs text-blue-500 font-semibold inline-flex items-center mt-3 hover:underline">
                    Ir al recurso <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
                </a>
            </div>
        <?php endforeach; ?> <?php else: ?>
        <div class="text-center py-12 bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
            <div class="text-gray-300 text-5xl mb-3"><i class="fa-solid fa-box-open"></i></div>
            <p class="text-gray-600 font-medium">No encontramos ningún módulo o página con ese criterio.</p>
            <p class="text-xs text-gray-400 mt-1">Intenta usar palabras clave diferentes o revisa la ortografía.</p>
        </div>
    <?php endif; ?>
</div>
    </main>

</body>
</html>