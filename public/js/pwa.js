(() => {
  if ('serviceWorker' in navigator && location.protocol === 'https:') {
    window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(() => {}));
  }
  const button = document.getElementById('pwa-install');
  const help = document.getElementById('pwa-install-help');
  if (!button) return;
  let promptEvent;
  window.addEventListener('beforeinstallprompt', event => {
    event.preventDefault(); promptEvent = event; button.hidden = false;
  });
  button.addEventListener('click', async () => {
    if (!promptEvent) return;
    promptEvent.prompt();
    await promptEvent.userChoice;
    promptEvent = null; button.hidden = true;
  });
  window.addEventListener('appinstalled', () => { button.hidden = true; });
  if (!('onbeforeinstallprompt' in window) && help) help.hidden = false;
})();
