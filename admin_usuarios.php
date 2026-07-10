<?php
require_once 'config/auth.php';
verificarAcceso(['admin']);

require_once 'config/database.php';

try 
    $stmtUsers = $pdo->query("SELECT u.id, u.nombre, u.email, u.rol_id, r.nombre_rol 
                              FROM usuarios u 
                              INNER JOIN roles r ON u.rol_id = r.id 
                              ORDER BY u.id DESC");
    $usuarios = $stmtUsers->fetchAll();

    $stmtRoles = $pdo->query("SELECT id, nombre_rol FROM roles");
    $rolesDisponibles = $stmtRoles->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Admin</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f172a; color: white; }
        
        .glow-sync-card {
            position: relative;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.75rem;
            overflow: hidden;
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

        .btn-elastic { transition: transform 0.1s; }
        .btn-elastic:active { transform: scale(0.96); }
    </style>
</head>
<body class="p-8">

    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8 animate-fade-in-up">
            <h1 class="text-3xl font-bold text-amber-500"><i class="fa-solid fa-users-gear mr-2"></i> Gestión de Roles</h1>
            <a href="dashboard.php" class="text-slate-400 hover:text-white transition"><i class="fa-solid fa-arrow-left mr-1"></i> Volver</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="cards-container">
            
            <?php 
            $delay = 100;
            foreach ($usuarios as $user): 
                $animClass = 'delay-' . ($delay > 300 ? 300 : $delay);
            ?>
            
            <div class="glow-sync-card p-6 animate-fade-in-up <?= $animClass ?>">
                <div class="relative z-10">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-amber-500/20 text-amber-500 flex items-center justify-center text-xl font-bold mr-4">
                            <?= strtoupper(substr($user['nombre'], 0, 1)) ?>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-100"><?= htmlspecialchars($user['nombre']) ?></h2>
                            <p class="text-sm text-slate-400"><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-2">Rol del sistema</label>
                        <form onsubmit="actualizarRol(event, <?= $user['id'] ?>)" class="flex gap-2">
                            <select id="rol_<?= $user['id'] ?>" class="flex-1 bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5">
                                <?php foreach ($rolesDisponibles as $rol): ?>
                                    <option value="<?= $rol['id'] ?>" <?= ($rol['id'] == $user['rol_id']) ? 'selected' : '' ?>>
                                        <?= ucfirst($rol['nombre_rol']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <button type="submit" class="btn-elastic bg-amber-600 hover:bg-amber-500 text-white font-medium rounded-lg text-sm px-4 py-2 text-center transition">
                                <i class="fa-solid fa-save"></i>
                            </button>
                        </form>
                        <p id="msg_<?= $user['id'] ?>" class="text-xs mt-2 hidden"></p>
                    </div>
                </div>
            </div>

            <?php 
                $delay += 100;
            endforeach; 
            ?>
        </div>
    </div>

    <script>
        document.querySelectorAll('.glow-sync-card').forEach(card => {
            card.addEventListener('mousemove', e => {
                const rect = card.getBoundingClientRect();
                card.style.setProperty('--mouse-x', `${e.clientX - rect.left}px`);
                card.style.setProperty('--mouse-y', `${e.clientY - rect.top}px`);
            });
        });

        async function actualizarRol(event, usuarioId) {
            event.preventDefault(); 
            
            const selectElement = document.getElementById(`rol_${usuarioId}`);
            const nuevoRolId = selectElement.value;
            const msgElement = document.getElementById(`msg_${usuarioId}`);
            
            try {
                const response = await fetch('api/actualizar_rol.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_usuario: usuarioId,
                        nuevo_rol_id: nuevoRolId
                    })
                });

                const data = await response.json();

                msgElement.classList.remove('hidden', 'text-green-400', 'text-red-400');
                if (data.ok) {
                    msgElement.innerText = "¡Rol actualizado con éxito!";
                    msgElement.classList.add('text-green-400', 'block');
                } else {
                    msgElement.innerText = data.error || "Hubo un error al actualizar.";
                    msgElement.classList.add('text-red-400', 'block');
                }
                
                setTimeout(() => msgElement.classList.add('hidden'), 3000);

            } catch (error) {
                console.error('Error:', error);
                alert("Error de conexión con el servidor.");
            }
        }
    </script>
</body>
</html>