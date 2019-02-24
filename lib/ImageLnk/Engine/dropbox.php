<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_dropbox
{
    const LANGUAGE = null;
    const SITENAME = 'https://www.dropbox.com/';

    public static function handle($url)
    {
        if (! preg_match('%^https://www\.dropbox\.com%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        $img = $dom->find('div.prerender-preview__wrapper img', 0);
        if ($img) {
            $srcset = $img->getAttribute('srcset');
            $srcset = explode(',', $srcset);
            $image = $srcset[count($srcset) - 1];
            $image = trim($image);
            $image = explode(' ', $image);
            $response->addImageURL($image[0]);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_dropbox');
