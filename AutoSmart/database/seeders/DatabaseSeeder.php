<?php

namespace Database\Seeders;

use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء المستخدمين
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@autosmart.sa',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0500000000',
            'city' => 'الرياض',
            'is_active' => true,
        ]);

        $seller1 = User::create([
            'name' => 'أحمد محمد',
            'email' => 'seller@autosmart.sa',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'phone' => '0501111111',
            'city' => 'جدة',
            'is_active' => true,
        ]);

        $seller2 = User::create([
            'name' => 'سعد العتيبي',
            'email' => 'seller2@autosmart.sa',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'phone' => '0502222222',
            'city' => 'الدمام',
            'is_active' => true,
        ]);

        $customer = User::create([
            'name' => 'خالد العمري',
            'email' => 'customer@autosmart.sa',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '0503333333',
            'city' => 'الرياض',
            'is_active' => true,
        ]);

        // إنشاء التصنيفات
        $categories = [
            ['name' => 'Engine Parts', 'name_ar' => 'قطع المحرك', 'slug' => 'engine-parts'],
            ['name' => 'Brakes', 'name_ar' => 'الفرامل', 'slug' => 'brakes'],
            ['name' => 'Suspension', 'name_ar' => 'نظام التعليق', 'slug' => 'suspension'],
            ['name' => 'Electrical', 'name_ar' => 'الكهرباء', 'slug' => 'electrical'],
            ['name' => 'Body Parts', 'name_ar' => 'قطع الهيكل', 'slug' => 'body-parts'],
            ['name' => 'Filters', 'name_ar' => 'الفلاتر', 'slug' => 'filters'],
            ['name' => 'Lighting', 'name_ar' => 'الإضاءة', 'slug' => 'lighting'],
            ['name' => 'Cooling System', 'name_ar' => 'نظام التبريد', 'slug' => 'cooling-system'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat + ['is_active' => true, 'sort_order' => 0]);
        }

        // إنشاء ماركات السيارات
        $brands = [
            ['name' => 'Toyota', 'name_ar' => 'تويوتا', 'slug' => 'toyota', 'country' => 'اليابان', 'models' => ['Camry', 'Corolla', 'Land Cruiser', 'Hilux', 'Yaris']],
            ['name' => 'Honda', 'name_ar' => 'هوندا', 'slug' => 'honda', 'country' => 'اليابان', 'models' => ['Accord', 'Civic', 'CR-V', 'Pilot']],
            ['name' => 'Nissan', 'name_ar' => 'نيسان', 'slug' => 'nissan', 'country' => 'اليابان', 'models' => ['Altima', 'Patrol', 'Maxima', 'Sunny']],
            ['name' => 'Hyundai', 'name_ar' => 'هيونداي', 'slug' => 'hyundai', 'country' => 'كوريا', 'models' => ['Sonata', 'Elantra', 'Tucson', 'Santa Fe']],
            ['name' => 'Kia', 'name_ar' => 'كيا', 'slug' => 'kia', 'country' => 'كوريا', 'models' => ['Optima', 'Cerato', 'Sportage', 'Sorento']],
            ['name' => 'Ford', 'name_ar' => 'فورد', 'slug' => 'ford', 'country' => 'أمريكا', 'models' => ['F-150', 'Explorer', 'Expedition', 'Taurus']],
            ['name' => 'Chevrolet', 'name_ar' => 'شيفروليه', 'slug' => 'chevrolet', 'country' => 'أمريكا', 'models' => ['Tahoe', 'Suburban', 'Silverado', 'Malibu']],
            ['name' => 'Mercedes-Benz', 'name_ar' => 'مرسيدس', 'slug' => 'mercedes', 'country' => 'ألمانيا', 'models' => ['E-Class', 'S-Class', 'C-Class', 'GLE']],
            ['name' => 'BMW', 'name_ar' => 'بي إم دبليو', 'slug' => 'bmw', 'country' => 'ألمانيا', 'models' => ['3 Series', '5 Series', '7 Series', 'X5']],
            ['name' => 'Lexus', 'name_ar' => 'لكزس', 'slug' => 'lexus', 'country' => 'اليابان', 'models' => ['ES', 'LS', 'LX', 'RX']],
        ];

        foreach ($brands as $brandData) {
            $models = $brandData['models'];
            unset($brandData['models']);
            $brand = CarBrand::create($brandData + ['is_active' => true]);

            foreach ($models as $modelName) {
                CarModel::create([
                    'brand_id' => $brand->id,
                    'name' => $modelName,
                    'slug' => $brand->slug.'-'.strtolower(str_replace(' ', '-', $modelName)),
                    'year_from' => 2015,
                    'year_to' => 2024,
                    'is_active' => true,
                ]);
            }
        }

        // إنشاء المتاجر
        $store1 = Store::create([
            'user_id' => $seller1->id,
            'name' => 'Auto Parts Pro',
            'name_ar' => 'قطع غيار المحترفين',
            'slug' => 'auto-parts-pro',
            'description' => 'متجر متخصص في قطع غيار السيارات اليابانية والكورية',
            'phone' => '0501111111',
            'whatsapp' => '0501111111',
            'email' => 'info@autopartspro.sa',
            'address' => 'شارع الملك عبدالعزيز',
            'city' => 'جدة',
            'status' => 'approved',
            'is_verified' => true,
            'is_featured' => true,
            'rating' => 4.5,
            'rating_count' => 120,
            'approved_at' => now(),
        ]);

        $store2 = Store::create([
            'user_id' => $seller2->id,
            'name' => 'Eastern Auto Parts',
            'name_ar' => 'قطع غيار الشرقية',
            'slug' => 'eastern-auto-parts',
            'description' => 'أكبر مركز لقطع غيار السيارات في المنطقة الشرقية',
            'phone' => '0502222222',
            'whatsapp' => '0502222222',
            'email' => 'info@easternparts.sa',
            'address' => 'حي الراكة',
            'city' => 'الدمام',
            'status' => 'approved',
            'is_verified' => true,
            'is_featured' => true,
            'rating' => 4.2,
            'rating_count' => 85,
            'approved_at' => now(),
        ]);

        // إنشاء المنتجات
        $products = [
            ['name' => 'Oil Filter - Toyota', 'name_ar' => 'فلتر زيت تويوتا', 'category' => 6, 'price' => 45, 'quantity' => 100],
            ['name' => 'Air Filter - Toyota Camry', 'name_ar' => 'فلتر هواء كامري', 'category' => 6, 'price' => 65, 'quantity' => 80],
            ['name' => 'Brake Pads - Front', 'name_ar' => 'تيل فرامل أمامي', 'category' => 2, 'price' => 180, 'sale_price' => 150, 'quantity' => 50],
            ['name' => 'Brake Disc - Front', 'name_ar' => 'دسك فرامل أمامي', 'category' => 2, 'price' => 250, 'quantity' => 30],
            ['name' => 'Spark Plugs Set', 'name_ar' => 'طقم بواجي', 'category' => 1, 'price' => 120, 'quantity' => 60],
            ['name' => 'Engine Oil 5W-30 4L', 'name_ar' => 'زيت محرك 5W-30 4 لتر', 'category' => 1, 'price' => 95, 'quantity' => 200],
            ['name' => 'Radiator - Toyota Corolla', 'name_ar' => 'رديتر كورولا', 'category' => 8, 'price' => 450, 'sale_price' => 380, 'quantity' => 15],
            ['name' => 'Alternator', 'name_ar' => 'دينمو', 'category' => 4, 'price' => 650, 'quantity' => 20],
            ['name' => 'Starter Motor', 'name_ar' => 'سلف', 'category' => 4, 'price' => 580, 'quantity' => 25],
            ['name' => 'Shock Absorber - Front', 'name_ar' => 'مساعد أمامي', 'category' => 3, 'price' => 320, 'quantity' => 40],
            ['name' => 'Shock Absorber - Rear', 'name_ar' => 'مساعد خلفي', 'category' => 3, 'price' => 280, 'quantity' => 40],
            ['name' => 'Headlight Assembly - Left', 'name_ar' => 'شمعة أمامية يسار', 'category' => 7, 'price' => 750, 'quantity' => 10],
            ['name' => 'Tail Light - Right', 'name_ar' => 'إضاءة خلفية يمين', 'category' => 7, 'price' => 350, 'quantity' => 15],
            ['name' => 'Side Mirror - Left', 'name_ar' => 'مرآة جانبية يسار', 'category' => 5, 'price' => 280, 'quantity' => 20],
            ['name' => 'Battery 70Ah', 'name_ar' => 'بطارية 70 أمبير', 'category' => 4, 'price' => 420, 'sale_price' => 380, 'quantity' => 50],
            ['name' => 'Timing Belt Kit', 'name_ar' => 'طقم سير توقيت', 'category' => 1, 'price' => 280, 'quantity' => 25],
            ['name' => 'Water Pump', 'name_ar' => 'طرمبة ماء', 'category' => 8, 'price' => 220, 'quantity' => 35],
            ['name' => 'Thermostat', 'name_ar' => 'ثيرموستات', 'category' => 8, 'price' => 85, 'quantity' => 60],
            ['name' => 'CV Joint Boot', 'name_ar' => 'جلدة عكس', 'category' => 3, 'price' => 45, 'quantity' => 100],
            ['name' => 'Tie Rod End', 'name_ar' => 'رأس مقص', 'category' => 3, 'price' => 95, 'quantity' => 50],
        ];

        $carModelIds = CarModel::pluck('id')->toArray();
        $stores = [$store1, $store2];

        foreach ($products as $index => $productData) {
            $store = $stores[$index % 2];
            $product = Product::create([
                'store_id' => $store->id,
                'category_id' => $productData['category'],
                'name' => $productData['name'],
                'name_ar' => $productData['name_ar'],
                'slug' => \Str::slug($productData['name']).'-'.uniqid(),
                'description' => 'قطعة غيار أصلية بجودة عالية وضمان',
                'price' => $productData['price'],
                'sale_price' => $productData['sale_price'] ?? null,
                'quantity' => $productData['quantity'],
                'condition' => 'new',
                'warranty' => '6_months',
                'is_active' => true,
                'is_featured' => $index < 8,
                'rating' => rand(35, 50) / 10,
                'rating_count' => rand(5, 50),
            ]);

            // ربط موديلات عشوائية
            $randomModels = array_rand(array_flip($carModelIds), min(5, count($carModelIds)));
            if (! is_array($randomModels)) {
                $randomModels = [$randomModels];
            }
            $product->carModels()->attach($randomModels);
        }

        $this->command->info('✅ تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->info('');
        $this->command->info('🔑 بيانات الدخول:');
        $this->command->info('   المدير: admin@autosmart.sa / password');
        $this->command->info('   البائع: seller@autosmart.sa / password');
        $this->command->info('   العميل: customer@autosmart.sa / password');
    }
}
