jQuery(document).ready(function ($) {
    const api = window.WebBooksAjax;
    const getUrlVars = window.WebBooksCompat.getUrlVars;


    let count = {
        id: getUrlVars()["count"],
        key: getUrlVars()["key"]
    };

    let countdown = $('#countdown'),
        timer;
    if (count.id === "" || !count.id) {
        $('#js-content').html(`<h3>Неверная ссылка на скачивания</h3><br><a href="${webbooksAjax.home_url}">На главную</a>`);

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

            api.wpRequest({
                url: webbooksAjax.admin_ajax,
                action: 'return_link_to_book',
                parameters: parameters,
                _nonce: webbooksAjax.download_nonce
            }).then(function (data) {
                if (!data || !data.success || !data.data || !data.data.html) {
                    setState('error', resolveErrorMessage(null, data));
                    return;
                }

                linkInstance.html(data.data.html);
                setState('ready');
            }).catch(function (error) {
                console.log(error);
                setState('error', resolveErrorMessage(error && error.jqXHR ? error.jqXHR : null));
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
