<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_sankakucomplex
{
    const language = 'English';
    const sitename = 'https://chan.sankakucomplex.com/';

    public static function handle($url)
    {
        if (! preg_match('%^https://chan\.sankakucomplex\.com/post/show/(\d+)/%', $url, $matches)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();

        $title = $dom->find('meta[property=og:title]', 0);
        if ($title) {
            $response->setTitle($title->content);
        }

        $img = $dom->find('#image', 0);
        if ($img) {
            $response->addImageURL('https:' . $img->getAttribute('src'));
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_sankakucomplex');
