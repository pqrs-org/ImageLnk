<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_engadget
{
    const language = null;
    const sitename = 'http://www.engadget.com/galleries/';

    public static function handle($url)
    {
        if (! preg_match('%^http://www\.engadget\.com/gallery/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        if (preg_match('%<!-- M:body-gallery-image -->(.+?)<!-- /M -->%s', $html, $matches)) {
            foreach (ImageLnk_Helper::scanSingleTag('img', $matches[1]) as $img) {
                if (preg_match('/ src="(.+?)"/', $img, $m)) {
                    $response->addImageURL($m[1]);
                    break;
                }
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_engadget');
