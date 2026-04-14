<?php

const WEBBOOKS_COMMENT_NONCE_ACTION = 'webbooks_comment_submit';
const WEBBOOKS_COMMENT_NONCE_NAME = 'webbooks_comment_nonce';
const WEBBOOKS_COMMENT_PRIVACY_FIELD = 'webbooks_comment_privacy';



add_action('admin_notices', 'webbooks_comments_recaptcha_admin_notice');
function webbooks_comments_recaptcha_admin_notice(): void {
    if (!is_admin() || !current_user_can('manage_options') || webbooks_is_recaptcha_configured()) {
        return;
    }

    echo '<div class="notice notice-warning"><p>';
    echo esc_html__('Comments are disabled. Please configure GC_V2_PUBLIC and GC_V2_SECRET constants in wp-config.php.', 'webbooks');
    echo '</p></div>';
}

function webbooks_get_recaptcha_site_key(): string {
    $fromConst = defined('GC_V2_PUBLIC') ? (string) constant('GC_V2_PUBLIC') : '';

    return (string) apply_filters('webbooks_recaptcha_site_key', trim($fromConst));
}

function webbooks_get_recaptcha_secret_key(): string {
    $fromConst = defined('GC_V2_SECRET') ? (string) constant('GC_V2_SECRET') : '';

    return (string) apply_filters('webbooks_recaptcha_secret_key', trim($fromConst));
}

function webbooks_is_recaptcha_configured(): bool {
    return webbooks_get_recaptcha_site_key() !== '' && webbooks_get_recaptcha_secret_key() !== '';
}
add_filter('preprocess_comment', 'webbooks_validate_comment_security');
function webbooks_validate_comment_security(array $commentData): array {
    if (is_admin()) {
        return $commentData;
    }

    if (!webbooks_is_recaptcha_configured()) {
        wp_die(
            esc_html__('Comments are currently disabled by site configuration.', 'webbooks'),
            esc_html__('Comments unavailable', 'webbooks'),
            ['response' => 503, 'back_link' => true]
        );
    }

    $nonce = sanitize_text_field((string) filter_input(INPUT_POST, WEBBOOKS_COMMENT_NONCE_NAME));
    if (empty($nonce) || !wp_verify_nonce($nonce, WEBBOOKS_COMMENT_NONCE_ACTION)) {
        wp_die(
            esc_html__('Security check failed. Please refresh the page and try again.', 'webbooks'),
            esc_html__('Security error', 'webbooks'),
            ['response' => 403, 'back_link' => true]
        );
    }

    $isGuest = !is_user_logged_in();

    if ($isGuest) {
        $author = sanitize_text_field((string) ($commentData['comment_author'] ?? ''));
        $email = sanitize_email((string) ($commentData['comment_author_email'] ?? ''));
        $privacyAccepted = sanitize_text_field((string) filter_input(INPUT_POST, WEBBOOKS_COMMENT_PRIVACY_FIELD));

        if ($author === '' || $email === '' || !is_email($email)) {
            wp_die(
                esc_html__('Please provide a display name and a valid email address.', 'webbooks'),
                esc_html__('Validation error', 'webbooks'),
                ['response' => 400, 'back_link' => true]
            );
        }

        if ($privacyAccepted !== '1') {
            wp_die(
                esc_html__('You must accept the privacy policy before posting a comment.', 'webbooks'),
                esc_html__('Validation error', 'webbooks'),
                ['response' => 400, 'back_link' => true]
            );
        }

        $cleanContent = wp_strip_all_tags((string) ($commentData['comment_content'] ?? ''));
        $cleanContent = preg_replace('~(https?://\S+|www\.\S+)~iu', '', $cleanContent) ?? '';
        $cleanContent = trim($cleanContent);

        if ($cleanContent === '') {
            wp_die(
                esc_html__('Comment text is required.', 'webbooks'),
                esc_html__('Validation error', 'webbooks'),
                ['response' => 400, 'back_link' => true]
            );
        }

        $commentData['comment_author'] = $author;
        $commentData['comment_author_email'] = $email;
        $commentData['comment_author_url'] = '';
        $commentData['comment_content'] = $cleanContent;
    }

    $captchaResponse = sanitize_text_field((string) filter_input(INPUT_POST, 'g-recaptcha-response'));
    if (!webbooks_verify_recaptcha($captchaResponse)) {
        wp_die(
            esc_html__('Captcha verification failed. Please confirm you are not a robot.', 'webbooks'),
            esc_html__('Captcha error', 'webbooks'),
            ['response' => 403, 'back_link' => true]
        );
    }

    if ($isGuest) {
        $ip = webbooks_get_comment_request_ip();
        $email = sanitize_email((string) ($commentData['comment_author_email'] ?? ''));

        if (webbooks_is_comment_rate_limited($ip, $email)) {
            $cooldown = max(1, (int) apply_filters('webbooks_comment_rate_limit_seconds', 30));
            wp_die(
                esc_html(sprintf(__('Too many comments. Please wait %d seconds before posting again.', 'webbooks'), $cooldown)),
                esc_html__('Rate limit reached', 'webbooks'),
                ['response' => 429, 'back_link' => true]
            );
        }
    }

    return $commentData;
}

