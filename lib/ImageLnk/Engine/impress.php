<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use KubAT\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_impress
{
    const LANGUAGE = 'Japanese';
    const SITENAME = 'https://watch.impress.co.jp/';

    public static function handle_common($url)
    {
        if (! preg_match('/^(https?:\/\/([^\/]+\.)?impress\.co\.jp)(\/img\/.+).html/', $url, $matches)) {
            return false;
        }

        $baseurl = $matches[1];
        $id = preg_quote(preg_replace('/\/html\//', '/', $matches[3]), '/');

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        $img = $dom->find('#main-image-wrap img', 0);
        if ($img) {
            $response->addImageURL($img->getAttribute('src'));
        }

        return $response;
    }

    public static function handle_akiba($url)
    {
        if (! preg_match('|^(https?://akiba-pc.watch.impress.co.jp/hotline/.+?/image/)|', $url, $matches)) {
            return false;
        }

        $baseurl = $matches[1];

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $imgtag) {
            if (preg_match('/ src="(.+?)" style="border: 0px;"/', $imgtag, $m)) {
                $response->addImageURL($baseurl . $m[1]);
            }
        }

        return $response;
    }

    public static function handle($url)
    {
        $response = self::handle_common($url);

        if ($response === false) {
            $response = self::handle_akiba($url);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_impress');
