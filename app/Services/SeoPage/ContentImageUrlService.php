<?php

namespace App\Services\SeoPage;

class ContentImageUrlService
{
    /** An image file directly inside the seo-pages folder, at the end of any URL path. */
    private const PATH_PATTERN = '~(?:^|/)assets/'.SeoPageImageService::DIRECTORY.'/([A-Za-z0-9_-][A-Za-z0-9._-]*\.(?:jpe?g|png|webp))$~i';

    /**
     * Points every <img src> that refers to a file in `assets/general/images/seo-pages/` at this
     * site's copy of that file, whatever host or path prefix it was saved with (an absolute URL
     * from a local or staging install, `/build-saas-links/assets/...`, `assets/...`). Other image
     * sources and all other markup are left exactly as they are.
     */
    public function rewrite(string $html): string
    {
        if (stripos($html, '<img') === false) {
            return $html;
        }

        return preg_replace_callback(
            '/<img\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*>/i',
            fn (array $tag) => $this->rewriteTag($tag[0]),
            $html
        ) ?? $html;
    }

    private function rewriteTag(string $tag): string
    {
        // Attributes are consumed left to right with their quoted values, so text inside another
        // attribute (e.g. an alt mentioning a URL) is never read as a src.
        return preg_replace_callback(
            '/(\s)([A-Za-z_:][-\w:.]*)(\s*=\s*)("[^"]*"|\'[^\']*\'|[^\s"\'>]+)/',
            function (array $attribute) {
                if (strtolower($attribute[2]) !== 'src') {
                    return $attribute[0];
                }

                $quote = in_array($attribute[4][0], ['"', "'"], true) ? $attribute[4][0] : '"';
                $value = html_entity_decode(trim($attribute[4], '"\''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $path = parse_url($value, PHP_URL_PATH);

                if (! is_string($path) || ! preg_match(self::PATH_PATTERN, $path, $match)) {
                    return $attribute[0];
                }

                $url = e(asset(SeoPageImageService::DIRECTORY.'/'.$match[1]));

                return $attribute[1].$attribute[2].$attribute[3].$quote.$url.$quote;
            },
            $tag
        ) ?? $tag;
    }
}
