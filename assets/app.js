import './bootstrap.js';
import './styles/app.css';

console.log('[zoom] app.js chargé');


function bindZoom() {
  const dlg = document.getElementById('zoomDlg');
  const img = document.getElementById('zoomImg');
  if (!dlg || !img) return;

  // Ouvrir au clic sur tout élément avec data-zoom-src
  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-zoom-src]');
    if (trigger) {
      const src = trigger.getAttribute('data-zoom-src');
      if (!src) return;
      img.src = src;
      if (typeof dlg.showModal === 'function') dlg.showModal();
      else dlg.setAttribute('open', '');
      return;
    }
    // Bouton Fermer
    if (e.target.closest('[data-zoom-close]')) {
      dlg.close();
    }
  });

  // Fermer si clic sur le backdrop
  dlg.addEventListener('click', (e) => {
    if (e.target === dlg) dlg.close();
  });

  // Fermer avec Échap
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && dlg.open) dlg.close();
  });
}

// --- Ces lignes DOIVENT être hors de la fonction ---
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', bindZoom);
} else {
  bindZoom();
}
// Si tu utilises Symfony UX Turbo :
document.addEventListener('turbo:load', bindZoom);
