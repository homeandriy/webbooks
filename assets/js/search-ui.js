jQuery(function ($) {
    const messages = webbooksAjax.messages;
    const $loop = $('.content-loop');
    const initialResults = $loop.html();
    const initialLoadMoreVisible = $('.alm-btn-wrap').is(':visible');
    const $form = $('#main-search');
    const keys = ['wb_mode', 'wb_category', 'wb_level', 'wb_language', 'wb_page', 'wb_query', 'wb_links'];
    let filterId = 0;
    let searchId = 0;
    let timer;
    let activeSearch;
    let restoring = false;
    const $status = $('<p class="search-status" role="status" aria-live="polite"></p>').insertBefore($loop);
    const $error = $('<div class="search-error" hidden></div>').insertBefore($loop);

    function writeUrl(mode, data, method) {
        if (!method) return;
        const url = new window.URL(window.location.href);
        keys.forEach(key => url.searchParams.delete(key));
        url.searchParams.set('wb_mode', mode);
        if (mode === 'filter') {
            [['wb_category', data.category], ['wb_level', data.statusbook], ['wb_language', data.language], ['wb_links', data.selectToLink ? '1' : '']]
                .forEach(([key, value]) => { if (value) url.searchParams.set(key, value); });
        } else url.searchParams.set('wb_query', data.StrTosearch);
        if (data.paged > 1) url.searchParams.set('wb_page', data.paged);
        if (url.href !== window.location.href) window.history[method + 'State'](null, '', url);
    }

    function showError($container, retry, error) {
        const expired = error && (error.status || (error.jqXHR && error.jqXHR.status)) === 403;
        $container.empty().prop('hidden', false);
        $('<p role="alert"></p>').text(expired ? messages.expired : messages.error).appendTo($container);
        $('<button type="button" class="btn btn-default"></button>').text(expired ? messages.reload : messages.retry)
            .on('click', expired ? () => window.location.reload() : retry).appendTo($container);
    }

    function values(page = 1) {
        const category = $('#category-main').val();
        return {category: category === '-1' ? '' : category || '', statusbook: $('#status-book').val() || '',
            language: $('#language').val() || '', selectToLink: $('#send-links').prop('checked') || false, paged: page};
    }

    function filter(data, method, focus) {
        const id = ++filterId;
        $loop.attr('aria-busy', 'true').addClass('results-is-loading');
        $status.text(messages.loading);
        $error.empty().prop('hidden', true);
        $form.find('[type="submit"]').prop('disabled', true);
        return window.WebBooksAjax.wpRequest({url: webbooksAjax.admin_ajax, action: 'main_search_on_site', nonce: webbooksAjax.nonce, var: data})
            .then(response => {
                if (id !== filterId) return;
                if (!response || !response.success || !response.data || typeof response.data.html !== 'string') throw new Error('Invalid response');
                $loop.html(response.data.html);
                $('.alm-btn-wrap').hide();
                $status.text(messages.found.replace('%d', response.data.total || 0));
                writeUrl('filter', data, method);
                if (focus && $loop.length) {
                    $loop.attr('tabindex', '-1')[0].focus({preventScroll: true});
                    $('html, body').stop(true).animate({scrollTop: Math.max(0, $loop.offset().top - 80)},
                        window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 300);
                }
            }).catch(error => {
                if (id !== filterId) return;
                $status.text('');
                showError($error, () => filter(data, method, focus), error);
            }).finally(() => {
                if (id !== filterId) return;
                $loop.attr('aria-busy', 'false').removeClass('results-is-loading');
                $form.find('[type="submit"]').prop('disabled', false);
            });
    }

    function closeSearch() {
        ++searchId;
        activeSearch = null;
        $('.navbar-form').removeClass('open search-is-loading');
        $('#search-result-mobile-modal-wrap').removeClass('is-open search-is-loading').hide();
        $('.search-results').attr('aria-busy', 'false').empty();
        $('.search-feedback').text('');
        $('.load-search').removeClass('fa-spinner fa-spin').addClass('fa-search');
    }

    function search(data, method) {
        // A pending catalog response must not replace the URL of a newer search.
        ++filterId;
        $loop.attr('aria-busy', 'false').removeClass('results-is-loading');
        $form.find('[type="submit"]').prop('disabled', false);
        $status.text('');
        const id = ++searchId;
        activeSearch = {...data};
        const $results = $('#' + (data.isMobile ? data.id : 'search-result'));
        const $wrapper = data.isMobile ? $('#' + data.id + '-wrap') : $results.closest('.navbar-form');
        const $feedback = $('#' + (data.isMobile ? 'mobile-search-feedback' : 'desktop-search-feedback'));
        $wrapper.addClass('search-is-loading ' + (data.isMobile ? 'is-open' : 'open'));
        if (data.isMobile) $wrapper.show();
        $results.attr('aria-busy', 'true');
        $feedback.text(messages.loading);
        $('.load-search').removeClass('fa-search').addClass('fa-spinner fa-spin');
        return window.WebBooksAjax.wpRequest({url: webbooksAjax.admin_ajax, action: 'global_search', nonce: webbooksAjax.nonce, var: data})
            .then(response => {
                if (id !== searchId) return;
                if (!response || !response.success || !response.data || typeof response.data.html !== 'string') throw new Error('Invalid response');
                $results.html(response.data.html);
                $feedback.text(messages.found.replace('%d', response.data.total || 0));
                writeUrl('search', data, method);
            }).catch(error => {
                if (id !== searchId) return;
                $feedback.text('');
                showError($results, () => search(data, method), error);
            }).finally(() => {
                if (id !== searchId) return;
                $wrapper.removeClass('search-is-loading');
                $results.attr('aria-busy', 'false');
                $('.load-search').removeClass('fa-spinner fa-spin').addClass('fa-search');
            });
    }

    function fromInput(input, explicit) {
        const term = input.value.trim();
        if (!term || (!explicit && Array.from(term).length < 4)) { closeSearch(); return; }
        const data = {StrTosearch: term, explicit: Boolean(explicit), paged: 1};
        if (input.dataset.idres) { data.isMobile = true; data.id = input.dataset.idres; }
        search(data, explicit ? 'push' : 'replace');
    }

    $(document).on('input', '.main-search', function () {
        clearTimeout(timer);
        closeSearch();
        if (Array.from(this.value.trim()).length < 4) {
            const url = new window.URL(window.location.href);
            if (url.searchParams.get('wb_mode') === 'search') {
                keys.forEach(key => url.searchParams.delete(key));
                window.history.replaceState(null, '', url);
            }
            return;
        }
        timer = setTimeout(() => fromInput(this, false), 300);
    });
    $(document).on('submit', '.navbar-form, .mobile-search-form', function (event) {
        event.preventDefault(); clearTimeout(timer);
        fromInput($(this).find('.main-search')[0], true);
    });
    $(document).on('keydown', '.main-search', function (event) {
        if (event.key === 'Escape') { clearTimeout(timer); closeSearch(); }
    });
    $(document).on('click', '.ajax-pagination a[data-page]', function (event) {
        event.preventDefault();
        const page = Number(this.dataset.page);
        if (page < 1 || !Number.isFinite(page)) return;
        if (this.dataset.ajaxAction === 'global_search' && activeSearch) search({...activeSearch, paged: page}, 'push');
        else filter(values(page), 'push', true);
    });
    $form.on('submit', event => { event.preventDefault(); clearTimeout(timer); closeSearch(); filter(values(), 'push', true); });
    $form.on('reset', () => {
        if (!restoring) { clearTimeout(timer); closeSearch(); setTimeout(() => filter(values(), 'push', true), 0); }
    });

    function restore() {
        clearTimeout(timer); closeSearch(); ++filterId;
        $error.empty().prop('hidden', true); $status.text('');
        $loop.attr('aria-busy', 'false').removeClass('results-is-loading').html(initialResults);
        $('.alm-btn-wrap').toggle(initialLoadMoreVisible);
        $form.find('[type="submit"]').prop('disabled', false);
        const params = new window.URL(window.location.href).searchParams;
        const page = Math.max(1, Math.min(1000, Number(params.get('wb_page')) || 1));
        restoring = true;
        if ($form.length) $form[0].reset();
        restoring = false;
        $('#category-main').val(params.get('wb_category') || '');
        $('#status-book').val(params.get('wb_level') || '');
        $('#language').val(params.get('wb_language') || '');
        $('#send-links').prop('checked', params.get('wb_links') === '1');
        $('.main-search').val(params.get('wb_query') || '');
        if (params.get('wb_mode') === 'filter' && $loop.length) {
            if ($.fn.collapse) $('#filter-search').collapse('show');
            filter(values(page), null, false);
        }
        else if (params.get('wb_mode') === 'search' && params.get('wb_query')) {
            const mobile = window.matchMedia('(max-width: 991px)').matches;
            if (mobile && $.fn.modal) $('#mobile-search-modal').modal('show');
            search({StrTosearch: params.get('wb_query'), explicit: true, paged: page,
                isMobile: mobile, id: 'search-result-mobile-modal'}, null);
        }
    }
    window.addEventListener('popstate', restore);
    restore();
});
