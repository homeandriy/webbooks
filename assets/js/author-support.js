export const initializeAuthorSupport = (ClipboardJS) => {
    const config = window.webbooksSupportConfig;

    if (!config?.adminAjax || !config?.nonce) {
        return;
    }

    const request = async (action) => {
        const body = new URLSearchParams({ action, nonce: config.nonce });
        const response = await fetch(config.adminAjax, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString(),
            credentials: 'same-origin',
        });
        const payload = await response.json();

        if (!response.ok || !payload.success) {
            throw new Error(payload?.data?.message || config.i18n.error);
        }

        return payload.data;
    };

    const showCopiedTooltip = (button) => {
        const tooltip = window.bootstrap?.Tooltip?.getOrCreateInstance(button, { trigger: 'manual' });

        button.setAttribute('data-bs-original-title', config.i18n.copied);
        tooltip?.show();
        window.setTimeout(() => {
            tooltip?.hide();
            button.setAttribute('data-bs-original-title', config.i18n.copy);
        }, 1800);
    };

    const iconClassForCardType = (type) => {
        if (type === 'visa') {
            return 'fa-cc-visa';
        }

        if (type === 'master' || type === 'mastercard') {
            return 'fa-cc-mastercard';
        }

        return 'fa-credit-card';
    };

    const renderCards = (cards, cardList) => {
        cardList.replaceChildren();

        cards.forEach((card) => {
            const cardRow = document.createElement('div');
            cardRow.className = 'author-support-card';

            const icon = document.createElement('i');
            icon.className = `fa ${iconClassForCardType(card.type)} author-support-card__icon`;
            icon.setAttribute('aria-hidden', 'true');

            const label = document.createElement('span');
            label.className = 'author-support-card__type';
            label.textContent = card.label;

            const input = document.createElement('input');
            input.className = 'form-control author-support-card__number';
            input.type = 'text';
            input.value = card.number;
            input.readOnly = true;
            input.setAttribute('aria-label', `${card.label}: ${config.i18n.copy}`);

            const copyButton = document.createElement('button');
            copyButton.className = 'btn btn-outline-secondary author-support-card__copy';
            copyButton.type = 'button';
            copyButton.setAttribute('aria-label', config.i18n.copy);
            copyButton.setAttribute('data-bs-toggle', 'tooltip');
            copyButton.setAttribute('data-bs-original-title', config.i18n.copy);
            copyButton.innerHTML = '<i class="fa fa-copy" aria-hidden="true"></i>';

            const copyCardNumber = () => {
                input.focus();
                input.select();
            };

            const clipboard = new ClipboardJS(copyButton, {
                text: () => card.number,
            });
            clipboard.on('success', () => {
                showCopiedTooltip(copyButton);
            });

            input.addEventListener('focus', () => {
                copyButton.click();
            }, { once: true });
            copyButton.addEventListener('click', () => {
                copyCardNumber();
            });

            cardRow.append(icon, label, input, copyButton);
            cardList.append(cardRow);
        });
    };

    const initializeSupportCards = () => {
        const modal = document.getElementById('webbooks-author-support-modal');
        const triggerButtons = document.querySelectorAll('[data-webbooks-support-cards]');

        if (!modal || !triggerButtons.length) {
            return;
        }

        const status = modal.querySelector('[data-webbooks-support-status]');
        const cardList = modal.querySelector('[data-webbooks-support-card-list]');

        triggerButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                status.textContent = config.i18n.loading;
                cardList.replaceChildren();

                try {
                    const data = await request('webbooks_author_support_cards');
                    if (!data.cards?.length) {
                        status.textContent = config.i18n.noCards;

                        return;
                    }

                    status.textContent = '';
                    renderCards(data.cards, cardList);
                } catch (error) {
                    status.textContent = error.message || config.i18n.error;
                }
            });
        });
    };

    const initializeTelegramButtons = () => {
        document.querySelectorAll('[data-webbooks-telegram]').forEach((button) => {
            button.addEventListener('click', async () => {
                const newTab = window.open('about:blank', '_blank');
                if (newTab) {
                    newTab.opener = null;
                }
                button.disabled = true;

                try {
                    const data = await request('webbooks_author_support_telegram');
                    if (newTab) {
                        newTab.location.href = data.url;
                    } else {
                        window.open(data.url, '_blank', 'noopener');
                    }
                } catch (error) {
                    newTab?.close();
                } finally {
                    button.disabled = false;
                }
            });
        });
    };

    const initialize = () => {
        initializeSupportCards();
        initializeTelegramButtons();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
};
