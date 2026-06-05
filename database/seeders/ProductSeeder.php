<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private array $palette = [
        'men'   => [44,  62,  80],
        'women' => [142, 68,  173],
        'kids'  => [39,  174, 96],
    ];

    public function run(): void
    {
        $products = [
            // ── Men ─────────────────────────────────────────────────────────
            [
                'name'        => 'Classic White T-Shirt',
                'description' => 'A timeless white cotton t-shirt, perfect for everyday wear. Soft and breathable fabric.',
                'department'  => 'men',
                'category'    => 'T-Shirts',
                'base_price'  => '12.00',
                'sold_count'  => 45,
                'days_ago'    => 30,
                'sizes'       => [['S', 10], ['M', 15], ['L', 8], ['XL', 0]],
                'images'      => 2,
            ],
            [
                'name'        => 'Slim Fit Jeans',
                'description' => 'Modern slim fit jeans with a clean cut. Available in classic dark-blue wash.',
                'department'  => 'men',
                'category'    => 'Jeans',
                'base_price'  => '35.00',
                'sold_count'  => 30,
                'days_ago'    => 20,
                'sizes'       => [['S', 5], ['M', 12], ['L', 6], ['XL', 3]],
                'images'      => 3,
            ],
            [
                'name'        => 'Formal Button Shirt',
                'description' => 'Crisp formal button-down shirt ideal for office or occasions.',
                'department'  => 'men',
                'category'    => 'Shirts',
                'base_price'  => '28.00',
                'sold_count'  => 15,
                'days_ago'    => 10,
                'sizes'       => [['S', 0], ['M', 8], ['L', 10], ['XL', 4]],
                'images'      => 2,
            ],
            // ── Women ────────────────────────────────────────────────────────
            [
                'name'        => 'Floral Summer Dress',
                'description' => 'Lightweight floral print dress perfect for warm days. Flowy and comfortable.',
                'department'  => 'women',
                'category'    => 'Dresses',
                'base_price'  => '42.00',
                'sold_count'  => 62,
                'days_ago'    => 45,
                'sizes'       => [['XS', 3], ['S', 7], ['M', 5], ['L', 0]],
                'images'      => 3,
            ],
            [
                'name'        => 'High Waist Trousers',
                'description' => 'Elegant high-waist trousers with a tailored cut for a polished look.',
                'department'  => 'women',
                'category'    => 'Trousers',
                'base_price'  => '38.00',
                'sold_count'  => 28,
                'days_ago'    => 15,
                'sizes'       => [['XS', 0], ['S', 9], ['M', 11], ['L', 6]],
                'images'      => 2,
            ],
            [
                'name'        => 'Casual Linen Blouse',
                'description' => 'Relaxed linen blouse with a soft drape. Easy to pair with trousers or jeans.',
                'department'  => 'women',
                'category'    => 'Tops',
                'base_price'  => '22.00',
                'sold_count'  => 50,
                'days_ago'    => 35,
                'sizes'       => [['XS', 4], ['S', 6], ['M', 8], ['L', 5]],
                'images'      => 2,
            ],
            [
                'name'        => 'Knit Cardigan',
                'description' => 'A cozy ribbed knit cardigan. Great layering piece for cooler days.',
                'department'  => 'women',
                'category'    => 'Tops',
                'base_price'  => '48.00',
                'sold_count'  => 10,
                'days_ago'    => 5,
                'sizes'       => [['S', 8], ['M', 5], ['L', 3]],
                'images'      => 2,
            ],
            // ── Kids ─────────────────────────────────────────────────────────
            [
                'name'        => 'Boys Polo Shirt',
                'description' => 'Smart polo shirt for boys. Breathable pique cotton fabric.',
                'department'  => 'kids',
                'category'    => 'Polo',
                'base_price'  => '15.00',
                'sold_count'  => 35,
                'days_ago'    => 25,
                'sizes'       => [['4Y', 5], ['6Y', 8], ['8Y', 10], ['10Y', 0]],
                'images'      => 2,
            ],
            [
                'name'        => 'Girls Sundress',
                'description' => 'Pretty sundress with smocked bodice and flared skirt. Light and playful.',
                'department'  => 'kids',
                'category'    => 'Dresses',
                'base_price'  => '20.00',
                'sold_count'  => 42,
                'days_ago'    => 40,
                'sizes'       => [['4Y', 6], ['6Y', 9], ['8Y', 4], ['10Y', 2]],
                'images'      => 3,
            ],
            [
                'name'        => 'Kids Denim Shorts',
                'description' => 'Durable denim shorts with elastic waistband. Ideal for active kids.',
                'department'  => 'kids',
                'category'    => 'Shorts',
                'base_price'  => '18.00',
                'sold_count'  => 5,
                'days_ago'    => 3,
                'sizes'       => [['4Y', 0], ['6Y', 7], ['8Y', 5], ['10Y', 8]],
                'images'      => 2,
            ],
        ];

        foreach ($products as $data) {
            $product = Product::create([
                'name'        => $data['name'],
                'description' => $data['description'],
                'department'  => $data['department'],
                'category'    => $data['category'],
                'base_price'  => $data['base_price'],
                'status'      => 'active',
                'sold_count'  => $data['sold_count'],
                'created_at'  => now()->subDays($data['days_ago']),
                'updated_at'  => now()->subDays($data['days_ago']),
            ]);

            // Sizes
            foreach ($data['sizes'] as [$size, $stock]) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size'       => $size,
                    'stock'      => $stock,
                ]);
            }

            // Placeholder images
            for ($i = 0; $i < $data['images']; $i++) {
                $path = $this->makePlaceholderImage($data['name'], $data['department'], $i);
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                ]);
            }
        }
    }

    private function makePlaceholderImage(string $name, string $department, int $variant): string
    {
        $uuid     = (string) Str::uuid();
        $filename = $uuid . '.png';

        [$r, $g, $b] = $this->palette[$department] ?? [176, 141, 87];

        // Slightly vary shade per variant so thumbnails look different
        $r = max(20, min(235, $r + $variant * 18));
        $g = max(20, min(235, $g + $variant * 10));
        $b = max(20, min(235, $b - $variant * 12));

        $w   = 400;
        $h   = 500;
        $img = imagecreatetruecolor($w, $h);

        $bg    = imagecolorallocate($img, $r, $g, $b);
        $white = imagecolorallocate($img, 255, 255, 255);

        imagefilledrectangle($img, 0, 0, $w, $h, $bg);

        // Print product name centred
        $words = explode(' ', $name);
        $y     = (int) ($h / 2) - (count($words) * 20) / 2;
        foreach ($words as $word) {
            $tw = imagefontwidth(4) * strlen($word);
            imagestring($img, 4, (int) (($w - $tw) / 2), $y, $word, $white);
            $y += 22;
        }

        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        imagepng($img, $tmp);
        imagedestroy($img);

        Storage::disk('public')->putFileAs('products', new File($tmp), $filename);
        unlink($tmp);

        return 'products/' . $filename;
    }
}
