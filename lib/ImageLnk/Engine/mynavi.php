<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_mynavi
{
    const language = 'Japanese';
    const sitename = 'http://news.mynavi.jp/';

    public static function handle($url)
    {
        if (! preg_match('%http://news\.mynavi\.jp/photo/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        $img = $dom->find('#photo-link > img', 0);
        if ($img) {
            $response->addImageURL($img->src);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_mynavi');
