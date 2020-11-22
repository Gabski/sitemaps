<?php

namespace Sitemap;

use XMLWriter;

abstract class AbstractSiteMap
{
    protected $nodes;
    const EXT = '.xml';

    protected function writeNewElement(XMLWriter $xml, string $name, array $items)
    {
        $xml->startElement($name);
        foreach ($items as $key => $value) {

            if (is_array($value)) {
                $this->writeNewElement($xml, $key, $value);
                continue;
            }

            $xml->writeElement($key, $value);
        }
        $xml->endElement();
    }

    abstract public function generate($path = './', $baseFileName = self::FILE_NAME);
}
