<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_yaplog
{
    const language = 'Japanese';
    const sitename = 'http://www.yaplog.jp/';

    public static function handle($url)
    {
        if (! preg_match('/^http:\/\/yaplog\.jp\/.+\/image\//', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];
        $html = @iconv("SHIFT_JIS", "UTF-8//IGNORE", $html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        if (preg_match('/<h1 class="imgMain">.*?<img src="(.+?)"/s', $html, $matches)) {
            $response->addImageURL($matches[1]);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_yaplog');
