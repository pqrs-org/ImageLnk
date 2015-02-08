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

        $title = ImageLnk_Helper::getTitle($html);
        if ($title !== false) {
            $response->setTitle($title);
        }

        foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $img) {
            if (preg_match('/ src="(http:\/\/tn-skr\d+\.smilevideo\.jp\/smile\?i=\d+)" /', $img, $m)) {
                $response->addImageURL($m[1]);
                break;
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_nicovideo');
