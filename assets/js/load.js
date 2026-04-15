document.addEventListener('DOMContentLoaded', function () {
    window.WebBooksAjax.wpRequest({
        url: webbooksLoader.admin_ajax,
        action: 'load-scripts'
    }).catch(function (error) {
        console.log('load-scripts error:', error);
    });
});
