<?php
/**
 * Book complexity domain value.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Domain\Book;

enum Complexity: string {

	case BEGINNER     = 'beginner';
	case PROFESSIONAL = 'professional';

	/**
	 * Get the localized complexity label.
	 */
	public function label(): string {
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext -- false positive for enum instance context.
		return match ( $this ) {
			self::BEGINNER     => __( 'For beginner programmers', 'webbooks' ),
			self::PROFESSIONAL => __( 'For advanced programmers', 'webbooks' ),
		};
	}

	/**
	 * Create an enum instance from an optional stored value.
	 *
	 * @param string|null $value Stored complexity value.
	 */
	public static function fromNullable( ?string $value ): ?self {
		$value = is_string( $value ) ? trim( $value ) : '';

		return '' === $value ? null : self::tryFrom( $value );
	}
}
