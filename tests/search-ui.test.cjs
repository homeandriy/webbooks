const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const {JSDOM} = require('jsdom');
const jquery = require('jquery');
const tick = (ms = 20) => new Promise(resolve => setTimeout(resolve, ms));

async function setup(url = 'https://books.test/?lang=uk') {
    const dom = new JSDOM(`<form id="main-search"><select id="category-main"><option value="">All</option><option value="fiction">Fiction</option></select>
        <select id="status-book"><option value="">Any</option><option value="beginner">Beginner</option></select>
        <select id="language"><option value="">Any</option><option value="en">English</option></select><button type="submit">Search</button><button type="reset">Reset</button></form>
        <form class="webbooks-search-form"><input class="main-search"><div id="search-result" class="search-results"></div></form>
        <p id="desktop-search-feedback" class="search-feedback"></p>
        <form class="mobile-search-form"><input class="main-search" data-idres="search-result-mobile-modal"><button>Search</button></form>
        <div id="search-result-mobile-modal-wrap"><div id="search-result-mobile-modal" class="search-results"></div></div><p id="mobile-search-feedback" class="search-feedback"></p>
        <div class="content-loop"><p>Initial catalog</p></div><div class="alm-btn-wrap"></div>`, {url, runScripts: 'outside-only'});
    const window = dom.window;
    const $ = jquery(window);
    $.fx.off = true;
    window.jQuery = $;
    window.matchMedia = () => ({matches: false});
    const calls = [];
    window.webbooksAjax = {admin_ajax: '/ajax', nonce: 'test', messages: {loading: 'Loading', found: 'Found: %d', error: 'Failed', expired: 'Expired', retry: 'Retry', reload: 'Reload'}};
    window.WebBooksAjax = {wpRequest(config) {
        return new Promise((resolve, reject) => calls.push({config, resolve, reject}));
    }};
    window.eval(fs.readFileSync('assets/js/search-ui.js', 'utf8'));
    await tick();
    return {dom, window, $, calls};
}
const success = (html, total = 1) => ({success: true, data: {html, total}});

test('category-only search can be submitted repeatedly; failures preserve results and allow retry', async () => {
    const {$, calls, dom} = await setup();
    $('#category-main').val('fiction'); $('#main-search').trigger('submit');
    assert.equal(calls[0].config.var.language, '');
    assert.equal($('.content-loop').attr('aria-busy'), 'true');
    calls[0].resolve(success('<p>Book A</p>', 5)); await tick();
    assert.equal($('#main-search [type="submit"]').prop('disabled'), false);
    $('#main-search').trigger('submit'); calls[1].reject(new Error('Offline')); await tick();
    assert.match($('.content-loop').text(), /Book A/);
    assert.equal($('.content-loop').attr('aria-busy'), 'false');
    assert.equal($('.search-error button').text(), 'Retry');
    $('.search-error button').trigger('click'); calls[2].resolve(success('<p>Book B</p>')); await tick();
    assert.match($('.content-loop').text(), /Book B/);
    dom.window.close();
});

test('three characters start search; shorter terms never bypass the threshold on submit', async () => {
    const {$, calls, dom} = await setup();
    $('.mobile-search-form input').val('JS'); $('.mobile-search-form').trigger('submit');
    assert.equal(calls.length, 0);
    $('.mobile-search-form input').val('PHP'); $('.mobile-search-form').trigger('submit');
    assert.equal(calls[0].config.var.explicit, true);
    calls[0].resolve({success: false}); await tick();
    assert.equal($('#search-result-mobile-modal').attr('aria-busy'), 'false');
    assert.equal($('#search-result-mobile-modal-wrap').hasClass('search-is-loading'), false);
    assert.equal($('#search-result-mobile-modal button').text(), 'Retry');
    dom.window.close();
});

test('desktop suggestions become visible after a three-character query', async () => {
    const {$, calls, dom} = await setup();
    const $input = $('.webbooks-search-form input');

    $input.val('PHP').trigger('input');
    await tick(350);
    assert.equal(calls.length, 1);
    assert.equal($('.webbooks-search-form').hasClass('open'), true);
    calls[0].resolve(success('<p>PHP result</p>'));
    await tick();
    assert.match($('#search-result').text(), /PHP result/);
    dom.window.close();
});

