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
            'description_id' => '<p>Penyedia sistem informasi & teknologi terintegrasi yang berdedikasi menghadirkan solusi paling efektif di bidang Sistem Informasi. Bisnis inti kami berfokus pada layanan teknologi informasi, transformasi digital, dan infrastruktur TI.</p><p>Perusahaan kami berawal dari hanya 10 (sepuluh) personel; kini, setelah bertahun-tahun pengalaman, kami didukung oleh lebih dari 1000 konsultan dan developer.</p>',
            'description_en' => '<p>An integrated information and technology system provider, dedicated to delivering highly effective solutions in the field of Information Systems. Our core business focuses on information technology services, digital transformation and IT infrastructure.</p><p>Our company started with only 10 (ten) personnel; currently, after years of experience, we are supported by more than 1000 consultants and developers.</p>',
            'vision_id' => 'Solusi TI dan Transformasi Digital yang terintegrasi, terdiversifikasi, dan terkemuka secara nasional.',
            'vision_en' => 'Reputable Nation-wide Integrated, Diversified IT and Digital Transformation Solution.',
            'mission_id' => 'Kami menghadirkan solusi TI dan Transformasi Digital terdepan untuk industri terspesialisasi dan secara konsisten menambah nilai demi menjaga loyalitas para pemangku kepentingan kami.',
            'mission_en' => 'We provide a cutting-edge IT and Digital Transformation solution for specialized industries and consistently add value to retain the loyalty of our stakeholders.',
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
    }

    private function seedMilestones(): void
    {
        if (Milestone::query()->exists()) {
            $this->command->info('milestones already exist, skipping');

            return;
        }

        $items = [
            ['year' => '2023', 'id' => ['Creatio', 'Menjadi Mitra SAP'], 'en' => ['Creatio', 'Being a Partner of SAP']],
            ['year' => '2022', 'id' => ['Menjadi Mitra Software AG', 'Informatica', 'UiPath Partner Diamond', 'Newgen'], 'en' => ['Being a Partner of Software AG', 'Informatica', 'UiPath Partner Diamond', 'Newgen']],
            ['year' => '2021', 'id' => ['Tableau'], 'en' => ['Tableau']],
            ['year' => '2019', 'id' => ['UiPath'], 'en' => ['UiPath']],
            ['year' => '2018', 'id' => ['DataRobot', '3Dolphins'], 'en' => ['DataRobot', '3Dolphins']],
            ['year' => '2017', 'id' => ['Menjadi Mitra IBM', 'Menjadi Mitra Lenddo', 'Menjadi Mitra Pitney Bowes'], 'en' => ['Being a Partner of IBM', 'Being a Partner of Lenddo', 'Being a Partner of Pitney Bowes']],
            ['year' => '2016', 'id' => ['Menjadi Mitra LAMP'], 'en' => ['Being a Partner of LAMP']],
            ['year' => '2014', 'id' => ['Menjadi Mitra Tibco'], 'en' => ['Being a Partner of Tibco']],
            ['year' => '2012', 'id' => ['Menjadi Gold Partner Oracle'], 'en' => ['Being a Gold Partner of Oracle']],
            ['year' => '2009', 'id' => ['Menjadi Mitra Blackberry'], 'en' => ['Being a Partner of Blackberry']],
            ['year' => '2008', 'id' => ['Menjadi Mitra Microsoft'], 'en' => ['Being a Partner of Microsoft']],
            ['year' => '2007', 'id' => ['Menjadi Mitra Telkomsel'], 'en' => ['Being a Partner of Telkomsel']],
            ['year' => '2006', 'id' => ['Menjadi Mitra Oracle'], 'en' => ['Being a Partner of Oracle']],
            ['year' => '2003', 'id' => ['Menjadi Satu-Satunya Distributor MAGIC Software'], 'en' => ['Being The Only One MAGIC Software Distributor']],
            ['year' => '2001', 'id' => ['Menjadi Mitra MAGIC Software'], 'en' => ['Being a Partner of MAGIC Software']],
        ];

        $total = count($items);
        foreach ($items as $i => $item) {
            $milestone = Milestone::create([
                'year' => $item['year'],
                'order' => $total - $i, // newest first
            ]);
            foreach ($item['id'] as $j => $textId) {
                $milestone->events()->create([
                    'text_id' => $textId,
                    'text_en' => $item['en'][$j],
                    'order' => $j,
                ]);
            }
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
