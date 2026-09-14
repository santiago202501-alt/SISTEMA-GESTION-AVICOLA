/**
 * SIGA - JavaScript principal
 */
'use strict';

// ── Sidebar toggle (mobile) ──────────────────────────────────
const sidebar       = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');

if (sidebarToggle) {
  sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
  });
  // Cerrar al hacer clic fuera
  document.addEventListener('click', (e) => {
    if (sidebar.classList.contains('open') &&
        !sidebar.contains(e.target) &&
        e.target !== sidebarToggle) {
      sidebar.classList.remove('open');
    }
  });
}

// ── Auto-dismiss alerts Bootstrap ───────────────────────────
document.querySelectorAll('.alert-dismissible').forEach(el => {
  setTimeout(() => {
    const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
    if (bsAlert) bsAlert.close();
  }, 5000);
});

// ── Confirmación de eliminación ──────────────────────────────
document.querySelectorAll('[data-confirm]').forEach(btn => {
  btn.addEventListener('click', e => {
    if (!confirm(btn.dataset.confirm || '¿Confirmar acción?')) {
      e.preventDefault();
    }
  });
});

// ── Utilidad: formatear número colombiano ───────────────────
function formatNum(n, dec = 0) {
  return Number(n).toLocaleString('es-CO', {
    minimumFractionDigits: dec,
    maximumFractionDigits: dec
  });
}

// ── Colores por defecto para Chart.js ───────────────────────
const CHART_COLORS = {
  blue:   'rgba(42,82,152,.82)',
  green:  'rgba(46,125,82,.82)',
  amber:  'rgba(230,126,34,.82)',
  red:    'rgba(192,57,43,.80)',
  teal:   'rgba(13,148,136,.80)',
  blueLt: 'rgba(61,110,181,.50)',
};

// ── Defaults globales Chart.js ───────────────────────────────
if (typeof Chart !== 'undefined') {
  Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
  Chart.defaults.font.size   = 12;
  Chart.defaults.color       = '#64748b';
  Chart.defaults.plugins.legend.labels.usePointStyle = true;
  Chart.defaults.plugins.legend.labels.padding = 16;
}
