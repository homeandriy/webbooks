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
                url: php_array.admin_ajax,
                action: 'theme_post_example',
                nonce: php_array.nonce,
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
