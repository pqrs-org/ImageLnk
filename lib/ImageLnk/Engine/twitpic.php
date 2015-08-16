<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_twitpic
{
    const language = null;
    const sitename = 'http://twitpic.com/';

    public static function handle($url)
    {
        if (! preg_match('/^http:\/\/twitpic\.com\/+([^\/]+)/', $url, $matches)) {
            return false;
        }

        $id = $matches[1];

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        $img = $dom->find('#media > img', 0);
        if ($img) {
            $response->addImageURL($img->src);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_twitpic');
