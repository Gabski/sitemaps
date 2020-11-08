<?php

namespace Sitemap;

use XMLWriter;

class SitemapsList
{
    private $nodes;
    private $baseUrl;

    const FILE_NAME = 'sitemap';
    const EXT       = '.xml';

    public function __construct($baseUrl)
    {
        $this->nodes   = [];
        $this->baseUrl = $baseUrl;
    }

    public function addItem($loc)
    {
        $this->nodes[] = [
            'loc' => $this->baseUrl . $loc,
        ];
    }

    public function generate($path = './')
    {
        $this->generateSitemapsList(self::FILE_NAME, $this->nodes, $path);
    }

    public function generateSitemapsList($name, $items, $path = './')
    {
        $xml = new XMLWriter();
        $xml->openURI($path . $name . self::EXT);
        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(true);
        $xml->startElement('sitemapindex');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($items as $index => $item) {
            $xml->startElement('sitemap');
            foreach ($item as $key => $value) {
                $xml->writeElement($key, $value);
            }
            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();
    }
}
