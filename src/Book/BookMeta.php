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

    public static function getNormalizedMeta(int $postId): array
    {
        $title = get_the_title($postId);
        $author = trim((string) get_post_meta($postId, 'autor', true));
        $yearRaw = trim((string) get_post_meta($postId, 'year', true));
        $format = trim((string) get_post_meta($postId, 'format', true));
        $languageRaw = function_exists('get_field') ? trim((string) get_field('language', $postId)) : '';
        $pagesRaw = trim((string) get_post_meta($postId, 'number_page', true));

        return [
            'title' => $title,
            'author' => $author,
            'year' => preg_match('/^\d{4}$/', $yearRaw) ? $yearRaw : '',
            'format' => $format,
            'language' => self::getLanguage($languageRaw),
            'pages' => is_numeric($pagesRaw) ? (int) $pagesRaw : null,
            'image' => self::getBookImage($postId),
        ];
    }

    public static function getBookSchema(int $postId): array
    {
        $meta = self::getNormalizedMeta($postId);

        return self::filterSchema([
            '@context' => 'https://schema.org',
            '@type' => 'Book',
            'name' => $meta['title'],
            'author' => $meta['author'] !== '' ? [
                '@type' => 'Person',
                'name' => $meta['author'],
            ] : null,
            'inLanguage' => $meta['language'],
            'bookFormat' => $meta['format'],
            'numberOfPages' => $meta['pages'],
            'image' => $meta['image'],
            'datePublished' => $meta['year'],
        ]);
    }

    public static function filterSchema(array $data): array
    {
        $filtered = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = self::filterSchema($value);
                if ($value === []) {
                    continue;
                }
            }

            if ($value === null || $value === '') {
                continue;
            }

            $filtered[$key] = $value;
        }

        return $filtered;
    }

    private static function getBookImage(int $postId): string
    {
        $image = get_the_post_thumbnail_url($postId, 'full');

        return $image ?: get_template_directory_uri() . '/screenshot.png';
    }
}
