<?php
/**
 * Private Telegram support bridge for WebBooks visitors and staff.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Telegram;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Receives Telegram updates and bridges visitor messages to a private team chat.
 */
final class SupportBot {

	private const API_URL = 'https://api.telegram.org/bot';

	private const WEBHOOK_ROUTE = '/telegram/webhook';

	private const MESSAGE_MAP_PREFIX = 'webbooks_telegram_support_message_';

	private const MESSAGE_MAP_TTL = MONTH_IN_SECONDS;

	private const RATE_LIMIT_PREFIX = 'webbooks_telegram_support_rate_';

	private const RATE_LIMIT_WINDOW = 5 * MINUTE_IN_SECONDS;

	private const RATE_LIMIT_MAXIMUM = 20;

	/**
	 * Register the protected public webhook endpoint.
	 */
	public static function register(): void {
		add_action( 'rest_api_init', array( self::class, 'registerWebhookRoute' ) );
	}

	/**
	 * Register the endpoint called by Telegram.
	 */
	public static function registerWebhookRoute(): void {
		register_rest_route(
			'webbooks/v1',
			self::WEBHOOK_ROUTE,
			array(
				'methods'             => 'POST',
				'callback'            => array( self::class, 'handleWebhook' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Process a Telegram webhook update.
	 *
	 * The endpoint is public by necessity, but Telegram's secret-token header is
	 * required before any payload is processed.
	 *
	 * @param WP_REST_Request $request Incoming REST request.
	 * @return WP_REST_Response Webhook acknowledgement.
	 */
	public static function handleWebhook( WP_REST_Request $request ): WP_REST_Response {
		if ( ! self::isValidWebhookRequest( $request ) ) {
			return new WP_REST_Response( array( 'ok' => false ), 403 );
		}

		$update = $request->get_json_params();
		if ( ! is_array( $update ) || ! isset( $update['message'] ) || ! is_array( $update['message'] ) ) {
			return new WP_REST_Response( array( 'ok' => true ), 200 );
		}

		$message = $update['message'];
		$chat    = $message['chat'] ?? array();
		if ( ! is_array( $chat ) || ! isset( $chat['id'], $chat['type'] ) ) {
			return new WP_REST_Response( array( 'ok' => true ), 200 );
		}

		if ( 'private' === $chat['type'] ) {
			self::handleVisitorMessage( $message );
		} elseif ( self::connectSupportChat( $message ) ) {
			return new WP_REST_Response( array( 'ok' => true ), 200 );
		} elseif ( self::isSupportChat( $chat['id'] ) ) {
			self::handleSupportReply( $message );
		}

		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}

	/**
	 * Relay a visitor's private message to the configured support group.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 */
	private static function handleVisitorMessage( array $message ): void {
		$chat_id = self::chatIdFromMessage( $message );
		if ( null === $chat_id || self::isBotMessage( $message ) ) {
			return;
		}

		if ( self::isStartCommand( $message ) ) {
			self::sendMessage(
				$chat_id,
				self::visitorText( $message, 'start' )
			);
			return;
		}

		if ( ! self::isConfigured() ) {
			self::sendMessage( $chat_id, self::visitorText( $message, 'unavailable' ) );
			return;
		}

		if ( ! self::allowVisitorMessage( $chat_id ) ) {
			self::sendMessage( $chat_id, self::visitorText( $message, 'rate_limited' ) );
			return;
		}

		$forwarded = self::sendMessage(
			self::supportChatId(),
			self::formatSupportMessage( $message )
		);
		if ( ! is_array( $forwarded ) || ! isset( $forwarded['message_id'] ) ) {
			self::sendMessage( $chat_id, self::visitorText( $message, 'delivery_failed' ) );
			return;
		}

		self::storeMessageMap( (int) $forwarded['message_id'], $chat_id, self::messageId( $message ) );

		if ( self::hasCopyableContent( $message ) ) {
			$copied = self::copyMessage( self::supportChatId(), $chat_id, self::messageId( $message ) );
			if ( is_array( $copied ) && isset( $copied['message_id'] ) ) {
				self::storeMessageMap( (int) $copied['message_id'], $chat_id, self::messageId( $message ) );
			}
		}

		self::sendMessage( $chat_id, self::visitorText( $message, 'received' ) );
	}

	/**
	 * Send a staff reply from the private support group back to its visitor.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 */
	private static function handleSupportReply( array $message ): void {
		if ( self::isBotMessage( $message ) || ! isset( $message['reply_to_message'] ) || ! is_array( $message['reply_to_message'] ) ) {
			return;
		}

		$reply_to_message_id = self::messageId( $message['reply_to_message'] );
		if ( $reply_to_message_id <= 0 ) {
			return;
		}

		$mapping = get_transient( self::MESSAGE_MAP_PREFIX . $reply_to_message_id );
		if ( ! is_array( $mapping ) || ! isset( $mapping['chat_id'], $mapping['message_id'] ) ) {
			return;
		}

		self::copyMessage(
			(string) $mapping['chat_id'],
			self::supportChatId(),
			self::messageId( $message ),
			(int) $mapping['message_id']
		);
	}

	/**
	 * Pair the support chat once through a secret /connect command.
	 *
	 * This avoids giving the team a third-party bot access to a private group just
	 * to learn its numeric chat ID. The setup secret is only used until pairing
	 * succeeds and can then be removed from wp-config.php.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 * @return bool Whether the update was a processed connection attempt.
	 */
	private static function connectSupportChat( array $message ): bool {
		if ( '' !== self::supportChatId() || self::isBotMessage( $message ) || ! self::isGroupMessage( $message ) ) {
			return false;
		}

		$text = $message['text'] ?? '';
		if ( ! is_string( $text ) || 1 !== preg_match( '/^\/connect(?:@[A-Za-z0-9_]+)?\s+(.+)$/', $text, $matches ) ) {
			return false;
		}

		$setup_secret = self::setupSecret();
		if ( '' === $setup_secret || ! hash_equals( $setup_secret, trim( $matches[1] ) ) ) {
			return true;
		}

		$chat_id = self::chatIdFromMessage( $message );
		if ( null === $chat_id ) {
			return true;
		}

		update_option( 'webbooks_telegram_support_chat_id', $chat_id, false );
		self::sendMessage( $chat_id, 'WebBooks support group connected. You can now reply to forwarded visitor messages.' );

		return true;
	}

	/**
	 * Verify Telegram's webhook secret without exposing configuration details.
	 *
	 * @param WP_REST_Request $request Incoming REST request.
	 * @return bool Whether the request is authenticated.
	 */
	private static function isValidWebhookRequest( WP_REST_Request $request ): bool {
		$secret = self::webhookSecret();
		if ( '' === $secret ) {
			return false;
		}

		return hash_equals( $secret, (string) $request->get_header( 'x-telegram-bot-api-secret-token' ) );
	}

	/**
	 * Determine whether the webhook has all mandatory configuration.
	 */
	private static function isConfigured(): bool {
		return '' !== self::botToken() && '' !== self::supportChatId();
	}

	/**
	 * Determine whether a message was sent in a Telegram group or supergroup.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 */
	private static function isGroupMessage( array $message ): bool {
		$chat_type = $message['chat']['type'] ?? '';

		return is_string( $chat_type ) && in_array( $chat_type, array( 'group', 'supergroup' ), true );
	}

	/**
	 * Determine whether a group message belongs to the private support chat.
	 *
	 * @param mixed $chat_id Telegram chat ID.
	 */
	private static function isSupportChat( mixed $chat_id ): bool {
		return '' !== self::supportChatId() && self::supportChatId() === (string) $chat_id;
	}

	/**
	 * Build readable metadata for the support group's team members.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 * @return string Plain-text message.
	 */
	private static function formatSupportMessage( array $message ): string {
		$from        = isset( $message['from'] ) && is_array( $message['from'] ) ? $message['from'] : array();
		$first_name  = sanitize_text_field( (string) ( $from['first_name'] ?? '' ) );
		$last_name   = sanitize_text_field( (string) ( $from['last_name'] ?? '' ) );
		$username    = sanitize_text_field( (string) ( $from['username'] ?? '' ) );
		$display     = trim( $first_name . ' ' . $last_name );
		$identity    = '' !== $display ? $display : __( 'Telegram visitor', 'webbooks' );
		$user_handle = '' !== $username ? ' (@' . $username . ')' : '';
		$content     = self::messageContent( $message );
		$content     = '' !== $content ? $content : __( '[Media or attachment]', 'webbooks' );

		return sprintf(
			/* translators: 1: Visitor's display name. 2: Optional Telegram username. 3: Message preview. */
			__( "New WebBooks support request from %1\$s%2\$s:\n\n%3\$s", 'webbooks' ),
			$identity,
			$user_handle,
			$content
		);
	}

	/**
	 * Get a concise message preview without Telegram markup.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 * @return string Sanitized preview.
	 */
	private static function messageContent( array $message ): string {
		$content = $message['text'] ?? $message['caption'] ?? '';
		if ( ! is_string( $content ) ) {
			return '';
		}

		$content = trim( wp_strip_all_tags( $content ) );

		return mb_substr( $content, 0, 3000 );
	}

	/**
	 * Determine whether the message includes media that can be copied to the group.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 */
	private static function hasCopyableContent( array $message ): bool {
		$media_keys = array( 'animation', 'audio', 'document', 'photo', 'sticker', 'video', 'video_note', 'voice' );

		foreach ( $media_keys as $key ) {
			if ( isset( $message[ $key ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Store the relation between a group message and an originating visitor.
	 *
	 * @param int    $support_message_id Telegram message ID in the support group.
	 * @param string $visitor_chat_id Visitor's private Telegram chat ID.
	 * @param int    $visitor_message_id Original visitor message ID.
	 */
	private static function storeMessageMap( int $support_message_id, string $visitor_chat_id, int $visitor_message_id ): void {
		if ( $support_message_id <= 0 || $visitor_message_id <= 0 ) {
			return;
		}

		set_transient(
			self::MESSAGE_MAP_PREFIX . $support_message_id,
			array(
				'chat_id'    => $visitor_chat_id,
				'message_id' => $visitor_message_id,
			),
			self::MESSAGE_MAP_TTL
		);
	}

	/**
	 * Apply a modest rate limit while allowing normal multi-message conversations.
	 *
	 * @param string $chat_id Visitor's private Telegram chat ID.
	 */
	private static function allowVisitorMessage( string $chat_id ): bool {
		$key   = self::RATE_LIMIT_PREFIX . md5( $chat_id );
		$count = (int) get_transient( $key );
		if ( $count >= self::RATE_LIMIT_MAXIMUM ) {
			return false;
		}

		set_transient( $key, $count + 1, self::RATE_LIMIT_WINDOW );

		return true;
	}

	/**
	 * Copy one message between chats through the Telegram Bot API.
	 *
	 * @param string $target_chat_id Target Telegram chat ID.
	 * @param string $source_chat_id Source Telegram chat ID.
	 * @param int    $message_id Source message ID.
	 * @param int    $reply_to_message_id Target-chat message ID to reply to.
	 * @return array<string, mixed>|null Telegram result data, if successful.
	 */
	private static function copyMessage( string $target_chat_id, string $source_chat_id, int $message_id, int $reply_to_message_id = 0 ): ?array {
		$arguments = array(
			'chat_id'      => $target_chat_id,
			'from_chat_id' => $source_chat_id,
			'message_id'   => $message_id,
		);

		if ( $reply_to_message_id > 0 ) {
			$arguments['reply_to_message_id'] = $reply_to_message_id;
		}

		return self::callApi( 'copyMessage', $arguments );
	}

	/**
	 * Send a plain text message through the Telegram Bot API.
	 *
	 * @param string $chat_id Target Telegram chat ID.
	 * @param string $text Plain-text message body.
	 * @return array<string, mixed>|null Telegram result data, if successful.
	 */
	private static function sendMessage( string $chat_id, string $text ): ?array {
		return self::callApi(
			'sendMessage',
			array(
				'chat_id' => $chat_id,
				'text'    => $text,
			)
		);
	}

	/**
	 * Make an authenticated Telegram Bot API request.
	 *
	 * @param string               $method Telegram method name.
	 * @param array<string, mixed> $arguments Method payload.
	 * @return array<string, mixed>|null Telegram result data, if successful.
	 */
	private static function callApi( string $method, array $arguments ): ?array {
		$token = self::botToken();
		if ( '' === $token ) {
			return null;
		}

		$response = wp_remote_post(
			self::API_URL . rawurlencode( $token ) . '/' . rawurlencode( $method ),
			array(
				'timeout' => 10,
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode( $arguments ),
			)
		);
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}

		$payload = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $payload ) || empty( $payload['ok'] ) || ! isset( $payload['result'] ) || ! is_array( $payload['result'] ) ) {
			return null;
		}

		return $payload['result'];
	}

	/**
	 * Return a localized visitor-facing message based on Telegram language code.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 * @param string               $key Message identifier.
	 */
	private static function visitorText( array $message, string $key ): string {
		$language = self::visitorLanguage( $message );
		$messages = array(
			'start'           => array(
				'uk' => 'Вітаю! Напишіть ваше запитання або пропозицію — я передам повідомлення команді WebBooks.',
				'ru' => 'Здравствуйте! Напишите ваш вопрос или предложение — я передам сообщение команде WebBooks.',
				'pl' => 'Cześć! Napisz pytanie lub propozycję — przekażę wiadomość zespołowi WebBooks.',
				'en' => 'Hello! Send your question or proposal and I will pass it to the WebBooks team.',
			),
			'received'        => array(
				'uk' => 'Дякуємо! Повідомлення передано. Відповідь надійде сюди.',
				'ru' => 'Спасибо! Сообщение передано. Ответ придёт сюда.',
				'pl' => 'Dziękujemy! Wiadomość została przekazana. Odpowiedź przyjdzie tutaj.',
				'en' => 'Thank you! Your message was sent. The reply will arrive here.',
			),
			'delivery_failed' => array(
				'uk' => 'Не вдалося передати повідомлення. Будь ласка, спробуйте ще раз трохи пізніше.',
				'ru' => 'Не удалось передать сообщение. Пожалуйста, попробуйте ещё раз немного позже.',
				'pl' => 'Nie udało się przekazać wiadomości. Spróbuj ponownie za chwilę.',
				'en' => 'Your message could not be delivered. Please try again shortly.',
			),
			'rate_limited'    => array(
				'uk' => 'Будь ласка, зачекайте кілька хвилин перед наступним повідомленням.',
				'ru' => 'Пожалуйста, подождите несколько минут перед следующим сообщением.',
				'pl' => 'Poczekaj kilka minut przed wysłaniem kolejnej wiadomości.',
				'en' => 'Please wait a few minutes before sending another message.',
			),
			'unavailable'     => array(
				'uk' => 'Підтримка тимчасово недоступна. Будь ласка, спробуйте пізніше.',
				'ru' => 'Поддержка временно недоступна. Пожалуйста, попробуйте позже.',
				'pl' => 'Wsparcie jest chwilowo niedostępne. Spróbuj ponownie później.',
				'en' => 'Support is temporarily unavailable. Please try again later.',
			),
		);

		return $messages[ $key ][ $language ] ?? $messages[ $key ]['en'];
	}

	/**
	 * Resolve the visitor's supported language code.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 */
	private static function visitorLanguage( array $message ): string {
		$from     = isset( $message['from'] ) && is_array( $message['from'] ) ? $message['from'] : array();
		$language = strtolower( (string) ( $from['language_code'] ?? '' ) );
		$language = substr( $language, 0, 2 );

		return in_array( $language, array( 'uk', 'ru', 'pl', 'en' ), true ) ? $language : 'en';
	}

	/**
	 * Determine whether a message originates from a Telegram bot.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 */
	private static function isBotMessage( array $message ): bool {
		return ! empty( $message['from']['is_bot'] );
	}

	/**
	 * Determine whether the message invokes the standard bot start command.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 */
	private static function isStartCommand( array $message ): bool {
		return isset( $message['text'] ) && is_string( $message['text'] ) && 1 === preg_match( '/^\/start(?:\s|$)/', $message['text'] );
	}

	/**
	 * Get a private chat ID from a Telegram message.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 */
	private static function chatIdFromMessage( array $message ): ?string {
		$chat_id = $message['chat']['id'] ?? null;

		return is_int( $chat_id ) || is_string( $chat_id ) ? (string) $chat_id : null;
	}

	/**
	 * Get a Telegram message identifier.
	 *
	 * @param array<string, mixed> $message Telegram message payload.
	 */
	private static function messageId( array $message ): int {
		return isset( $message['message_id'] ) ? absint( $message['message_id'] ) : 0;
	}

	/** Return the configured Bot API token. */
	private static function botToken(): string {
		return defined( 'WEBBOOKS_TELEGRAM_BOT_TOKEN' ) ? trim( (string) constant( 'WEBBOOKS_TELEGRAM_BOT_TOKEN' ) ) : '';
	}

	/** Return the configured private support chat identifier. */
	private static function supportChatId(): string {
		if ( defined( 'WEBBOOKS_TELEGRAM_SUPPORT_CHAT_ID' ) ) {
			return trim( (string) constant( 'WEBBOOKS_TELEGRAM_SUPPORT_CHAT_ID' ) );
		}

		$chat_id = get_option( 'webbooks_telegram_support_chat_id', '' );

		return is_int( $chat_id ) || is_string( $chat_id ) ? trim( (string) $chat_id ) : '';
	}

	/** Return the Telegram webhook secret. */
	private static function webhookSecret(): string {
		return defined( 'WEBBOOKS_TELEGRAM_WEBHOOK_SECRET' ) ? trim( (string) constant( 'WEBBOOKS_TELEGRAM_WEBHOOK_SECRET' ) ) : '';
	}

	/** Return the one-time secret used to pair the support group. */
	private static function setupSecret(): string {
		return defined( 'WEBBOOKS_TELEGRAM_SETUP_SECRET' ) ? trim( (string) constant( 'WEBBOOKS_TELEGRAM_SETUP_SECRET' ) ) : '';
	}
}
