<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_amazon
{
    const language = null;
    const sitename = 'https://www.amazon.com/';

    public static function handle($url)
    {
        if (! preg_match('%^https://www\.amazon\.com/%', $url) &&
            ! preg_match('%^https://www\.amazon\.co\.jp/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        $img = $dom->find('img[data-old-hires]', 0);
        if ($img) {
            $data_old_hires = $img->getAttribute('data-old-hires');
            if ($data_old_hires) {
                $response->addImageURL($data_old_hires);
            } else {
                $data_a_dynamic_image = $img->getAttribute('data-a-dynamic-image');
                if ($data_a_dynamic_image) {
                    $urls = array_keys(json_decode(htmlspecialchars_decode($data_a_dynamic_image), true));
                    if (count($urls) > 0) {
                        $response->addImageURL($urls[0]);
                    }
                }
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_amazon');