test('clearing a search suppresses old responses and errors', async () => {
    const {$, calls, dom} = await setup();
    $('.webbooks-search-form input').val('Books'); $('.webbooks-search-form').trigger('submit');
    $('.webbooks-search-form input').val('').trigger('input');
    calls[0].resolve(success('<p>Stale book</p>')); await tick();
    assert.equal($('#search-result').text(), '');
    assert.equal($('.webbooks-search-form').hasClass('open'), false);
    assert.equal($('#search-result').attr('aria-busy'), 'false');
    dom.window.close();
});

test('filter pagination, shared URL and Back restore filters and original results', async () => {
    const {$, calls, window, dom} = await setup();
    $('#category-main').val('fiction'); $('#main-search').trigger('submit');
    calls[0].resolve(success('<a class="ajax-pagination" href="#">unused</a><div class="ajax-pagination"><a href="#" data-page="2" data-ajax-action="main_search_on_site">2</a></div>')); await tick();
    $('.ajax-pagination a').trigger('click'); calls[1].resolve(success('<p>Page 2</p>')); await tick();
    assert.equal(new URL(window.location.href).searchParams.get('wb_page'), '2');
    assert.equal(new URL(window.location.href).searchParams.get('lang'), 'uk');
    window.history.replaceState(null, '', '?lang=uk&wb_mode=filter&wb_category=fiction');
    window.dispatchEvent(new window.PopStateEvent('popstate')); calls[2].resolve(success('<p>Page 1 again</p>')); await tick();
    assert.equal(calls[2].config.var.paged, 1);
    window.history.replaceState(null, '', '?lang=uk'); window.dispatchEvent(new window.PopStateEvent('popstate')); await tick();
    assert.match($('.content-loop').text(), /Initial catalog/);
    assert.equal($('#category-main').val(), '');
    dom.window.close();
});

test('shared filter URL restores on initial load and reset clears optional filters', async () => {
    const {$, calls, dom} = await setup('https://books.test/?wb_mode=filter&wb_category=fiction&wb_language=en&wb_page=2');
    assert.equal(calls.length, 1);
    assert.equal(calls[0].config.var.language, 'en');
    assert.equal(calls[0].config.var.paged, 2);
    calls[0].resolve(success('<p>Filtered</p>')); await tick();
    $('#main-search')[0].reset(); await tick();
    assert.equal(calls[1].config.var.category, '');
    assert.equal(calls[1].config.var.language, '');
    dom.window.close();
});

test('a newer global search suppresses a pending filter response and its URL', async () => {
    const {$, calls, window, dom} = await setup();
    $('#main-search').trigger('submit');
    $('.webbooks-search-form input').val('PHP'); $('.webbooks-search-form').trigger('submit');
    calls[1].resolve(success('<p>PHP books</p>')); await tick();
    calls[0].resolve(success('<p>Old catalog</p>')); await tick();
    assert.equal(new URL(window.location.href).searchParams.get('wb_mode'), 'search');
    assert.equal($('.content-loop').text(), 'Initial catalog');
    assert.equal($('.content-loop').attr('aria-busy'), 'false');
    assert.equal($('#main-search [type="submit"]').prop('disabled'), false);
    assert.match($('#search-result').text(), /PHP books/);
    dom.window.close();
});

test('AJAX timeouts and expired sessions reject without retrying through jQuery', async () => {
    const dom = new JSDOM('', {url: 'https://books.test/', runScripts: 'outside-only'});
    const window = dom.window;
    let fallbackCalls = 0;
    window.jQuery = {ajax() { fallbackCalls++; }};
    window.fetch = (_url, options) => new Promise((_resolve, reject) => {
        options.signal.addEventListener('abort', () => reject(new window.DOMException('Timed out', 'AbortError')));
    });
    window.eval(fs.readFileSync('assets/js/ajax-client.js', 'utf8'));
    await assert.rejects(window.WebBooksAjax.request({url: '/ajax', timeout: 5}), {name: 'AbortError'});
    window.fetch = async () => ({ok: false, status: 403});
    await assert.rejects(window.WebBooksAjax.request({url: '/ajax'}), error => error.status === 403);
    assert.equal(fallbackCalls, 0);
    dom.window.close();
});
