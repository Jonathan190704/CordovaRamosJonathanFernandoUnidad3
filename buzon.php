<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PortalCore – Buzón de contacto seguro con validación perimetral avanzada." />
    <title>Buzón de Contacto – PortalCore</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet" />

    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { display: ['Sora', 'sans-serif'], body: ['DM Sans', 'sans-serif'] },
            colors: {
              brand: {
                50: '#f0f4ff', 100: '#dde8ff', 200: '#b8ccff', 500: '#4f6ef7',
                600: '#3b55e6', 700: '#2a3fcb', 800: '#1e2fa8', 900: '#131f7a', 950: '#0c1455'
              }
            }
          }
        }
      };
    </script>

    <style>
      *, *::before, *::after { box-sizing: border-box; }
      html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
      body { font-family: 'DM Sans', sans-serif; background: #f6f7fb; color: #1e2230; }
      h1, h2 { font-family: 'Sora', sans-serif; }

      .skip-link { position: absolute; top: -100%; left: 1rem; background: #131f7a; color: #fff; padding: 0.5rem 1rem; border-radius: 0 0 8px 8px; font-weight: 600; font-size: 0.875rem; z-index: 9999; transition: top 0.2s; }
      .skip-link:focus { top: 0; }

      #toast-container { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; pointer-events: none; }
      .toast { display: flex; align-items: flex-start; gap: 0.75rem; background: #fff; border: 1px solid #e5e7eb; border-left: 4px solid #4f6ef7; border-radius: 10px; padding: 0.875rem 1.125rem; box-shadow: 0 8px 32px rgba(30, 34, 48, 0.12); min-width: 280px; max-width: 360px; pointer-events: all; animation: toastIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
      .toast.toast--success { border-left-color: #22c55e; }
      .toast.toast--error   { border-left-color: #ef4444; }
      .toast.toast--warning { border-left-color: #f59e0b; }
      .toast__icon { font-size: 1.25rem; margin-top: 1px; flex-shrink: 0; }
      .toast--success .toast__icon { color: #22c55e; }
      .toast--error   .toast__icon { color: #ef4444; }
      .toast--warning .toast__icon { color: #f59e0b; }
      .toast--info    .toast__icon { color: #4f6ef7; }
      .toast__content { flex: 1; }
      .toast__title { font-family: 'Sora', sans-serif; font-size: 0.875rem; font-weight: 700; color: #1e2230; }
      .toast__msg   { font-size: 0.8125rem; color: #6b7280; margin-top: 2px; }
      .toast__close { background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 1rem; padding: 0; line-height: 1; transition: color 0.15s; flex-shrink: 0; }
      .toast__close:hover { color: #4b5563; }

      @keyframes toastIn { from { opacity: 0; transform: translateX(2rem) scale(0.95); } to { opacity: 1; transform: none; } }
      @keyframes toastOut { from { opacity: 1; transform: none; } to { opacity: 0; transform: translateX(2rem) scale(0.9); } }
      .toast--exiting { animation: toastOut 0.25s ease forwards; }

      /* Estilos de Validación de Campos */
      .field-error input, .field-error textarea { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.12); }
      .field-error .field-msg { color: #ef4444; }
      .field-success input, .field-success textarea { border-color: #22c55e !important; box-shadow: 0 0 0 3px rgba(34,197,94,0.12); }
      .field-success .field-msg { color: #22c55e; }
      .field-msg { font-size: 0.75rem; margin-top: 4px; display: flex; align-items: center; gap: 4px; transition: color 0.2s; }

      .hero-gradient { background: linear-gradient(135deg, #0c1455 0%, #1e2fa8 40%, #2a3fcb 70%, #3b55e6 100%); position: relative; overflow: hidden; }
      :focus-visible { outline: 2px solid #4f6ef7; outline-offset: 3px; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

  <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

  <header class="bg-white border-b border-gray-200 shadow-sm" role="banner">
    <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between gap-4">
      <a href="index.html" class="flex items-center gap-2 flex-shrink-0" aria-label="PortalCore – Volver al inicio">
        <div class="bg-brand-600 text-white p-2 rounded-lg flex items-center justify-center w-10 h-10 shadow-md" aria-hidden="true">
          <i class="fa-solid fa-cubes text-xl"></i>
        </div>
        <span class="font-bold text-xl tracking-tight text-brand-900" style="font-family:'Sora',sans-serif">
          Portal<span class="text-brand-600">Core</span>
        </span>
      </a>
      <div>
        <a href="index.html" class="text-sm font-semibold bg-gray-100 text-gray-700 px-5 py-2 rounded-full hover:bg-gray-200 transition-colors shadow-sm focus-visible:ring-2 focus-visible:ring-brand-500">
          Volver al Inicio
        </a>
      </div>
    </div>
  </header>

  <main id="main-content" class="flex-grow hero-gradient flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 sm:p-8 transition-all duration-300">
      
      <div id="pane-contact">
        <div class="text-center mb-6">
          <div class="w-12 h-12 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-3 text-xl" aria-hidden="true">
            <i class="fa-solid fa-inbox"></i>
          </div>
          <h1 class="text-2xl font-bold text-brand-900">Buzón de Contacto</h1>
          <p class="text-gray-500 text-xs mt-1.5 leading-relaxed">Envíanos tus dudas o comentarios. Tu comunicación cuenta con validación perimetral de seguridad.</p>
        </div>

        <form id="form-buzon" novalidate class="space-y-4">
          
          <div id="field-nombre">
            <label for="user-name" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre completo</label>
            <input type="text" id="user-name" required placeholder="Escribe tu nombre" autocomplete="name" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition text-sm" aria-describedby="nombre-msg" />
            <p id="nombre-msg" class="field-msg" aria-live="polite"></p>
          </div>

          <div id="field-email">
            <label for="user-email" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
            <input type="email" id="user-email" required placeholder="correo@ejemplo.com" autocomplete="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition text-sm" aria-describedby="email-msg" />
            <p id="email-msg" class="field-msg" aria-live="polite"></p>
          </div>
          
          <input type="text" id="user-website" style="display:none;" autocomplete="off">

          <div id="field-mensaje">
            <label for="user-message" class="block text-sm font-medium text-gray-700 mb-1.5">Mensaje</label>
            <textarea id="user-message" required rows="4" placeholder="¿En qué podemos ayudarte?" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition text-sm resize-none" aria-describedby="mensaje-msg"></textarea>
            <p id="mensaje-msg" class="field-msg" aria-live="polite"></p>
          </div>

          <button type="submit" id="btn-submit" class="w-full bg-brand-600 text-white font-bold py-3 rounded-xl hover:bg-brand-700 transition shadow-md text-sm focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
            Enviar mensaje seguro
          </button>
        </form>
      </div>

    </div>
  </main>

  <footer class="bg-gray-950 text-gray-600 text-xs py-6 text-center border-t border-gray-800" role="contentinfo">
    &copy; 2026 PortalCore. Protección y diseño adaptativo.
  </footer>

  <div id="toast-container" aria-live="polite" aria-atomic="false" role="status"></div>

  <script>
const Toast = (() => {
  const ICONS = {
    success: '<i class="fa-solid fa-circle-check text-lg" aria-hidden="true"></i>',
    error:   '<i class="fa-solid fa-circle-xmark text-lg" aria-hidden="true"></i>',
    warning: '<i class="fa-solid fa-triangle-exclamation text-lg" aria-hidden="true"></i>',
    info:    '<i class="fa-solid fa-circle-info text-lg" aria-hidden="true"></i>',
  };
  function show(type = 'info', title = '', message = '', duration = 4500) {
    const container = document.getElementById('toast-container');
    const el = document.createElement('div');
    el.className = `toast toast--${type}`;
    el.setAttribute('role', 'alert');
    el.innerHTML = `${ICONS[type] || ICONS.info}<div class="toast__content flex-1"><p class="font-bold text-sm">${title}</p>${message ? `<p class="text-xs opacity-90">${message}</p>` : ''}</div><button class="toast__close opacity-70 hover:opacity-100 p-1 transition"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>`;
    el.querySelector('.toast__close').addEventListener('click', () => dismiss(el));
    container.appendChild(el);
    if (duration > 0) setTimeout(() => dismiss(el), duration);
  }
  function dismiss(el) {
    if (!el || el.classList.contains('toast--exiting')) return;
    el.classList.add('toast--exiting');
    el.addEventListener('animationend', () => el.remove(), { once: true });
  }
  return { show };
})();

const Validators = {
  email(value) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value.trim()); },
  text(value) { return value.trim().length >= 3; }
};

const FieldUI = {
  setError(wrapId, msgId, text) {
    const wrap = document.getElementById(wrapId);
    const msg  = document.getElementById(msgId);
    if (!wrap || !msg) return;
    wrap.className = wrap.className.replace(/field-error|field-success/g, '').trim();
    wrap.classList.add('field-error');
    msg.innerHTML = `<i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> ${text}`;
  },
  setSuccess(wrapId, msgId) {
    const wrap = document.getElementById(wrapId);
    const msg  = document.getElementById(msgId);
    if (!wrap || !msg) return;
    wrap.className = wrap.className.replace(/field-error|field-success/g, '').trim();
    wrap.classList.add('field-success');
    msg.innerHTML = '';
  },
  clear(wrapId, msgId) {
    const wrap = document.getElementById(wrapId);
    const msg  = document.getElementById(msgId);
    if (wrap) wrap.className = wrap.className.replace(/field-error|field-success/g, '').trim();
    if (msg) msg.innerHTML = '';
  }
};

document.addEventListener('DOMContentLoaded', () => {

  const nombreInput  = document.getElementById('user-name');
  const emailInput   = document.getElementById('user-email');
  const mensajeInput = document.getElementById('user-message');
  const websiteInput = document.getElementById('user-website');

  // Validaciones en tiempo real (Blur)
  if(nombreInput) {
    nombreInput.addEventListener('blur', () => {
      if (!nombreInput.value.trim()) FieldUI.setError('field-nombre', 'nombre-msg', 'El nombre es requerido.');
      else if (!Validators.text(nombreInput.value)) FieldUI.setError('field-nombre', 'nombre-msg', 'Ingresa al menos 3 caracteres.');
      else FieldUI.setSuccess('field-nombre', 'nombre-msg');
    });
  }

  if(emailInput) {
    emailInput.addEventListener('blur', () => {
      if (!emailInput.value.trim()) FieldUI.setError('field-email', 'email-msg', 'El correo electrónico es requerido.');
      else if (!Validators.email(emailInput.value)) FieldUI.setError('field-email', 'email-msg', 'Ingresa un correo electrónico válido.');
      else FieldUI.setSuccess('field-email', 'email-msg');
    });
  }

  if(mensajeInput) {
    mensajeInput.addEventListener('blur', () => {
      if (!mensajeInput.value.trim()) FieldUI.setError('field-mensaje', 'mensaje-msg', 'El mensaje no puede estar vacío.');
      else if (mensajeInput.value.trim().length < 10) FieldUI.setError('field-mensaje', 'mensaje-msg', 'Escribe un mensaje detallado (mínimo 10 caracteres).');
      else FieldUI.setSuccess('field-mensaje', 'mensaje-msg');
    });
  }

  document.getElementById('form-buzon').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nombre = nombreInput.value.trim();
    const email = emailInput.value.trim();
    const mensaje = mensajeInput.value.trim();
    const website = websiteInput.value; 

    if (!Validators.text(nombre)) {
      FieldUI.setError('field-nombre', 'nombre-msg', 'Verifica tu nombre.');
      return;
    }
    if (!Validators.email(email)) {
      FieldUI.setError('field-email', 'email-msg', 'Verifica tu correo.');
      return;
    }
    if (mensaje.length < 10) {
      FieldUI.setError('field-mensaje', 'mensaje-msg', 'El mensaje es demasiado corto.');
      return;
    }

    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Transmitiendo datos…';

    try {
      const response = await fetch('api/procesar_buzon.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre, correo: email, website, mensaje })
      });
      const data = await response.json();

      if(data.ok) {
        Toast.show('success', 'Mensaje Enviado', data.msg);
        document.getElementById('form-buzon').reset();
        FieldUI.clear('field-nombre', 'nombre-msg');
        FieldUI.clear('field-email', 'email-msg');
        FieldUI.clear('field-mensaje', 'mensaje-msg');
      } else {
        Toast.show('error', 'Error de Validación', data.msg);
      }
    } catch {
      Toast.show('error', 'Fallo del Sistema', 'No se pudo establecer conexión segura con el servidor de correo.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Enviar mensaje seguro';
    }
  });
});
  </script>
</body>
</html>