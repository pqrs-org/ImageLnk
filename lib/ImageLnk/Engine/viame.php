<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_viame
{
    const language = null;
    const sitename = 'http://via.me/';

    public static function handle($url)
    {
        if (! preg_match('%^http://via\.me/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        ImageLnk_Helper::setResponseFromOpenGraph($response, $html);
        $response->setTitle(ImageLnk_Helper::collapseWhiteSpaces(ImageLnk_Helper::getTitle($html)));

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_viame');
