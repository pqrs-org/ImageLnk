<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_cnet_jp
{
    const LANGUAGE = 'Japanese';
    const SITENAME = 'https://japan.cnet.com/';

    public static function handle($url)
    {
        if (! preg_match('%^(https://japan\.cnet\.com)/%', $url, $matches)) {
            return false;
        }

        $base = $matches[1];

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        $mediaImg = $dom->find('#mediaImg', 0);
        if ($mediaImg) {
            $response->addImageURL($base . $mediaImg->src);
            $response->setTitle($response->getTitle() . ': ' . $mediaImg->alt);
            return $response;
        }

        $l_img_main = $dom->find('#l_img_main', 0);
        if ($l_img_main) {
            $response->addImageURL($base . $l_img_main->find('img', 0)->src);
            $response->setTitle($response->getTitle() . ': ' . $l_img_main->find('div.caption', 0)->plaintext);
            return $response;
        }

        return null;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_cnet_jp');
