<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_news_livedoor_com
{
    const language = 'Japanese';
    const sitename = 'http://news.livedoor.com/';

    public static function handle($url)
    {
        if (! preg_match('%http://news\.livedoor\.com/article/image_detail/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];
        $html = @iconv("EUC-JP", "UTF-8//IGNORE", $html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        ImageLnk_Helper::setResponseFromOpenGraph($response, $html);

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_news_livedoor_com');
