<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_engadget_jp
{
    const language = 'Japanese';
    const sitename = 'http://japanese.engadget.com/gallery';

    public static function handle($url)
    {
        if (! preg_match('/^http:\/\/japanese\.engadget\.com\/gallery\//', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        try {
            $response->addImageURL($dom->find('meta[name=twitter:image0:src]', 0)->content);
            $response->addImageURL($dom->find('meta[name=twitter:image1:src]', 0)->content);
            $response->addImageURL($dom->find('meta[name=twitter:image2:src]', 0)->content);
            $response->addImageURL($dom->find('meta[name=twitter:image3:src]', 0)->content);
        } catch (Exception $ex) {
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_engadget_jp');
