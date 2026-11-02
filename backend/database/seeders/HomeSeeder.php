<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\HomePage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class HomeSeeder extends Seeder
{
    /**
     * Migrates the existing bilingual Home page copy from the iglo
     * frontend's lib/content.js (hero, intro, productsSection, news.hero).
     * Runs only once, on a fresh database: the Railway container runs
     * db:seed on every boot, and re-seeding would bring back slides an
     * editor deleted on purpose.
     */
    public function run(): void
    {
        if (HomePage::query()->exists()) {
            $this->command?->info('home_page already set up, skipping');

            return;
        }

        $this->seedHeroSlides();
        $this->seedHomePage();
    }

    private function seedHomePage(): void
    {
        if (HomePage::query()->exists()) {
            $this->command?->info('home_page already has content, skipping');

            return;
        }

        HomePage::create([
            'intro_kicker_id' => 'PERUSAHAAN TEKNOLOGI PROFESIONAL TERDEPAN',
            'intro_kicker_en' => 'A LEADING PROFESSIONAL TECH COMPANY',
            'intro_title_id' => 'Kami Menyediakan Solusi Terdepan untuk Kebutuhan Anda',
            'intro_title_en' => 'We Provide A Cutting Edge Solution For What You Need',
            'intro_description_id' => 'PT Indocyber Global Teknologi adalah penyedia sistem informasi & teknologi terintegrasi yang berkomitmen menghadirkan solusi paling efektif di bidang Sistem Informasi.',
            'intro_description_en' => 'PT Indocyber Global Teknologi is an integrated information & technology system provider, dedicated to delivering highly effective solutions in the field of Information Systems.',
            'intro_cta_label_id' => 'Perjalanan Kami',
            'intro_cta_label_en' => 'Our Journey',
            'intro_cta_url' => '/about',
            'products_kicker_id' => 'PRODUK & SOLUSI',
            'products_kicker_en' => 'PRODUCT & SOLUTIONS',
            'products_title_id' => 'Layanan Unggulan Kami',
            'products_title_en' => 'Our Featured Services',
            'products_description_id' => 'Rangkaian solusi teknologi menyeluruh untuk mempercepat transformasi digital bisnis Anda.',
            'products_description_en' => "A complete suite of technology solutions to accelerate your business's digital transformation.",
            'news_kicker_id' => 'BERITA & INSIGHT',
            'news_kicker_en' => 'NEWS & INSIGHTS',
            'news_title_id' => 'Berita & Wawasan Terkini',
            'news_title_en' => 'Latest News & Insights',
        ]);
    }

    private function seedHeroSlides(): void
    {
        if (HeroSlide::query()->exists()) {
            $this->command?->info('hero_slides already has content, skipping');

            return;
        }

        self::createDefaultHeroSlides();
    }

    /**
     * The hero slides currently on the live iglo frontend (lib/content.js
     * `hero`, with the banner_1..4.jpg photos), copied to the media disk.
     */
    public static function createDefaultHeroSlides(): void
    {
        $slides = [
            [
                'file' => 'banner_1.jpg',
                'title_id' => 'Mengapa Strategi Rekrutmen Tradisional Tidak Lagi Cukup untuk Proyek IT Modern',
                'title_en' => 'Why Traditional Hiring Strategies Are No Longer Enough for Modern IT Projects.',
                'description_id' => 'Siklus rekrutmen yang panjang, onboarding yang rumit, dan tuntutan keahlian AI membuat strategi rekrutmen tradisional semakin sulit mengikuti kecepatan bisnis.',
                'description_en' => 'Long hiring cycles, complex onboarding, and the demand for AI skills are making traditional recruitment strategies harder to keep pace with business speed.',
            ],
            [
                'file' => 'banner_2.jpg',
                'title_id' => 'Dari Otomasi Workflow Menuju Digital Workforce: Mengenal Creatio 10x',
                'title_en' => 'From Workflow Automation to Digital Workforce: Inside Creatio 10x.',
                'description_id' => 'Ketahui bagaimana Creatio 10x mengubah otomasi workflow konvensional menjadi digital workforce bagi perusahaan modern.',
                'description_en' => 'Discover how Creatio 10x transforms traditional workflow automation into a digital workforce for modern enterprises.',
            ],
            [
                'file' => 'banner_3.jpg',
                'title_id' => 'Cloud ERP vs On-Premise ERP: Mana yang Tepat untuk Implementasi SAP Pertama Anda?',
                'title_en' => 'Cloud ERP vs On-Premise ERP: Which Is Right for Your First SAP Implementation?',
                'description_id' => 'Bingung memilih Cloud ERP atau On-Premise untuk implementasi SAP pertama? Bandingkan biaya, kecepatan, dan risikonya sebelum memutuskan.',
                'description_en' => 'Choosing between Cloud ERP and On-Premise for your first SAP implementation? Compare cost, speed, and risk before you decide.',
            ],
            [
                'file' => 'banner_4.jpg',
                'title_id' => 'Mengapa Perusahaan Kini Mencari Kemampuan AI Engineering, Bukan Sekadar Programming',
                'title_en' => 'Why Companies Are Now Looking for AI Engineering Skills, Not Just Programming Skills.',
                'description_id' => 'Mengapa perusahaan kini lebih mencari keahlian AI Engineering dibanding sekadar kemampuan programming? Simak tren, skill yang dibutuhkan, dan strategi membangun kapabilitas AI enterprise.',
                'description_en' => 'Why are companies now looking for AI Engineering Skills instead of just programming ability? Discover the trends, in-demand skills, and strategies for building enterprise AI capability.',
            ],
        ];

        $disk = Storage::disk(config('filesystems.media_disk'));

        foreach ($slides as $i => $slide) {
            $path = 'hero-slides/'.$slide['file'];

            if (! $disk->exists($path)) {
                $disk->put($path, File::get(database_path('seed-assets/'.$path)));
            }

            HeroSlide::create([
                ...collect($slide)->except('file')->all(),
                'image_path' => $path,
                'cta_label_id' => 'Selengkapnya',
                'cta_label_en' => 'Read More',
                'cta_url' => '/news',
                'is_active' => true,
                'order' => $i,
            ]);
        }
    }
}
