<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_cookpad
{
    const LANGUAGE = 'Japanese';
    const SITENAME = 'https://cookpad.com/';

    public static function handle($url)
    {
        if (!preg_match('%^https?://cookpad\.com/recipe/\d+%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        if (preg_match("%<div class='clearfix' id='main-photo'>(.+?)</div>%s", $html, $matches)) {
            foreach (ImageLnk_Helper::scanSingleTag('img', $matches[1]) as $img) {
                if (preg_match('/ src="(.+?)"/s', $img, $m)) {
                    $response->addImageURL($m[1]);
                }
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_cookpad');
