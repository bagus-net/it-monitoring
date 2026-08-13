<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ChecklistItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Software
            ['title'=>'Scan Antivirus & AntiSpyware','description'=>'','frequency'=>'monthly','sort_order'=>10],
            ['title'=>'Check Upgrade Software','description'=>'','frequency'=>'monthly','sort_order'=>11],
            ['title'=>'Advanced SystemCare','description'=>'','frequency'=>'monthly','sort_order'=>12],
            ['title'=>'Backup Database','description'=>'','frequency'=>'monthly','sort_order'=>13],
            ['title'=>'Backup Data','description'=>'','frequency'=>'monthly','sort_order'=>14],
            ['title'=>'Registry Cleaner','description'=>'','frequency'=>'monthly','sort_order'=>15],
            ['title'=>'Disk Defragmenter','description'=>'','frequency'=>'monthly','sort_order'=>16],
            // Hardware
            ['title'=>'Membersihkan Casing Luar','description'=>'','frequency'=>'monthly','sort_order'=>20],
            ['title'=>'Membersihkan Mouse dan Keyboard','description'=>'','frequency'=>'monthly','sort_order'=>21],
            ['title'=>'Membersihkan Power Supply','description'=>'','frequency'=>'monthly','sort_order'=>22],
            ['title'=>'Membersihkan Motherboard','description'=>'','frequency'=>'monthly','sort_order'=>23],
            ['title'=>'Membersihkan Monitor','description'=>'','frequency'=>'monthly','sort_order'=>24],
            ['title'=>'Check Expansion Card','description'=>'','frequency'=>'monthly','sort_order'=>25],
            ['title'=>'Membersihkan Printer','description'=>'','frequency'=>'monthly','sort_order'=>26],
            // Networking
            ['title'=>'Check Internet Download Upload','description'=>'','frequency'=>'monthly','sort_order'=>30],
            ['title'=>'Membersihkan Router & Swicht HUB','description'=>'','frequency'=>'monthly','sort_order'=>31],
        ];

        foreach($items as $it) {
            \App\Models\ChecklistItem::create($it);
        }
    }
}
