const consentCookie = 'webbooks_analytics_consent';
const consentMaxAge = 60 * 60 * 24 * 180;

function saveConsent(state) {
    const secure = window.location.protocol === 'https:' ? '; Secure' : '';

    document.cookie = `${consentCookie}=${state}; Max-Age=${consentMaxAge}; Path=/; SameSite=Lax${secure}`;
}

export function initializeCookieConsent() {
    const banner = document.querySelector('[data-webbooks-cookie-consent]');
    const manageButton = document.querySelector('[data-webbooks-consent-manage]');

    if (!(banner instanceof HTMLElement)) {
        return;
    }

    document.addEventListener('click', (event) => {
        const target = event.target;

        if (!(target instanceof Element)) {
            return;
        }

        const choice = target.closest('[data-webbooks-consent-action]');
        if (choice instanceof HTMLButtonElement) {
            const state = choice.dataset.webbooksConsentAction;

            if (state === 'accepted' || state === 'rejected') {
                saveConsent(state);
                window.location.reload();
            }

            return;
        }

        if (target.closest('[data-webbooks-consent-manage]')) {
            banner.hidden = false;
            if (manageButton instanceof HTMLButtonElement) {
                manageButton.hidden = true;
                manageButton.setAttribute('aria-expanded', 'true');
            }
            banner.querySelector('[data-webbooks-consent-action]')?.focus();
        }
    });

    if (manageButton instanceof HTMLButtonElement && banner.dataset.consentState === 'pending') {
        manageButton.hidden = true;
    }
}
