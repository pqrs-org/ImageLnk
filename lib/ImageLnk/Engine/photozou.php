<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_photozou
{
    const LANGUAGE = 'Japanese';
    const SITENAME = 'http://photozou.jp/';

    public static function handle($url)
    {
        if (!preg_match('/^http:\/\/photozou\.jp\/photo\/[^\/]+?\/(\d+)\/(\d+)/', $url, $matches)) {
            return false;
        }

        $id1 = $matches[1];
        $id2 = $matches[2];

        $url = "http://photozou.jp/photo/photo_only/{$id1}/{$id2}";

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        if (preg_match('/<a href="(.+?)">この写真をダウンロードする<\/a>/', $html, $matches)) {
            $response->addImageURL($matches[1]);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_photozou');
