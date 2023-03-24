<?php

namespace App;

use DOMDocument;
use Illuminate\Support\Facades\Storage;

class WebsiteMetadataImageDownloader
{
    public function downloadForUrl(string $url): ?string
    {
        $disk = Storage::disk();

        $options = [
            'http' => [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36'
            ]
        ];
        $context = stream_context_create($options);

        $url = filter_var($url, FILTER_VALIDATE_URL);
        if (!$url) {
            return null;
        }

        $contents = file_get_contents($url, false, $context);

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($contents);

        foreach ($dom->getElementsByTagName('meta') as $meta) {
            if ($meta->getAttribute('property') === 'og:image') {
                $imageUrl = $meta->getAttribute('content');
                $ext = strtolower(pathinfo($imageUrl, PATHINFO_EXTENSION));
                $ext = explode('?', $ext)[0];

                $filename = 'seeds/' . md5($url) . '.' . $ext;

                if (!$disk->exists($filename)) {
                    $disk->put($filename, file_get_contents($imageUrl, false, $context), 'public');
                }

                return $filename;
            }
        }

        return null;
    }
}
