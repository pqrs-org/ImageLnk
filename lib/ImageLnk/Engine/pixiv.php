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
            if (preg_match('/<div class="works_display">(.*?)<\/div>/s', $html, $matches)) {
                if (preg_match('/ href="(member_illust.php\?mode=manga&.*?)"/', $matches[1], $m)) {
                    $newurl = preg_replace('/mode=manga/', 'mode=manga_big', $m[1]);
                    $newurl = 'http://www.pixiv.net/' . html_entity_decode($newurl, ENT_QUOTES, 'UTF-8') . '&page=0';
                    return self::handle($newurl);
                }
            }
        }

        // --------------------
        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);
        $response->setTitle(ImageLnk_Helper::getTitle($html));

        switch ($query['mode']) {
        case 'medium':
            $response->addImageURL($dom->find('img.original-image', 0)->getAttribute('data-src'));
            break;

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
