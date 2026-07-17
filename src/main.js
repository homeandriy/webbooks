import jQuery from 'jquery';
import * as bootstrap from 'bootstrap';
import { initBootstrapBridge } from '../assets/js/legacy-adapter.js';

window.jQuery = jQuery;
window.$ = jQuery;
window.bootstrap = bootstrap;

initBootstrapBridge(bootstrap);

import '../assets/css/material-design-icons.min.css';
import 'font-awesome/css/font-awesome.min.css';
import '@fancyapps/ui/dist/fancybox/fancybox.css';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../style.css';

import 'slick-carousel/slick/slick.js';
import '../assets/js/compat-layer.js';
import '../assets/js/ajax-client.js';
import '../assets/js/custom.js';
import '../assets/js/theme.js';
