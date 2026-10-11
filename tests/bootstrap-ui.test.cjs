const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const {JSDOM} = require('jsdom');
const tick = () => new Promise(resolve => setTimeout(resolve, 35));

test('the actual responsive sidebar closes through its close button and Escape', async () => {
    const template = fs.readFileSync('sidebar.php', 'utf8').replace(/<\?php[\s\S]*?\?>/g, '');
    const dom = new JSDOM('<button id="trigger" data-bs-toggle="offcanvas" data-bs-target="#webbooks-sidebar">Menu</button>' + template, {pretendToBeVisual: true});
    const {window} = dom;
    const saved = {};
    for (const name of ['window', 'document', 'Element', 'HTMLElement', 'Node', 'Event', 'CustomEvent', 'ShadowRoot', 'getComputedStyle']) {
        saved[name] = Object.getOwnPropertyDescriptor(global, name);
        Object.defineProperty(global, name, {value: name === 'getComputedStyle' ? window.getComputedStyle.bind(window) : (window[name] || window), configurable: true});
    }
    try {
        const Offcanvas = require('bootstrap/js/dist/offcanvas');
        const trigger = window.document.getElementById('trigger');
        // jsdom has no layout; Bootstrap restores focus only to visible triggers.
        trigger.getClientRects = () => [{width: 44, height: 44}];
        const sidebar = window.document.getElementById('webbooks-sidebar');
        trigger.click();
        await tick();
        assert.ok(sidebar.classList.contains('show'));
        assert.equal(sidebar.getAttribute('role'), 'dialog');
        assert.equal(window.document.activeElement, sidebar);
        sidebar.querySelector('[data-bs-dismiss="offcanvas"]').click();
        await tick();
        assert.equal(sidebar.classList.contains('show'), false);
        assert.equal(window.document.querySelector('.offcanvas-backdrop'), null);
        assert.equal(window.document.activeElement, trigger);
        trigger.click();
        await tick();
        sidebar.dispatchEvent(new window.KeyboardEvent('keydown', {key: 'Escape', bubbles: true}));
        await tick();
        assert.equal(sidebar.classList.contains('show'), false);
        Offcanvas.getInstance(sidebar).dispose();
    } finally {
        dom.window.close();
        for (const [name, descriptor] of Object.entries(saved)) {
            if (descriptor) Object.defineProperty(global, name, descriptor);
            else delete global[name];
        }
    }
});
