<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_picasa
{
    const language = null;
    const sitename = 'https://plus.google.com/';

    public static function handle($url)
    {
        if (! preg_match('#^https://plus\.google\.com/#', $url, $matches)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);
        $response->setTitle($dom->find('meta[property=og:title]', 0)->content);
        $response->addImageURL($dom->find('meta[property=og:image]', 0)->content);

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_picasa');