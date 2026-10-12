import 'bootstrap/dist/css/bootstrap.min.css';
import '../assets/css/font-awesome.min.css';
import 'highlight.js/styles/github-dark.css';
import '../style.css';
import '../assets/css/search-ui.css';
import './bootstrap-ui.js';
import './carousels.js';
import './author-support.js';
import {registerAjaxClient} from '../assets/js/ajax-client.js';
import {initializeDownload} from '../assets/js/ajax-filter.js';
import {initializeSearchUi} from '../assets/js/search-ui.js';
import '../assets/css/bootstrap5-theme.css';
import '../assets/css/navigation-ui.css';
import hljs from 'highlight.js/lib/common';
// inc/assets.php enqueues compat-layer, AJAX client, functions and search before this module.
registerAjaxClient(window);
initializeSearchUi();
initializeDownload();
document.querySelectorAll('pre[class*="language-"], pre code[class*="language-"]').forEach((codeBlock) => {
    hljs.highlightElement(codeBlock);
});
