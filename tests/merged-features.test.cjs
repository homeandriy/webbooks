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