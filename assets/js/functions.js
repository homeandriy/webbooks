const SELECTORS = Object.freeze({
    postContainer: '#container_for_post',
    previewButtons: '.load-post',
    allPostLoadMore: '[data-all-post-load-more]',
    allPostNavigation: '[data-all-post-navigation]',
    allPostResults: '[data-all-post-results]',
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
    let isLoadingAllPosts = false;

    document.addEventListener('click', async (event) => {
        const loadMoreLink = event.target.closest(SELECTORS.allPostLoadMore);
        if (!loadMoreLink || isLoadingAllPosts) {
            return;
        }

        const navigation = loadMoreLink.closest(SELECTORS.allPostNavigation);
        const results = document.querySelector(SELECTORS.allPostResults);

        if (!navigation || !results) {
            return;
        }

        event.preventDefault();
        isLoadingAllPosts = true;
        loadMoreLink.setAttribute('aria-busy', 'true');
        loadMoreLink.classList.add('is-loading');

        try {
            const response = await fetch(loadMoreLink.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                throw new Error(`Unable to load page: ${response.status}`);
            }

            const nextDocument = new window.DOMParser().parseFromString(await response.text(), 'text/html');
            const nextResults = nextDocument.querySelector(SELECTORS.allPostResults);

            if (!nextResults || nextResults.children.length === 0) {
                throw new Error('The next page does not contain post cards.');
            }

            results.append(...nextResults.children);

            const nextNavigation = nextDocument.querySelector(SELECTORS.allPostNavigation);
            if (nextNavigation) {
                navigation.replaceWith(nextNavigation);
            } else {
                navigation.remove();
            }

            window.history.pushState({}, '', loadMoreLink.href);
        } catch (error) {
            console.error('Unable to load more posts:', error);
            window.location.assign(loadMoreLink.href);
        } finally {
            isLoadingAllPosts = false;
            loadMoreLink.removeAttribute('aria-busy');
            loadMoreLink.classList.remove('is-loading');
        }
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
