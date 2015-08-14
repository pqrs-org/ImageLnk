<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_amazon
{
    const language = null;
    const sitename = 'http://www.amazon.com/';

    public static function handle($url)
    {
        if (! preg_match('%^http://www\.amazon\.com/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        $response->addImageURL($dom->find('img[data-old-hires]', 0)->getAttribute('data-old-hires'));

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_amazon');
