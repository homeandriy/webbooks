const SELECTORS = Object.freeze({
    postContainer: '#container_for_post',
    previewButtons: '.load-post',
    languageModal: '#language-switcher-modal',
    languageOpenButtons: '[data-language-switcher-open]',
    languageCloseButtons: '[data-language-switcher-close]',
});

document.addEventListener('DOMContentLoaded', () => {
    const postContainer = document.querySelector(SELECTORS.postContainer);

    document.querySelectorAll(SELECTORS.previewButtons).forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();

            const postId = button.dataset.postId;
            window.WebBooksBootstrap?.showModal('#webbooks-post-preview-modal');

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
    const commentForm = document.querySelector('#commentform');
    const commentTextarea = document.querySelector('#comment');

    if (!commentForm || !commentTextarea) {
        return;
    }

    commentForm.addEventListener('click', (event) => {
        const button = event.target.closest('.comment-emoji-btn');
        if (!button) {
            return;
        }

        event.preventDefault();
        commentTextarea.value += `${button.dataset.emoji ?? ''} `;
        commentTextarea.focus();
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
