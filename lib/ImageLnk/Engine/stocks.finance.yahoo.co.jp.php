<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_stocks_finance_yahoo_co_jp
{
    const LANGUAGE = 'Japanese';
    const SITENAME = 'https://stocks.finance.yahoo.co.jp/';

    public static function handle($url)
    {
        if (! preg_match('#^https://stocks.finance.yahoo.co.jp/#', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));

        $img = $dom->find('img[alt="チャート画像"]', 0);
        if ($img) {
            $response->addImageURL($img->getAttribute('src'));
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_stocks_finance_yahoo_co_jp');
