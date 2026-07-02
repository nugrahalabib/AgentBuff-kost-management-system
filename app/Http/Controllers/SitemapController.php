<?php

namespace App\Http\Controllers;

class SitemapController extends Controller
{
    /**
     * Render an XML sitemap of public, indexable pages so search engines
     * (Google, Bing) can discover the site. Uses url() so the host follows
     * the trusted proxy headers in production rather than APP_URL.
     */
    public function index()
    {
        // Only public, indexable URLs belong here. The landing page is a
        // single document; its sections (#galeri, #fasilitas, ...) are
        // anchors on the same URL, so we list the homepage only.
        $urls = [
            ['loc' => url('/'), 'changefreq' => 'weekly', 'priority' => '1.0'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . "</loc>\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
