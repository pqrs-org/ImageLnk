<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_picasa
{
    const language = null;
    const sitename = 'http://picasa.google.com/';

    public static function handle($url)
    {
        if (! preg_match('/^https?:\/\/picasaweb\.google\.com\/.+#(\d+)$/', $url, $matches)) {
            return false;
        }

        $id = $matches[1];

        // Use http because we cannot connect to https using HTTP_Request2.
        $url = preg_replace('/^https/', 'http', $url);

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        if (preg_match(sprintf('/"gphoto\$id":"%s".+?"url":"(.+?)"/s', $id), $html, $matches)) {
            $response->addImageURL($matches[1]);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_picasa');
