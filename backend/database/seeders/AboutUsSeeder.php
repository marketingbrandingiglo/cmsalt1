<?php

namespace Database\Seeders;

use App\Models\AboutUs;
use App\Models\ClientCategory;
use App\Models\Milestone;
use Illuminate\Database\Seeder;

class AboutUsSeeder extends Seeder
{
    /**
     * Migrates the existing bilingual About Us copy from the iglo frontend's
     * lib/content.js, so the page has real content immediately. Safe to
     * re-run: skips whatever already has rows.
     */
    public function run(): void
    {
        $this->seedAboutUs();
        $this->seedMilestones();
        $this->seedClientCategories();
    }

    private function seedAboutUs(): void
    {
        if (AboutUs::query()->exists()) {
            $this->command->info('about_us already has content, skipping');

            return;
        }

        $aboutUs = AboutUs::create([
            'company_name' => 'Indocyber Global Teknologi',
            'banner_title_id' => 'Kami Memberikan Solusi Terbaik untuk Anda',
            'banner_title_en' => 'We Give You The Best Solution',
            'banner_description_id' => 'Para profesional TI membantu pengembangan dan pemeliharaan sistem perangkat keras serta perangkat lunak komputer. Mereka juga mengembangkan solusi teknologi baru bagi bisnis dan pemerintahan. Menjadi seorang ahli TI adalah jalur karier yang bermakna dan dapat memberi perbedaan nyata dalam kehidupan sehari-hari banyak orang.',
            'banner_description_en' => "IT professionals help with the development and maintenance of computer hardware and software systems. They also develop new technology solutions for businesses and governments. Being an IT expert is a rewarding career path that can make a difference in people's daily lives.",
            'description_id' => '<p>Penyedia sistem informasi & teknologi terintegrasi yang berdedikasi menghadirkan solusi paling efektif di bidang Sistem Informasi. Bisnis inti kami berfokus pada layanan teknologi informasi, transformasi digital, dan infrastruktur TI.</p><p>Perusahaan kami berawal dari hanya 10 (sepuluh) personel; kini, setelah bertahun-tahun pengalaman, kami didukung oleh lebih dari 1000 konsultan dan developer.</p>',
            'description_en' => '<p>An integrated information and technology system provider, dedicated to delivering highly effective solutions in the field of Information Systems. Our core business focuses on information technology services, digital transformation and IT infrastructure.</p><p>Our company started with only 10 (ten) personnel; currently, after years of experience, we are supported by more than 1000 consultants and developers.</p>',
            'vision_id' => 'Solusi TI dan Transformasi Digital yang terintegrasi, terdiversifikasi, dan terkemuka secara nasional.',
            'vision_en' => 'Reputable Nation-wide Integrated, Diversified IT and Digital Transformation Solution.',
            'mission_id' => 'Kami menghadirkan solusi TI dan Transformasi Digital terdepan untuk industri terspesialisasi dan secara konsisten menambah nilai demi menjaga loyalitas para pemangku kepentingan kami.',
            'mission_en' => 'We provide a cutting-edge IT and Digital Transformation solution for specialized industries and consistently add value to retain the loyalty of our stakeholders.',
            'milestone_title_id' => 'Milestone Kami',
            'milestone_title_en' => 'Our Milestones',
            'milestone_description_id' => 'Mengawali perjalanan sebagai mitra dan pengembang MAGIC, IGLO terus berupaya meningkatkan kinerjanya hingga menjadi bagian dari distributor MAGIC. Pada tahun berikutnya, IGLO berhasil meraih gelar sebagai SAP Partner. Kerja keras seluruh elemen perusahaan membuat IGLO terus berkembang dan menjadi mitra perusahaan-perusahaan ternama.',
            'milestone_description_en' => 'Starting the journey as a partner and developer of MAGIC, IGLO continues to strive to improve its performance so that it becomes part of the MAGIC distributor. In the following year, IGLO managed to get the title as SAP Partner. The hard work of all elements of the company has made IGLO continue to expand its company and make it a partner of well-known companies.',
            'video_title_id' => 'Video Perusahaan',
            'video_title_en' => 'Company Video',
            'video_description_id' => 'Dengan bangga mempersembahkan video perusahaan IGLO! Ini akan membantu Anda untuk lebih mengenal, serta belajar lebih banyak tentang budaya perusahaan dan apa yang IGLO harapkan.',
            'video_description_en' => "We proudly present IGLO's company video! It will help you get to know us better, and learn more about our company culture and what IGLO expects.",
            'video_youtube_url' => 'https://www.youtube.com/embed/t3oH5RdaiVs',
        ]);

        $values = [
            ['title' => 'Integrity', 'id' => 'Kami menjunjung kejujuran, etika, dan tanggung jawab dalam setiap hal yang kami lakukan.', 'en' => 'We uphold honesty, ethics, and accountability in everything we do.'],
            ['title' => 'Involved', 'id' => 'Kami sepenuhnya terlibat dan berkomitmen pada keberhasilan klien kami.', 'en' => "We are fully engaged and committed to our clients' success."],
            ['title' => 'Integrated', 'id' => 'Kami menghadirkan solusi teknologi menyeluruh dan terpadu.', 'en' => 'We deliver unified, end-to-end technology solutions.'],
            ['title' => 'Impressive', 'id' => 'Kami konsisten melampaui ekspektasi dengan hasil berkualitas.', 'en' => 'We consistently exceed expectations with quality results.'],
            ['title' => 'Innovative', 'id' => 'Kami terus berinovasi dengan teknologi terkini.', 'en' => 'We continuously innovate with the latest technology.'],
        ];
        foreach ($values as $i => $v) {
            $aboutUs->values()->create([
                'title' => $v['title'],
                'description_id' => $v['id'],
                'description_en' => $v['en'],
                'order' => $i,
            ]);
        }

        $stats = [
            [
                'value' => '50',
                'label_id' => 'Klien Teratas Telah Menjadi Klien Selama 10 Tahun Terakhir',
                'label_en' => 'Top Clients Have Stayed With Us for the Past 10 Years',
                'icon' => 'speed',
                'note_id' => 'Mempercepat Kinerja Bisnis dan Ciptakan Nilai Nyata',
                'note_en' => 'Accelerate Business Performance and Create Tangible Value',
            ],
            [
                'value' => '1100',
                'label_id' => 'Konsultan & Pengembang',
                'label_en' => 'Consultants & Developers',
                'icon' => 'layers',
                'note_id' => 'Perusahaan Solusi & Layanan Transformasi Digital',
                'note_en' => 'Digital Transformation Solutions & Services Company',
            ],
        ];
        foreach ($stats as $i => $s) {
            $aboutUs->stats()->create([...$s, 'order' => $i]);
        }
    }

