<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\HomePage;
use Database\Seeders\HomeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_returns_null_when_empty(): void
    {
        $this->getJson('/api/home')->assertOk()->assertExactJson(['data' => null]);
    }

    public function test_home_returns_localized_sections(): void
    {
        $this->seed(HomeSeeder::class);

        $this->getJson('/api/home?locale=en')
            ->assertOk()
            ->assertJsonPath('data.intro.title', 'We Provide A Cutting Edge Solution For What You Need')
            ->assertJsonPath('data.intro.ctaUrl', '/about')
            ->assertJsonPath('data.productsSection.kicker', 'PRODUCT & SOLUTIONS')
            ->assertJsonPath('data.newsSection.title', 'Latest News & Insights');

        $this->getJson('/api/home?locale=id')
            ->assertJsonPath('data.intro.ctaLabel', 'Perjalanan Kami');
    }

    public function test_unknown_locale_falls_back_to_indonesian(): void
    {
        $this->seed(HomeSeeder::class);

        $this->getJson('/api/home?locale=<script>')
            ->assertOk()
            ->assertJsonPath('data.newsSection.kicker', 'BERITA & INSIGHT');
    }

    public function test_hero_slides_returns_only_active_slides_in_order(): void
    {
        HeroSlide::create(['title_id' => 'Dua', 'title_en' => 'Two', 'order' => 2]);
        HeroSlide::create(['title_id' => 'Satu', 'title_en' => 'One', 'order' => 1]);
        HeroSlide::create(['title_id' => 'Mati', 'title_en' => 'Hidden', 'order' => 0, 'is_active' => false]);

        $this->getJson('/api/home/hero-slides?locale=en')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.title', 'One')
            ->assertJsonPath('data.1.title', 'Two')
            ->assertJsonPath('data.0.imageUrl', null);
    }

    public function test_home_api_is_read_only(): void
    {
        $this->postJson('/api/home', ['intro_title_en' => 'x'])->assertStatus(405);
        $this->postJson('/api/home/hero-slides', [])->assertStatus(405);
        $this->assertSame(0, HomePage::query()->count());
    }

    public function test_link_regex_allows_paths_and_http_urls_only(): void
    {
        foreach (['/about', '/news?x=1', 'https://indocyber.id', 'http://example.com/a'] as $ok) {
            $this->assertMatchesRegularExpression(HomePage::LINK_REGEX, $ok);
        }
        foreach (['javascript:alert(1)', '//evil.com', 'data:text/html,x', 'about', '/a b'] as $bad) {
            $this->assertDoesNotMatchRegularExpression(HomePage::LINK_REGEX, $bad);
        }
    }
}
