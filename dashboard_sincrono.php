<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demostración Síncrona - Corekit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glow-sync-card {
            position: relative;
        }
        .glow-sync-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle 120px at var(--mouse-x, 0px) var(--mouse-y, 0px), rgba(245, 158, 11, 0.12), transparent 80%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 1;
            border-radius: inherit;
        }
        .glow-sync-card:hover::before {
            opacity: 1;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); filter: blur(4px); }
            to { opacity: 1; transform: translateY(0); filter: blur(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }

        .btn-elastic:active { transform: scale(0.96); }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen font-sans antialiased selection:bg-amber-500 selection:text-white">

    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <header class="bg-blue-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-layer-group text-2xl text-amber-400 animate-spin" style="animation-duration: 4s;"></i>
                <span class="text-xl font-bold tracking-wider">Corekit Lab: Modo Síncrono</span>
            </div>
            <nav class="flex flex-wrap items-center gap-5 text-sm font-medium">
                <a href="dashboard.php" class="btn-elastic bg-blue-800 hover:bg-blue-700 px-3 py-1.5 rounded-lg transition-all flex items-center gap-1 shadow">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Dashboard
                </a>
                <a href="api/logout.php" class="btn-elastic bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg transition-all font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-right-from-bracket"></i> Salir
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 py-8">
        <div id="main-panel" class="animate-fade-in-up bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8 glow-sync-card transition-shadow duration-500 hover:shadow-md mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">¿Qué es una Función Síncrona?</h2>
                    <p class="text-gray-600 max-w-3xl">Es una ejecución lineal y bloqueante. Mientras corre, el navegador suspende todas las actualizaciones de la interfaz y eventos del cursor.</p>
                </div>
                <div class="bg-gray-100 px-4 py-2 rounded-lg border border-gray-200 text-right min-w-[140px]">
                    <span class="text-xs text-gray-500 block uppercase font-bold tracking-wider">Reloj en Vivo</span>
                    <span id="live-clock" class="text-sm font-mono text-red-600 font-bold tracking-widest">00:00:00</span>
                </div>
            </div>

            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-900 flex items-start gap-3 transition-all duration-300 hover:bg-amber-100/30">
                <i class="fa-solid fa-triangle-exclamation text-xl text-amber-600 animate-pulse"></i>
                <div>
                    <strong>Instrucciones del Experimento:</strong> Haz clic en el botón amarillo. De inmediato, intenta pasar el mouse sobre las 3 tarjetas de abajo o pulsa el botón azul. ¡Verás que las transiciones visuales dejan de responder por completo durante el ciclo!
                </div>
            </div>

            <div class="bg-gray-900 text-gray-100 rounded-xl p-6 border border-gray-800 shadow-2xl">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2"><i class="fa-solid fa-flask text-amber-400"></i> Consola del Procesador</h3>
                
                <div class="flex flex-col sm:flex-row gap-4 items-center mb-4">
                    <button id="btn-sync-heavy" class="btn-elastic w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold px-6 py-3 rounded-lg shadow-lg transition-all duration-300 hover:shadow-amber-500/20">
                        <i class="fa-solid fa-hourglass-start"></i> Bloquear Hilo Principal (3s)
                    </button>
                    <button id="btn-test-click" class="btn-elastic w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-lg transition-colors">
                        Mover cursor / Probar Click aquí
                    </button>
                </div>

                <div class="bg-black/60 p-4 rounded-lg font-mono text-xs text-emerald-400 h-28 overflow-y-auto border border-gray-800 transition-all" id="console-log">
                    [Corekit System] Esperando inicio de bloqueo...
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="dashboard-card animate-fade-in-up delay-100 glow-sync-card p-5 border border-gray-200 rounded-lg bg-white shadow-sm transition-all duration-500 hover:-translate-y-2 hover:shadow-xl hover:border-amber-500 cursor-pointer">
                <div class="text-blue-600 text-2xl mb-2"><i class="fa-solid fa-list-ol"></i></div>
                <h4 class="font-bold text-gray-800 mb-1">1. Flujo Lineal</h4>
                <p class="text-xs text-gray-500">Cada instrucción se encola. Hasta que no termine la línea actual, el hilo no procesará el movimiento del ratón.</p>
            </div>
            <div class="dashboard-card animate-fade-in-up delay-200 glow-sync-card p-5 border border-gray-200 rounded-lg bg-white shadow-sm transition-all duration-500 hover:-translate-y-2 hover:shadow-xl hover:border-amber-500 cursor-pointer">
                <div class="text-red-600 text-2xl mb-2"><i class="fa-solid fa-ban"></i></div>
                <h4 class="font-bold text-gray-800 mb-1">2. Bloqueo Absoluto</h4>
                <p class="text-xs text-gray-500">La interfaz gráfica no se redibuja mientras la CPU procese un ciclo síncrono masivo.</p>
            </div>
            <div class="dashboard-card animate-fade-in-up delay-300 glow-sync-card p-5 border border-gray-200 rounded-lg bg-white shadow-sm transition-all duration-500 hover:-translate-y-2 hover:shadow-xl hover:border-amber-500 cursor-pointer">
                <div class="text-emerald-600 text-2xl mb-2"><i class="fa-solid fa-check-double"></i></div>
                <h4 class="font-bold text-gray-800 mb-1">3. Casos de Uso Útiles</h4>
                <p class="text-xs text-gray-500">Útil únicamente en procesos instantáneos y locales como transformaciones matemáticas cortas.</p>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-gray-400 text-xs py-6 border-t border-gray-700 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center sm:text-left">
            &copy; 2026 Corekit Lab. Demostración técnica de hilos de ejecución.
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const liveClock = document.getElementById('live-clock');
        const btnSyncHeavy = document.getElementById('btn-sync-heavy');
        const btnTestClick = document.getElementById('btn-test-click');
        const consoleLog = document.getElementById('console-log');
        const toastContainer = document.getElementById('toast-container');
        const syncGlowCards = document.querySelectorAll('.glow-sync-card');

        syncGlowCards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });

        function actualizarReloj() {
            const ahora = new Date();
            liveClock.innerText = ahora.toLocaleTimeString('es-ES');
        }
        setInterval(actualizarReloj, 1000);
        actualizarReloj();

        function registrarLog(texto) {
            const p = document.createElement('p');
            p.innerText = `[${new Date().toLocaleTimeString()}] ${texto}`;
            consoleLog.appendChild(p);
            consoleLog.scrollTop = consoleLog.scrollHeight;
        }

        function mostrarToast(titulo, mensaje) {
            const toast = document.createElement('div');
            toast.className = "p-3 rounded-lg shadow-2xl text-xs flex flex-col gap-1 w-64 border bg-white text-amber-900 border-amber-200 transition-all duration-300 transform scale-100";
            toast.innerHTML = `<strong>${titulo}</strong><span>${mensaje}</span>`;
            toastContainer.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function ejecutarCicloSincrono() {
            registrarLog(">>> HILO PRINCIPAL BLOQUEADO. Intenta interactuar ahora...");
            
            const detenerseEn = Date.now() + 3000;
            while (Date.now() < detenerseEn) {
            }

            registrarLog(">>> Hilo liberado con éxito.");
            mostrarToast("Laboratorio Corekit", "La interfaz vuelve a responder de forma fluida.");
        }

        btnSyncHeavy.addEventListener('click', () => {
            btnSyncHeavy.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Hilo Congelado...';
            setTimeout(() => {
                ejecutarCicloSincrono();
                btnSyncHeavy.innerHTML = '<i class="fa-solid fa-hourglass-start"></i> Bloquear Hilo Principal (3s)';
            }, 40);
        });

        btnTestClick.addEventListener('click', () => {
            registrarLog("Interacción normal detectada en botón azul.");
        });
    });
    </script>
</body>
</html>