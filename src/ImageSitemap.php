<?php

namespace Sitemap;

use XMLWriter;

class ImageSitemap extends AbstractSiteMap
{
    private $baseUrl;
    const ITEM_PER_SITEMAP = 10000;
    const FILE_NAME        = 'image-sitemap';

    public function __construct($baseUrl)
    {
        $this->nodes   = [];
        $this->baseUrl = $baseUrl;
    }

    public function addItem($loc, $imageLoc)
    {
        $this->nodes[] = [
            'loc'         => $this->baseUrl . $loc,
            'image:image' => [
                'image:loc' => $this->baseUrl . $imageLoc,
            ],
        ];
    }

    public function generate($path = './', $baseFileName = self::FILE_NAME)
    {
        $sitemapsList = new SitemapsList($this->baseUrl);
        $itemsByPage  = self::ITEM_PER_SITEMAP;
        $pages        = ceil(count($this->nodes) / $itemsByPage);

        for ($i = 0; $i < $pages; $i++) {
            $offset     = $i * $itemsByPage;
            $slicedData = array_slice($this->nodes, $offset, $itemsByPage);
            $fileName   = $baseFileName . '-' . ($i + 1) . self::EXT;
            $this->generateSingleSitemap($fileName, $slicedData, $path);
            $sitemapsList->addItem($fileName);
        }

        $sitemapsList->generate($path, 'images-sitemap-index');
    }


    public function generateSingleSitemap($name, $items, $path = './')
    {
        $xml = new XMLWriter();
        $xml->openURI($path . $name);
        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(true);
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

        foreach ($items as $index => $item) {
            $this->writeNewElement($xml, 'url', $item);
        }

        $xml->endElement();
        $xml->endDocument();
    }


}
