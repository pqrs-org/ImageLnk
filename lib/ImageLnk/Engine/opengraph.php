<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_opengraph
{
    const language = null;
    const sitename = 'any site which has og:image';

    public static function handle($url)
    {
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        ImageLnk_Helper::setResponseFromOpenGraph($response, $html);

        if (empty($response->getImageURLs())) {
            return false;
        }

        return $response;
    }
}
// Do not call ImageLnk_Engine::push here for opengraph
