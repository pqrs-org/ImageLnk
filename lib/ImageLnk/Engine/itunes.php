<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_itunes
{
    const language = null;
    const sitename = 'http://itunes.apple.com/';

    public static function handle($url)
    {
        if (! preg_match('%^https?://itunes\.apple\.com/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        ImageLnk_Helper::setResponseFromOpenGraph($response, $html);

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_itunes');
