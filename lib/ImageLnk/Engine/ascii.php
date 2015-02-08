<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_ascii
{
    const language = 'Japanese';
    const sitename = 'http://ascii.jp/';

    public static function handle_common($url)
    {
        if (! preg_match('/^http:\/\/ascii\.jp(\/elem\/.*?\/)img.*\.html$/', $url, $matches)) {
            return false;
        }

        $id = preg_quote($matches[1], '/');

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle(ImageLnk_Helper::getTitle($html));
        if (preg_match('/<ul id="rellist" >.*?<a .*?>(.+?)<\/a>/s', $html, $matches)) {
            $response->setTitle($matches[1]);
        }

        if (preg_match("/src=\"({$id}.*?)\"/", $html, $matches)) {
            $response->addImageURL('http://ascii.jp' . $matches[1]);
        }

        return $response;
    }

    public static function handle_weekly($url)
    {
        if (! preg_match('%http://weekly\.ascii\.jp/elem/%', $url, $matches)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        if (! preg_match('%<body id="imgExp">%s', $html)) {
            return false;
        }

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        if (preg_match('%<h4>(.*?)</h4>%s', $html, $matches)) {
            $response->setTitle(strip_tags(html_entity_decode($matches[1])));
        }

        foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $img) {
            if (preg_match('% src="(/elem/.+?)"%s', $img, $m)) {
                $response->addImageURL('http://weekly.ascii.jp' . $m[1]);
            }
        }

        return $response;
    }

    public static function handle($url)
    {
        $response = self::handle_common($url);

        if ($response === false) {
            $response = self::handle_weekly($url);
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_ascii');
