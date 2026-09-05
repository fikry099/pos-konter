<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PulsaProductSeeder extends Seeder
{
    public function run(): void
    {
        $operators = [
            'Telkomsel' => ['slug' => 'pulsa-telkomsel', 'code' => 'TSEL'],
            'Indosat'   => ['slug' => 'pulsa-indosat',   'code' => 'ISAT'],
            'XL'        => ['slug' => 'pulsa-xl',        'code' => 'XL'],
            'Tri'       => ['slug' => 'pulsa-tri',       'code' => 'THREE'],
            'Axis'      => ['slug' => 'pulsa-axis',      'code' => 'AXIS'],
            'Smartfren' => ['slug' => 'pulsa-smartfren', 'code' => 'SMART'],
        ];

        $denominations = [
            5   => ['nominal' => '5.000',   'cost' => 5300],
            10  => ['nominal' => '10.000',  'cost' => 10200],
            15  => ['nominal' => '15.000',  'cost' => 15100],
            20  => ['nominal' => '20.000',  'cost' => 20100],
            25  => ['nominal' => '25.000',  'cost' => 24900],
            50  => ['nominal' => '50.000',  'cost' => 49500],
            100 => ['nominal' => '100.000', 'cost' => 98500],
            150 => ['nominal' => '150.000', 'cost' => 148000],
            200 => ['nominal' => '200.000', 'cost' => 197000],
        ];

        foreach ($operators as $opName => $opData) {
            $catProvider = Category::where('slug', $opData['slug'])->first();
            $catId = $catProvider ? $catProvider->id : Category::where('slug', 'pulsa-reguler')->first()?->id;

            if (!$catId) continue;

            foreach ($denominations as $k => $denom) {
                // LOGIKA MARGIN ADMIN:
                // Jika nominal di bawah 100 (misal 5k - 50k) -> Admin 2.000 (Harga Jual = Nominal + 2.000)
                // Jika nominal 100k ke atas -> Admin 3.000 (Harga Jual = Nominal + 3.000)
                $adminFee = ($k < 100) ? 2000 : 3000;
                $sellingPrice = ($k * 1000) + $adminFee;

                Product::create([
                    'category_id'   => $catId,
                    'name'          => "Pulsa {$opName} {$denom['nominal']}",
                    'code'          => "{$opData['code']}{$k}K",
                    'type'          => 'digital',
                    'cost_price'    => $denom['cost'],
                    'selling_price' => $sellingPrice,
                    'stock'         => 0,
                    'min_stock'     => 0,
                    'is_active'     => true,
                ]);
            }
        }
    }
}