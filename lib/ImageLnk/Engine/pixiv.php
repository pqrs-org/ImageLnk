<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_pixiv
{
    const language = null;
    const sitename = 'http://www.pixiv.net/';

    public static function handle($url)
    {
        if (! preg_match('/^http:\/\/(www|touch)\.pixiv\.net\/member_illust\.php/', $url)) {
            return false;
        }

        $url = preg_replace('/^http:\/\/touch\.pixiv\.net/', 'http://www.pixiv.net', $url);

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        // --------------------
        // If mode=medium, fetch large image page
        $url_info = parse_url($url);
        if (! isset($url_info['query'])) {
            return false;
        }

        $query = array();
        parse_str($url_info['query'], $query);
        if (! isset($query['mode'])) {
            return false;
        }

        if ($query['mode'] == 'medium') {
            foreach ($dom->find('div.works_display a') as $e) {
                if (preg_match('/mode=big/', $e->href)) {
                    $newurl = 'http://www.pixiv.net/' . $e->href;
                    return self::handle($newurl);
                }
                if (preg_match('/mode=manga/', $e->href)) {
                    $newurl = preg_replace('/mode=manga/', 'mode=manga_big', $e->href);
                    $newurl = 'http://www.pixiv.net/' . $newurl . '&page=0';
                    return self::handle($newurl);
                }
            }
        }

        // --------------------
        $response = new ImageLnk_Response();
        $response->setReferer($url);
        $response->setTitle(ImageLnk_Helper::getTitle($html));

        switch ($query['mode']) {
        case 'medium':
            $response->addImageURL($dom->find('img.original-image', 0)->getAttribute('data-src'));
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
            foreach ($dom->find('img[data-filter=manga-image]') as $e) {
                $response->addImageURL($e->getAttribute('data-src'));
            }
            break;

        default:
            $response->addImageURL($dom->find('meta[property=og:image]', 0)->content);
            break;
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_pixiv');
