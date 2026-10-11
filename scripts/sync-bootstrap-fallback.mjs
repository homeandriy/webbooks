import {copyFileSync, mkdirSync} from 'node:fs';

const destination = new URL('../assets/vendor/bootstrap/', import.meta.url);
mkdirSync(destination, {recursive: true});
for (const file of ['css/bootstrap.min.css', 'js/bootstrap.bundle.min.js']) {
  const output = new URL(file.split('/').pop(), destination);
  copyFileSync(new URL('../node_modules/bootstrap/dist/' + file, import.meta.url), output);
}
// These local backups are used only when the Vite manifest is unavailable.
// Normal requests load the npm imports compiled into the Vite bundle.
