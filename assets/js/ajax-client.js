(function (window) {
    'use strict';

    function toFormData(data) {
        const formData = new FormData();

        Object.keys(data || {}).forEach(function (key) {
            const value = data[key];
            if (value && typeof value === 'object' && !(value instanceof Blob) && !(value instanceof File)) {
                formData.append(key, JSON.stringify(value));
                return;
            }
            formData.append(key, value);
        });

        return formData;
    }

    function fromJsonPayload(entry) {
        if (typeof entry !== 'string') {
            return entry;
        }
        try {
            return JSON.parse(entry);
        } catch (error) {
            return entry;
        }
    }

    function ajaxFallback(options) {
        if (!window.jQuery || !window.jQuery.ajax) {
            return Promise.reject(new Error('Neither fetch nor jQuery.ajax are available.'));
        }

        return new Promise(function (resolve, reject) {
            window.jQuery.ajax({
                cache: options.cache,
                timeout: options.timeout,
                url: options.url,
                type: options.method || 'POST',
                dataType: options.dataType || 'json',
                data: options.data,
                beforeSend: options.beforeSend,
                success: function (response) {
                    resolve(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    reject({ jqXHR: jqXHR, textStatus: textStatus, errorThrown: errorThrown });
                }
            });
        });
    }

    function request(options) {
        const payload = options.data || {};
        if (!window.fetch) {
            return ajaxFallback(options);
        }

        if (typeof options.beforeSend === 'function') {
            options.beforeSend();
        }

        return fetch(options.url, {
            method: options.method || 'POST',
            body: toFormData(payload),
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw { status: response.status, response: response };
            }

            const parser = options.dataType === 'text' ? response.text.bind(response) : response.json.bind(response);
            return parser();
        }).catch(function (error) {
            if (window.jQuery && window.jQuery.ajax) {
                return ajaxFallback(options);
            }
            throw error;
        });
    }

    function wpRequest(config) {
        const basePayload = {
            action: config.action,
            nonce: config.nonce
        };

        if (config.var !== undefined) {
            basePayload.var = JSON.stringify(config.var);
        }

        if (config.parameters !== undefined) {
            basePayload.parameters = JSON.stringify(config.parameters);
        }

        if (config._nonce !== undefined) {
            basePayload._nonce = config._nonce;
        }

        if (config.extraData) {
            Object.keys(config.extraData).forEach(function (key) {
                basePayload[key] = config.extraData[key];
            });
        }

        return request({
            url: config.url,
            method: 'POST',
            dataType: config.dataType || 'json',
            cache: config.cache,
            timeout: config.timeout,
            beforeSend: config.beforeSend,
            data: basePayload
        }).then(function (response) {
            if (response && response.var && typeof response.var === 'string') {
                response.var = fromJsonPayload(response.var);
            }
            return response;
        });
    }

    window.WebBooksAjax = {
        request: request,
        wpRequest: wpRequest
    };
})(window);
