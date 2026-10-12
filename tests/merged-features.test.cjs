const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const { JSDOM } = require('jsdom');

async function page(html) {
  const dom = new JSDOM(html, { url: 'https://example.test/allpost/', runScripts: 'outside-only' });
  await new Promise(resolve => dom.window.addEventListener('load', resolve, { once: true }));
  return dom;
}

test('Previews work once for initial and dynamically appended cards', async () => {
  const dom = await page('<div id="container_for_post"></div><button class="load-post" id="21">Preview</button>');
  const { window } = dom;
  const calls = [];
  const modals = [];
  window.webbooksConfig = { admin_ajax: '/ajax', nonce: 'test', i18n: { preview_loading: 'Loading' } };
  window.WebBooksUI = { showModal: selector => modals.push(selector) };
  window.WebBooksAjax = { wpRequest: options => {
    calls.push(options);
    options.beforeSend();
    return Promise.resolve({ success: true, data: { html: '<p>Preview loaded</p>' } });
  } };
  window.eval(fs.readFileSync('assets/js/functions.js', 'utf8'));
  window.document.dispatchEvent(new window.Event('DOMContentLoaded'));
  window.document.querySelector('.load-post').click();
  await Promise.resolve();
  window.document.body.insertAdjacentHTML('beforeend', '<button class="load-post" data-post-id="22"><span>New card</span></button>');
  window.document.querySelector('[data-post-id] span').click();
  await Promise.resolve();
  assert.deepEqual(calls.map(call => call.extraData.id), ['21', '22']);
  assert.deepEqual(modals, ['#myModal', '#myModal']);
  assert.equal(window.document.querySelector('#container_for_post').textContent, 'Preview loaded');
  dom.window.close();
});

test('Progressive catalog appends the next page and preserves its public URL', async () => {
  const dom = await page('<div data-all-post-results><article>First</article></div><nav data-all-post-navigation><a data-all-post-load-more href="?page=2">More</a></nav>');
  const { window } = dom;
  window.fetch = async url => {
    assert.equal(url, 'https://example.test/allpost/?page=2');
    return { ok: true, text: async () => '<div data-all-post-results><article>Second</article></div>' };
  };
  window.eval(fs.readFileSync('assets/js/functions.js', 'utf8'));
  window.document.dispatchEvent(new window.Event('DOMContentLoaded'));
  window.document.querySelector('[data-all-post-load-more]').click();
  await new Promise(resolve => setImmediate(resolve));
  assert.equal(window.document.querySelector('[data-all-post-results]').children.length, 2);
  assert.equal(window.document.querySelector('[data-all-post-navigation]'), null);
  assert.equal(window.location.search, '?page=2');
  dom.window.close();
});

test('article pages keep related posts but do not render a book gallery', () => {
  const articleTemplate = fs.readFileSync('single-post-type.php', 'utf8');
  const styles = fs.readFileSync('assets/css/components.css', 'utf8');

  assert.doesNotMatch(articleTemplate, /webbooks_render_book_gallery/);
  assert.match(articleTemplate, /class="col-12 article-related"/);
  assert.match(articleTemplate, /post__not_in'\s*=>\s*array\( get_the_ID\(\) \)/);
  assert.match(articleTemplate, /wp_reset_postdata\(\)/);
  assert.match(styles, /body\.webbooks-article \.article-related \.info-block/);
  assert.match(styles, /max-width: min\(72ch, 100%\)/);
});

test('theme uses a local Cyrillic-capable font without Google Fonts requests', () => {
  const fontCss = fs.readFileSync('assets/css/fonts.css', 'utf8');
  const tokens = fs.readFileSync('assets/css/tokens.css', 'utf8');
  const style = fs.readFileSync('style.css', 'utf8');
  const legacyAssets = fs.readFileSync('inc/assets.php', 'utf8');
  const assetManager = fs.readFileSync('src/Assets/AssetManager.php', 'utf8');

  assert.match(fontCss, /font-family: 'Inter';/);
  assert.match(fontCss, /font-display: swap;/);
  assert.match(fontCss, /InterVariable\.woff2/);
  assert.match(tokens, /--font-family-base: 'Inter'/);
  assert.match(style, /@import url\('\.\/assets\/css\/fonts\.css'\);/);
  assert.doesNotMatch(style, /font-family: Questrial/);
  assert.doesNotMatch(legacyAssets, /fonts\.googleapis\.com|fonts\.gstatic\.com/);
  assert.doesNotMatch(assetManager, /fonts\.googleapis\.com|fonts\.gstatic\.com/);
  assert.ok(fs.statSync('assets/fonts/InterVariable.woff2').size > 300000);
  assert.ok(fs.statSync('assets/fonts/Inter-LICENSE.txt').size > 1000);
});
