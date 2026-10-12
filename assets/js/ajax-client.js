export function registerAjaxClient(window) {
    'use strict';

    function toFormData(data) {
        const formData = new FormData();

        Object.keys(data || {}).forEach(function (key) {
            const value = data[key];
            if (key === 'parameters' && value && typeof value === 'object' && !(value instanceof Blob) && !(value instanceof File)) {
                Object.keys(value).forEach(function (nestedKey) {
                    formData.append('parameters[' + nestedKey + ']', value[nestedKey]);
                });
                return;
            }
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

    function request(options) {
        const payload = options.data || {};
        if (!window.fetch || !window.AbortController) {
            return Promise.reject(new Error('This browser does not support the required Fetch API.'));
        }

        if (typeof options.beforeSend === 'function') {
            options.beforeSend();
        }

        const controller = new window.AbortController();
        const timeoutId = window.setTimeout(function () { controller.abort(); }, options.timeout || 15000);
        return fetch(options.url, {
            signal: controller.signal,
            method: options.method || 'POST',
            body: toFormData(payload),
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw { status: response.status, response: response, isHttpError: true };
            }

            const parser = options.dataType === 'text' ? response.text.bind(response) : response.json.bind(response);
            return parser();
        }).finally(function () { window.clearTimeout(timeoutId); });
    }

    function wpRequest(config) {
        const basePayload = {
            action: config.action
        };

        if (window.webbooksConfig && window.webbooksConfig.language) {
            basePayload.lang = window.webbooksConfig.language;
        }

        if (config.nonce !== undefined) {
            basePayload.nonce = config.nonce;
        }

        if (config.var !== undefined) {
            basePayload.var = JSON.stringify(config.var);
        }

        if (config.parameters !== undefined) {
            basePayload.parameters = config.parameters;
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
}
