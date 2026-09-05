<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class AksesorisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Parent Utama (Aksesoris)
        $aksesoris = Category::firstOrCreate([
            'slug' => 'aksesoris'
        ], [
            'name'      => 'Aksesoris',
            'parent_id' => null,
        ]);

        // 2. Sub-Kategori Level 1
        $proteksi = Category::firstOrCreate(['slug' => 'proteksi'], ['name' => 'Proteksi', 'parent_id' => $aksesoris->id]);
        $power    = Category::firstOrCreate(['slug' => 'power'], ['name' => 'Power', 'parent_id' => $aksesoris->id]);
        $audio    = Category::firstOrCreate(['slug' => 'audio'], ['name' => 'Audio', 'parent_id' => $aksesoris->id]);
        $storage  = Category::firstOrCreate(['slug' => 'penyimpanan'], ['name' => 'Penyimpanan (Data & Storage)', 'parent_id' => $aksesoris->id]);
        $stand    = Category::firstOrCreate(['slug' => 'mount-stand'], ['name' => 'Mount & Stand', 'parent_id' => $aksesoris->id]);

        // 3. Sub-Kategori Level 2
        // Proteksi
        Category::firstOrCreate(['slug' => 'casing'], ['name' => 'Casing', 'parent_id' => $proteksi->id]);
        Category::firstOrCreate(['slug' => 'tempered-glass'], ['name' => 'Tempered Glass', 'parent_id' => $proteksi->id]);
        Category::firstOrCreate(['slug' => 'hydrogel'], ['name' => 'Hydrogel', 'parent_id' => $proteksi->id]);

        // Power
        Category::firstOrCreate(['slug' => 'charger'], ['name' => 'Charger (Batok / Set)', 'parent_id' => $power->id]);
        Category::firstOrCreate(['slug' => 'kabel-data'], ['name' => 'Kabel Data', 'parent_id' => $power->id]);
        Category::firstOrCreate(['slug' => 'power-bank'], ['name' => 'Power Bank', 'parent_id' => $power->id]);

        // Audio
        Category::firstOrCreate(['slug' => 'tws'], ['name' => 'TWS (True Wireless)', 'parent_id' => $audio->id]);
        Category::firstOrCreate(['slug' => 'headset'], ['name' => 'Headset / Earphone (Wired)', 'parent_id' => $audio->id]);
        Category::firstOrCreate(['slug' => 'bluetooth-speaker'], ['name' => 'Bluetooth Speaker', 'parent_id' => $audio->id]);

        // Storage
        Category::firstOrCreate(['slug' => 'flashdisk'], ['name' => 'Flashdisk', 'parent_id' => $storage->id]);
        Category::firstOrCreate(['slug' => 'memory-card'], ['name' => 'Memory Card (MicroSD)', 'parent_id' => $storage->id]);

        // Mount & Stand
        Category::firstOrCreate(['slug' => 'holder'], ['name' => 'Holder Mobil / Motor', 'parent_id' => $stand->id]);
        Category::firstOrCreate(['slug' => 'ring-light-tripod'], ['name' => 'Ring Light / Tripod', 'parent_id' => $stand->id]);
    }
}