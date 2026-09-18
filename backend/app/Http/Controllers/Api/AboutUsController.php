<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\ClientCategory;
use App\Models\Milestone;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Read-only public endpoints for the About Us page. No auth is required —
 * this is public marketing content and there is no secret involved (see
 * CLAUDE.md hard rule: never expose secrets to the frontend — there simply
 * aren't any here to expose).
 */
class AboutUsController extends Controller
{
    private function locale(Request $request): string
    {
        return $request->query('locale') === 'en' ? 'en' : 'id';
    }

    private function logoUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    public function aboutUs(Request $request)
    {
        $locale = $this->locale($request);
        $aboutUs = AboutUs::with('values')->first();

        if (! $aboutUs) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'companyName' => $aboutUs->company_name,
                'description' => $aboutUs->{"description_{$locale}"},
                'vision' => $aboutUs->{"vision_{$locale}"},
                'mission' => $aboutUs->{"mission_{$locale}"},
                'values' => $aboutUs->values->map(fn ($v) => [
                    'id' => $v->id,
                    'title' => $v->title,
                    'description' => $v->{"description_{$locale}"},
                    'order' => $v->order,
                ]),
            ],
        ]);
    }

    public function milestones()
    {
        $milestones = Milestone::with('logos')->orderByDesc('order')->get();

        return response()->json([
            'data' => $milestones->map(fn ($m) => [
                'id' => $m->id,
                'period' => $m->period,
                'order' => $m->order,
                'logos' => $m->logos->map(fn ($l) => [
                    'id' => $l->id,
                    'name' => $l->name,
                    'logoUrl' => $this->logoUrl($l->logo_path),
                    'order' => $l->order,
                ]),
            ]),
        ]);
    }

    public function partners()
    {
        $partners = Partner::orderBy('order')->get();

        return response()->json([
            'data' => $partners->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'logoUrl' => $this->logoUrl($p->logo_path),
                'websiteUrl' => $p->website_url,
                'order' => $p->order,
            ]),
        ]);
    }

    public function clientCategories(Request $request)
    {
        $locale = $this->locale($request);
        $categories = ClientCategory::with('clients')->orderBy('order')->get();

        return response()->json([
            'data' => $categories->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->{"name_{$locale}"},
                'order' => $c->order,
                'clients' => $c->clients->map(fn ($cl) => [
                    'id' => $cl->id,
                    'name' => $cl->name,
                    'logoUrl' => $this->logoUrl($cl->logo_path),
                    'websiteUrl' => $cl->website_url,
                    'order' => $cl->order,
                ]),
            ]),
        ]);
    }
}
