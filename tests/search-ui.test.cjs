const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const {JSDOM} = require('jsdom');

const tick = (ms = 20) => new Promise((resolve) => setTimeout(resolve, ms));
const source = fs.readFileSync('assets/js/search-ui.js', 'utf8')
    .replace('export function initializeSearchUi', 'function initializeSearchUi') + '\ninitializeSearchUi();';

function dispatch(window, element, type) {
    element.dispatchEvent(new window.Event(type, {bubbles: true, cancelable: true}));
}

async function setup(url = 'https://books.test/?lang=uk') {
    const dom = new JSDOM(`<form id="main-search"><select id="category-main"><option value="">All</option><option value="fiction">Fiction</option></select>
        <select id="status-book"><option value="">Any</option><option value="beginner">Beginner</option></select>
        <select id="language"><option value="">Any</option><option value="en">English</option></select><button type="submit">Search</button><button type="reset">Reset</button></form>
        <form class="webbooks-search-form"><input class="main-search"><div id="search-result" class="search-results"></div></form>
        <p id="desktop-search-feedback" class="search-feedback" hidden></p>
        <form class="mobile-search-form"><input class="main-search" data-idres="search-result-mobile-modal"><button>Search</button></form>
        <div id="search-result-mobile-modal-wrap"><div id="search-result-mobile-modal" class="search-results"></div></div><p id="mobile-search-feedback" class="search-feedback" hidden></p>
        <div class="content-loop"><p>Initial catalog</p></div><div class="alm-btn-wrap"></div>`, {url, runScripts: 'outside-only'});
    const {window} = dom;
    window.matchMedia = () => ({matches: false});
    window.scrollTo = () => {};
    const calls = [];
    window.webbooksAjax = {admin_ajax: '/ajax', nonce: 'test', messages: {loading: 'Loading', found: 'Found: %d', error: 'Failed', expired: 'Expired', retry: 'Retry', reload: 'Reload'}};
    window.WebBooksAjax = {wpRequest(config) {
        return new Promise((resolve, reject) => calls.push({config, resolve, reject}));
    }};
    window.eval(source);
    await tick();
    return {dom, window, document: window.document, calls};
}

const success = (html, total = 1) => ({success: true, data: {html, total}});

test('category-only search can be submitted repeatedly; failures preserve results and allow retry', async () => {
    const {window, document, calls, dom} = await setup();
    document.querySelector('#category-main').value = 'fiction';
    dispatch(window, document.querySelector('#main-search'), 'submit');
    assert.equal(calls[0].config.var.language, '');
    assert.equal(document.querySelector('.content-loop').getAttribute('aria-busy'), 'true');
    calls[0].resolve(success('<p>Book A</p>', 5)); await tick();
    assert.equal(document.querySelector('#main-search [type="submit"]').disabled, false);
    dispatch(window, document.querySelector('#main-search'), 'submit');
    calls[1].reject(new Error('Offline')); await tick();
    assert.match(document.querySelector('.content-loop').textContent, /Book A/);
    assert.equal(document.querySelector('.content-loop').getAttribute('aria-busy'), 'false');
    assert.equal(document.querySelector('.search-error button').textContent, 'Retry');
    document.querySelector('.search-error button').click();
    calls[2].resolve(success('<p>Book B</p>')); await tick();
    assert.match(document.querySelector('.content-loop').textContent, /Book B/);
    dom.window.close();
});

test('three characters start search; shorter terms never bypass the threshold on submit', async () => {
    const {window, document, calls, dom} = await setup();
    const mobileForm = document.querySelector('.mobile-search-form');
    mobileForm.querySelector('input').value = 'JS';
    dispatch(window, mobileForm, 'submit');
    assert.equal(calls.length, 0);
    assert.equal(document.querySelector('#mobile-search-feedback').hidden, false);
    assert.match(document.querySelector('#mobile-search-feedback').textContent, /3 characters/);
    mobileForm.querySelector('input').value = 'PHP';
    dispatch(window, mobileForm, 'submit');
    assert.equal(calls[0].config.var.explicit, true);
    calls[0].resolve({success: false}); await tick();
    assert.equal(document.querySelector('#search-result-mobile-modal').getAttribute('aria-busy'), 'false');
    assert.equal(document.querySelector('#search-result-mobile-modal-wrap').classList.contains('search-is-loading'), false);
    assert.equal(document.querySelector('#search-result-mobile-modal button').textContent, 'Retry');
    dom.window.close();
});

test('desktop three-character queries send a request while shorter terms show the threshold', async () => {
    const {window, document, calls, dom} = await setup();
    const input = document.querySelector('.webbooks-search-form input');
    input.value = 'di';
    dispatch(window, input, 'input');
    await tick(350);

    assert.equal(calls.length, 0);
    assert.equal(document.querySelector('#desktop-search-feedback').hidden, false);
    assert.match(document.querySelector('#desktop-search-feedback').textContent, /3 characters/);
    assert.equal(document.querySelector('.webbooks-search-form').classList.contains('has-search-feedback'), true);
    input.value = 'диз';
    dispatch(window, input, 'input');
    await tick(350);
    assert.equal(calls.length, 1);
    assert.equal(document.querySelector('.webbooks-search-form').classList.contains('has-search-feedback'), false);
    calls[0].resolve(success('<p>Design result</p>'));
    await tick();
    assert.match(document.querySelector('#search-result').textContent, /Design result/);
    assert.equal(document.querySelector('.webbooks-search-form').classList.contains('open'), true);
    dom.window.close();
});

