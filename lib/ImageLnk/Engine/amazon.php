<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_amazon
{
    const language = null;
    const sitename = 'http://www.amazon.com/';

    public static function handle($url)
    {
        if (! preg_match('%^http://www\.amazon\.com/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        if (preg_match('%var colorImages = ({"initial":.+?);%', $html, $m)) {
            $images = json_decode($m[1]);
            foreach ($images->initial as $i) {
                $response->addImageURL($i->hiRes);
            }
        }

        if (count($response->getImageURLs()) == 0) {
            return false;
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_amazon');
