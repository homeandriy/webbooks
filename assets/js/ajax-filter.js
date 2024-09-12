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
        let TypeData = 'html';
        // Проверка типа получаемых данных

        $.ajax({
            url: js_attributes.admin_ajax,
            type: 'POST',
            dataType: TypeData,
            data: {
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
                $('#result').append('');
                $('.content-loop').removeClass('fa-spinner')
                    .removeClass('fa')
                    .removeClass('fa-spin')
                    .removeClass('fa-5x')
                    .removeClass('custom-spin')
                    .html('')
                    .append(response);
                ScrollToResult();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
            }
        });
    }

    mainSearchSelector.bind('keyup', function (e) {
        e.preventDefault();
        if (mainSearchSelector.val().length >= 3) {
            let sendsearch = {};
            sendsearch.StrTosearch = mainSearchSelector.val();
            mainSearch(sendsearch, searchParam.action);
        }
    });


    // Глобальний поиск по сайту
    function mainSearch(param, action) {
        $.ajax({
            url: js_attributes.admin_ajax,
            type: 'POST',
            dataType: 'html',
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
                if (param.isMobile) {
                    $('#' + param.id + '-wrap').css({'max-width': window.screen.availWidth - 10, 'display': 'block'})
                    $('#' + param.id).html('').html(data);
                } else {
                    $('.load-search').removeClass('fa-spinner').removeClass('fa-spin').addClass('fa-search');
                    $('#search-result').html('').append(data);
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

    let countdown = $('#countdown span'),
        button = $('button'),
        timer;
    if (count.id === "" || !count.id) {
        $('#link').html(`<h3>Неверная ссылка на скачивания</h3><br><a href="${js_attributes.home_url}">На главную</a>`);

        return false;
    }
    startCountdown(count, button, countdown);
    function startCountdown(parameters, button, countdown) {
        let startFrom = 20;
        countdown.text(startFrom).parent('p').show();
        button.hide();
        timer = setInterval(function () {
            countdown.text(--startFrom);
            if (startFrom <= 0) {
                clearInterval(timer);
                countdown.text('Сейчас появится ссылка');
                let linkInstance =  $('#link');
                $.ajax({
                    url: js_attributes.admin_ajax,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        action: 'return_link_to_book',
                        parameters: parameters,
                        _nonce: js_attributes.nonce
                    },
                    beforeSend: function () {
                        linkInstance.html('<i class="fa fa-spinner fa-pulse"></i>');
                    },
                    success: function (data) {
                        linkInstance.html('');
                        linkInstance.append(data);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
                    }
                });
                button.show();
            }
        }, 1000);
    }
});
