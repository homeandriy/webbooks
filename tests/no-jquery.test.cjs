const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');

const read = (file) => fs.readFileSync(file, 'utf8');

test('theme runtime is module-based and does not declare a jQuery dependency', () => {
    const assets = read('inc/assets.php');
    const manifest = read('package.json');
    const vite = read('vite.config.mjs');
    const runtime = [
        'assets/js/ajax-client.js',
        'assets/js/ajax-filter.js',
        'assets/js/search-ui.js',
		'src/cookie-consent.js',
		'assets/portfolio/js/portfolio.js',
        'src/main.js',
        'src/fallback-runtime.js'
    ].map(read).join('\n');

    assert.doesNotMatch(assets, /['"]jquery['"]/i);
    assert.doesNotMatch(manifest, /["']jquery["']/i);
    assert.doesNotMatch(vite, /jquery/i);
    assert.doesNotMatch(runtime, /window\.jQuery|\bjQuery\s*\(|\$\s*\(/);
    assert.match(read('assets/js/ajax-client.js'), /export function registerAjaxClient/);
    assert.match(read('assets/js/search-ui.js'), /export function initializeSearchUi/);
    assert.match(read('assets/js/ajax-filter.js'), /export function initializeDownload/);
	assert.match(read('assets/portfolio/js/portfolio.js'), /export function initializePortfolio/);
	assert.match(assets, /'webbooks-author-support', 'webbooks-portfolio', 'webbooks-runtime-fallback'/);
});
