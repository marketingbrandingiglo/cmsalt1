<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Partner names mentioned in the iglo frontend's About Us milestone
     * timeline (lib/content.js) — the only real partner data that exists
     * there today. Logos/website URLs are left blank for an editor to fill
     * in via the admin panel. Safe to re-run: skips if partners already exist.
     */
    public function run(): void
    {
        if (Partner::query()->exists()) {
            $this->command->info('partners already exist, skipping');

            return;
        }

        $names = [
            'Creatio',
            'SAP',
            'Software AG',
            'Informatica',
            'UiPath',
            'Newgen',
            'Tableau',
            'DataRobot',
            '3Dolphins',
            'IBM',
            'Lenddo',
            'Pitney Bowes',
            'LAMP',
            'Tibco',
            'Oracle',
            'Blackberry',
            'Microsoft',
            'Telkomsel',
            'MAGIC Software',
        ];

        foreach ($names as $i => $name) {
            Partner::create([
                'name' => $name,
                'order' => $i,
            ]);
        }
    }
}
