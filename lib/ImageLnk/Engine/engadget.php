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

        $meta = $dom->find('meta[name=twitter:image]', 0);
        if ($meta) {
            $response->addImageURL(trim($meta->content));
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_engadget');