test('desktop suggestions become visible after a three-character query', async () => {
    const {window, document, calls, dom} = await setup();
    const input = document.querySelector('.webbooks-search-form input');
    input.value = 'PHP';
    dispatch(window, input, 'input');
    await tick(350);
    assert.equal(calls.length, 1);
    assert.equal(document.querySelector('.webbooks-search-form').classList.contains('open'), true);
    calls[0].resolve(success('<p>PHP result</p>'));
    await tick();
    assert.match(document.querySelector('#search-result').textContent, /PHP result/);
    dom.window.close();
});

test('desktop suggestions close when focus leaves the form or the user clicks away', async () => {
    const {window, document, calls, dom} = await setup();
    const input = document.querySelector('.webbooks-search-form input');
    input.value = 'PHP';
    dispatch(window, input, 'input');
    await tick(350);
    assert.equal(calls.length, 1);
    assert.equal(document.querySelector('.webbooks-search-form').classList.contains('open'), true);
    document.body.dispatchEvent(new window.Event('pointerdown', {bubbles: true}));
    assert.equal(document.querySelector('.webbooks-search-form').classList.contains('open'), false);
    input.value = 'PHP';
    dispatch(window, input, 'input');
    await tick(350);
    dispatch(window, input, 'focusout');
    await tick();
    assert.equal(document.querySelector('.webbooks-search-form').classList.contains('open'), false);
    dom.window.close();
});

test('clearing a search suppresses old responses and errors', async () => {
    const {window, document, calls, dom} = await setup();
    const form = document.querySelector('.webbooks-search-form');
    const input = form.querySelector('input');
    input.value = 'Books';
    dispatch(window, form, 'submit');
    input.value = '';
    dispatch(window, input, 'input');
    calls[0].resolve(success('<p>Stale book</p>')); await tick();
    assert.equal(document.querySelector('#search-result').textContent, '');
    assert.equal(form.classList.contains('open'), false);
    assert.equal(document.querySelector('#search-result').getAttribute('aria-busy'), 'false');
    dom.window.close();
});

test('filter pagination, shared URL and Back restore filters and original results', async () => {
    const {window, document, calls, dom} = await setup();
    document.querySelector('#category-main').value = 'fiction';
    dispatch(window, document.querySelector('#main-search'), 'submit');
    calls[0].resolve(success('<div class="ajax-pagination"><a href="#" data-page="2" data-ajax-action="main_search_on_site">2</a></div>')); await tick();
    document.querySelector('.ajax-pagination a').click();
    calls[1].resolve(success('<p>Page 2</p>')); await tick();
    assert.equal(new URL(window.location.href).searchParams.get('wb_page'), '2');
    assert.equal(new URL(window.location.href).searchParams.get('lang'), 'uk');
    window.history.replaceState(null, '', '?lang=uk&wb_mode=filter&wb_category=fiction');
    window.dispatchEvent(new window.PopStateEvent('popstate'));
    calls[2].resolve(success('<p>Page 1 again</p>')); await tick();
    assert.equal(calls[2].config.var.paged, 1);
    window.history.replaceState(null, '', '?lang=uk');
    window.dispatchEvent(new window.PopStateEvent('popstate')); await tick();
    assert.match(document.querySelector('.content-loop').textContent, /Initial catalog/);
    assert.equal(document.querySelector('#category-main').value, '');
    dom.window.close();
});

test('shared filter URL restores on initial load and reset clears optional filters', async () => {
    const {document, calls, dom} = await setup('https://books.test/?wb_mode=filter&wb_category=fiction&wb_language=en&wb_page=2');
    assert.equal(calls.length, 1);
    assert.equal(calls[0].config.var.language, 'en');
    assert.equal(calls[0].config.var.paged, 2);
    calls[0].resolve(success('<p>Filtered</p>')); await tick();
    document.querySelector('#main-search').reset(); await tick();
    assert.equal(calls[1].config.var.category, '');
    assert.equal(calls[1].config.var.language, '');
    dom.window.close();
});

test('a newer global search suppresses a pending filter response and its URL', async () => {
    const {window, document, calls, dom} = await setup();
    dispatch(window, document.querySelector('#main-search'), 'submit');
    const form = document.querySelector('.webbooks-search-form');
    form.querySelector('input').value = 'PHP';
    dispatch(window, form, 'submit');
    calls[1].resolve(success('<p>PHP books</p>')); await tick();
    calls[0].resolve(success('<p>Old catalog</p>')); await tick();
    assert.equal(new URL(window.location.href).searchParams.get('wb_mode'), 'search');
    assert.equal(document.querySelector('.content-loop').textContent, 'Initial catalog');
    assert.equal(document.querySelector('.content-loop').getAttribute('aria-busy'), 'false');
    assert.equal(document.querySelector('#main-search [type="submit"]').disabled, false);
    assert.match(document.querySelector('#search-result').textContent, /PHP books/);
    dom.window.close();
});

test('AJAX timeouts and expired sessions reject without a jQuery fallback', async () => {
    const dom = new JSDOM('', {url: 'https://books.test/', runScripts: 'outside-only'});
    const {window} = dom;
    let fallbackCalls = 0;
    window.jQuery = {ajax() { fallbackCalls += 1; }};
    window.fetch = (_url, options) => new Promise((_resolve, reject) => {
        options.signal.addEventListener('abort', () => reject(new window.DOMException('Timed out', 'AbortError')));
    });
    const ajaxSource = fs.readFileSync('assets/js/ajax-client.js', 'utf8')
        .replace('export function registerAjaxClient', 'function registerAjaxClient') + '\nregisterAjaxClient(window);';
    window.eval(ajaxSource);
    await assert.rejects(window.WebBooksAjax.request({url: '/ajax', timeout: 5}), {name: 'AbortError'});
    window.fetch = async () => ({ok: false, status: 403});
    await assert.rejects(window.WebBooksAjax.request({url: '/ajax'}), (error) => error.status === 403);
    assert.equal(fallbackCalls, 0);
    dom.window.close();
});
