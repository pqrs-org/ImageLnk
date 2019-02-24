<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_hatena
{
    const LANGUAGE = 'Japanese';
    const SITENAME = 'http://f.hatena.ne.jp/';

    public static function handle($url)
    {
        if (!preg_match('/^http:\/\/f\.hatena\.ne\.jp\//', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $img) {
            if (preg_match('/ class="foto"/', $img)) {
                if (preg_match('/src="(.+?)"/', $img, $m)) {
                    $response->addImageURL($m[1]);
                }
                if (preg_match('/title="(.+?)"/', $img, $m)) {
                    $response->setTitle($m[1]);
                }
                break;
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_hatena');
