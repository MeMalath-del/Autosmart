<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ImportLog;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\Store;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProductImportService
{
    protected array $requiredColumns = ['name', 'price', 'quantity'];

    protected array $optionalColumns = ['sku', 'description', 'category', 'brand', 'condition', 'warranty'];

    public function createImport(Store $store, UploadedFile $file, array $mapping = []): ProductImport
    {
        $path = $file->store('imports', 'local');

        return ProductImport::create([
            'store_id' => $store->id,
            'user_id' => auth()->id(),
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'status' => 'pending',
            'mapping' => $mapping,
        ]);
    }

    public function processImport(ProductImport $import): void
    {
        $import->start();

        try {
            $data = $this->parseFile($import->file_path);
            $import->update(['total_rows' => count($data)]);

            foreach ($data as $index => $row) {
                $this->processRow($import, $index + 1, $row);
                $import->increment('processed_rows');
            }

            $import->complete();
        } catch (\Exception $e) {
            $import->fail();
            $import->update(['errors' => [$e->getMessage()]]);
        }
    }

    protected function parseFile(string $path): array
    {
        $fullPath = storage_path('app/'.$path);
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

        if ($extension === 'csv') {
            return $this->parseCsv($fullPath);
        }

        throw new \Exception('نوع الملف غير مدعوم');
    }

    protected function parseCsv(string $path): array
    {
        $data = [];
        $handle = fopen($path, 'r');

        if (! $handle) {
            throw new \Exception('لا يمكن فتح الملف');
        }

        $headers = fgetcsv($handle);
        $headers = array_map('trim', $headers);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }

        fclose($handle);

        return $data;
    }

    protected function processRow(ProductImport $import, int $rowNumber, array $row): void
    {
        try {
            // Validate required fields
            $errors = $this->validateRow($row, $import->mapping);

            if (! empty($errors)) {
                $this->logError($import, $rowNumber, implode(', ', $errors), $row);

                return;
            }

            // Map columns
            $mapping = $import->mapping;
            $productData = $this->mapRowToProduct($row, $mapping);

            // Find or create category
            if (! empty($productData['category_name'])) {
                $category = Category::firstOrCreate(
                    ['name' => $productData['category_name']],
                    ['slug' => Str::slug($productData['category_name'])]
                );
                $productData['category_id'] = $category->id;
            }

            // Create product
            $product = Product::create([
                'store_id' => $import->store_id,
                'name' => $productData['name'],
                'name_ar' => $productData['name'],
                'slug' => Str::slug($productData['name']).'-'.Str::random(5),
                'description' => $productData['description'] ?? null,
                'price' => $productData['price'],
                'quantity' => $productData['quantity'],
                'sku' => $productData['sku'] ?? null,
                'category_id' => $productData['category_id'] ?? null,
                'condition' => $productData['condition'] ?? 'new',
                'warranty_months' => $productData['warranty'] ?? 0,
                'is_active' => true,
            ]);

            $this->logSuccess($import, $rowNumber, $product->id, $row);

        } catch (\Exception $e) {
            $this->logError($import, $rowNumber, $e->getMessage(), $row);
        }
    }

    protected function validateRow(array $row, array $mapping): array
    {
        $errors = [];

        $nameCol = $mapping['name'] ?? 'name';
        $priceCol = $mapping['price'] ?? 'price';
        $qtyCol = $mapping['quantity'] ?? 'quantity';

        if (empty($row[$nameCol] ?? null)) {
            $errors[] = 'اسم المنتج مطلوب';
        }

        if (! isset($row[$priceCol]) || ! is_numeric($row[$priceCol])) {
            $errors[] = 'السعر مطلوب ويجب أن يكون رقم';
        }

        if (! isset($row[$qtyCol]) || ! is_numeric($row[$qtyCol])) {
            $errors[] = 'الكمية مطلوبة ويجب أن تكون رقم';
        }

        return $errors;
    }

    protected function mapRowToProduct(array $row, array $mapping): array
    {
        return [
            'name' => $row[$mapping['name'] ?? 'name'] ?? null,
            'price' => floatval($row[$mapping['price'] ?? 'price'] ?? 0),
            'quantity' => intval($row[$mapping['quantity'] ?? 'quantity'] ?? 0),
            'sku' => $row[$mapping['sku'] ?? 'sku'] ?? null,
            'description' => $row[$mapping['description'] ?? 'description'] ?? null,
            'category_name' => $row[$mapping['category'] ?? 'category'] ?? null,
            'condition' => $row[$mapping['condition'] ?? 'condition'] ?? 'new',
            'warranty' => intval($row[$mapping['warranty'] ?? 'warranty'] ?? 0),
        ];
    }

    protected function logSuccess(ProductImport $import, int $rowNumber, int $productId, array $row): void
    {
        ImportLog::create([
            'import_id' => $import->id,
            'row_number' => $rowNumber,
            'status' => 'success',
            'product_id' => $productId,
            'row_data' => $row,
        ]);

        $import->increment('success_count');
    }

    protected function logError(ProductImport $import, int $rowNumber, string $message, array $row): void
    {
        ImportLog::create([
            'import_id' => $import->id,
            'row_number' => $rowNumber,
            'status' => 'error',
            'error_message' => $message,
            'row_data' => $row,
        ]);

        $import->increment('error_count');
    }

    public function generateTemplate(): string
    {
        $headers = ['name', 'price', 'quantity', 'sku', 'description', 'category', 'condition', 'warranty'];
        $example = ['فلتر زيت تويوتا', '45.00', '100', 'FLT-001', 'فلتر زيت أصلي', 'فلاتر', 'new', '12'];

        $csv = implode(',', $headers)."\n";
        $csv .= implode(',', $example)."\n";

        return $csv;
    }
}
