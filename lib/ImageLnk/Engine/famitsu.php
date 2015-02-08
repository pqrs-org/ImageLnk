<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_famitsu
{
    const language = 'Japanese';
    const sitename = 'http://www.famitsu.com/';

    public static function handle($url)
    {
        if (! preg_match('/^(http:\/\/www\.famitsu\.com\/news\/\d+\/images\/\d+\/)(.+\.)html$/', $url, $matches)) {
            return false;
        }

        $baseurl = $matches[1];
        $id = preg_quote($matches[2], '/');

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];
        $html = @iconv("SHIFT_JIS", "UTF-8//IGNORE", $html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        if (preg_match("/ src=\"($id.+?)\"/", $html, $matches)) {
            $response->addImageURL($baseurl . $matches[1]);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_famitsu');
