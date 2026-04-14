jQuery(document).ready(function ($) {
    let sendStatus = false;
    let mainSearchSelector = $('.main-search');

    // Функция поиска в шапке, результати буду подгружатся после ввода трех символов
    let searchParam = {
        action: 'global_search',
    }

    //Scroll to TOP
    function ScrollToResult() {
        $('html, body').animate({scrollTop: $('.content-loop').offset().top}, 500);
    }

    function AjaxSend(param, action) {
        let TypeData = 'json';
        // Проверка типа получаемых данных

        $.ajax({
            url: js_attributes.admin_ajax,
            type: 'POST',
            dataType: TypeData,
            data: {
                nonce: js_attributes.nonce,
                action: action,
                var: param,
            },
            beforeSend: function () {
                $('.content-loop')
                    .html('')
                    .addClass('fa-spinner')
                    .addClass('fa')
                    .addClass('fa-spin')
                    .addClass('fa-5x')
                    .addClass('custom-spin');
                $('.alm-btn-wrap').hide();

            },
            success: function (response) {
                if (!response || !response.success) {
                    return;
                }

                $('#result').append('');
                $('.content-loop').removeClass('fa-spinner')
                    .removeClass('fa')
                    .removeClass('fa-spin')
                    .removeClass('fa-5x')
                    .removeClass('custom-spin')
                    .html('')
                    .append(response.data.html);
                ScrollToResult();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
            }
        });
    }

    $(document).on('click', '.ajax-pagination a[data-page]', function (event) {
        event.preventDefault();
        let page = parseInt($(this).data('page'), 10);
        let ajaxAction = $(this).data('ajax-action');
        if (!page || page < 1) {
            return;
        }

        if (ajaxAction === 'global_search') {
            let requestData = {
                StrTosearch: mainSearchSelector.val(),
                paged: page
            };

            mainSearch(requestData, searchParam.action);
            return;
        }

        let requestData = {
            category: $('#category-main option:selected').val(),
            statusbook: $('#status-book option:selected').val(),
            language: $('#language option:selected').val(),
            paged: page
        };

        if ($('#send-links').attr('checked') === 'checked') {
            requestData.selectToLink = 'true';
        }

        AjaxSend(requestData, 'main_search_on_site');
    });

    mainSearchSelector.bind('keyup', function (e) {
        e.preventDefault();
        if (mainSearchSelector.val().length >= 3) {
            let sendsearch = {};
            sendsearch.StrTosearch = mainSearchSelector.val();
            sendsearch.paged = 1;
            mainSearch(sendsearch, searchParam.action);
        }
    });


    // Глобальний поиск по сайту
    function mainSearch(param, action) {
        $.ajax({
            url: js_attributes.admin_ajax,
            type: 'POST',
            dataType: 'json',
            data: {
                nonce: js_attributes.nonce,
                action: action,
                var: param,
            },
            beforeSend: function () {
                $('#search-result').html('');
                $('.load-search').removeClass('fa-search').addClass('fa-spin').addClass('fa-spinner');
            },
            success: function (data, textStatus, jqXHR) {
                if (!data || !data.success) {
                    return;
                }

                if (param.isMobile) {
                    $('#' + param.id + '-wrap').css({'max-width': window.screen.availWidth - 10, 'display': 'block'})
                    $('#' + param.id).html('').html(data.data.html);
                } else {
                    $('.load-search').removeClass('fa-spinner').removeClass('fa-spin').addClass('fa-search');
                    $('#search-result').html('').append(data.data.html);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
            }
        });
    }

    window.searchGlobal = function (evt) {
        if ($(evt.target).val().length > 3) {
            let searchRequest = {};
            searchRequest.StrTosearch = $(evt.target).val();
            searchRequest.paged = 1;
            searchRequest.isMobile = true;
            searchRequest.id = $(evt.target).data('idres');

            mainSearch(searchRequest, searchParam.action);
        } else {
            $('#search-result-mobile-wrap').css({'display': 'none'});
        }
    }

    // Change Category
    $("#category").change(function (e) {
        if (+$(this).val() === 0) return false;
        $('#result').text('');
        let param = {};
        param.autor = $('input[name="author"]').val();
        param.category = $('#category option:selected').val();
        $('#TitleName').empty();

        AjaxSend(param, 'main_search_on_site');
        LoadAutors(param)

    });

    // Load Authors
    function LoadAutors(param) {
        AjaxSend(param, 'select_author');
    }

    // Change Authors
    function QueryBooksAutor(param) {
        AjaxSend(param, 'main_search_on_site');
    }

    // обработка допольнительных параметров поиска (paramSelect проверяет статус активности чекбокса активации опций)
    function activeFormOther(paramSelect) {
        if (paramSelect === true) {
            $("input[name='blankRadio']").prop('disabled', false);
        }
        if (paramSelect === false) {
            $("input[name='blankRadio']").prop({disabled: true});
        }
    }
    $('#main-search').on('change select ', 'select, input', 'her', function (event) {
        const CATEGORY = 'category-main';
        const STATUS_BOOK = 'status-book';
        const LANGUAGE = 'language';
        const CHOOSE_BOOK = 'choose-book';
        const OTHERPARAM = 'other-parameter';
        let request = false;
        // Отправка запроса при нажатии
        $('#send-data-button').on('click', function (e) {
            e.preventDefault();

            if (sendStatus === false) {
                sendStatus = true;
                let postData = {};
                postData.category = $('#category-main option:selected').val();
                postData.statusbook = $('#status-book option:selected').val();
                postData.language = $('#language option:selected').val();

                if ($("#send-links").attr("checked") === 'checked') {
                    postData.selectToLink = 'true';
                }
                AjaxSend(postData, 'main_search_on_site');
            }
        });
        // Получаем id формы на котором произошло соботие
        let EventMain = event.currentTarget.id;
        switch (EventMain) {
            case CATEGORY:
                $("#status-book").prop('disabled', false);
                break;
            case STATUS_BOOK:
                $("#language").prop('disabled', false);
                break;
            case LANGUAGE:
                $("#send-data-button").prop('disabled', false);
                break;
            case CHOOSE_BOOK:
        }
        const checkboxInstance = $("#inlineCheckbox1");
        if (checkboxInstance.attr("checked") === 'checked') {
            request = true;
            activeFormOther(request);
        }
        if (checkboxInstance.attr("checked") !== 'checked') {
            request = false;
            activeFormOther(request);
        }
    });

    // Генерация ссилок на скачивание
    function getUrlVars() {
        let vars = [], hash;
        let hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (let i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }

    let count = {
        id: getUrlVars()["count"],
        key: getUrlVars()["key"]
    };

    let countdown = $('#countdown'),
        timer;
    if (count.id === "" || !count.id) {
        $('#js-content').html(`<h3>Неверная ссылка на скачивания</h3><br><a href="${js_attributes.home_url}">На главную</a>`);

        return false;
    }
    startCountdown(count, countdown);

    function startCountdown(parameters, countdownContainer) {
        let totalSeconds = 20;
        let remainingSeconds = totalSeconds;
        let linkInstance = $('#js-content');

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
                <button type="button" class="btn btn-warning download-countdown__retry hidden" data-role="retry">Повторить</button>
            `);
        }

        function setState(state, details) {
            let status = countdownContainer.find('[data-role="status"]');
            let seconds = countdownContainer.find('[data-role="seconds"]');
            let progress = countdownContainer.find('[data-role="bar"]');
            let error = countdownContainer.find('[data-role="error"]');
            let retry = countdownContainer.find('[data-role="retry"]');

            error.addClass('hidden').text('');
            retry.addClass('hidden');

            if (state === 'counting') {
                status.text('Готовим ссылку для скачивания...');
                seconds.text('Осталось секунд: ' + remainingSeconds);
                return;
            }

            if (state === 'loading-link') {
                status.text('Проверяем ссылку...');
                seconds.text('Секунды: 0');
                progress.removeClass('progress-bar-danger').addClass('progress-bar-striped active');
                progress.attr({'aria-valuenow': 100, 'style': 'width:100%'});
                linkInstance.html('<i class="fa fa-spinner fa-pulse"></i>');
                return;
            }

            if (state === 'error') {
                status.text('Не удалось получить ссылку.');
                seconds.text('');
                progress.removeClass('active progress-bar-striped').addClass('progress-bar-danger');
                error.removeClass('hidden').text(details || 'Произошла ошибка сети. Попробуйте еще раз.');
                retry.removeClass('hidden');
                linkInstance.html('');
                return;
            }

            if (state === 'ready') {
                status.text('Ссылка готова.');
                seconds.text('');
                progress.removeClass('active progress-bar-striped progress-bar-danger');
            }
        }

        function updateProgress() {
            let percentage = Math.round(((totalSeconds - remainingSeconds) / totalSeconds) * 100);
            let progress = countdownContainer.find('[data-role="bar"]');
            progress.attr({'aria-valuenow': percentage, 'style': 'width:' + percentage + '%'});
        }

        function resolveErrorMessage(jqXHR, responseData) {
            let nonceError = jqXHR && jqXHR.status === 403;
            if (responseData && responseData.data && responseData.data.message) {
                return responseData.data.message;
            }
            if (nonceError) {
                return 'Токен безопасности устарел. Обновите страницу и попробуйте снова.';
            }

            return 'Проблема с сетью или сервером. Нажмите "Повторить".';
        }

        function requestLink() {
            setState('loading-link');

            $.ajax({
                url: js_attributes.admin_ajax,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'return_link_to_book',
                    parameters: parameters,
                    _nonce: js_attributes.download_nonce
                },
                success: function (data) {
                    if (!data || !data.success || !data.data || !data.data.html) {
                        setState('error', resolveErrorMessage(null, data));
                        return;
                    }

                    linkInstance.html(data.data.html);
                    setState('ready');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
                    setState('error', resolveErrorMessage(jqXHR));
                }
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
