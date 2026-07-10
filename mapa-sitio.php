<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa del Sitio - PortalCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-portal-dark { background-color: #0f172a; } 
        .bg-portal-blue { background-color: #3b82f6; } 
        .text-portal-blue { color: #3b82f6; }
    </style>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col">

    <header class="w-full">
        <div class="bg-portal-dark text-gray-300 text-xs py-2 px-4 flex justify-between items-center hidden md:flex">
            <div class="flex space-x-4 items-center">
                <a href="ayuda.html" class="hover:text-white transition"><i class="fa-regular fa-circle-question mr-1"></i> Ayuda / FAQ</a>
                <a href="contactanos.html" class="hover:text-white transition"><i class="fa-regular fa-envelope mr-1"></i> Contáctanos</a>
                <a href="mapa-sitio.php" class="text-white border-b border-white pb-0.5"><i class="fa-solid fa-sitemap mr-1"></i> Mapa del sitio</a>
                <a href="recuperar.html" class="text-gray-400 hover:text-white transition italic">¿Olvidaste tu contraseña?</a>
            </div>
            <div class="flex space-x-4 items-center">
                <a href="buzon.php" class="hover:text-white transition relative flex items-center">
                    <i class="fa-regular fa-bell mr-1"></i> Buzón
                    <span class="absolute -top-2 -right-3 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center">3</span>
                </a>
                <span class="text-gray-600">|</span>
                <a href="#" class="hover:text-white transition"><i class="fa-regular fa-comments mr-1"></i> Soporte en vivo</a>
            </div>
        </div>

        <div class="bg-white border-b border-gray-200 py-3 px-6 flex justify-between items-center shadow-sm">
            <div class="flex items-center space-x-2">
                <div class="bg-portal-blue text-white p-2 rounded-lg shadow-sm">
                    <i class="fa-solid fa-cubes text-lg"></i>
                </div>
                <a href="index.html" class="text-xl font-bold text-gray-800 tracking-tight">Portal<span class="text-portal-blue">Core</span></a>
            </div>

            <nav class="hidden lg:flex space-x-6 text-gray-600 font-medium text-sm">
                <a href="index.html" class="hover:text-portal-blue transition pb-1">Inicio</a>
                <a href="#" class="hover:text-portal-blue transition pb-1">Servicios</a>
                <a href="#" class="hover:text-portal-blue transition pb-1">Blog</a>
                <a href="#" class="hover:text-portal-blue transition pb-1">Nosotros</a>
            </nav>

            <div class="flex items-center space-x-4">
                <a href="api/login.php" class="text-gray-600 font-medium hover:text-portal-blue transition text-sm hidden sm:block">Iniciar sesión</a>
                <a href="api/registro.php" class="bg-portal-blue hover:bg-blue-600 text-white font-semibold px-5 py-2 rounded-full text-sm shadow-md transition">Registrarse</a>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-6xl w-full mx-auto py-12 px-6">
        <div class="mb-10 text-center md:text-left">
            <h1 class="text-4xl font-black text-gray-800 tracking-tight mb-2">Mapa del Sitio</h1>
            <p class="text-gray-500 text-lg">Explora la arquitectura modular y el árbol de directorios de la plataforma.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center space-x-3 mb-4 text-blue-600">
                    <i class="fa-solid fa-house-laptop text-xl"></i>
                    <h2 class="text-xl font-bold text-gray-800">Núcleo Central</h2>
                </div>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li><a href="index.html" class="hover:text-portal-blue flex items-center"><i class="fa-solid fa-angle-right text-xs mr-2 text-gray-400"></i> Página de Inicio <span class="ml-2 text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">index</span></a></li>
                    <li><a href="#" class="hover:text-portal-blue flex items-center text-gray-400 pointer-events-none"><i class="fa-solid fa-angle-right text-xs mr-2 text-gray-300"></i> Servicios (Próximamente)</a></li>
                    <li><a href="#" class="hover:text-portal-blue flex items-center text-gray-400 pointer-events-none"><i class="fa-solid fa-angle-right text-xs mr-2 text-gray-300"></i> Blog Corporativo</a></li>
                </ul>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center space-x-3 mb-4 text-emerald-600">
                    <i class="fa-solid fa-user-shield text-xl"></i>
                    <h2 class="text-xl font-bold text-gray-800">Acceso y Seguridad</h2>
                </div>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li><a href="index.html" class="hover:text-portal-blue flex items-center"><i class="fa-solid fa-angle-right text-xs mr-2 text-gray-400"></i> Iniciar Sesión <span class="ml-2 text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">login.php</span></a></li>
                    <li><a href="index.html" class="hover:text-portal-blue flex items-center"><i class="fa-solid fa-angle-right text-xs mr-2 text-gray-400"></i> Registro de Usuarios <span class="ml-2 text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">registro</span></a></li>
                    <li><a href="recuperar.html" class="hover:text-portal-blue flex items-center"><i class="fa-solid fa-angle-right text-xs mr-2 text-gray-400"></i> Recuperar Acceso (Vista) <span class="ml-2 text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">recuperar</span></a></li>
                </ul>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center space-x-3 mb-4 text-purple-600">
                    <i class="fa-regular fa-comments text-xl"></i>
                    <h2 class="text-xl font-bold text-gray-800">Soporte y Atención</h2>
                </div>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li><a href="ayuda.html" class="hover:text-portal-blue flex items-center"><i class="fa-solid fa-angle-right text-xs mr-2 text-gray-400"></i> Centro de Ayuda / FAQ <span class="ml-2 text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">ayuda</span></a></li>
                    <li><a href="contactanos.html" class="hover:text-portal-blue flex items-center"><i class="fa-solid fa-angle-right text-xs mr-2 text-gray-400"></i> Formulario de Contacto <span class="ml-2 text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">contactanos</span></a></li>
                    <li><a href="buzon.php" class="hover:text-portal-blue flex items-center"><i class="fa-solid fa-angle-right text-xs mr-2 text-gray-400"></i> Buzón de Mensajes <span class="ml-2 text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">buzon</span></a></li>
                    <li><a href="index.html" class="hover:text-portal-blue flex items-center"><i class="fa-solid fa-angle-right text-xs mr-2 text-gray-400"></i> Chat de Soporte <span class="ml-2 text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">chat</span></a></li>
                </ul>
            </div>

        </div>

        <div class="mt-12 bg-blue-50 border border-blue-100 rounded-2xl p-4 flex items-start space-x-3">
            <i class="fa-solid fa-circle-info text-portal-blue mt-0.5"></i>
            <p class="text-xs text-blue-800 leading-relaxed">
                Este mapa del sitio se actualiza dinámicamente según los despliegues en tu rama de desarrollo. Las rutas que pertenecen al directorio <code class="bg-white px-1 py-0.5 rounded border font-mono">/api</code> cuentan con validación de sesión perimetral activa.
            </p>
        </div>
    </main>

    <div class="fixed bottom-6 right-6 z-50">
        <button class="bg-portal-blue hover:bg-blue-600 text-white w-14 h-14 rounded-full shadow-2xl transition transform hover:scale-105 active:scale-95 flex items-center justify-center">
            <i class="fa-solid fa-robot text-2xl"></i>
        </button>
    </div>

</body>
</html>