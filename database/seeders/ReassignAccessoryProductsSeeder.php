<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ReassignAccessoryProductsSeeder extends Seeder
{
    /**
     * Memindahkan produk aksesoris yang category_id-nya salah nyasar
     * (misal ke kategori "Aksesoris HP" yang flat/tanpa parent, atau ke level
     * "Proteksi"/"Power" saja) ke kategori LEAF yang tepat sesuai struktur
     * yang dibuat AksesorisSeeder (Casing, Charger, TWS, dst).
     *
     * Jalankan SETELAH AksesorisSeeder.
     */

    // slug leaf => kata kunci yang dicari di nama produk (lowercase)
    private array $keywordMap = [
        'casing'            => ['softcase', 'hardcase', 'case', 'casing', 'sarung'],
        'tempered-glass'    => ['tempered', 'anti gores', 'screen protector', 'screen guard'],
        'hydrogel'          => ['hydrogel'],
        'charger'           => ['charger', 'adaptor', 'batok'],
        'kabel-data'        => ['kabel data', 'kabel'],
        'power-bank'        => ['power bank', 'powerbank'],
        'tws'               => ['tws', 'true wireless'],
        'headset'           => ['headset', 'earphone'],
        'bluetooth-speaker' => ['bluetooth speaker', 'speaker'],
        'flashdisk'         => ['flashdisk', 'flash disk'],
        'memory-card'       => ['memory card', 'microsd', 'micro sd'],
        'holder'            => ['holder'],
        'ring-light-tripod' => ['ring light', 'tripod'],
    ];

    public function run(): void
    {
        // 1. Ambil semua kategori leaf yang relevan (key-kan berdasarkan slug)
        $leafSlugs = array_keys($this->keywordMap);
        $leafCategories = Category::whereIn('slug', $leafSlugs)->get()->keyBy('slug');

        $missingLeafs = array_diff($leafSlugs, $leafCategories->keys()->toArray());
        if (count($missingLeafs) > 0) {
            $this->command->error('Kategori leaf berikut belum ada di DB, jalankan AksesorisSeeder dulu: ' . implode(', ', $missingLeafs));
            return;
        }

        // 2. Ambil semua produk yang saat ini masih menempel ke kategori duplikat/nyasar
        //    "Aksesoris HP" (slug: aksesoris-hp) — dikonfirmasi lewat tinker sebagai sumber masalah.
        //    Kategori "Aksesoris" (id 7, slug: aksesoris) yang jadi root pohon leaf TIDAK disentuh,
        //    karena produk seharusnya tidak pernah nempel di level root itu juga.
        $flatSourceCategory = Category::where('slug', 'aksesoris-hp')->first();

        if (!$flatSourceCategory) {
            $this->command->warn('Kategori "aksesoris-hp" tidak ditemukan, tidak ada yang perlu dipindahkan.');
            return;
        }

        $products = Product::where('category_id', $flatSourceCategory->id)->get();

        if ($products->isEmpty()) {
            $this->command->warn('Tidak ada produk yang perlu dipindahkan. Cek dulu category_id produk kamu sekarang.');
            return;
        }

        $movedCount = 0;
        $unmatchedNames = [];

        foreach ($products as $product) {
            $productNameLower = strtolower($product->name);
            $matchedSlug = null;

            foreach ($this->keywordMap as $slug => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($productNameLower, $keyword)) {
                        $matchedSlug = $slug;
                        break 2;
                    }
                }
            }

            if ($matchedSlug) {
                $product->category_id = $leafCategories[$matchedSlug]->id;
                $product->save();
                $movedCount++;
            } else {
                $unmatchedNames[] = $product->name;
            }
        }

        $this->command->info("Berhasil memindahkan {$movedCount} produk ke kategori leaf yang sesuai.");

        if (count($unmatchedNames) > 0) {
            $this->command->warn('Produk berikut tidak cocok kata kunci apapun, assign manual:');
            foreach ($unmatchedNames as $name) {
                $this->command->line("- {$name}");
            }
        }
    }
}
