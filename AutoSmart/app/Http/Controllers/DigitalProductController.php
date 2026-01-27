<?php

namespace App\Http\Controllers;

use App\Models\DigitalDownload;
use App\Models\DigitalProduct;
use Illuminate\Support\Facades\Storage;

class DigitalProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function downloads()
    {
        $downloads = DigitalDownload::where('user_id', auth()->id())
            ->with('digitalProduct.product')
            ->latest()
            ->paginate(20);

        return view('digital.downloads', compact('downloads'));
    }

    public function download(string $token)
    {
        $download = DigitalDownload::where('download_token', $token)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Check expiry
        if ($download->expires_at && $download->expires_at->isPast()) {
            return back()->with('error', 'انتهت صلاحية التحميل');
        }

        // Check download limit
        $digital = $download->digitalProduct;
        if ($digital->download_limit && $download->download_count >= $digital->download_limit) {
            return back()->with('error', 'تم استنفاد عدد مرات التحميل المسموح');
        }

        // Update download record
        $download->increment('download_count');
        $download->update([
            'first_download_at' => $download->first_download_at ?? now(),
            'last_download_at' => now(),
        ]);

        // Return file download
        return Storage::download($digital->file_path, $digital->file_name);
    }

    public function preview(DigitalProduct $digital)
    {
        if (! $digital->preview_path) {
            abort(404);
        }

        return response()->file(Storage::path($digital->preview_path));
    }
}
