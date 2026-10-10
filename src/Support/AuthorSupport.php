<?php
/**
 * Public author-support data endpoints.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Support;

/**
 * Keeps payment details and Telegram links out of rendered page markup.
 */
final class AuthorSupport {

	private const OPTION_NAME           = 'wpuniq_theme_options';
	private const PAGE_MIGRATION_OPTION = 'webbooks_author_support_pages_version';
	private const DEFAULT_TELEGRAM_URL  = 'https://t.me/webbooks_support_bot?start=site';

	private const PAGE_TEMPLATE = 'template-support-author.php';

	private const PAGE_STATUS = 'draft';

	private const SUPPORT_PAGE_CACHE_TTL = 7200;

	/** Register public AJAX endpoints. */
	public static function register(): void {
		add_action( 'after_setup_theme', array( self::class, 'maybeInstallSupportPages' ), 20 );
		add_action( 'transition_post_status', array( self::class, 'invalidatePublishedPageUrlCache' ), 10, 3 );
		add_action( 'wp_ajax_webbooks_author_support_cards', array( self::class, 'paymentCards' ) );
		add_action( 'wp_ajax_nopriv_webbooks_author_support_cards', array( self::class, 'paymentCards' ) );
		add_action( 'wp_ajax_webbooks_author_support_telegram', array( self::class, 'telegramUrl' ) );
		add_action( 'wp_ajax_nopriv_webbooks_author_support_telegram', array( self::class, 'telegramUrl' ) );
	}

	/**
	 * Create and link the localized support pages once per theme version.
	 *
	 * The slug lookup makes this migration safe to repeat if a previous request
	 * was interrupted before all Polylang relationships could be saved.
	 */
	public static function maybeInstallSupportPages(): void {
		if ( self::pageMigrationVersion() === get_option( self::PAGE_MIGRATION_OPTION ) && self::supportPagesExist() ) {
			return;
		}

		if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
			return;
		}

		$definitions = self::supportPageDefinitions();
		$page_ids    = array();

		foreach ( $definitions as $language => $definition ) {
			$page_id = self::findOrCreateSupportPage( $definition['slug'], $definition['title'] );
			if ( $page_id <= 0 ) {
				return;
			}

			$page_ids[ $language ] = $page_id;
		}

		foreach ( $page_ids as $language => $page_id ) {
			pll_set_post_language( $page_id, $language );
		}

		pll_save_post_translations( $page_ids );

