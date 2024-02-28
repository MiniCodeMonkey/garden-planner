<?php

namespace App;

use DOMDocument;
use SVG\SVG;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteMetadataDownloader
{
    private function createContext() {
        libxml_use_internal_errors(true);

        $options = [
            'http' => [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36'
            ]
        ];
        return stream_context_create($options);
    }

    public function downloadImageForUrl(string $url): ?string
    {
        $disk = Storage::disk();

        $context = $this->createContext();

        $url = filter_var($url, FILTER_VALIDATE_URL);
        if (!$url) {
            return null;
        }

        $contents = file_get_contents($url, false, $context);

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

    public function downloadMetadataForUrl(string $url): array
    {
        $metadata = [];
        $context = $this->createContext();

        $url = filter_var($url, FILTER_VALIDATE_URL);
        if (!$url) {
            return $metadata;
        }

        $contents = file_get_contents($url, false, $context);

        $dom = new DOMDocument();
        $dom->loadHTML($contents);

        $calendarImageUrl = null;
        foreach ($dom->getElementsByTagName('img') as $img) {
            $src = $img->getAttribute('src');
            if (str_contains($src, 'cal') && str_contains($src, '.svg')) {
                $calendarImageUrl = $src;
                break;
            }
        }

        if ($calendarImageUrl) {
            $metadata = array_merge($metadata, $this->extractDatesFromCalendarImage($calendarImageUrl));
        }

        return null;
    }

    private function extractDatesFromCalendarImage($url) {
        if (Str::startsWith($url, '//')) {
            $url = 'https:' . $url;
        }

        $image = SVG::fromFile($url);
        $parent = $image->getDocument()->getElementById('Kalender');
        for ($i = $parent->countChildren() - 1; $i >= 0; --$i) {
            dump($parent->getChild($i));
        }

        $dom = new DOMDocument();
        $dom->loadHTML($contents);

        foreach ($dom->getElementsByTagName('rect') as $rect) {
            dump([
                'x' => $rect->getAttribute('x'),
                'y' => $rect->getAttribute('x'),
                'width' => $rect->getAttribute('x'),
                'height' => $rect->getAttribute('x'),
            ]);
        }
    }
}
