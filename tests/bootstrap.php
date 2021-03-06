<?php

class WP_Post
{
    public $ID;
    public $post_title = 'Title of post';
    public $post_name = 'title-of-post';
    public $post_content = '<p>Post content</p>';
    public $post_date = '2021-03-24 14:15:18';
    public $post_author = 69;
    public $meta = [
        'date' => '20220112',
        'time' => '13:00:00',
        'date_to' => '20220113',
        'time_to' => '18:00:00',
        'registration_start' => '12-01-2021 15:00:00',
        'registration_end' => '12-01-2029 15:00:00',
        'tickets' => ['adult' => 4, 'child' => 1],
        'registration_limit' => 500,
    ];

    public function __construct($id = 420)
    {
        $this->ID = $id;
    }
}

function get_the_title(int $postId)
{
    $post = new WP_Post();
    return $post->post_title;
}

function date_i18n($format, $timestamp_with_offset)
{
    $dateTime = new DateTime();
    $dateTime->setTimestamp($timestamp_with_offset);
    return $dateTime->format($format);
}

function get_post_type(int $postId)
{
    if (987 == $postId) {
        return 'invalid_post_type';
    }
    if (69 == $postId) {
        return 'registration';
    }
    return 'event';
}

function get_post_meta(int $post_id, string $key = '', bool $single = false)
{
    $post = new WP_Post();
    return $post->meta[$key];
}

function get_option(string $option, $default = false)
{
    switch ($option) {
        case 'date_format':
            return 'j F Y';
            break;

        case 'time_format':
            return 'H:i';
            break;
    }
    return false;
}

function get_field(string $selector, $postid, bool $format = true)
{
    switch ($selector) {
        case 'ticket_types':
            return [
                [
                    "title" => "Adult",
                    "price" => "44",
                    "ticket_limit_per_registration" => "",
                    "registration_limit" => ""
                ],
                [
                    "title" => "Child",
                    "price" => "22",
                    "ticket_limit_per_registration" => "",
                    "registration_limit" => ""
                ]
            ];
            break;
        case 'extra_fields':
            return [
                [
                    "field_type" => "textarea",
                    "label" => "Remark",
                    "required" => true
                ]
            ];
    }
    return false;
}

function __(string $string, string $textdomain)
{
    return $string;
}

function wp_parse_args(array $args, array $defaults = array())
{
    if (is_object($args)) {
        $parsed_args = get_object_vars($args);
    } elseif (is_array($args)) {
        $parsed_args =& $args;
    } else {
        wp_parse_str($args, $parsed_args);
    }
 
    if (is_array($defaults) && $defaults) {
        return array_merge($defaults, $parsed_args);
    }
    return $parsed_args;
}

function get_posts(array $args)
{
    $id = 0;
    switch ($args['post_type']) {
        case 'event':
            $id = 420;
            break;
        case 'registration':
            $id = 69;
            break;
    }
    $return = [];
    $args['posts_per_page'] = ( $args['posts_per_page'] == -1 ? 999 : $args['posts_per_page'] );
    for ($i=0; $i < $args['posts_per_page']; $i++) {
        $return[] = new WP_Post($id);
    }
    return $return;
}

function sanitize_title($title, $fallback_title = '', $context = 'save')
{
    $raw_title = $title;

    if ('save' === $context) {
        $title = remove_accents($title);
    }

    if ('' === $title || false === $title) {
        $title = $fallback_title;
    }

    return strtolower($title);
}

