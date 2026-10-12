const minimumSearchLength = 3;
const keys = ['wb_mode', 'wb_category', 'wb_level', 'wb_language', 'wb_page', 'wb_query', 'wb_links'];

export function initializeSearchUi() {
        const config = window.webbooksAjax || {};
        const messages = config.messages || {};
        const loop = document.querySelector('.content-loop');
        const form = document.querySelector('#main-search');
        const initialResults = loop ? loop.innerHTML : '';
        const loadMore = document.querySelector('.alm-btn-wrap');
        const initialLoadMoreVisible = loadMore ? !loadMore.hidden && window.getComputedStyle(loadMore).display !== 'none' : false;
        let filterId = 0;
        let searchId = 0;
        let timer;
        let activeSearch;
        let restoring = false;
        const status = document.createElement('p');
        const errorBox = document.createElement('div');

        status.className = 'search-status';
        status.setAttribute('role', 'status');
        status.setAttribute('aria-live', 'polite');
        errorBox.className = 'search-error';
        errorBox.hidden = true;
        if (loop) {
            loop.before(status, errorBox);
        }

        const text = (key, fallback) => messages[key] || fallback;
        const setBusy = (element, value) => element?.setAttribute('aria-busy', String(value));
        const setSearchIcons = (loading) => document.querySelectorAll('.load-search').forEach((icon) => {
            icon.classList.toggle('fa-spinner', loading);
            icon.classList.toggle('fa-spin', loading);
            icon.classList.toggle('fa-search', !loading);
        });

        function writeUrl(mode, data, method) {
            if (!method) return;
            const url = new URL(window.location.href);
            keys.forEach((key) => url.searchParams.delete(key));
            url.searchParams.set('wb_mode', mode);
            if (mode === 'filter') {
                [['wb_category', data.category], ['wb_level', data.statusbook], ['wb_language', data.language], ['wb_links', data.selectToLink ? '1' : '']]
                    .forEach(([key, value]) => { if (value) url.searchParams.set(key, value); });
            } else {
                url.searchParams.set('wb_query', data.StrTosearch);
            }
            if (data.paged > 1) url.searchParams.set('wb_page', data.paged);
            if (url.href !== window.location.href) window.history[method + 'State'](null, '', url);
        }

        function showError(container, retry, requestError) {
            const expired = requestError?.status === 403 || requestError?.jqXHR?.status === 403;
            container.replaceChildren();
            container.hidden = false;
            const message = document.createElement('p');
            message.setAttribute('role', 'alert');
            message.textContent = expired ? text('expired', 'Your session has expired.') : text('error', 'Could not load results.');
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-default';
            button.textContent = expired ? text('reload', 'Reload page') : text('retry', 'Try again');
            button.addEventListener('click', expired ? () => window.location.reload() : retry, {once: true});
            container.append(message, button);
        }

        function values(page = 1) {
            const category = document.querySelector('#category-main')?.value || '';
            return {
                category: category === '-1' ? '' : category,
                statusbook: document.querySelector('#status-book')?.value || '',
                language: document.querySelector('#language')?.value || '',
                selectToLink: Boolean(document.querySelector('#send-links')?.checked),
                paged: page
            };
        }

        function filter(data, method, focus) {
            if (!loop || !window.WebBooksAjax) return Promise.resolve();
            const id = ++filterId;
            loop.classList.add('results-is-loading');
            setBusy(loop, true);
            status.textContent = text('loading', 'Loading results...');
            errorBox.replaceChildren();
            errorBox.hidden = true;
            const submit = form?.querySelector('[type="submit"]');
            if (submit) submit.disabled = true;

            return window.WebBooksAjax.wpRequest({url: config.admin_ajax, action: 'main_search_on_site', nonce: config.nonce, var: data})
                .then((response) => {
                    if (id !== filterId) return;
                    if (!response?.success || typeof response.data?.html !== 'string') throw new Error('Invalid response');
                    loop.innerHTML = response.data.html;
                    if (loadMore) loadMore.hidden = true;
                    status.textContent = text('found', 'Found results: %d').replace('%d', response.data.total || 0);
                    writeUrl('filter', data, method);
                    if (focus) {
                        loop.tabIndex = -1;
                        loop.focus({preventScroll: true});
                        const top = Math.max(0, loop.getBoundingClientRect().top + window.scrollY - 80);
                        window.scrollTo({top, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'});
                    }
                })
                .catch((requestError) => {
                    if (id !== filterId) return;
                    status.textContent = '';
                    showError(errorBox, () => filter(data, method, focus), requestError);
                })
                .finally(() => {
                    if (id !== filterId) return;
                    loop.classList.remove('results-is-loading');
                    setBusy(loop, false);
                    if (submit) submit.disabled = false;
                });
        }

        function closeSearch() {
            ++searchId;
            activeSearch = null;
            document.querySelectorAll('.webbooks-search-form').forEach((searchForm) => searchForm.classList.remove('open', 'search-is-loading'));
            const mobileWrap = document.querySelector('#search-result-mobile-modal-wrap');
            if (mobileWrap) {
                mobileWrap.classList.remove('is-open', 'search-is-loading');
                mobileWrap.hidden = true;
            }
            document.querySelectorAll('.search-results').forEach((results) => {
                setBusy(results, false);
                results.replaceChildren();
            });
            document.querySelectorAll('.search-feedback').forEach((feedback) => { feedback.textContent = ''; });
            setSearchIcons(false);
        }

        function search(data, method) {
            if (!window.WebBooksAjax) return Promise.resolve();
            ++filterId;
            if (loop) {
                setBusy(loop, false);
                loop.classList.remove('results-is-loading');
            }
            const submit = form?.querySelector('[type="submit"]');
            if (submit) submit.disabled = false;
            status.textContent = '';
            const id = ++searchId;
            activeSearch = {...data};
            const results = document.querySelector('#' + (data.isMobile ? data.id : 'search-result'));
            if (!results) return Promise.resolve();
            const wrapper = data.isMobile ? document.querySelector('#' + data.id + '-wrap') : results.closest('.webbooks-search-form');
            const feedback = document.querySelector('#' + (data.isMobile ? 'mobile-search-feedback' : 'desktop-search-feedback'));
            wrapper?.classList.add('search-is-loading', data.isMobile ? 'is-open' : 'open');
            if (data.isMobile && wrapper) wrapper.hidden = false;
            setBusy(results, true);
            if (feedback) feedback.textContent = text('loading', 'Loading results...');
            setSearchIcons(true);

            return window.WebBooksAjax.wpRequest({url: config.admin_ajax, action: 'global_search', nonce: config.nonce, var: data})
                .then((response) => {
                    if (id !== searchId) return;
                    if (!response?.success || typeof response.data?.html !== 'string') throw new Error('Invalid response');
                    results.innerHTML = response.data.html;
                    if (feedback) feedback.textContent = text('found', 'Found results: %d').replace('%d', response.data.total || 0);
                    writeUrl('search', data, method);
                })
                .catch((requestError) => {
                    if (id !== searchId) return;
                    if (feedback) feedback.textContent = '';
                    showError(results, () => search(data, method), requestError);
                })
                .finally(() => {
                    if (id !== searchId) return;
                    wrapper?.classList.remove('search-is-loading');
                    setBusy(results, false);
                    setSearchIcons(false);
                });
        }

        function fromInput(input, explicit) {
            const term = input.value.trim();
            if (Array.from(term).length < minimumSearchLength) {
                closeSearch();
                return;
            }
            const data = {StrTosearch: term, explicit: Boolean(explicit), paged: 1};
            if (input.dataset.idres) {
                data.isMobile = true;
                data.id = input.dataset.idres;
            }
            search(data, explicit ? 'push' : 'replace');
        }

        document.addEventListener('input', (event) => {
            if (!event.target.matches('.main-search')) return;
            window.clearTimeout(timer);
            closeSearch();
            if (Array.from(event.target.value.trim()).length < minimumSearchLength) {
                const url = new URL(window.location.href);
                if (url.searchParams.get('wb_mode') === 'search') {
                    keys.forEach((key) => url.searchParams.delete(key));
                    window.history.replaceState(null, '', url);
                }
                return;
            }
            timer = window.setTimeout(() => fromInput(event.target, false), 300);
        });
        document.addEventListener('submit', (event) => {
            const searchForm = event.target.closest('.webbooks-search-form, .mobile-search-form');
            if (!searchForm) return;
            event.preventDefault();
            window.clearTimeout(timer);
            const input = searchForm.querySelector('.main-search');
            if (input) fromInput(input, true);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && event.target.matches('.main-search')) {
                window.clearTimeout(timer);
                closeSearch();
            }
        });
        document.addEventListener('pointerdown', (event) => {
            if (!event.target.closest('.webbooks-search-form, #mobile-search-modal')) {
                window.clearTimeout(timer);
                closeSearch();
            }
        });
        document.addEventListener('focusout', (event) => {
            const searchForm = event.target.closest('.webbooks-search-form');
            if (!searchForm) return;
            window.setTimeout(() => {
                if (!searchForm.contains(document.activeElement)) closeSearch();
            }, 0);
        });
        document.addEventListener('click', (event) => {
            const link = event.target.closest('.ajax-pagination a[data-page]');
            if (!link) return;
            event.preventDefault();
            const page = Number(link.dataset.page);
            if (page < 1 || !Number.isFinite(page)) return;
            if (link.dataset.ajaxAction === 'global_search' && activeSearch) search({...activeSearch, paged: page}, 'push');
            else filter(values(page), 'push', true);
        });
        form?.addEventListener('submit', (event) => {
            event.preventDefault();
            window.clearTimeout(timer);
            closeSearch();
            filter(values(), 'push', true);
        });
        form?.addEventListener('reset', () => {
            if (!restoring) {
                window.clearTimeout(timer);
                closeSearch();
                window.setTimeout(() => filter(values(), 'push', true), 0);
            }
        });

        function restore() {
            window.clearTimeout(timer);
            closeSearch();
            ++filterId;
            errorBox.replaceChildren();
            errorBox.hidden = true;
            status.textContent = '';
            if (loop) {
                setBusy(loop, false);
                loop.classList.remove('results-is-loading');
                loop.innerHTML = initialResults;
            }
            if (loadMore) loadMore.hidden = !initialLoadMoreVisible;
            const submit = form?.querySelector('[type="submit"]');
            if (submit) submit.disabled = false;
            const params = new URL(window.location.href).searchParams;
            const page = Math.max(1, Math.min(1000, Number(params.get('wb_page')) || 1));
            restoring = true;
            form?.reset();
            restoring = false;
            const setValue = (selector, value) => {
                const element = document.querySelector(selector);
                if (element) element.value = value;
            };
            setValue('#category-main', params.get('wb_category') || '');
            setValue('#status-book', params.get('wb_level') || '');
            setValue('#language', params.get('wb_language') || '');
            const links = document.querySelector('#send-links');
            if (links) links.checked = params.get('wb_links') === '1';
            document.querySelectorAll('.main-search').forEach((input) => { input.value = params.get('wb_query') || ''; });
            if (params.get('wb_mode') === 'filter' && loop) {
                window.WebBooksUI?.showCollapse('#filter-search');
                filter(values(page), null, false);
            } else if (params.get('wb_mode') === 'search' && params.get('wb_query')) {
                const mobile = window.matchMedia('(max-width: 991px)').matches;
                if (mobile) window.WebBooksUI?.showModal('#mobile-search-modal');
                search({StrTosearch: params.get('wb_query'), explicit: true, paged: page, isMobile: mobile, id: 'search-result-mobile-modal'}, null);
            }
        }

        window.addEventListener('popstate', restore);
        restore();
}
