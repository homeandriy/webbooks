document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('a.ajax-post').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            const postId = link.id;
            const postContainer = document.querySelector('#single-post-container');

            if (window.WebBooksLegacy) {
                window.WebBooksLegacy.showModal('#myModal');
            }

            window.WebBooksAjax.wpRequest({
                cache: false,
                timeout: 8000,
                url: webbooksConfig.admin_ajax,
                action: 'theme_post_example',
                nonce: webbooksConfig.nonce,
                extraData: { id: postId },
                beforeSend: function () {
                    if (postContainer) {
                        postContainer.innerHTML = 'Loading';
                    }
                }
            }).then(function (response) {
                if (!response || !response.success || !postContainer) {
                    return;
                }

                postContainer.innerHTML = response.data.html;
            }).catch(function (error) {
                console.log('The following error occured:', error);
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('language-switcher-modal');
    if (!modal) {
        return;
    }

    var openButtons = document.querySelectorAll('[data-language-switcher-open]');
    var closeButtons = modal.querySelectorAll('[data-language-switcher-close]');

    function openModal() {
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('language-switcher-modal-open');
    }

    function closeModal() {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('language-switcher-modal-open');
    }

    openButtons.forEach(function (button) {
        button.addEventListener('click', openModal);
    });

    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
    if (!(window.jQuery && window.jQuery.fn)) {
        return;
    }

    var $ = window.jQuery;

    function hydrateLazyImages() {
        $('img.lazy[data-original]').each(function () {
            var $img = $(this);
            var original = $img.attr('data-original');

            if (!original) {
                return;
            }

            if (!$img.attr('src') || $img.attr('src').indexOf('data:image/') === 0) {
                $img.attr('src', original);
            }
        });
    }

    function initLazyImages() {
        if (!$.fn.lazyload) {
            hydrateLazyImages();
            return;
        }

        $('img.lazy').lazyload({
            effect: 'fadeIn',
            threshold: 100
        });

        $(window).trigger('scroll');
        hydrateLazyImages();
    }

    initLazyImages();

    $(document).on('ajaxComplete', initLazyImages);
});
