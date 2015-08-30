<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_engadget
{
    const language = null;
    const sitename = 'http://www.engadget.com/galleries/';

    public static function handle($url)
    {
        if (! preg_match('%^http://www\.engadget\.com/gallery/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        $gallery_title = $dom->find('h1.gallery-title', 0);
        if ($gallery_title) {
            $response->setTitle($gallery_title->plaintext);
        }

        $element = $dom->find('.knot-slideshow-data > li > a', 0);
        if ($element) {
            $response->addImageURL(trim($element->getAttribute('href')));
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_engadget');
