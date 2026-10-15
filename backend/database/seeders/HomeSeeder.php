<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\HomePage;
use Illuminate\Database\Seeder;

class HomeSeeder extends Seeder
{
    /**
     * Migrates the existing bilingual Home page copy from the iglo
     * frontend's lib/content.js (hero, intro, productsSection, news.hero).
     * Safe to re-run: skips whatever already has rows.
     */
    public function run(): void
    {
        $this->seedHomePage();
        $this->seedHeroSlides();
    }

    private function seedHomePage(): void
    {
        if (HomePage::query()->exists()) {
            $this->command->info('home_page already has content, skipping');

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
            $this->command->info('hero_slides already has content, skipping');

            return;
        }

        $slides = [
            [
                'tag_id' => 'Insight Industri',
                'tag_en' => 'Industry Insight',
                'title_id' => 'Kekurangan Talenta AI Menjadi Tantangan Baru bagi Industri Keuangan',
                'title_en' => 'AI Skills Shortage Becomes a New Challenge for the Financial Industry',
                'description_id' => 'Kelangkaan talenta AI kini memperlambat transformasi digital di sektor keuangan. Pahami penyebab, dampak, dan solusinya bagi bank serta lembaga keuangan.',
                'description_en' => 'AI talent shortage is now slowing digital transformation in the financial sector. Learn the causes, impact, and solutions for banks and financial institutions.',
            ],
            [
                'tag_id' => 'Enterprise',
                'tag_en' => 'Enterprise',
                'title_id' => 'Cara Mempercepat Sistem Bisnis Anda',
                'title_en' => 'How to Accelerate Your Business System',
                'description_id' => 'Banyak perusahaan masih menghadapi proses bisnis yang lambat akibat sistem yang sulit dikembangkan, integrasi rumit, dan ketergantungan pada coding konvensional.',
                'description_en' => 'Many companies still face slow business processes caused by systems that are difficult to develop, complex integrations, and heavy dependence on conventional coding.',
            ],
            [
                'tag_id' => 'SAP GROW',
                'tag_en' => 'SAP GROW',
                'title_id' => 'Mengapa AI Bekerja Lebih Baik dengan SAP GROW Dibanding Sistem Terpisah',
                'title_en' => 'Why AI Performs Better with SAP GROW Than Disconnected Systems',
                'description_id' => 'Banyak perusahaan gagal memaksimalkan AI karena fondasinya belum siap. Temukan tiga komponen penting yang dibutuhkan AI berdasarkan kerangka SAP GROW.',
                'description_en' => "Many companies fail to maximize AI because their foundation isn't ready. Discover the three critical components AI needs, based on the SAP GROW framework.",
            ],
        ];

        foreach ($slides as $i => $slide) {
            HeroSlide::create([
                ...$slide,
                'cta_label_id' => 'Selengkapnya',
                'cta_label_en' => 'Read More',
                'cta_url' => '/news',
                'is_active' => true,
                'order' => $i,
            ]);
        }
    }
}
