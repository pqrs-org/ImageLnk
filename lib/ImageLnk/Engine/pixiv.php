<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_pixiv
{
    const LANGUAGE = null;
    const SITENAME = 'https://www.pixiv.net/';

    public static function handle($url)
    {
        if (!preg_match('/^https?:\/\/(www|touch)\.pixiv\.net\//', $url)) {
            return false;
        }

        $url = preg_replace('/^http:\/\//', 'https://', $url);

        // --------------------
        // If mode=medium, fetch large image page
        $url_info = parse_url($url);
        if (!isset($url_info['query'])) {
            return false;
        }

        $query = [];
        parse_str($url_info['query'], $query);

        // Handle return_to

        if (isset($query['return_to'])) {
            $url = $url_info['scheme'] . '://' . $url_info['host'] . rawurldecode($query['return_to']);
            return self::handle($url);
        }

        if (!isset($query['mode'])) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        if ($data['data'] === '') {
            return false;
        }
        $json = json_decode($data['data']);

        // --------------------
        $response = new ImageLnk_Response();
        $response->setReferer($url);
        $response->setTitle($json->illust->title);

        if (isset($json->illust->meta_pages) &&
            count($json->illust->meta_pages) > 0) {
            foreach ($json->illust->meta_pages as $page) {
                $response->addImageURL($page->image_urls->large);
            }
        } else {
            $response->addImageURL($json->illust->image_urls->large);
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