		update_option( self::PAGE_MIGRATION_OPTION, self::pageMigrationVersion(), false );
	}

	/**
	 * Keep the draft conversion as a separately tracked, one-time migration.
	 */
	private static function pageMigrationVersion(): string {
		return WEBBOOKS_VERSION . ':support-pages-draft-1';
	}

	/**
	 * Verify that every language-specific support page exists by slug.
	 */
	private static function supportPagesExist(): bool {
		$page_ids = array();

		foreach ( self::supportPageDefinitions() as $language => $definition ) {
			$page = get_page_by_path( $definition['slug'], OBJECT, 'page' );
			if ( ! $page instanceof \WP_Post ) {
				return false;
			}

			$page_ids[ $language ] = (int) $page->ID;
		}

		if ( ! function_exists( 'pll_get_post_translations' ) ) {
			return false;
		}

		$translations = pll_get_post_translations( $page_ids['uk'] );
		foreach ( $page_ids as $language => $page_id ) {
			if ( (int) ( $translations[ $language ] ?? 0 ) !== $page_id ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Return the localized support-page definitions keyed by Polylang slug.
	 *
	 * @return array<string, array{slug: string, title: string}>
	 */
	private static function supportPageDefinitions(): array {
		return array(
			'uk' => array(
				'slug'  => 'pidtrymaty-avtora',
				'title' => 'Підтримати автора',
			),
			'ru' => array(
				'slug'  => 'podderzhat-avtora',
				'title' => 'Поддержать автора',
			),
			'pl' => array(
				'slug'  => 'wesprzyj-autora',
				'title' => 'Wesprzyj autora',
			),
		);
	}

	/**
	 * Return the current language support page URL only when it is published.
	 *
	 * The result, including an unavailable page, is cached for two hours.
	 */
	public static function publishedPageUrl(): string {
		$language = \Webbooks\Localization\Polylang::currentLanguageSlug();
		if ( null === $language || ! array_key_exists( $language, self::supportPageDefinitions() ) ) {
			return '';
		}

		$cache_key = self::publishedPageUrlCacheKey( $language );
		$cached    = get_transient( $cache_key );
		if ( is_array( $cached ) && isset( $cached['url'] ) ) {
			return esc_url_raw( (string) $cached['url'] );
		}

		$definition = self::supportPageDefinitions()[ $language ];
		$page       = get_page_by_path( $definition['slug'], OBJECT, 'page' );
		$url        = '';

		if ( $page instanceof \WP_Post && 'publish' === $page->post_status ) {
			$page_language = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $page->ID, 'slug' ) : $language;
			if ( $language === $page_language ) {
				$url = (string) get_permalink( $page );
			}
		}

		set_transient( $cache_key, array( 'url' => $url ), self::SUPPORT_PAGE_CACHE_TTL );

		return $url;
	}

	/**
	 * Clear the matching language cache as soon as a support page changes status.
	 *
	 * @param string   $new_status New post status.
	 * @param string   $old_status Previous post status.
	 * @param \WP_Post $post Updated post.
	 */
	public static function invalidatePublishedPageUrlCache( string $new_status, string $old_status, \WP_Post $post ): void {
		if ( 'page' !== $post->post_type || $new_status === $old_status ) {
			return;
		}

		foreach ( self::supportPageDefinitions() as $language => $definition ) {
			if ( $definition['slug'] === $post->post_name ) {
				delete_transient( self::publishedPageUrlCacheKey( $language ) );

				return;
			}
		}
	}

	/**
	 * Build an isolated transient key for a localized public support link.
	 *
	 * @param string $language Polylang language slug.
	 */
	private static function publishedPageUrlCacheKey( string $language ): string {
		return 'webbooks_author_support_page_url_' . $language;
	}

	/**
	 * Find a page by stable slug or create it without overwriting existing copy.
	 * Support pages stay as drafts until their content is approved for publication.
	 *
	 * @param string $slug Stable support page slug.
	 * @param string $title Initial support page title.
	 */
	private static function findOrCreateSupportPage( string $slug, string $title ): int {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page instanceof \WP_Post ) {
			$updated_page_id = wp_update_post(
				array(
					'ID'          => $page->ID,
					'post_status' => self::PAGE_STATUS,
				),
				true
			);
			if ( is_wp_error( $updated_page_id ) ) {
				return 0;
			}

			update_post_meta( $page->ID, '_wp_page_template', self::PAGE_TEMPLATE );

			return (int) $page->ID;
		}

		$page_id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => self::PAGE_STATUS,
				'post_title'  => $title,
				'post_name'   => $slug,
			),
			true
		);
		if ( is_wp_error( $page_id ) ) {
			return 0;
		}

		update_post_meta( (int) $page_id, '_wp_page_template', self::PAGE_TEMPLATE );

		return (int) $page_id;
	}

	/**
	 * Return the supported card type values and labels.
	 *
	 * @return array<string, string>
	 */
	public static function cardTypes(): array {
		return array(
			'master'     => __( 'Master', 'webbooks' ),
			'visa'       => __( 'Visa', 'webbooks' ),
			'mastercard' => __( 'MasterCard', 'webbooks' ),
			'union'      => __( 'Union', 'webbooks' ),
			'other'      => __( 'Other', 'webbooks' ),
		);
	}

	/** Return the configured author name. */
	public static function authorName(): string {
		$options = self::options();
		$name    = sanitize_text_field( (string) ( $options['author_name'] ?? '' ) );

		return '' !== $name ? $name : 'Andrii';
	}

	/**
	 * Return rotating donation messages in the visitor's active language.
	 *
	 * @param string $language Polylang language slug.
	 * @return list<string>
	 */
	public static function motivationMessages( string $language ): array {
		$messages = array(
			'uk' => array(
				'Ваш донат допомагає WebBooks залишатися безкоштовним для читачів.',
				'Кожна підтримка додає до бібліотеки корисні книги з програмування.',
				'Навіть невеликий внесок робить незалежні знання доступнішими.',
				'Ви допомагаєте читачам навчатися, створювати й ділитися досвідом.',
				'Підтримка покриває непомітну, але важливу роботу над бібліотекою.',
				'Ваш внесок перетворює корисні книги на нові можливості для інших.',
				'Кожен донат зберігає простір для спокійного читання без зайвого шуму.',
				'Ви допомагаєте наступному читачеві швидше знайти потрібні знання.',
				'Підтримувана бібліотека може зростати й залишатися відкритою.',
				'Ваша доброта допомагає каталогу бути актуальним і корисним.',
				'Ви допомагаєте розробникам швидше знаходити наступну ідею.',
				'Донати дають незалежним навчальним ресурсам довше життя.',
				'Ваша підтримка зберігає WebBooks доступним саме тоді, коли це потрібно.',
				'Один внесок може допомогти комусь почати свій перший проєкт.',
				'Ви робите якісні технічні книги ближчими для всіх охочих.',
				'Підтримуючи проєкт, ви підтримуєте допитливі уми.',
				'Ваш донат живить маленькі покращення, які помічають читачі.',
				'Разом ми зберігаємо знання відкритими, впорядкованими й привітними.',
				'Ваша допомога дозволяє WebBooks думати про читачів, а не про шум.',
				'Дякуємо, що допомагаєте корисним знанням залишатися доступними.',
			),
			'ru' => array(
				'Ваш донат помогает WebBooks оставаться бесплатным для читателей.',
				'Каждая поддержка добавляет в библиотеку полезные книги по программированию.',
				'Даже небольшой вклад делает независимые знания доступнее.',
				'Вы помогаете читателям учиться, создавать и делиться опытом.',
				'Поддержка покрывает незаметную, но важную работу над библиотекой.',
				'Ваш вклад превращает полезные книги в новые возможности для других.',
				'Каждый донат сохраняет пространство для спокойного чтения без лишнего шума.',
				'Вы помогаете следующему читателю быстрее найти нужные знания.',
				'Поддерживаемая библиотека может расти и оставаться открытой.',
				'Ваша доброта помогает каталогу быть актуальным и полезным.',
				'Вы помогаете разработчикам быстрее находить следующую идею.',
				'Донаты дают независимым учебным ресурсам более долгую жизнь.',
				'Ваша поддержка сохраняет WebBooks доступным именно тогда, когда это нужно.',
				'Один вклад может помочь кому-то начать свой первый проект.',
				'Вы делаете качественные технические книги ближе для всех желающих.',
				'Поддерживая проект, вы поддерживаете любознательные умы.',
				'Ваш донат питает небольшие улучшения, которые замечают читатели.',
				'Вместе мы сохраняем знания открытыми, упорядоченными и дружелюбными.',
				'Ваша помощь позволяет WebBooks думать о читателях, а не о шуме.',
				'Спасибо, что помогаете полезным знаниям оставаться доступными.',
			),
			'pl' => array(
				'Twoja wpłata pomaga WebBooks pozostać bezpłatnym dla czytelników.',
				'Każde wsparcie dodaje do biblioteki wartościowe książki programistyczne.',
				'Nawet niewielka wpłata czyni niezależną wiedzę bardziej dostępną.',
				'Pomagasz czytelnikom uczyć się, tworzyć i dzielić doświadczeniem.',
				'Wsparcie pokrywa niewidoczną, ale ważną pracę nad biblioteką.',
				'Twój wkład zamienia użyteczne książki w nowe możliwości dla innych.',
				'Każda wpłata zachowuje przestrzeń do spokojnego czytania bez szumu.',
				'Pomagasz kolejnemu czytelnikowi szybciej znaleźć potrzebną wiedzę.',
				'Wspierana biblioteka może rosnąć i pozostawać otwarta.',
				'Twoja życzliwość pomaga katalogowi być aktualnym i użytecznym.',
				'Pomagasz programistom szybciej znaleźć kolejną inspirację.',
				'Darowizny dają niezależnym zasobom edukacyjnym dłuższe życie.',
				'Twoje wsparcie utrzymuje WebBooks dostępne wtedy, gdy jest potrzebne.',
				'Jedna wpłata może pomóc komuś rozpocząć pierwszy projekt.',
				'Dzięki Tobie dobre książki techniczne są bliżej wszystkich chętnych.',
				'Wspierając projekt, wspierasz ciekawość i rozwój.',
				'Twoja wpłata napędza małe ulepszenia, które zauważają czytelnicy.',
				'Razem zachowujemy wiedzę otwartą, uporządkowaną i przyjazną.',
				'Twoja pomoc pozwala WebBooks skupiać się na czytelnikach, nie na szumie.',
				'Dziękujemy, że pomagasz użytecznej wiedzy pozostać dostępna.',
			),
			'en' => array(
				'Every contribution helps keep WebBooks free to read.',
				'Your donation helps add useful programming books to the library.',
				'A small gift keeps independent technical knowledge accessible.',
				'You help readers learn, build, and share better software.',
				'Support covers the quiet but important work behind the library.',
				'Your contribution turns useful books into new opportunities for others.',
				'Every donation protects a calm reading space without extra noise.',
				'You make practical knowledge easier for the next reader to find.',
				'A supported library can grow while keeping its doors open.',
				'Your kindness helps the catalog stay current and useful.',
				'You help developers reach their next idea faster.',
				'Donations give independent learning resources a longer life.',
				'Your support keeps WebBooks available when it matters.',
				'One contribution can help someone begin their first project.',
				'You help keep quality technical books within reach.',
				'Supporting the project means supporting curious minds.',
				'Your donation powers small improvements that readers notice.',
				'Together we keep knowledge open, organized, and welcoming.',
				'Your help lets WebBooks focus on readers, not noise.',
				'Thank you for helping useful knowledge stay available.',
			),
		);

		return $messages[ $language ] ?? $messages['en'];
	}

	/** Return the configured Telegram URL only after an explicit AJAX request. */
	public static function configuredTelegramUrl(): string {
		$options = self::options();
		$url     = (string) ( $options['telegram_url'] ?? self::DEFAULT_TELEGRAM_URL );

		return self::sanitizeTelegramUrl( $url );
	}

	/**
	 * Validate a Telegram URL for use in the public support interface.
	 *
	 * @param string $url Submitted URL.
	 */
	public static function sanitizeTelegramUrl( string $url ): string {
		$url    = esc_url_raw( trim( $url ), array( 'https' ) );
		$parsed = wp_parse_url( $url );
		$host   = strtolower( (string) ( $parsed['host'] ?? '' ) );

		if ( 'https' !== ( $parsed['scheme'] ?? '' ) || ! in_array( $host, array( 't.me', 'www.t.me' ), true ) ) {
			return '';
		}

		return $url;
	}

	/** Send configured payment cards after a user-initiated request. */
	public static function paymentCards(): void {
		self::verifyPublicRequest();

		wp_send_json_success(
			array(
				'author_name' => self::authorName(),
				'cards'       => self::cards(),
			)
		);
	}

	/** Send the Telegram URL after a user-initiated request. */
	public static function telegramUrl(): void {
		self::verifyPublicRequest();

		$url = self::configuredTelegramUrl();
		if ( '' === $url ) {
			wp_send_json_error( array( 'message' => __( 'Telegram contact is unavailable.', 'webbooks' ) ), 404 );
		}

		wp_send_json_success( array( 'url' => $url ) );
	}

	/**
	 * Return safely formatted configured cards for the public modal.
	 *
	 * @return array<int, array{number: string, type: string, label: string}>
	 */
	private static function cards(): array {
		$options    = self::options();
		$cards      = $options['payment_cards'] ?? array();
		$card_types = self::cardTypes();
		$public     = array();

		if ( ! is_array( $cards ) ) {
			return $public;
		}

		foreach ( $cards as $card ) {
			if ( ! is_array( $card ) ) {
				continue;
			}

			$number = preg_replace( '/\D+/', '', (string) ( $card['card_number'] ?? '' ) ) ?? '';
			$type   = sanitize_key( (string) ( $card['card_type'] ?? '' ) );
			if ( ! self::isValidCardNumber( $number ) || ! isset( $card_types[ $type ] ) ) {
				continue;
			}

			$public[] = array(
				'number' => trim( chunk_split( $number, 4, ' ' ) ),
				'type'   => $type,
				'label'  => $card_types[ $type ],
			);
		}

		return $public;
	}

	/**
	 * Confirm the request originated from a rendered theme page.
	 */
	private static function verifyPublicRequest(): void {
		$nonce = sanitize_text_field( (string) ( filter_input( INPUT_POST, 'nonce' ) ?? '' ) );
		if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid security token.', 'webbooks' ) ), 403 );
		}
	}

	/**
	 * Check an unformatted payment card number using the Luhn algorithm.
	 *
	 * @param string $number Digits only.
	 */
	public static function isValidCardNumber( string $number ): bool {
		if ( 1 !== preg_match( '/^\d{12,19}$/', $number ) ) {
			return false;
		}

		$sum       = 0;
		$alternate = false;
		for ( $index = strlen( $number ) - 1; $index >= 0; --$index ) {
			$digit = (int) $number[ $index ];
			if ( $alternate ) {
				$digit *= 2;
				$digit  = $digit > 9 ? $digit - 9 : $digit;
			}

			$sum      += $digit;
			$alternate = ! $alternate;
		}

		return 0 === $sum % 10;
	}

	/**
	 * Read the complete theme option array.
	 *
	 * @return array<string, mixed>
	 */
	private static function options(): array {
		$options = get_option( self::OPTION_NAME, array() );

		return is_array( $options ) ? $options : array();
	}
}
