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

test('header account and mobile offcanvas retain an aligned, scrollable layout', () => {
    const header = fs.readFileSync('header.php', 'utf8');
    const styles = fs.readFileSync('assets/css/bootstrap5-theme.css', 'utf8');
    const sidebar = fs.readFileSync('sidebar.php', 'utf8');
    const functions = fs.readFileSync('functions.php', 'utf8');

    assert.match(header, /class="dropdown messages-menu header-account d-none d-md-flex"/);
    assert.match(header, /class="header-account__label"/);
    assert.match(styles, /\.header-account \{ align-items: center; white-space: nowrap; \}/);
    assert.match(styles, /height: 100dvh; max-height: 100dvh/);
    assert.match(styles, /flex: 1 1 auto; min-height: 0; overflow-y: auto/);
    assert.match(sidebar, /Polylang::translatedPostUrl\( WEBBOOKS_PORTFOLIO_PAGE_ID \)/);
    assert.match(functions, /const WEBBOOKS_PORTFOLIO_PAGE_ID = 846;/);
    assert.match(sidebar, />Andrii Beznosko</);
});

test('mobile search modal identifies and focuses its search field after opening', () => {
    const template = fs.readFileSync('template-parts/forms/mobile-search-modal.php', 'utf8');
    const uiBootstrap = fs.readFileSync('src/bootstrap-ui.js', 'utf8');
    const styles = fs.readFileSync('assets/css/search-ui.css', 'utf8');

    assert.match(template, /data-webbooks-mobile-search-input/);
    assert.match(uiBootstrap, /shown\.bs\.modal/);
    assert.match(uiBootstrap, /input\.focus\(\{preventScroll: true\}\)/);
    assert.match(styles, /\.mobile-search-form \.main-search:focus/);
    assert.match(styles, /\.search-results, #mobile-search-modal \.search-result-item \{ background: #fff; \}/);
});