add_action('comment_post', 'webbooks_mark_comment_rate_limit', 10, 2);
function webbooks_mark_comment_rate_limit(int $commentId, $commentApproved): void {
    if ((int) $commentApproved === 0 || $commentApproved === 'spam' || $commentApproved === 'trash') {
        return;
    }

    $comment = get_comment($commentId);
    if (!$comment instanceof WP_Comment) {
        return;
    }

    if ((int) $comment->user_id === 0) {
        $ip = webbooks_get_comment_request_ip();
        $email = sanitize_email((string) $comment->comment_author_email);
        $key = webbooks_comment_rate_limit_key($ip, $email);
        $cooldown = max(1, (int) apply_filters('webbooks_comment_rate_limit_seconds', 30));
        set_transient($key, time(), $cooldown);
    }

    if (function_exists('pll_current_language')) {
        $lang = pll_current_language('slug');
        if (!empty($lang)) {
            add_comment_meta($commentId, 'webbooks_comment_lang', sanitize_key((string) $lang), true);
        }
    }
}

add_filter('comments_array', 'webbooks_filter_comments_by_current_language', 10, 2);
function webbooks_filter_comments_by_current_language(array $comments, int $postId): array {
    if (is_admin() || !function_exists('pll_current_language')) {
        return $comments;
    }

    $currentLang = (string) pll_current_language('slug');
    if ($currentLang === '') {
        return $comments;
    }

    return array_values(array_filter($comments, static function ($comment) use ($currentLang) {
        if (!$comment instanceof WP_Comment) {
            return false;
        }

        $commentLang = (string) get_comment_meta((int) $comment->comment_ID, 'webbooks_comment_lang', true);
        if ($commentLang === '') {
            return false;
        }

        return $commentLang === $currentLang;
    }));
}


add_filter('get_comments_number', 'webbooks_filter_comments_number_by_language', 10, 2);
function webbooks_filter_comments_number_by_language($count, $postId) {
    if (is_admin() || !function_exists('pll_current_language')) {
        return $count;
    }

    $currentLang = (string) pll_current_language('slug');
    if ($currentLang === '') {
        return $count;
    }

    $localizedCount = get_comments([
        'post_id' => (int) $postId,
        'status' => 'approve',
        'count' => true,
        'meta_key' => 'webbooks_comment_lang',
        'meta_value' => $currentLang,
    ]);

    return (string) $localizedCount;
}
function webbooks_is_comment_rate_limited(string $ip, string $email): bool {
    $key = webbooks_comment_rate_limit_key($ip, $email);

    return get_transient($key) !== false;
}

function webbooks_comment_rate_limit_key(string $ip, string $email): string {
    return 'webbooks_comment_rl_' . md5(strtolower(trim($ip)) . '|' . strtolower(trim($email)));
}

function webbooks_get_comment_request_ip(): string {
    return sanitize_text_field((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'));
}

function webbooks_verify_recaptcha(string $captchaResponse): bool {
    $secretKey = webbooks_get_recaptcha_secret_key();
    if (empty($secretKey) || empty($captchaResponse)) {
        return false;
    }

    $ip = webbooks_get_comment_request_ip();
    $request = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
        'timeout' => 10,
        'body' => [
            'secret' => $secretKey,
            'response' => $captchaResponse,
            'remoteip' => $ip,
        ],
    ]);

    if (is_wp_error($request)) {
        return false;
    }

    $statusCode = (int) wp_remote_retrieve_response_code($request);
    if ($statusCode !== 200) {
        return false;
    }

    $payload = json_decode((string) wp_remote_retrieve_body($request), true);

    return is_array($payload) && !empty($payload['success']);
}
