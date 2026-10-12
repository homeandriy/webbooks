import {registerAjaxClient} from '../assets/js/ajax-client.js';
import {initializeDownload} from '../assets/js/ajax-filter.js';
import {initializeSearchUi} from '../assets/js/search-ui.js';
import {initializeCookieConsent} from './cookie-consent.js';

registerAjaxClient(window);
initializeSearchUi();
initializeDownload();
initializeCookieConsent();
