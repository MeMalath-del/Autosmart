<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ProductImport;
use App\Services\ProductImportService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    protected ProductImportService $importService;

    public function __construct(ProductImportService $importService)
    {
        $this->importService = $importService;
        $this->middleware(['auth', 'seller']);
    }

    public function index()
    {
        $store = auth()->user()->store;
        $imports = ProductImport::where('store_id', $store->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('seller.imports.index', compact('imports'));
    }

    public function create()
    {
        return view('seller.imports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $store = auth()->user()->store;

        $import = $this->importService->createImport(
            $store,
            $request->file('file'),
            $request->input('mapping', [])
        );

        return redirect()->route('seller.imports.mapping', $import)
            ->with('success', 'تم رفع الملف بنجاح، يرجى تحديد الأعمدة');
    }

    public function mapping(ProductImport $import)
    {
        $this->authorize('view', $import);

        // Parse first few rows for preview
        $preview = $this->getPreviewData($import);

        return view('seller.imports.mapping', compact('import', 'preview'));
    }

    public function process(Request $request, ProductImport $import)
    {
        $this->authorize('update', $import);

        $request->validate([
            'mapping' => 'required|array',
            'mapping.name' => 'required|string',
            'mapping.price' => 'required|string',
            'mapping.quantity' => 'required|string',
        ]);

        $import->update(['mapping' => $request->mapping]);

        // Process import (in production, use queue)
        $this->importService->processImport($import);

        return redirect()->route('seller.imports.show', $import)
            ->with('success', 'تم معالجة الاستيراد');
    }

    public function show(ProductImport $import)
    {
        $this->authorize('view', $import);

        $import->load('logs');

        return view('seller.imports.show', compact('import'));
    }

    public function downloadTemplate()
    {
        $csv = $this->importService->generateTemplate();

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="import_template.csv"');
    }

    protected function getPreviewData(ProductImport $import): array
    {
        $path = storage_path('app/'.$import->file_path);
        $handle = fopen($path, 'r');

        $headers = fgetcsv($handle);
        $rows = [];

        for ($i = 0; $i < 5; $i++) {
            $row = fgetcsv($handle);
            if ($row) {
                $rows[] = array_combine($headers, $row);
            }
        }

        fclose($handle);

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }
}
