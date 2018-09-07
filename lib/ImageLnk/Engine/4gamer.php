<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_4gamer
{
    const language = 'Japanese';
    const sitename = 'http://www.4gamer.net/';

    public static function handle($url)
    {
        if (!preg_match('/^https?:\/\/www\.4gamer\.net(\/.+)\/screenshot\.html(\?num=(\d+))?$/', $url, $matches)) {
            return false;
        }

        $id = preg_quote($matches[1], '/');
        $number = '001';
        if (isset($matches[3])) {
            $number = $matches[3];
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];
        $html = @iconv("EUC-JP", "UTF-8//IGNORE", $html);

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        $pattern = 'li[id=SSTHUMB_' . $number . '] a';
        foreach ($dom->find($pattern) as $e) {
            $response->addImageURL('https://www.4gamer.net' . $e->getAttribute('href'));
            break;
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_4gamer');
