<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_twitpic
{
    const language = null;
    const sitename = 'http://twitpic.com/';

    public static function handle($url)
    {
        if (! preg_match('/^http:\/\/twitpic\.com\/+([^\/]+)/', $url, $matches)) {
            return false;
        }

        $id = $matches[1];

        // ----------------------------------------
        if (! preg_match('/\/full$/', $url)) {
            $url = "http://twitpic.com/$id/full";
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        if (preg_match('%<div id="media-full">(.+?)</div>%s', $html, $matches)) {
            foreach (ImageLnk_Helper::scanSingleTag('img', $matches[1]) as $img) {
                if (preg_match('/ src="(.+?)"/', $img, $m)) {
                    $response->addImageURL($m[1]);
                }
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_twitpic');
