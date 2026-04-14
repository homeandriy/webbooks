<?php

declare(strict_types=1);

namespace Domain\Book;

enum Language: string
{
    case RU = 'ru';
    case UA = 'ua';
    case EN = 'en';
    case OTH = 'oth';

    public function label(): string
    {
        return match ($this) {
            self::RU => 'Русский',
            self::UA => 'Украинский',
            self::EN => 'Английский',
            self::OTH => 'Другой',
        };
    }

    public static function fromNullable(?string $value): ?self
    {
        $value = is_string($value) ? trim($value) : '';

        return $value === '' ? null : self::tryFrom($value);
    }
}
