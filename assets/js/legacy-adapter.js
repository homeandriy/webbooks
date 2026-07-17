export function initBootstrapBridge(bootstrap) {
    const $ = window.jQuery;
    const hasjQuery = typeof $ !== 'undefined';

    function showModal(selector) {
        const modal = document.querySelector(selector);
        if (modal) {
            bootstrap.Modal.getOrCreateInstance(modal).show();
        }
    }

    function initLegacyPlugins() {
        if (!hasjQuery) {
            return;
        }

        document.querySelectorAll("[data-bs-toggle='tooltip']").forEach(function (element) {
            bootstrap.Tooltip.getOrCreateInstance(element);
        });

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

    window.WebBooksBootstrap = {
        showModal: showModal,
        initLegacyPlugins: initLegacyPlugins,
        applySlimscroll: applySlimscroll
    };
}
