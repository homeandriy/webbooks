import Collapse from 'bootstrap/js/dist/collapse';
import Dropdown from 'bootstrap/js/dist/dropdown';
import Modal from 'bootstrap/js/dist/modal';
import Offcanvas from 'bootstrap/js/dist/offcanvas';
import Tab from 'bootstrap/js/dist/tab';

// Delegated data-bs controls also support markup inserted by AJAX.
window.WebBooksUI = {
  showModal(selector) {
    const element = document.querySelector(selector);
    if (element) Modal.getOrCreateInstance(element).show();
  },
  showCollapse(selector) {
    const element = document.querySelector(selector);
    if (element) Collapse.getOrCreateInstance(element, {toggle: false}).show();
  },
};
export {Collapse, Dropdown, Modal, Offcanvas, Tab};
