(function (window, document) {
    'use strict';

    function debounce(callback, wait) {
        let timer = null;
        return function debounced() {
            const context = this;
            const args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, wait);
        };
    }

    function getUrlVars(url) {
        const source = url || window.location.href;
        const query = source.split('?')[1] || '';
        const params = new URLSearchParams(query);
        const result = {};

        params.forEach(function (value, key) {
            result[key] = value;
        });

        return result;
    }

    function on(root, eventName, selector, handler) {
        root.addEventListener(eventName, function (event) {
            const matched = event.target.closest(selector);
            if (!matched || !root.contains(matched)) {
                return;
            }

            handler.call(matched, event, matched);
        });
    }

    function toggleOpenState(element, shouldOpen, openClass) {
        if (!element) {
            return;
        }
        element.classList.toggle(openClass || 'open', Boolean(shouldOpen));
    }

    window.WebBooksCompat = {
        debounce: debounce,
        getUrlVars: getUrlVars,
        on: on,
        toggleOpenState: toggleOpenState
    };
})(window, document);