    private function seedMilestones(): void
    {
        if (Milestone::query()->exists()) {
            $this->command->info('milestones already exist, skipping');

            return;
        }

        // Matches the 5 periods grouped in the iglo frontend's About page
        // (lib/content.js MILESTONE_GROUPS). Logos are left for an editor to
        // upload via the admin panel — the frontend falls back to its own
        // static logos for a period until it has at least one here.
        $periods = [
            '2021 - Present',
            '2016 - 2020',
            '2011 - 2015',
            '2006 - 2010',
            '2001 - 2005',
        ];

        $total = count($periods);
        foreach ($periods as $i => $period) {
            Milestone::create([
                'period' => $period,
                'order' => $total - $i, // newest first
            ]);
        }
    }

    private function seedClientCategories(): void
    {
        if (ClientCategory::query()->exists()) {
            $this->command->info('client categories already exist, skipping');

            return;
        }

        $categories = [
            ['name_id' => 'Multifinance', 'name_en' => 'Multifinance'],
            ['name_id' => 'Asuransi', 'name_en' => 'Insurance'],
            ['name_id' => 'Bank', 'name_en' => 'Bank'],
            ['name_id' => 'Retail & Manufaktur', 'name_en' => 'Retail & Manufacture'],
            ['name_id' => 'Lainnya', 'name_en' => 'Others'],
        ];
        foreach ($categories as $i => $cat) {
            ClientCategory::create([...$cat, 'order' => $i]);
        }
    }
}
