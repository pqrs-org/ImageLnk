<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_pixiv
{
    const LANGUAGE = null;
    const SITENAME = 'https://www.pixiv.net/';

    public static function handle($url)
    {
        $response = self::handle_whitecube($url);
        if ($response) {
            return $response;
        }

        $response = self::handle_old($url);
        if ($response) {
            return $response;
        }

        return false;
    }

    public static function handle_old($url)
    {
        if (!preg_match('/^https?:\/\/(www|touch)\.pixiv\.net\//', $url)) {
            return false;
        }

        $url = preg_replace('/^http:\/\//', 'https://', $url);

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        // --------------------
        // If mode=medium, fetch large image page
        $url_info = parse_url($url);
        if (!isset($url_info['query'])) {
            return false;
        }

        $query = [];
        parse_str($url_info['query'], $query);

        if (isset($query['return_to'])) {
            $url = $url_info['scheme'] . '://' . $url_info['host'] . rawurldecode($query['return_to']);
            return self::handle_old($url);
        }

        if (!isset($query['mode'])) {
            return false;
        }

        // --------------------
        $response = new ImageLnk_Response();
        $response->setReferer($url);
        $response->setTitle(ImageLnk_Helper::getTitle($html));

        switch ($query['mode']) {
            case 'medium':
                $urls = [];
                if (preg_match('/"urls":({.+?})/', $html, $matches)) {
                    $urls = json_decode($matches[1]);
                    $response->addImageURL($urls->original);
                }
                break;

            case 'big':
            case 'manga_big':
                foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $img) {
                    if (preg_match('/src="(.+?)"/', $img, $m)) {
                        $response->addImageURL($m[1]);
                    }
                }
                break;

            case 'manga':
                foreach (self::fetchPages($query['illust_id'])->body as $p) {
                    $response->addImageURL($p->urls->regular);
                }
                break;

            default:
                $response->addImageURL($dom->find('meta[property=og:image]', 0)->content);
                break;
        }

        return $response;
    }

    public static function handle_whitecube($url)
    {
        if (!preg_match('#https?://www.pixiv.net/whitecube/user/(\d+)/illust/(\d+)#', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);
        if (!$dom) {
            return false;
        }

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $title = $dom->find('meta[property=og:title]', 0);
        if ($title) {
            $response->setTitle($title->content);
        }

        $image = $dom->find('meta[property=og:image]', 0);
        if ($image) {
            if (preg_match('#(/img-master/.+)$#', $image->content, $matches)) {
                $response->addImageURL('https://i.pximg.net' . $matches[1]);
            }
        }

        return $response;
    }

    public static function fetchPages($id)
    {
        if (!$id) {
            return [];
        }

        $url = 'https://www.pixiv.net/ajax/illust/' . $id . '/pages';
        $data = ImageLnk_Cache::get($url);
        $pages = json_decode($data['data']);
        return $pages;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_pixiv');
