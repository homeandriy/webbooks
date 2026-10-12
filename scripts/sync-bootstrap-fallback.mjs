import {copyFileSync, mkdirSync} from 'node:fs';

const destination = new URL('../assets/vendor/bootstrap/', import.meta.url);
mkdirSync(destination, {recursive: true});
for (const file of ['css/bootstrap.min.css', 'js/bootstrap.bundle.min.js']) {
  const output = new URL(file.split('/').pop(), destination);
  copyFileSync(new URL('../node_modules/bootstrap/dist/' + file, import.meta.url), output);
}
// These local backups are used only when the Vite manifest is unavailable.
// Normal requests load the npm imports compiled into the Vite bundle.
// Keep Font Awesome 4.7 glyphs (including Telegram) available in the local fallback.
mkdirSync(new URL('../assets/css/', import.meta.url), {recursive: true});
mkdirSync(new URL('../assets/fonts/', import.meta.url), {recursive: true});
copyFileSync(new URL('../node_modules/font-awesome/css/font-awesome.min.css', import.meta.url), new URL('../assets/css/font-awesome.min.css', import.meta.url));
for (const file of ['FontAwesome.otf', 'fontawesome-webfont.eot', 'fontawesome-webfont.svg', 'fontawesome-webfont.ttf', 'fontawesome-webfont.woff', 'fontawesome-webfont.woff2']) {
  copyFileSync(new URL('../node_modules/font-awesome/fonts/' + file, import.meta.url), new URL('../assets/fonts/' + file, import.meta.url));
}
