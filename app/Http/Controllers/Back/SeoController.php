<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SeoController extends Controller
{
    /**
     * عرض صفحة إعدادات SEO
     */
    public function index()
    {
        return view('back.seo.index');
    }

    /**
     * حفظ إعدادات SEO
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_title' => 'required|string|max:255',
            'site_description' => 'required|string|max:500',
            'site_keywords' => 'nullable|string|max:500',
            'og_image' => 'nullable|string',
            'google_analytics' => 'nullable|string',
            'facebook_pixel' => 'nullable|string',
        ]);

        // حفظ في config file
        $configPath = config_path('seo.php');
        $config = "<?php\n\nreturn [\n";
        $config .= "    'site_title' => '" . addslashes($request->site_title) . "',\n";
        $config .= "    'site_description' => '" . addslashes($request->site_description) . "',\n";
        $config .= "    'site_keywords' => '" . addslashes($request->site_keywords ?? '') . "',\n";
        $config .= "    'og_image' => '" . addslashes($request->og_image ?? '') . "',\n";
        $config .= "    'google_analytics' => '" . addslashes($request->google_analytics ?? '') . "',\n";
        $config .= "    'facebook_pixel' => '" . addslashes($request->facebook_pixel ?? '') . "',\n";
        $config .= "];\n";
        
        File::put($configPath, $config);
        
        return redirect()->back()->with('success', 'تم حفظ إعدادات SEO بنجاح');
    }

    /**
     * إنشاء Sitemap
     */
    public function generateSitemap()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // الصفحة الرئيسية
        $sitemap .= '  <url>' . "\n";
        $sitemap .= '    <loc>' . url('/') . '</loc>' . "\n";
        $sitemap .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $sitemap .= '    <changefreq>daily</changefreq>' . "\n";
        $sitemap .= '    <priority>1.0</priority>' . "\n";
        $sitemap .= '  </url>' . "\n";
        
        // صفحات عامة
        $pages = [
            ['url' => url('/student-login'), 'priority' => '0.8'],
            ['url' => url('/student-signup'), 'priority' => '0.8'],
        ];
        
        foreach ($pages as $page) {
            $sitemap .= '  <url>' . "\n";
            $sitemap .= '    <loc>' . $page['url'] . '</loc>' . "\n";
            $sitemap .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
            $sitemap .= '    <changefreq>monthly</changefreq>' . "\n";
            $sitemap .= '    <priority>' . $page['priority'] . '</priority>' . "\n";
            $sitemap .= '  </url>' . "\n";
        }
        
        // المحاضرات المميزة
        $featuredLectures = Lecture::where('is_featured', 1)->where('status', 1)->get();
        foreach ($featuredLectures as $lecture) {
            $sitemap .= '  <url>' . "\n";
            $sitemap .= '    <loc>' . url('/') . '#lecture-' . $lecture->id . '</loc>' . "\n";
            $sitemap .= '    <lastmod>' . $lecture->updated_at->format('Y-m-d') . '</lastmod>' . "\n";
            $sitemap .= '    <changefreq>weekly</changefreq>' . "\n";
            $sitemap .= '    <priority>0.7</priority>' . "\n";
            $sitemap .= '  </url>' . "\n";
        }
        
        $sitemap .= '</urlset>';
        
        File::put(public_path('sitemap.xml'), $sitemap);
        
        return redirect()->back()->with('success', 'تم إنشاء Sitemap بنجاح في: ' . url('sitemap.xml'));
    }
}
