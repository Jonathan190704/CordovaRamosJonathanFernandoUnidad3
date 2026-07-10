<?php
require_once 'config/auth.php';
// verificarAcceso();  <--- COMENTA ESTA LÍNEA TEMPORALMENTE
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
    <title>Panel de Usuario (Asíncrono) - Corekit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glow-dynamic {
            position: relative;
        }
        .glow-dynamic::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle 120px at var(--mouse-x, 0px) var(--mouse-y, 0px), rgba(59, 130, 246, 0.12), transparent 80%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 1;
            border-radius: inherit;
        }
        .glow-dynamic:hover::before {
            opacity: 1;
        }

        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(24px); 
                filter: blur(4px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
                filter: blur(0); 
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }

        .btn-elastic:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen font-sans antialiased selection:bg-blue-500 selection:text-white">

    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <header class="bg-blue-900 text-white shadow-md transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col md:flex-row items-center justify-between gap-4">
            
            <div class="flex items-center gap-2 group cursor-pointer">
                <i class="fa-solid fa-layer-group text-2xl text-blue-400 group-hover:rotate-12 transition-transform duration-300"></i>
                <span class="text-xl font-bold tracking-wider group-hover:text-blue-200 transition-colors">Corekit Portal</span>
                <span class="bg-blue-500/30 text-blue-300 text-[10px] uppercase font-bold px-2 py-0.5 rounded border border-blue-500/40 animate-pulse">Asíncrono</span>
            </div>

            <div class="relative w-full md:w-64 transition-all duration-500 ease-in-out focus-within:md:w-80">
                <input id="search-input" type="text" placeholder="Buscar en el sitio... (Presiona Esc)" class="w-full bg-blue-800 text-sm text-white placeholder-blue-300 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-blue-950 transition-all shadow-inner">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-blue-300 text-sm"></i>
            </div>

            <nav class="flex flex-wrap items-center gap-5 text-sm font-medium">
                <a href="dashboard.php" class="text-blue-300 hover:text-white transition-colors flex items-center gap-1"><i class="fa-solid fa-house"></i> Inicio</a>
                <a href="dashboard_sincrono.php" class="btn-elastic bg-amber-500 hover:bg-amber-600 text-gray-900 px-3 py-1.5 rounded-lg transition-all duration-300 font-bold flex items-center gap-1 shadow hover:shadow-amber-500/20 hover:-translate-y-0.5">
                    <i class="fa-solid fa-flask animate-bounce"></i> Laboratorio Síncrono
                </a>
                <a href="#" class="hover:text-blue-300 transition-colors flex items-center gap-1"><i class="fa-solid fa-chart-line"></i> Servicios</a>
                <a href="#" class="hover:text-blue-300 transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-envelope"></i> Buzón 
                    <span id="buzon-badge" class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full ml-1 transition-all duration-500">2</span>
                </a>
                <a href="api/logout.php" class="btn-elastic bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg transition-all font-semibold flex items-center gap-1 shadow hover:shadow-red-600/30">
                    <i class="fa-solid fa-right-from-bracket"></i> Salir
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 py-8">
        <div id="main-panel" class="animate-fade-in-up bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8 glow-dynamic transition-all duration-500 hover:shadow-md">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 transition-colors hover:text-blue-900">
                        ¡Bienvenido/a de nuevo, <?php echo $_SESSION['user_name']; ?>! 
                    </h1>
                    <p class="text-gray-600">Has iniciado sesión de forma totalmente segura. Desde aquí puedes gestionar tu información.</p>
                </div>
                <div class="bg-gray-100 px-4 py-2 rounded-lg border border-gray-200 text-right group hover:border-blue-300 transition-all duration-300">
                    <span class="text-xs text-gray-500 block uppercase font-bold tracking-wider group-hover:text-blue-500 transition-colors">Hora del Sistema</span>
                    <span id="live-clock" class="text-sm font-mono text-blue-900 font-bold tracking-widest">00:00:00</span>
                </div>
            </div>

            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800 flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-lg text-blue-600 mt-0.5 animate-pulse"></i>
                <div>
                    <strong class="block mb-1">Entorno de Demostración Asíncrona (No Bloqueante)</strong>
                    A diferencia del laboratorio síncrono, los procesos de esta página (como la verificación de la API abajo o las respuestas del chat) corren en segundo plano utilizando promesas (`async/await`). Puedes hacer click en "Verificar API" y verás que el **Reloj del Sistema** y las animaciones siguen avanzando fluidamente sin congelarse.
                </div>
            </div>

            <div id="status-bar" class="mb-6 p-3 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-between text-xs text-emerald-800 transition-all duration-300 hover:bg-emerald-100/50">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Todos los sistemas operativos de Corekit funcionan correctamente.</span>
                </div>
                <button id="refresh-status-btn" class="btn-elastic hover:underline font-bold flex items-center gap-1 transition-all"><i class="fa-solid fa-rotate"></i> Verificar API</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="dashboard-card animate-fade-in-up delay-100 glow-dynamic p-5 border border-gray-200 rounded-lg shadow-sm transition-all duration-500 cubic-bezier(0.16, 1, 0.3, 1) cursor-pointer bg-white hover:-translate-y-2 hover:shadow-xl hover:border-blue-400" data-title="Buzón">
                    <div class="text-blue-600 text-2xl mb-2 flex justify-between items-center relative z-10">
                        <i class="fa-solid fa-inbox transition-transform duration-300 group-hover:scale-110"></i>
                        <span class="text-[10px] text-gray-400 font-normal">Doble click para fijar</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1 relative z-10">Tu Buzón</h3>
                    <p class="text-xs text-gray-500 relative z-10">Revisa las alertas internas y mensajes enviados por el administrador.</p>
                </div>

                <div class="dashboard-card animate-fade-in-up delay-200 glow-dynamic p-5 border border-gray-200 rounded-lg shadow-sm transition-all duration-500 cubic-bezier(0.16, 1, 0.3, 1) cursor-pointer bg-white hover:-translate-y-2 hover:shadow-xl hover:border-emerald-400" data-title="Centro de Ayuda">
                    <div class="text-emerald-600 text-2xl mb-2 flex justify-between items-center relative z-10">
                        <i class="fa-solid fa-life-ring"></i>
                        <span class="text-[10px] text-gray-400 font-normal">Doble click para fijar</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1 relative z-10">Centro de Ayuda</h3>
                    <p class="text-xs text-gray-500 relative z-10">Preguntas frecuentes, guías de usuario y documentación del portal.</p>
                </div>

                <div class="dashboard-card animate-fade-in-up delay-300 glow-dynamic p-5 border border-gray-200 rounded-lg shadow-sm transition-all duration-500 cubic-bezier(0.16, 1, 0.3, 1) cursor-pointer bg-white hover:-translate-y-2 hover:shadow-xl hover:border-amber-400" data-title="Seguridad">
                    <div class="text-amber-600 text-2xl mb-2 flex justify-between items-center relative z-10">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span class="text-[10px] text-gray-400 font-normal">Doble click para fijar</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1 relative z-10">Seguridad</h3>
                    <p class="text-xs text-gray-500 relative z-10">Configuración de credenciales y enlace para recuperación de contraseña.</p>
                </div>

            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-gray-400 text-xs py-6 border-t border-gray-700 mt-auto">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>&copy; 2026 Corekit Inc. Todos los derechos reservados bajo principios de protección de datos.</div>
            <div class="flex gap-4">
                <a href="#" class="hover:text-white transition-colors">Inicio</a> • 
                <a href="dashboard_sincrono.php" class="hover:text-amber-400 transition-colors font-semibold">Laboratorio Síncrono</a> • 
                <a href="#" class="hover:text-white transition-colors">Buzón de Mensajes</a> • 
                <a href="#" class="hover:text-white transition-colors">Ayuda Legal</a>
            </div>
        </div>
    </footer>

    <div class="fixed bottom-5 right-5 z-40">
        <button id="chat-btn" class="btn-elastic bg-blue-600 hover:bg-blue-700 text-white p-3.5 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center hover:scale-110 hover:rotate-6">
            <i class="fa-solid fa-comments text-xl"></i>
        </button>
        
        <div id="chat-window" class="opacity-0 scale-90 translate-y-10 pointer-events-none transform transition-all duration-500 cubic-bezier(0.16, 1, 0.3, 1) absolute bottom-16 right-0 w-72 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
            <div class="bg-blue-900 text-white p-3 font-bold text-sm flex justify-between items-center">
                <span><i class="fa-solid fa-robot mr-1"></i> Soporte Corekit (Async)</span>
                <button id="close-chat" class="hover:text-red-400 transition-colors"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div id="chat-messages" class="p-4 h-48 overflow-y-auto text-xs text-gray-600 flex flex-col gap-2 scroll-smooth">
                <p class="bg-gray-100 p-2 rounded-lg self-start mr-auto max-w-[85%] shadow-sm">¡Hola! ¿En qué podemos ayudarte con el mapa del sitio o tu buzón?</p>
            </div>
            
            <form id="chat-form" class="p-2 border-t border-gray-100 flex gap-1">
                <input id="chat-input" type="text" placeholder="Escribe aquí..." class="w-full text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all" required>
                <button type="submit" class="btn-elastic bg-blue-600 text-white px-3 rounded text-xs transition-colors hover:bg-blue-700"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const chatBtn = document.getElementById('chat-btn');
        const chatWindow = document.getElementById('chat-window');
        const closeChat = document.getElementById('close-chat');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');
        const cards = document.querySelectorAll('.dashboard-card');
        const liveClock = document.getElementById('live-clock');
        const buzonBadge = document.getElementById('buzon-badge');
        const refreshStatusBtn = document.getElementById('refresh-status-btn');
        const toastContainer = document.getElementById('toast-container');

        const glowElements = document.querySelectorAll('.glow-dynamic');
        glowElements.forEach(elem => {
            elem.addEventListener('mousemove', (e) => {
                const rect = elem.getBoundingClientRect();
                const x = e.clientX - rect.left; 
                const y = e.clientY - rect.top;
                elem.style.setProperty('--mouse-x', `${x}px`);
                elem.style.setProperty('--mouse-y', `${y}px`);
            });
        });

        function actualizarReloj() {
            const ahora = new Date();
            liveClock.innerText = ahora.toLocaleTimeString('es-ES');
        }
        setInterval(actualizarReloj, 1000);
        actualizarReloj();

        function mostrarToastNotificacion(titulo, mensaje, tipo = 'info') {
            const toast = document.createElement('div');
            toast.className = "transform translate-x-full opacity-0 transition-all duration-500 cubic-bezier(0.16, 1, 0.3, 1) p-3 rounded-lg shadow-xl text-xs flex flex-col gap-1 w-64 border pointer-events-auto bg-white";
            
            if (tipo === 'success') toast.className += " border-emerald-200 bg-emerald-50 text-emerald-900";
            else if (tipo === 'warn') toast.className += " border-amber-200 bg-amber-50 text-amber-900";
            else toast.className += " border-blue-200 bg-blue-50 text-blue-900";

            toast.innerHTML = `<strong>${titulo}</strong><span>${mensaje}</span>`;
            toastContainer.appendChild(toast);

            toast.offsetHeight;
            toast.classList.remove('translate-x-full', 'opacity-0');

            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }

        function agregarMensajeAlDOM(texto, esUsuario) {
            const wrapper = document.createElement('p');
            wrapper.innerText = texto;
            wrapper.className = esUsuario 
                ? "bg-blue-500 text-white p-2 rounded-lg text-left self-end ml-auto max-w-[85%] shadow-sm transform scale-75 opacity-0 transition-all duration-300"
                : "bg-gray-100 text-gray-800 p-2 rounded-lg self-start mr-auto max-w-[85%] shadow-sm transform scale-75 opacity-0 transition-all duration-300";
            
            chatMessages.appendChild(wrapper);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            setTimeout(() => {
                wrapper.classList.remove('scale-75', 'opacity-0');
            }, 50);
        }

        async function simularVerificacionAPI() {
            refreshStatusBtn.disabled = true;
            refreshStatusBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Conectando...';
            
            try {
                await new Promise(resolve => setTimeout(resolve, 1500));
                mostrarToastNotificacion("API Corekit", "Conexión exitosa. Base de datos sincronizada de forma asíncrona.", "success");
            } catch (err) {
                mostrarToastNotificacion("Error de red", "No se pudo conectar al clúster.", "warn");
            } finally {
                refreshStatusBtn.disabled = false;
                refreshStatusBtn.innerHTML = '<i class="fa-solid fa-rotate mr-0.5"></i> Verificar API';
            }
        }
        refreshStatusBtn.addEventListener('click', simularVerificacionAPI);

        async function simularMensajeBuzonEntrante() {
            await new Promise(resolve => setTimeout(resolve, 3000));
            buzonBadge.innerText = "3";
            buzonBadge.classList.add('scale-125', 'bg-red-600');
            mostrarToastNotificacion("Nuevo Mensaje", "Has recibido una nueva notificación en tu buzón.", "info");
            setTimeout(() => buzonBadge.classList.remove('scale-125'), 300);
        }
        simularMensajeBuzonEntrante();

        async function consultarBotAsincrono(mensajeDelUsuario) {
            await new Promise(resolve => setTimeout(resolve, 1200));
            const consulta = mensajeDelUsuario.toLowerCase();
            if (consulta.includes('hola')) return "¡Hola de nuevo! ¿Te sirvieron las nuevas herramientas visuales?";
            if (consulta.includes('ayuda')) return "El Centro de Ayuda técnico se encuentra disponible las 24 horas.";
            return "Entendido. He procesado tu consulta mediante la cola asíncrona del portal sin congelar tus animaciones.";
        }

        cards.forEach(card => {
            card.addEventListener('dblclick', () => {
                const titulo = card.getAttribute('data-title');
                const estaFijado = card.classList.toggle('ring-2');
                card.classList.toggle('ring-blue-500');
                card.classList.toggle('bg-blue-50/20');
                
                if (estaFijado) {
                    mostrarToastNotificacion("Tarjeta Fijada", `Anclaste la sección ${titulo} al inicio de tu panel.`, "success");
                } else {
                    mostrarToastNotificacion("Tarjeta Desanclada", `Removiste el anclaje de ${titulo}.`, "info");
                }
            });
        });

        function toggleChat() {
            chatWindow.classList.toggle('opacity-0');
            chatWindow.classList.toggle('scale-90');
            chatWindow.classList.toggle('translate-y-10');
            chatWindow.classList.toggle('pointer-events-none');
        }
        chatBtn.addEventListener('click', toggleChat);
        closeChat.addEventListener('click', toggleChat);

        chatBtn.classList.add('animate-bounce');
        setTimeout(() => chatBtn.classList.remove('animate-bounce'), 3000);

        document.getElementById('search-input').addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                e.target.value = '';
                mostrarToastNotificacion("Búsqueda limpia", "Se cancelaron los filtros de búsqueda.", "info");
            }
        });

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const mensaje = chatInput.value.trim();
            if (!mensaje) return;

            agregarMensajeAlDOM(mensaje, true);
            chatInput.value = '';

            const indicadorEscribiendo = document.createElement('p');
            indicadorEscribiendo.id = 'typing-indicator';
            indicadorEscribiendo.innerText = 'Soporte Corekit está respondiendo...';
            indicadorEscribiendo.className = 'text-gray-400 italic text-[10px] animate-pulse self-start ml-1';
            chatMessages.appendChild(indicadorEscribiendo);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                const respuestaDelServidor = await consultarBotAsincrono(mensaje);
                document.getElementById('typing-indicator')?.remove();
                agregarMensajeAlDOM(respuestaDelServidor, false);
            } catch (error) {
                document.getElementById('typing-indicator')?.remove();
            }
        });
    });
    </script>
</body>
</html>