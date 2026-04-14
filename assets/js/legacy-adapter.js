(function (window, document, $) {
    'use strict';

    const hasjQuery = typeof $ !== 'undefined';

    function showModal(selector) {
        if (hasjQuery && $(selector).modal) {
            $(selector).modal('show');
            return;
        }

        const modal = document.querySelector(selector);
        if (modal) {
            modal.classList.add('in');
            modal.style.display = 'block';
        }
    }

    function initLegacyPlugins() {
        if (!hasjQuery) {
            return;
        }

        if ($("[data-toggle='tooltip']").tooltip) {
            $("[data-toggle='tooltip']").tooltip();
        }

        if ($(".sidebar .treeview").tree) {
            $(".sidebar .treeview").tree();
        }
    }

    function applySlimscroll(height) {
        if (!hasjQuery || !$(".sidebar").slimscroll) {
            return;
        }

        $(".sidebar").slimscroll({
            height: height,
            color: "rgba(0,0,0,0.2)"
        });
    }

    window.WebBooksLegacy = {
        showModal: showModal,
        initLegacyPlugins: initLegacyPlugins,
        applySlimscroll: applySlimscroll
    };
})(window, document, window.jQuery);
