const AJAX_ACTIONS = Object.freeze({
    catalog: 'main_search_on_site',
    authors: 'select_author',
    globalSearch: 'global_search',
    downloadLink: 'return_link_to_book',
});
const SEARCH_CONFIG = Object.freeze({
    debounceDelay: 300,
    minimumLength: 4,
    countdownSeconds: 20,
});
const getTranslation = (key) => webbooksConfig.i18n[key];
const formatSecondsRemaining = (seconds) => getTranslation('seconds_remaining').replace('%d', seconds);

jQuery(document).ready(function ($) {
    let sendStatus = false;
    const mainSearchSelector = $('.main-search');
    const desktopSearchSelector = $('.navbar-form .main-search');
    const $searchButtons = $('.load-search');
    const $searchResult = $('#search-result');
    const $contentLoop = $('.content-loop');
    const $result = $('#result');
    const $categoryMain = $('#category-main');
    const $statusBook = $('#status-book');
    const $language = $('#language');
    const $sendDataButton = $('#send-data-button');
    const $sendLinks = $('#send-links');
    const $inlineCheckbox = $('#inlineCheckbox1');
    const $blankRadioInputs = $("input[name='blankRadio']");
    const categorySelect = document.querySelector('#category');
    let activeSearchRequestId = 0;
    const legacyJqueryAjaxWrapper = function (config) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: config.url,
                type: 'POST',
                dataType: config.dataType || 'json',
                cache: config.cache,
                timeout: config.timeout,
                beforeSend: config.beforeSend,
                data: {
                    action: config.action,
                    nonce: config.nonce,
                    var: config.var !== undefined ? JSON.stringify(config.var) : undefined,
                    parameters: config.parameters !== undefined ? JSON.stringify(config.parameters) : undefined,
                    _nonce: config._nonce
                },
                success: resolve,
                error: function (jqXHR, textStatus, errorThrown) {
                    reject({jqXHR: jqXHR, textStatus: textStatus, errorThrown: errorThrown});
                }
            });
        });
    };
    const compat = window.WebBooksCompat || {
        debounce: function (handler) { return handler; },
        on: function () {
            const args = arguments;
            $(args[0]).on(args[1], args[2], function (event) {
                args[3](event, event.currentTarget);
            });
        },
        toggleOpenState: function (element, shouldOpen, openClass) {
            if (!element) {
                return;
            }
            $(element).toggleClass(openClass || 'open', Boolean(shouldOpen));
        },
        getUrlVars: function (url) {
            const source = url || window.location.href;
            const query = source.split('?')[1] || '';
            const params = new URLSearchParams(query);
            const result = {};
            params.forEach(function (value, key) {
                result[key] = value;
            });
            return result;
        }
    };
    const api = window.WebBooksAjax || { wpRequest: legacyJqueryAjaxWrapper };
    const debounce = compat.debounce;
    const compatOn = compat.on;
    const toggleOpenState = compat.toggleOpenState;
    const desktopSearchForm = desktopSearchSelector.closest('.navbar-form');

    function setDesktopSearchLoading(isLoading) {
        const $form = desktopSearchForm;
        if (!$form.length) {
            return;
        }
        $form.toggleClass('search-is-loading', Boolean(isLoading));
    }

    function closeDesktopSearch() {
        $searchResult.empty();
        setDesktopSearchLoading(false);
        toggleOpenState(desktopSearchForm[0], false, 'open');
    }

    // Функция поиска в шапке, результати буду подгружатся после ввода трех символов
    const searchParam = {
        action: AJAX_ACTIONS.globalSearch,
    }

    //Scroll to TOP
    function ScrollToResult() {
        $('html, body').animate({scrollTop: $contentLoop.offset().top}, 500);
    }

    function AjaxSend(param, action) {
        api.wpRequest({
			url: webbooksConfig.admin_ajax,
            action: action,
			nonce: webbooksConfig.nonce,
            var: param,
            beforeSend: function () {
                $contentLoop
                    .empty()
                    .addClass('fa-spinner')
                    .addClass('fa')
                    .addClass('fa-spin')
                    .addClass('fa-5x')
                    .addClass('custom-spin');
                $('.alm-btn-wrap').hide();

            }
        }).then(function (response) {
            if (!response || !response.success) {
                return;
            }

            $result.empty();
            $contentLoop.removeClass('fa-spinner')
                .removeClass('fa')
                .removeClass('fa-spin')
                .removeClass('fa-5x')
                .removeClass('custom-spin')
                .html('')
                .append(response.data.html);
            ScrollToResult();
        }).catch(function (error) {
            console.log(error);
        });
    }

    compatOn(document, 'click', '.ajax-pagination a[data-page]', function (event, link) {
        event.preventDefault();
        const page = parseInt(link.dataset.page, 10);
        const ajaxAction = link.dataset.ajaxAction;
        if (!page || page < 1) {
            return;
        }

        if (ajaxAction === AJAX_ACTIONS.globalSearch) {
            const requestData = {
                StrTosearch: mainSearchSelector.val(),
                paged: page
            };

            mainSearch(requestData, searchParam.action);
            return;
        }

        const requestData = {
            category: $categoryMain.val(),
            statusbook: $statusBook.val(),
            language: $language.val(),
            paged: page
        };

        if ($sendLinks.prop('checked')) {
            requestData.selectToLink = 'true';
        }

        AjaxSend(requestData, AJAX_ACTIONS.catalog);
    });

    const handleSearchKeyup = debounce(function (event, input) {
        event.preventDefault();
        const $input = $(input);
        const value = $input.val().trim();
        const resultId = $input.data('idres');
        if (value.length >= SEARCH_CONFIG.minimumLength) {
            const requestData = {
                StrTosearch: value,
                paged: 1
            };

            if (resultId) {
                requestData.isMobile = true;
                requestData.id = resultId;
            }

            mainSearch(requestData, searchParam.action);
        } else if (resultId) {
            $(`#${resultId}-wrap`).removeClass('is-open').hide();
            $(`#${resultId}`).empty();
            $searchButtons.removeClass('fa-spinner fa-spin').addClass('fa-search');
        } else {
            closeDesktopSearch();
            $searchButtons.removeClass('fa-spinner fa-spin').addClass('fa-search');
        }
    }, SEARCH_CONFIG.debounceDelay);

    compatOn(document, 'keyup', '.main-search', handleSearchKeyup);
    compatOn(document, 'submit', '.navbar-form', function (event) {
        event.preventDefault();
    });


    // Глобальний поиск по сайту
    function mainSearch(param, action) {
        const requestId = ++activeSearchRequestId;
        api.wpRequest({
			url: webbooksConfig.admin_ajax,
            action: action,
			nonce: webbooksConfig.nonce,
            var: param,
            beforeSend: function () {
                $searchButtons.removeClass('fa-search').addClass('fa-spin fa-spinner');
                if (param.isMobile) {
                    $('#' + param.id).html('');
                    $('#' + param.id + '-wrap').show().addClass('search-is-loading is-open');
                    return;
                }

                $searchResult.empty();
                setDesktopSearchLoading(true);
                toggleOpenState(desktopSearchForm[0], true, 'open');
            }
        }).then(function (data) {
            if (requestId !== activeSearchRequestId) {
                return;
            }

            if (!data || !data.success) {
                return;
            }

            if (param.isMobile) {
                $('#' + param.id + '-wrap').removeClass('search-is-loading').addClass('is-open');
                $('#' + param.id).html('').html(data.data.html);
            } else {
                setDesktopSearchLoading(false);
                $searchButtons.removeClass('fa-spinner fa-spin').addClass('fa-search');
                if (param.StrTosearch && param.StrTosearch.length >= SEARCH_CONFIG.minimumLength && data.data.html && $.trim(data.data.html).length > 0) {
                    $searchResult.empty().append(data.data.html);
                    toggleOpenState(desktopSearchForm[0], true, 'open');
                } else {
                    closeDesktopSearch();
                }
            }
        }).catch(function (error) {
            setDesktopSearchLoading(false);
            $searchButtons.removeClass('fa-spinner fa-spin').addClass('fa-search');
            console.log(error);
        });
    }

    // Change Category
    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            const selectedValue = this.value;
            if (+selectedValue === 0) {
                return;
            }

            $result.empty();
            const param = {
                autor: $('input[name="author"]').val(),
                category: selectedValue,
            };

            $('#TitleName').empty();
            AjaxSend(param, AJAX_ACTIONS.catalog);
            LoadAutors(param);
        });
    }

    // Load Authors
    function LoadAutors(param) {
        AjaxSend(param, AJAX_ACTIONS.authors);
    }

    // Change Authors
    function QueryBooksAutor(param) {
        AjaxSend(param, AJAX_ACTIONS.catalog);
    }

    // обработка допольнительных параметров поиска (paramSelect проверяет статус активности чекбокса активации опций)
    function activeFormOther(paramSelect) {
        if (paramSelect === true) {
            $blankRadioInputs.prop('disabled', false);
        }
        if (paramSelect === false) {
            $blankRadioInputs.prop('disabled', true);
        }
    }
    const mainSearchForm = document.querySelector('#main-search');
    const handleMainSearchFormChange = function (event) {
        const categoryValue = $categoryMain.val();
        const statusValue = $statusBook.val();
        const languageValue = $language.val();

        $statusBook.prop('disabled', !categoryValue);
        if (!categoryValue) {
            $statusBook.val('');
            $language.val('').prop('disabled', true);
            $sendDataButton.prop('disabled', true);
        }

        $language.prop('disabled', !statusValue);
        if (!statusValue) {
            $language.val('');
            $sendDataButton.prop('disabled', true);
        }

        $sendDataButton.prop('disabled', !languageValue);

        activeFormOther($inlineCheckbox.prop('checked'));
    };

    if (mainSearchForm) {
        mainSearchForm.addEventListener('change', handleMainSearchFormChange);
        mainSearchForm.addEventListener('click', function (event) {
            if (event.target.matches('select, input')) {
                handleMainSearchFormChange(event);
            }
        });
        mainSearchForm.addEventListener('submit', function (event) {
            event.preventDefault();
        });
        $sendDataButton.on('click', function (e) {
            e.preventDefault();

            if (sendStatus === false) {
                sendStatus = true;
                const postData = {};
                postData.category = $categoryMain.val();
                postData.statusbook = $statusBook.val();
                postData.language = $language.val();

                if ($sendLinks.prop('checked')) {
                    postData.selectToLink = 'true';
                }
                AjaxSend(postData, AJAX_ACTIONS.catalog);
            }
        });
    }

    // Генерация ссилок на скачивание
    const getUrlVars = compat.getUrlVars;

    const count = {
        id: getUrlVars()["count"],
        key: getUrlVars()["key"]
    };

    const countdown = $('#countdown');
    let timer;
    if (count.id === "" || !count.id) {
		$('#js-content').html(`<h3>${getTranslation('invalid_download_link')}</h3><br><a href="${webbooksConfig.home_url}">${getTranslation('back_to_homepage')}</a>`);

        return false;
    }
    startCountdown(count, countdown);

    function startCountdown(parameters, countdownContainer) {
        const totalSeconds = SEARCH_CONFIG.countdownSeconds;
        let remainingSeconds = totalSeconds;
        const linkInstance = $('#js-content');

        function ensureCountdownLayout() {
            if (countdownContainer.find('[data-role="bar"]').length) {
                return;
            }

            countdownContainer.html(`
                <div class="download-countdown__status" data-role="status"></div>
                <div class="progress download-countdown__progress">
                    <div class="progress-bar progress-bar-striped active download-countdown__bar" data-role="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="download-countdown__seconds" data-role="seconds"></div>
                <div class="alert alert-danger download-countdown__error hidden" data-role="error"></div>
                <button type="button" class="btn btn-warning download-countdown__retry hidden" data-role="retry"></button>
            `);
        }

        function setState(state, details) {
            const status = countdownContainer.find('[data-role="status"]');
            const seconds = countdownContainer.find('[data-role="seconds"]');
            const progress = countdownContainer.find('[data-role="bar"]');
            const error = countdownContainer.find('[data-role="error"]');
            const retry = countdownContainer.find('[data-role="retry"]');

            error.addClass('hidden').text('');
            retry.addClass('hidden').text(getTranslation('try_again'));

            if (state === 'counting') {
                status.text(getTranslation('preparing_download_link'));
                seconds.text(formatSecondsRemaining(remainingSeconds));
                return;
            }

            if (state === 'loading-link') {
                status.text(getTranslation('checking_download_link'));
                seconds.text(getTranslation('seconds_zero'));
                progress.removeClass('progress-bar-danger').addClass('progress-bar-striped active');
                progress.attr({'aria-valuenow': 100, 'style': 'width:100%'});
                linkInstance.html('<i class="fa fa-spinner fa-pulse"></i>');
                return;
            }

            if (state === 'error') {
                status.text(getTranslation('download_link_unavailable'));
                seconds.text('');
                progress.removeClass('active progress-bar-striped').addClass('progress-bar-danger');
                error.removeClass('hidden').text(details || getTranslation('network_error'));
                retry.removeClass('hidden');
                linkInstance.html('');
                return;
            }

            if (state === 'ready') {
                status.text(getTranslation('download_link_ready'));
                seconds.text('');
                progress.removeClass('active progress-bar-striped progress-bar-danger');
            }
        }

        function updateProgress() {
            const percentage = Math.round(((totalSeconds - remainingSeconds) / totalSeconds) * 100);
            const progress = countdownContainer.find('[data-role="bar"]');
            progress.attr({'aria-valuenow': percentage, 'style': 'width:' + percentage + '%'});
        }

        function resolveErrorMessage(jqXHR, responseData) {
            const nonceError = jqXHR && jqXHR.status === 403;
            if (responseData && responseData.data && responseData.data.message) {
                return responseData.data.message;
            }
            if (nonceError) {
                return getTranslation('nonce_expired');
			}

            return getTranslation('network_or_server_error');
        }

        function requestLink() {
            setState('loading-link');

            api.wpRequest({
				url: webbooksConfig.admin_ajax,
                action: AJAX_ACTIONS.downloadLink,
                parameters: parameters,
				_nonce: webbooksConfig.download_nonce
            }).then(function (data) {
                if (!data || !data.success || !data.data || !data.data.html) {
                    setState('error', resolveErrorMessage(null, data));
                    return;
                }

                linkInstance.html(data.data.html);
                setState('ready');
            }).catch(function (error) {
                console.log(error);
                const jqXHR = error && error.jqXHR ? error.jqXHR : null;
                const responseData = jqXHR && jqXHR.responseJSON
                    ? jqXHR.responseJSON
                    : (error && error.responseJSON ? error.responseJSON : null);
                setState('error', resolveErrorMessage(jqXHR || error, responseData));
            });
        }

        function runCountdown() {
            clearInterval(timer);
            remainingSeconds = totalSeconds;
            linkInstance.html('');
            setState('counting');
            updateProgress();

            timer = setInterval(function () {
                remainingSeconds = remainingSeconds - 1;
                setState('counting');
                updateProgress();

                if (remainingSeconds <= 0) {
                    clearInterval(timer);
                    requestLink();
                }
            }, 1000);
        }

        ensureCountdownLayout();
        countdownContainer.off('click.downloadRetry').on('click.downloadRetry', '[data-role="retry"]', function () {
            runCountdown();
        });

        runCountdown();
    }
});
