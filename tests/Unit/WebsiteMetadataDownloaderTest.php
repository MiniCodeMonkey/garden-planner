<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\WebsiteMetadataDownloader;

class WebsiteMetadataDownloaderTest extends TestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testDownloadDetails($url, $expectedMetadata)
    {
        $websiteMetadataDownloader = new WebsiteMetadataDownloader();
        $metadata = $websiteMetadataDownloader->downloadMetadataForUrl($url);
        
        foreach ($expectedMetadata as $key => $val) {
            $this->assertEquals($val, $metadata[$val], 'Expected ' . $key . ' to be ' . $val . ' but got ' . $metadata[$val]);
        }
    }

    public function provideUrls() {
        return [
            [
                'https://fantastiskefroe.dk/products/spidskaal-filderkraut?_pos=1&_sid=65d1a29ac&_ss=r&a=b',
                [
                    'seeding_start' => date('Y').'-04-01',
                    'seeding_end' => date('Y').'-05-31',
                    'planting_start' => date('Y').'-05-01',
                    'planting_end' => date('Y').'-06-31',
                    'harvest_start' => date('Y').'-09-01',
                    'harvest_end' => date('Y').'-10-31',
                ],
            ]
        ];
    }
}
