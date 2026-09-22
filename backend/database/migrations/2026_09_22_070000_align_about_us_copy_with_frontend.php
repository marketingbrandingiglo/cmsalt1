<?php

use App\Models\AboutStat;
use App\Models\AboutUs;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * One-time content fix: the banner/milestone-section/video/stats copy
     * seeded when these fields were first added didn't quite match the
     * iglo frontend's real lib/content.js copy (mostly missing sentences
     * and a couple of Indonesian wording differences). Only touches rows
     * that still hold the exact original placeholder text, so it never
     * overwrites anything an editor has since changed via the admin panel.
     */
    public function up(): void
    {
        $aboutUs = AboutUs::query()->first();

        if (! $aboutUs) {
            return;
        }

        $this->fixIfUnchanged($aboutUs, 'banner_description_id',
            'Para profesional TI membantu pengembangan dan pemeliharaan sistem perangkat keras serta perangkat lunak komputer. Mereka juga mengembangkan solusi teknologi baru bagi bisnis dan pemerintahan.',
            'Para profesional TI membantu pengembangan dan pemeliharaan sistem perangkat keras serta perangkat lunak komputer. Mereka juga mengembangkan solusi teknologi baru bagi bisnis dan pemerintahan. Menjadi seorang ahli TI adalah jalur karier yang bermakna dan dapat memberi perbedaan nyata dalam kehidupan sehari-hari banyak orang.',
        );

        $this->fixIfUnchanged($aboutUs, 'banner_description_en',
            'IT professionals help with the development and maintenance of computer hardware and software systems. They also develop new technology solutions for businesses and governments.',
            "IT professionals help with the development and maintenance of computer hardware and software systems. They also develop new technology solutions for businesses and governments. Being an IT expert is a rewarding career path that can make a difference in people's daily lives.",
        );

        $this->fixIfUnchanged($aboutUs, 'milestone_description_id',
            'Mengawali perjalanan sebagai mitra dan pengembang MAGIC, IGLO terus berupaya meningkatkan kinerjanya hingga menjadi bagian dari distributor MAGIC.',
            'Mengawali perjalanan sebagai mitra dan pengembang MAGIC, IGLO terus berupaya meningkatkan kinerjanya hingga menjadi bagian dari distributor MAGIC. Pada tahun berikutnya, IGLO berhasil meraih gelar sebagai SAP Partner. Kerja keras seluruh elemen perusahaan membuat IGLO terus berkembang dan menjadi mitra perusahaan-perusahaan ternama.',
        );

        $this->fixIfUnchanged($aboutUs, 'milestone_description_en',
            'Starting the journey as a partner and developer of MAGIC, IGLO continues to strive to improve its performance so that it becomes part of the MAGIC distributor.',
            'Starting the journey as a partner and developer of MAGIC, IGLO continues to strive to improve its performance so that it becomes part of the MAGIC distributor. In the following year, IGLO managed to get the title as SAP Partner. The hard work of all elements of the company has made IGLO continue to expand its company and make it a partner of well-known companies.',
        );

        $this->fixIfUnchanged($aboutUs, 'video_title_id', 'Company Video', 'Video Perusahaan');

        $this->fixIfUnchanged($aboutUs, 'video_description_id',
            'Kami tahu sulit mengikuti semua hal yang terjadi di Indocyber. Karena itu kami dengan bangga mempersembahkan Company Video kami!',
            'Dengan bangga mempersembahkan video perusahaan IGLO! Ini akan membantu Anda untuk lebih mengenal, serta belajar lebih banyak tentang budaya perusahaan dan apa yang IGLO harapkan.',
        );

        $this->fixIfUnchanged($aboutUs, 'video_description_en',
            "We know it's hard to keep up with everything happening at Indocyber. That's why we are proud to present our Company Video!",
            "We proudly present IGLO's company video! It will help you get to know us better, and learn more about our company culture and what IGLO expects.",
        );

        $stat50 = AboutStat::where('about_us_id', $aboutUs->id)->where('value', '50')->first();
        if ($stat50) {
            $this->fixIfUnchanged($stat50, 'label_id',
                'Klien Utama Kami yang Telah Setia Selama 10 Tahun Terakhir',
                'Klien Teratas Telah Menjadi Klien Selama 10 Tahun Terakhir',
            );
            $this->fixIfUnchanged($stat50, 'note_id',
                'Mempercepat Kinerja Bisnis dan Menciptakan Nilai Nyata',
                'Mempercepat Kinerja Bisnis dan Ciptakan Nilai Nyata',
            );
        }

        $stat1100 = AboutStat::where('about_us_id', $aboutUs->id)->where('value', '1100')->first();
        if ($stat1100) {
            $this->fixIfUnchanged($stat1100, 'label_id', 'Konsultan & Developer', 'Konsultan & Pengembang');
        }
    }

    private function fixIfUnchanged(AboutUs|AboutStat $model, string $column, string $original, string $corrected): void
    {
        if ($model->{$column} === $original) {
            $model->update([$column => $corrected]);
        }
    }

    public function down(): void
    {
        // Text-only content correction; not worth reverting.
    }
};