function remove_accents($string, $locale = '')
{
    if (! preg_match('/[\x80-\xff]/', $string)) {
        return $string;
    }

    if (seems_utf8($string)) {
        $chars = array(
            // Decompositions for Latin-1 Supplement.
            '??' => 'a',
            '??' => 'o',
            '??' => 'A',
            '??' => 'A',
            '??' => 'A',
            '??' => 'A',
            '??' => 'A',
            '??' => 'A',
            '??' => 'AE',
            '??' => 'C',
            '??' => 'E',
            '??' => 'E',
            '??' => 'E',
            '??' => 'E',
            '??' => 'I',
            '??' => 'I',
            '??' => 'I',
            '??' => 'I',
            '??' => 'D',
            '??' => 'N',
            '??' => 'O',
            '??' => 'O',
            '??' => 'O',
            '??' => 'O',
            '??' => 'O',
            '??' => 'U',
            '??' => 'U',
            '??' => 'U',
            '??' => 'U',
            '??' => 'Y',
            '??' => 'TH',
            '??' => 's',
            '??' => 'a',
            '??' => 'a',
            '??' => 'a',
            '??' => 'a',
            '??' => 'a',
            '??' => 'a',
            '??' => 'ae',
            '??' => 'c',
            '??' => 'e',
            '??' => 'e',
            '??' => 'e',
            '??' => 'e',
            '??' => 'i',
            '??' => 'i',
            '??' => 'i',
            '??' => 'i',
            '??' => 'd',
            '??' => 'n',
            '??' => 'o',
            '??' => 'o',
            '??' => 'o',
            '??' => 'o',
            '??' => 'o',
            '??' => 'o',
            '??' => 'u',
            '??' => 'u',
            '??' => 'u',
            '??' => 'u',
            '??' => 'y',
            '??' => 'th',
            '??' => 'y',
            '??' => 'O',
            // Decompositions for Latin Extended-A.
            '??' => 'A',
            '??' => 'a',
            '??' => 'A',
            '??' => 'a',
            '??' => 'A',
            '??' => 'a',
            '??' => 'C',
            '??' => 'c',
            '??' => 'C',
            '??' => 'c',
            '??' => 'C',
            '??' => 'c',
            '??' => 'C',
            '??' => 'c',
            '??' => 'D',
            '??' => 'd',
            '??' => 'D',
            '??' => 'd',
            '??' => 'E',
            '??' => 'e',
            '??' => 'E',
            '??' => 'e',
            '??' => 'E',
            '??' => 'e',
            '??' => 'E',
            '??' => 'e',
            '??' => 'E',
            '??' => 'e',
            '??' => 'G',
            '??' => 'g',
            '??' => 'G',
            '??' => 'g',
            '??' => 'G',
            '??' => 'g',
            '??' => 'G',
            '??' => 'g',
            '??' => 'H',
            '??' => 'h',
            '??' => 'H',
            '??' => 'h',
            '??' => 'I',
            '??' => 'i',
            '??' => 'I',
            '??' => 'i',
            '??' => 'I',
            '??' => 'i',
            '??' => 'I',
            '??' => 'i',
            '??' => 'I',
            '??' => 'i',
            '??' => 'IJ',
            '??' => 'ij',
            '??' => 'J',
            '??' => 'j',
            '??' => 'K',
            '??' => 'k',
            '??' => 'k',
            '??' => 'L',
            '??' => 'l',
            '??' => 'L',
            '??' => 'l',
            '??' => 'L',
            '??' => 'l',
            '??' => 'L',
            '??' => 'l',
            '??' => 'L',
            '??' => 'l',
            '??' => 'N',
            '??' => 'n',
            '??' => 'N',
            '??' => 'n',
            '??' => 'N',
            '??' => 'n',
            '??' => 'n',
            '??' => 'N',
            '??' => 'n',
            '??' => 'O',
            '??' => 'o',
            '??' => 'O',
            '??' => 'o',
            '??' => 'O',
            '??' => 'o',
            '??' => 'OE',
            '??' => 'oe',
            '??' => 'R',
            '??' => 'r',
            '??' => 'R',
            '??' => 'r',
            '??' => 'R',
            '??' => 'r',
            '??' => 'S',
            '??' => 's',
            '??' => 'S',
            '??' => 's',
            '??' => 'S',
            '??' => 's',
            '??' => 'S',
            '??' => 's',
            '??' => 'T',
            '??' => 't',
            '??' => 'T',
            '??' => 't',
            '??' => 'T',
            '??' => 't',
            '??' => 'U',
            '??' => 'u',
            '??' => 'U',
            '??' => 'u',
            '??' => 'U',
            '??' => 'u',
            '??' => 'U',
            '??' => 'u',
            '??' => 'U',
            '??' => 'u',
            '??' => 'U',
            '??' => 'u',
            '??' => 'W',
            '??' => 'w',
            '??' => 'Y',
            '??' => 'y',
            '??' => 'Y',
            '??' => 'Z',
            '??' => 'z',
            '??' => 'Z',
            '??' => 'z',
            '??' => 'Z',
            '??' => 'z',
            '??' => 's',
            // Decompositions for Latin Extended-B.
            '??' => 'S',
            '??' => 's',
            '??' => 'T',
            '??' => 't',
            // Euro sign.
            '???' => 'E',
            // GBP (Pound) sign.
            '??' => '',
            // Vowels with diacritic (Vietnamese).
            // Unmarked.
            '??' => 'O',
            '??' => 'o',
            '??' => 'U',
            '??' => 'u',
            // Grave accent.
            '???' => 'A',
            '???' => 'a',
            '???' => 'A',
            '???' => 'a',
            '???' => 'E',
            '???' => 'e',
            '???' => 'O',
            '???' => 'o',
            '???' => 'O',
            '???' => 'o',
            '???' => 'U',
            '???' => 'u',
            '???' => 'Y',
            '???' => 'y',
            // Hook.
            '???' => 'A',
            '???' => 'a',
            '???' => 'A',
            '???' => 'a',
            '???' => 'A',
            '???' => 'a',
            '???' => 'E',
            '???' => 'e',
            '???' => 'E',
            '???' => 'e',
            '???' => 'I',
            '???' => 'i',
            '???' => 'O',
            '???' => 'o',
            '???' => 'O',
            '???' => 'o',
            '???' => 'O',
            '???' => 'o',
            '???' => 'U',
            '???' => 'u',
            '???' => 'U',
            '???' => 'u',
            '???' => 'Y',
            '???' => 'y',
            // Tilde.
            '???' => 'A',
            '???' => 'a',
            '???' => 'A',
            '???' => 'a',
            '???' => 'E',
            '???' => 'e',
            '???' => 'E',
            '???' => 'e',
            '???' => 'O',
            '???' => 'o',
            '???' => 'O',
            '???' => 'o',
            '???' => 'U',
            '???' => 'u',
            '???' => 'Y',
            '???' => 'y',
            // Acute accent.
            '???' => 'A',
            '???' => 'a',
            '???' => 'A',
            '???' => 'a',
            '???' => 'E',
            '???' => 'e',
            '???' => 'O',
            '???' => 'o',
            '???' => 'O',
            '???' => 'o',
            '???' => 'U',
            '???' => 'u',
            // Dot below.
            '???' => 'A',
            '???' => 'a',
            '???' => 'A',
            '???' => 'a',
            '???' => 'A',
            '???' => 'a',
            '???' => 'E',
            '???' => 'e',
            '???' => 'E',
            '???' => 'e',
            '???' => 'I',
            '???' => 'i',
            '???' => 'O',
            '???' => 'o',
            '???' => 'O',
            '???' => 'o',
            '???' => 'O',
            '???' => 'o',
            '???' => 'U',
            '???' => 'u',
            '???' => 'U',
            '???' => 'u',
            '???' => 'Y',
            '???' => 'y',
            // Vowels with diacritic (Chinese, Hanyu Pinyin).
            '??' => 'a',
            // Macron.
            '??' => 'U',
            '??' => 'u',
            // Acute accent.
            '??' => 'U',
            '??' => 'u',
            // Caron.
            '??' => 'A',
            '??' => 'a',
            '??' => 'I',
            '??' => 'i',
            '??' => 'O',
            '??' => 'o',
            '??' => 'U',
            '??' => 'u',
            '??' => 'U',
            '??' => 'u',
            // Grave accent.
            '??' => 'U',
            '??' => 'u',
        );

        // Used for locale-specific rules.
        if (empty($locale)) {
            $locale = get_locale();
        }

        /*
         * German has various locales (de_DE, de_CH, de_AT, ...) with formal and informal variants.
         * There is no 3-letter locale like 'def', so checking for 'de' instead of 'de_' is safe,
         * since 'de' itself would be a valid locale too.
         */
        if (str_starts_with($locale, 'de')) {
            $chars['??'] = 'Ae';
            $chars['??'] = 'ae';
            $chars['??'] = 'Oe';
            $chars['??'] = 'oe';
            $chars['??'] = 'Ue';
            $chars['??'] = 'ue';
            $chars['??'] = 'ss';
        } elseif ('da_DK' === $locale) {
            $chars['??'] = 'Ae';
            $chars['??'] = 'ae';
            $chars['??'] = 'Oe';
            $chars['??'] = 'oe';
            $chars['??'] = 'Aa';
            $chars['??'] = 'aa';
        } elseif ('ca' === $locale) {
            $chars['l??l'] = 'll';
        } elseif ('sr_RS' === $locale || 'bs_BA' === $locale) {
            $chars['??'] = 'DJ';
            $chars['??'] = 'dj';
        }

        $string = strtr($string, $chars);
    } else {
        $chars = array();
        // Assume ISO-8859-1 if not UTF-8.
        $chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
            . "\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
            . "\xc3\xc4\xc5\xc7\xc8\xc9\xca"
            . "\xcb\xcc\xcd\xce\xcf\xd1\xd2"
            . "\xd3\xd4\xd5\xd6\xd8\xd9\xda"
            . "\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
            . "\xe4\xe5\xe7\xe8\xe9\xea\xeb"
            . "\xec\xed\xee\xef\xf1\xf2\xf3"
            . "\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
            . "\xfc\xfd\xff";

        $chars['out'] = 'EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy';

        $string              = strtr($string, $chars['in'], $chars['out']);
        $double_chars        = array();
        $double_chars['in']  = array( "\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe" );
        $double_chars['out'] = array( 'OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th' );
        $string              = str_replace($double_chars['in'], $double_chars['out'], $string);
    }

    return $string;
}

function wp_timezone()
{
    return new DateTimeZone('Europe/Brussels');
}
