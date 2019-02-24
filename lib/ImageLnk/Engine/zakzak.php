<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_zakzak
{
    const LANGUAGE = 'Japanese';
    const SITENAME = 'https://www.zakzak.co.jp/';

    public static function handle($url)
    {
        if (!preg_match('/^https?:\/\/www\.zakzak\.co\.jp\/.+\/photos\/.+\.htm$/', $url, $matches)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];
        $html = @iconv("SHIFT_JIS", "UTF-8//IGNORE", $html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        if (preg_match('/<table class="photo">(.+?)<\/table>/s', $html, $matches)) {
            foreach (ImageLnk_Helper::scanSingleTag('img', $matches[1]) as $img) {
                if (preg_match('/ src="[\.\/]+(.+?)"/s', $img, $m)) {
                    $response->addImageURL('https://www.zakzak.co.jp/' . $m[1]);
                }
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_zakzak');
