<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * عرض Media Library
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, images, videos, documents
        $search = $request->get('search', '');
        
        $files = $this->getMediaFiles($type, $search);
        
        return view('back.media.index', compact('files', 'type', 'search'));
    }
    
    /**
     * رفع ملفات
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:100240', // 10MB max
        ]);
        
        $uploaded = [];
        
        foreach ($request->file('files') as $file) {
            // استخدام الاسم الأصلي للملف مع إضافة timestamp لتجنب التكرار
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            
            // حفظ الاسم الأصلي مع دعم العربية
            // نستخدم الاسم الأصلي كما هو مع timestamp، وننظف فقط الأحرف الخطيرة جداً
            $filename = $originalName . '_' . time() . '.' . $extension;
            
            // تنظيف فقط الأحرف الخطيرة التي قد تسبب مشاكل في نظام الملفات
            // (مثل / \ : * ? " < > |) - نترك الأحرف العربية والأحرف الآمنة الأخرى
            $filename = preg_replace('/[\/\\\\:\*\?"<>\|]/u', '_', $filename);
            
            // إزالة المسافات المتعددة
            $filename = preg_replace('/\s+/u', ' ', $filename);
            $filename = trim($filename);
            
            // إذا كان الاسم فارغاً بعد التنظيف، نستخدم اسم افتراضي
            if (empty($filename) || $filename === '_' . time() . '.' . $extension) {
                $filename = 'file_' . time() . '.' . $extension;
            }
            
            $path = $file->storeAs('media', $filename, 'public');
            
            $uploaded[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'url' => Storage::url($path),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
            ];
        }
        
        return response()->json(['success' => true, 'files' => $uploaded]);
    }
    
    /**
     * حذف ملف
     */
    public function delete(Request $request, $file)
    {
        $path = 'media/' . $file;
        
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }
    
    /**
     * الحصول على الملفات
     */
    private function getMediaFiles($type = 'all', $search = '')
    {
        $files = [];
        $mediaPath = storage_path('app/public/media');
        
        if (!is_dir($mediaPath)) {
            return $files;
        }
        
        $allFiles = glob($mediaPath . '/*');
        
        foreach ($allFiles as $filePath) {
            if (is_file($filePath)) {
                $file = [
                    'name' => basename($filePath),
                    'path' => 'media/' . basename($filePath),
                    'url' => Storage::url('media/' . basename($filePath)),
                    'size' => filesize($filePath),
                    'type' => mime_content_type($filePath),
                    'created_at' => date('Y-m-d H:i:s', filemtime($filePath)),
                ];
                
                // Filter by type
                if ($type !== 'all') {
                    $mimeType = $file['type'];
                    if ($type === 'images' && !str_starts_with($mimeType, 'image/')) continue;
                    if ($type === 'videos' && !str_starts_with($mimeType, 'video/')) continue;
                    if ($type === 'documents' && !in_array($mimeType, ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) continue;
                }
                
                // Filter by search
                if ($search && !str_contains(strtolower($file['name']), strtolower($search))) {
                    continue;
                }
                
                $files[] = $file;
            }
        }
        
        // Sort by created_at desc
        usort($files, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return $files;
    }
}




