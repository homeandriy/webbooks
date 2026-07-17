const SELECTORS = Object.freeze({
    postContainer: '#container_for_post',
    previewLinks: 'a.load-post',
    languageModal: '#language-switcher-modal',
    languageOpenButtons: '[data-language-switcher-open]',
    languageCloseButtons: '[data-language-switcher-close]',
});

document.addEventListener('DOMContentLoaded', () => {
    const postContainer = document.querySelector(SELECTORS.postContainer);

    document.querySelectorAll(SELECTORS.previewLinks).forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            const postId = link.id;
            window.WebBooksBootstrap?.showModal('#myModal');

            window.WebBooksAjax.wpRequest({
                cache: false,
                timeout: 8000,
                url: webbooksConfig.admin_ajax,
                action: 'theme_post_example',
                nonce: webbooksConfig.nonce,
                extraData: { id: postId },
                beforeSend: () => {
                    if (postContainer) {
                        postContainer.textContent = webbooksConfig.i18n.preview_loading;
                    }
                }
            }).then((response) => {
                if (!response || !response.success || !postContainer) {
                    return;
                }

                postContainer.innerHTML = response.data.html;
            }).catch((error) => {
                console.error('The preview request failed:', error);
            });
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector(SELECTORS.languageModal);
    if (!modal) {
        return;
    }

    const openButtons = document.querySelectorAll(SELECTORS.languageOpenButtons);
    const closeButtons = modal.querySelectorAll(SELECTORS.languageCloseButtons);

    const openModal = () => {
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('language-switcher-modal-open');
    };

    const closeModal = () => {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('language-switcher-modal-open');
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', openModal);
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });
});
