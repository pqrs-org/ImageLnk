<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use KubAT\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_ameblo
{
    const LANGUAGE = 'Japanese';
    const SITENAME = 'https://ameblo.jp/';

    public static function handle($url)
    {
        if (!preg_match('/^https?:\/\/([^\/]*\.)?ameblo\.jp\/.+\/image-(.+?)-(.+?)\.html/', $url, $matches)) {
            return false;
        }

        $entryId = $matches[2];
        $imageId = $matches[3];

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        foreach ($dom->find('img[data-entry-id="' . $entryId . '"][data-image-id="' . $imageId . '"]') as $img) {
            $response->addImageURL($img->getAttribute('src'));
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_ameblo');
