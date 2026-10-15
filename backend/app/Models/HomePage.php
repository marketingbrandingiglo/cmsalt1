<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    protected $table = 'home_page';

    public const FIELDS = [
        'intro_kicker_id',
        'intro_kicker_en',
        'intro_title_id',
        'intro_title_en',
        'intro_description_id',
        'intro_description_en',
        'intro_cta_label_id',
        'intro_cta_label_en',
        'intro_cta_url',
        'products_kicker_id',
        'products_kicker_en',
        'products_title_id',
        'products_title_en',
        'products_description_id',
        'products_description_en',
        'news_kicker_id',
        'news_kicker_en',
        'news_title_id',
        'news_title_en',
    ];

    protected $fillable = self::FIELDS;

    /**
     * Allowed CTA link targets: a site-relative path ("/about") or an
     * absolute http(s) URL. Blocks javascript:, data:, protocol-relative
     * "//host" links etc. Shared by the Home and Hero Slide forms.
     */
    public const LINK_REGEX = '/^(\/(?!\/)[^\s]*|https?:\/\/[^\s]+)$/i';

    /** There is always exactly one row (id = 1). */
    public static function singleton(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
