export function initializeDownload() {
        const countdownContainer = document.querySelector('#countdown');
        const linkContainer = document.querySelector('#js-content');
        const params = new URLSearchParams(window.location.search);
        const parameters = {id: params.get('count') || '', key: params.get('key') || ''};

        if (!countdownContainer || !linkContainer) return;
        if (!parameters.id) {
            const heading = document.createElement('h3');
            heading.textContent = 'Невірне посилання для завантаження';
            const home = document.createElement('a');
            home.href = window.webbooksAjax?.home_url || '/';
            home.textContent = 'На головну';
            linkContainer.replaceChildren(heading, document.createElement('br'), home);
            return;
        }

        countdownContainer.innerHTML = '<div class="download-countdown__status" data-role="status"></div><div class="progress download-countdown__progress"><div class="progress-bar progress-bar-striped active download-countdown__bar" data-role="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div></div><div class="download-countdown__seconds" data-role="seconds"></div><div class="alert alert-danger download-countdown__error hidden" data-role="error"></div><button type="button" class="btn btn-warning download-countdown__retry hidden" data-role="retry">Повторити</button>';
        const status = countdownContainer.querySelector('[data-role="status"]');
        const seconds = countdownContainer.querySelector('[data-role="seconds"]');
        const progress = countdownContainer.querySelector('[data-role="bar"]');
        const error = countdownContainer.querySelector('[data-role="error"]');
        const retry = countdownContainer.querySelector('[data-role="retry"]');
        const totalSeconds = 20;
        let remainingSeconds = totalSeconds;
        let timer;

        function setState(state, details) {
            error.classList.add('hidden');
            error.textContent = '';
            retry.classList.add('hidden');
            if (state === 'counting') {
                status.textContent = 'Готуємо посилання для завантаження…';
                seconds.textContent = 'Залишилося секунд: ' + remainingSeconds;
            } else if (state === 'loading-link') {
                status.textContent = 'Перевіряємо посилання…';
                seconds.textContent = 'Секунди: 0';
                progress.classList.remove('progress-bar-danger');
                progress.classList.add('progress-bar-striped', 'active');
                progress.setAttribute('aria-valuenow', '100');
                progress.style.width = '100%';
                linkContainer.innerHTML = '<i class="fa fa-spinner fa-pulse" aria-label="Завантаження"></i>';
            } else if (state === 'error') {
                status.textContent = 'Не вдалося отримати посилання.';
                seconds.textContent = '';
                progress.classList.remove('active', 'progress-bar-striped');
                progress.classList.add('progress-bar-danger');
                error.classList.remove('hidden');
                error.textContent = details || 'Проблема з мережею або сервером. Спробуйте ще раз.';
                retry.classList.remove('hidden');
                linkContainer.replaceChildren();
            } else if (state === 'ready') {
                status.textContent = 'Посилання готове.';
                seconds.textContent = '';
                progress.classList.remove('active', 'progress-bar-striped', 'progress-bar-danger');
            }
        }

        function updateProgress() {
            const percentage = Math.round(((totalSeconds - remainingSeconds) / totalSeconds) * 100);
            progress.setAttribute('aria-valuenow', String(percentage));
            progress.style.width = percentage + '%';
        }

        function errorMessage(requestError, response) {
            if (response?.data?.message) return response.data.message;
            if (requestError?.status === 403) return 'Токен безпеки застарів. Оновіть сторінку та спробуйте ще раз.';
            return 'Проблема з мережею або сервером. Натисніть «Повторити».';
        }

        function requestLink() {
            setState('loading-link');
            window.WebBooksAjax.wpRequest({
                url: window.webbooksAjax.admin_ajax,
                action: 'return_link_to_book',
                parameters,
                _nonce: window.webbooksAjax.download_nonce
            }).then((response) => {
                if (!response?.success || !response.data?.html) {
                    setState('error', errorMessage(null, response));
                    return;
                }
                linkContainer.innerHTML = response.data.html;
                setState('ready');
            }).catch((requestError) => setState('error', errorMessage(requestError)));
        }

        function runCountdown() {
            window.clearInterval(timer);
            remainingSeconds = totalSeconds;
            linkContainer.replaceChildren();
            setState('counting');
            updateProgress();
            timer = window.setInterval(() => {
                remainingSeconds -= 1;
                setState('counting');
                updateProgress();
                if (remainingSeconds <= 0) {
                    window.clearInterval(timer);
                    requestLink();
                }
            }, 1000);
        }

        retry.addEventListener('click', runCountdown);
        runCountdown();
}
