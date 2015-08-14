<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_dengeki
{
    const language = 'Japanese';
    const sitename = 'http://dengekionline.com/';

    public static function handle($url)
    {
        if (! preg_match('#(http://dengekionline.com)(/.+?/)img.html#', $url, $matches)) {
            return false;
        }

        $base = $matches[1];
        $path = $matches[2];

        $id = preg_quote($path, '/');

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        if (preg_match("/src=\"({$id}.*?)\"/", $html, $matches)) {
            $response->addImageURL($base . $matches[1]);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_dengeki');
