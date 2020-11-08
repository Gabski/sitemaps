<?php

namespace Sitemap;

use XMLWriter;

class Sitemap
{
    private $nodes;
    private $baseUrl;
    const ITEM_PER_SITEMAP = 10000;
    const FILE_NAME        = 'sitemap';
    const EXT              = '.xml';

    public function __construct($baseUrl)
    {
        $this->nodes   = [];
        $this->baseUrl = $baseUrl;
    }

    public function addItem($loc, $priority = 0.80, $lastMod = null)
    {
        $lastMod = $lastMod ?? date("Y-m-d") . "T" . date("H:i") . "+00:00";

        $this->nodes[] = [
            'loc'      => $this->baseUrl . $loc,
            'lastmod'  => $lastMod,
            'priority' => $priority,
        ];
    }

    public function generate($path = './')
    {
        $sitemapsList = new SitemapsList($this->baseUrl);
        $itemsByPage = self::ITEM_PER_SITEMAP;
        $pages       = ceil(count($this->nodes) / $itemsByPage);

        for ($i = 0; $i < $pages; $i++) {
            $offset     = $i * $itemsByPage;
            $slicedData = array_slice($this->nodes, $offset, $itemsByPage);
            $fileName   = self::FILE_NAME . '-' . ($i + 1) . self::EXT;
            $this->generateSingleSitemap($fileName, $slicedData, $path);
            $sitemapsList->addItem($fileName);
        }

        $sitemapsList->generate($path);
    }


    public function generateSingleSitemap($name, $items, $path = './')
    {
        $xml = new XMLWriter();
        $xml->openURI($path . $name);
        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(true);
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->writeAttribute('xsi:schemaLocation',
            'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

        foreach ($items as $index => $item) {
            $xml->startElement('url');
            foreach ($item as $key => $value) {
                $xml->writeElement($key, $value);
            }
            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();
    }
}
