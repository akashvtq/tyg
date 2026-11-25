<?php

namespace App\Services\SeoPage;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Str;

class TableOfContentsService
{
    /**
     * @return array{html: string, items: list<array{id: string, text: string, level: int, children: list<array>}>}
     */
    public function build(string $html): array
    {
        if (trim($html) === '') {
            return ['html' => $html, 'items' => []];
        }

        $dom = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div id="toc-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($dom);
        $root = $xpath->query('//div[@id="toc-root"]')->item(0);

        $used = [];
        $items = [];
        $currentH2 = null;

        foreach ($xpath->query('.//h2 | .//h3', $root) as $heading) {
            $text = trim(preg_replace('/\s+/u', ' ', $heading->textContent));
            if ($text === '') {
                continue;
            }

            $id = $this->uniqueId(Str::slug($text) ?: 'section', $used);
            $heading->setAttribute('id', $id);

            $item = ['id' => $id, 'text' => $text, 'level' => (int) substr($heading->nodeName, 1), 'children' => []];

            if ($item['level'] === 2) {
                $items[] = $item;
                $currentH2 = array_key_last($items);
            } elseif ($currentH2 !== null) {
                $items[$currentH2]['children'][] = $item;
            }
        }

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return ['html' => $output, 'items' => $items];
    }

    private function uniqueId(string $base, array &$used): string
    {
        $id = $base;
        $suffix = 2;

        while (isset($used[$id])) {
            $id = $base.'-'.$suffix++;
        }

        $used[$id] = true;

        return $id;
    }
}
