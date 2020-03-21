<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use KubAT\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_imgur
{
    const LANGUAGE = null;
    const SITENAME = 'https://imgur.com/';

    public static function handle($url)
    {
        if (!preg_match('%^https?://imgur.com/gallery/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $title = $dom->find('meta[property=og:title]', 0)->content;
        $response->setTitle($title);

        $link = $dom->find('link[rel=image_src]', 0);
        if ($link) {
            $response->addImageURL($link->getAttribute('href'));
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_imgur');
