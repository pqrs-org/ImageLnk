<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_nicovideo
{
    const language = 'Japanese';
    const sitename = 'http://www.nicovideo.jp/';

    public static function handle($url)
    {
        if (! preg_match('/^http:\/\/www\.nicovideo\.jp\/watch\//', $url)) {
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
ImageLnk_Engine::push('ImageLnk_Engine_nicovideo');
