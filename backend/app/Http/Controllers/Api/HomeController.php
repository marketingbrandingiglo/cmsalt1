<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\HomePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Read-only public endpoints for the Home page. No auth is required — this
 * is public marketing content with no secret involved (see CLAUDE.md).
 */
class HomeController extends Controller
{
    private function locale(Request $request): string
    {
        return $request->query('locale') === 'en' ? 'en' : 'id';
    }

    public function home(Request $request)
    {
        $locale = $this->locale($request);
        $home = HomePage::query()->first();

        if (! $home) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'intro' => [
                    'kicker' => $home->{"intro_kicker_{$locale}"},
                    'title' => $home->{"intro_title_{$locale}"},
                    'description' => $home->{"intro_description_{$locale}"},
                    'ctaLabel' => $home->{"intro_cta_label_{$locale}"},
                    'ctaUrl' => $home->intro_cta_url,
                ],
                'productsSection' => [
                    'kicker' => $home->{"products_kicker_{$locale}"},
                    'title' => $home->{"products_title_{$locale}"},
                    'description' => $home->{"products_description_{$locale}"},
                ],
                'newsSection' => [
                    'kicker' => $home->{"news_kicker_{$locale}"},
                    'title' => $home->{"news_title_{$locale}"},
                ],
            ],
        ]);
    }

    public function heroSlides(Request $request)
    {
        $locale = $this->locale($request);
        $slides = HeroSlide::query()->where('is_active', true)->orderBy('order')->orderBy('id')->get();

        return response()->json([
            'data' => $slides->map(fn ($s) => [
                'id' => $s->id,
                'tag' => $s->{"tag_{$locale}"},
                'title' => $s->{"title_{$locale}"},
                'description' => $s->{"description_{$locale}"},
                'imageUrl' => $s->image_path ? Storage::disk('public')->url($s->image_path) : null,
                'ctaLabel' => $s->{"cta_label_{$locale}"},
                'ctaUrl' => $s->cta_url,
                'order' => $s->order,
            ]),
        ]);
    }
}
