const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');

const read = (file) => fs.readFileSync(file, 'utf8');

test('optional trackers are gated by an explicit consent cookie', () => {
    const consent = read('inc/consent.php');
    const assets = read('inc/assets.php');
    const runtime = read('src/cookie-consent.js');
	const portfolio = read('src/portfolio.js');

    assert.match(consent, /WEBBOOKS_CONSENT_COOKIE/);
    assert.match(consent, /monsterinsights-frontend-script/);
    assert.match(consent, /wp_dequeue_script/);
    assert.match(assets, /webbooks_has_tracking_consent\(\)/);
    assert.match(runtime, /SameSite=Lax/);
    assert.match(runtime, /window\.location\.reload\(\)/);
	assert.match(portfolio, /initializeCookieConsent\(\)/);
});
