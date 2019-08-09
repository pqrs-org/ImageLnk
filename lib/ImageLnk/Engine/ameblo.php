<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use KubAT\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_ameblo
{
    const LANGUAGE = 'Japanese';
    const SITENAME = 'https://ameblo.jp/';

    public static function handle($url)
    {
        if (! preg_match('/^https?:\/\/([^\/]*\.)?ameblo\.jp\/.+\/image-/', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        foreach ($dom->find('script') as $script) {
            if (preg_match('/Amb\.Ameblo\.image = new Amb\.Ameblo\.Image\((.+)\);/s', $script->innertext, $matches)) {
                $json = json_decode($matches[1]);
                foreach ($json->imgData->current->imgList as $imgList) {
                    $imgUrl = 'https://stat.ameba.jp' . $imgList->imgUrl;
                    $response->addImageURL($imgUrl);
                }
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_ameblo');
