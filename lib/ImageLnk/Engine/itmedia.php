<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_itmedia
{
    const LANGUAGE = 'Japanese';
    const SITENAME = 'https://www.itmedia.co.jp/';

    public static function handle($url)
    {
        if (!preg_match('/^https?:\/\/image\.itmedia\.co\.jp\/l\/im(\/.+)$/', $url, $matches)) {
            return false;
        }
        $path = $matches[1];

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $title = $dom->find('meta[property=og:title]', 0)->content;
        $response->setTitle(ImageLnk_Encode::sjis_to_utf8($title));

        $response->addImageURL('https://image.itmedia.co.jp' . $path);
        $response->setBackLink($dom->find('meta[name=pcvurl]', 0)->content);

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_itmedia');
