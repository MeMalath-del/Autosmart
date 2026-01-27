<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>';
        $content .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        $content .= '<sitemap>';
        $content .= '<loc>' . url('/sitemap-main.xml') . '</loc>';
        $content .= '<lastmod>' . now()->toW3cString() . '</lastmod>';
        $content .= '</sitemap>';
        
        $content .= '<sitemap>';
        $content .= '<loc>' . url('/sitemap-products.xml') . '</loc>';
        $content .= '<lastmod>' . now()->toW3cString() . '</lastmod>';
        $content .= '</sitemap>';
        
        $content .= '<sitemap>';
        $content .= '<loc>' . url('/sitemap-stores.xml') . '</loc>';
        $content .= '<lastmod>' . now()->toW3cString() . '</lastmod>';
        $content .= '</sitemap>';
        
        $content .= '</sitemapindex>';

        return response($content)->header('Content-Type', 'text/xml');
    }

    public function main(): Response
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>';
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $pages = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/products', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/stores', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/about', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => '/contact', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ];

        foreach ($pages as $page) {
            $content .= '<url>';
            $content .= '<loc>' . url($page['url']) . '</loc>';
            $content .= '<changefreq>' . $page['changefreq'] . '</changefreq>';
            $content .= '<priority>' . $page['priority'] . '</priority>';
            $content .= '</url>';
        }

        // Categories
        foreach (Category::active()->get() as $category) {
            $content .= '<url>';
            $content .= '<loc>' . route('products.category', $category) . '</loc>';
            $content .= '<changefreq>weekly</changefreq>';
            $content .= '<priority>0.7</priority>';
            $content .= '</url>';
        }

        $content .= '</urlset>';

        return response($content)->header('Content-Type', 'text/xml');
    }

    public function products(): Response
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>';
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        Product::active()
            ->whereHas('store', fn($q) => $q->approved())
            ->chunk(100, function ($products) use (&$content) {
                foreach ($products as $product) {
                    $content .= '<url>';
                    $content .= '<loc>' . route('products.show', $product) . '</loc>';
                    $content .= '<lastmod>' . $product->updated_at->toW3cString() . '</lastmod>';
                    $content .= '<changefreq>weekly</changefreq>';
                    $content .= '<priority>0.8</priority>';
                    $content .= '</url>';
                }
            });

        $content .= '</urlset>';

        return response($content)->header('Content-Type', 'text/xml');
    }

    public function stores(): Response
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>';
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach (Store::approved()->get() as $store) {
            $content .= '<url>';
            $content .= '<loc>' . route('stores.show', $store) . '</loc>';
            $content .= '<lastmod>' . $store->updated_at->toW3cString() . '</lastmod>';
            $content .= '<changefreq>weekly</changefreq>';
            $content .= '<priority>0.7</priority>';
            $content .= '</url>';
        }

        $content .= '</urlset>';

        return response($content)->header('Content-Type', 'text/xml');
    }
}
