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
            'Telkomsel' => ['slug' => 'pulsa-telkomsel', 'code' => 'TSEL', 'margin' => 1800],
            'Indosat'   => ['slug' => 'pulsa-indosat',   'code' => 'ISAT', 'margin' => 1700],
            'XL'        => ['slug' => 'pulsa-xl',        'code' => 'XL',   'margin' => 1600],
            'Tri'       => ['slug' => 'pulsa-tri',       'code' => 'THREE','margin' => 1500],
            'Axis'      => ['slug' => 'pulsa-axis',      'code' => 'AXIS', 'margin' => 1500],
            'Smartfren' => ['slug' => 'pulsa-smartfren', 'code' => 'SMART','margin' => 1500],
        ];

        $denominations = [
            5   => ['nominal' => '5.000',   'cost' => 5300],
            10  => ['nominal' => '10.000',  'cost' => 10200],
            15  => ['nominal' => '15.000',  'cost' => 15100],
            20  => ['nominal' => '20.000',  'cost' => 20100],
            25  => ['nominal' => '25.000',  'cost' => 24900],
            50  => ['nominal' => '50.000',  'cost' => 49500],
            100 => ['nominal' => '100.000', 'cost' => 98500],
        ];

        foreach ($operators as $opName => $opData) {
            $catProvider = Category::where('slug', $opData['slug'])->first();
            $catId = $catProvider ? $catProvider->id : Category::where('slug', 'pulsa-reguler')->first()?->id;

            if (!$catId) continue;

            foreach ($denominations as $k => $denom) {
                Product::create([
                    'category_id'   => $catId,
                    'name'          => "Pulsa {$opName} {$denom['nominal']}",
                    'code'          => "{$opData['code']}{$k}K",
                    'type'          => 'digital',
                    'cost_price'    => $denom['cost'],
                    'selling_price' => $denom['cost'] + $opData['margin'],
                    'stock'         => 0,
                    'min_stock'     => 0,
                    'is_active'     => true,
                ]);
            }
        }
    }
}