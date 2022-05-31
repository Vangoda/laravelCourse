<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\Product;
use App\Models\LinkProduct;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Link::factory(30)->create()
            // Create a LinkProduct for every createdLink
            ->each(function (Link $link) {
                LinkProduct::create([
                    'link_id' => $link->id, // Generated id
                    'product_id' => Product::inRandomOrder()->first()->id // Random product id
                ]);
            });
    }
}
