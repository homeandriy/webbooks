<?php

namespace Webbooks\Book;

class BookMeta
{
    public static function getLanguage(string $slug): string
    {
        return \Domain\Book\Language::fromNullable($slug)?->label() ?? 'Не указано';
    }

    public static function getComplexity(string $slug): string
    {
        return \Domain\Book\Complexity::fromNullable($slug)?->label() ?? 'Не указано';
    }
}
