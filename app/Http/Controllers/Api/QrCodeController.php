<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class QrCodeController extends Controller
{
    public function generate(Variant $variant)
    {
        try {
            // We use SKU as the stable identifier for the QR code
            // We prepend a prefix to identify the type
            $content = 'v:' . $variant->sku;
            
            $renderer = new ImageRenderer(
                new RendererStyle(300, 1), // size 300, margin 1
                new SvgImageBackEnd()
            );
            
            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($content);
            
            return response($qrCode)->header('Content-Type', 'image/svg+xml');
        } catch (\Exception $e) {
            Log::error('QR Generation Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to generate QR code'], 500);
        }
    }

    public function show(Request $request)
    {
        $code = $request->query('code');
        
        if (!$code) {
            return response()->json(['message' => 'No code provided'], 400);
        }

        // Decode logic
        // We expect format "v:SKU-123"
        // If the code comes from a URL scan, it might be the full URL, handled by frontend.
        // This endpoint is for resolving a code string to a resource.

        if (str_starts_with($code, 'v:')) {
            $sku = substr($code, 2);
        } else {
            $sku = $code; // Fallback try raw SKU
        }

        $variant = Variant::where('sku', $sku)->with(['inventory', 'stockMovements' => function($q) {
            $q->latest()->limit(5);
        }])->first();

        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        return response()->json([
            'data' => $variant,
            'redirect_url' => "/variants/{$variant->id}" // For frontend navigation
        ]);
    }
    public function downloadAll()
    {
        try {
            $variants = Variant::all();
            $zipFileName = 'qr-codes-' . date('Y-m-d-H-i') . '.zip';
            $zipFilePath = storage_path('app/public/' . $zipFileName);
            
            // Ensure directory exists
            if (!file_exists(dirname($zipFilePath))) {
                mkdir(dirname($zipFilePath), 0755, true);
            }

            $zip = new \ZipArchive;
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                $renderer = new ImageRenderer(
                    new RendererStyle(300, 1),
                    new SvgImageBackEnd()
                );
                $writer = new Writer($renderer);

                foreach ($variants as $variant) {
                    $content = 'v:' . $variant->sku;
                    $qrCode = $writer->writeString($content);
                    // Sanitize filename
                    $safeSku = preg_replace('/[^a-zA-Z0-9_-]/', '_', $variant->sku);
                    $zip->addFromString($safeSku . '.svg', $qrCode);
                }
                $zip->close();
            }

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Bulk QR Download Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to generate bulk QR codes'], 500);
        }
    }
}
