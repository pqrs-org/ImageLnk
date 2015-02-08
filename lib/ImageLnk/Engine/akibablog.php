<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_akibablog
{
    const language = 'Japanese';
    const sitename = 'http://blog.livedoor.jp/geek/';

    public static function handle($url)
    {
        if (! preg_match('/^http:\/\/node.*?\.img.*?\.akibablog\.net\/.*\.html$/', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];
        $html = @iconv("SHIFT_JIS", "UTF-8//IGNORE", $html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        if (preg_match('/src="\.\/(.+?)"/', $html, $matches)) {
            $response->addImageURL(dirname($url) . '/' . $matches[1]);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_akibablog');
