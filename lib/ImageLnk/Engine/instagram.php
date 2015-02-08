<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_instagram
{
    const language = null;
    const sitename = 'http://instagram.com/';

    public static function handle($url)
    {
        if (! preg_match('%^http://instagram\.com/p/%', $url)
            && ! preg_match('%^http://instagr\.am/p/%', $url)
        ) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        ImageLnk_Helper::setResponseFromOpenGraph($response, $html);
        if (preg_match('/<span class="caption-text">(.*?)<\/span>/s', $html, $matches)) {
            $response->setTitle(trim($matches[1]));
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_instagram');
