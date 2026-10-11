<?php

declare(strict_types=1);

namespace Webbooks\Domain\Book;

enum Complexity: string {

	case BEGINNER     = 'beginner';
	case PROFESSIONAL = 'professional';

	public function label(): string {
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext -- false positive for enum instance context.
		return match ( $this ) {
			self::BEGINNER => __( 'For beginners', 'webbooks' ),
			self::PROFESSIONAL => __( 'For advanced readers', 'webbooks' ),
		};
	}

	public static function fromNullable( ?string $value ): ?self {
		$value = is_string( $value ) ? trim( $value ) : '';

		return $value === '' ? null : self::tryFrom( $value );
	}
}
