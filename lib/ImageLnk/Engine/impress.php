<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_impress
{
    const language = 'Japanese';
    const sitename = 'http://watch.impress.co.jp/';

    public static function handle_common($url)
    {
        if (! preg_match('/^(http:\/\/([^\/]+\.)?impress\.co\.jp)(\/img\/.+).html/', $url, $matches)) {
            return false;
        }

        $baseurl = $matches[1];
        $id = preg_quote(preg_replace('/\/html\//', '/', $matches[3]), '/');

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];
        $html = @iconv("SHIFT_JIS", "UTF-8//IGNORE", $html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $imgtag) {
            if (preg_match("/ src=\"({$id})\"/", $imgtag, $matches)) {
                $response->addImageURL($baseurl . $matches[1]);
            }
        }

        return $response;
    }

    public static function handle_akiba($url)
    {
        if (! preg_match('|^(http://akiba-pc.watch.impress.co.jp/hotline/.+?/image/)|', $url, $matches)) {
            return false;
        }

        $baseurl = $matches[1];

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];
        $html = @iconv("SHIFT_JIS", "UTF-8//IGNORE", $html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        if (preg_match('|<!--image-->(.+?)<!--/image-->|', $html, $matches)) {
            foreach (ImageLnk_Helper::scanSingleTag('img', $matches[1]) as $imgtag) {
                if (preg_match('/ src="(.+?)"/', $imgtag, $m)) {
                    $response->addImageURL($baseurl . $m[1]);
                }
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
