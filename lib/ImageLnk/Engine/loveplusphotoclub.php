<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_loveplusphotoclub
{
    const language = 'Japanese';
    const sitename = 'http://www.loveplusphotoclub.konami.jp/';

    public static function handle($url)
    {
        if (! preg_match('%^http://www\.loveplusphotoclub\.konami\.jp/post/%', $url, $matches)) {
            return false;
        }

        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        $regexp = '%(http://www\.loveplusphotoclub\.konami\.jp/cgm/ecommerce/loveplus/images/large/.+?)"%';
        if (preg_match_all($regexp, $html, $matches)) {
            foreach ($matches[1] as $imgurl) {
                $response->addImageURL($imgurl);
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_loveplusphotoclub');
