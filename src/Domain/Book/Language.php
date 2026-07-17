<?php
/**
 * Book language domain value.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Domain\Book;

enum Language: string {

	case RU  = 'ru';
	case UA  = 'ua';
	case EN  = 'en';
	case OTH = 'oth';

	/**
	 * Get the localized language label.
	 */
	public function label(): string {
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext -- false positive for enum instance context.
		return match ( $this ) {
			self::RU  => __( 'Russian', 'webbooks' ),
			self::UA  => __( 'Ukrainian', 'webbooks' ),
			self::EN  => __( 'English', 'webbooks' ),
			self::OTH => __( 'Other', 'webbooks' ),
		};
	}

	/**
	 * Create an enum instance from an optional stored value.
	 *
	 * @param string|null $value Stored language value.
	 */
	public static function fromNullable( ?string $value ): ?self {
		$value = is_string( $value ) ? trim( $value ) : '';

		return '' === $value ? null : self::tryFrom( $value );
	}
}
