<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_amazon_jp
{
    const language = 'Japanese';
    const sitename = 'http://www.amazon.co.jp/';

    public static function handle($url)
    {
        if (! preg_match('%^http://www\.amazon\.co\.jp/%', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];
        $html = @iconv("SJIS", "UTF-8//IGNORE", $html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $img) {
            if (preg_match('% id="prodImage"%s', $img)
                || preg_match('%\sid="original-main-image"%s', $img)
            ) {
                if (preg_match('% src="(.+?)"%s', $img, $m)) {
                    $response->addImageURL($m[1]);
                }
            }
        }

        if (count($response->getImageURLs()) == 0) {
            return false;
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_amazon_jp');
