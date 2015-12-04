<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use Sunra\PhpSimple\HtmlDomParser;

class ImageLnk_Helper
{
    public static function scanSingleTag($name, $html, $regexpoption = 's')
    {
        if (preg_match_all("/<{$name} .+?>/{$regexpoption}", $html, $matches) === false) {
            return array();
        }
        return $matches[0];
    }

    public static function getTitle($html)
    {
        if (preg_match('/<title( .*?)?>(.*?)<\/title>/is', $html, $matches)) {
            return $matches[2];
        } else {
            return false;
        }
    }

    public static function collapseWhiteSpaces($text)
    {
        return preg_replace("/[\t\r\n ]+/", ' ', $text);
    }

    public static function setResponseFromOpenGraph($response, $html)
    {
        $dom = HtmlDomParser::str_get_html($html);

        $title = $dom->find('meta[property=og:title]', 0);
        if ($title) {
            $response->setTitle($title->content);
        }

        $image = $dom->find('meta[property=og:image]', 0);
        if ($image) {
            // blacklist
            if ($image->content != 'http://www.yomiuri.co.jp/img/yol_icon.jpg' &&
                true) {
                $response->addImageURL($image->content);
            }
        }

        if (! $response->getTitle()) {
            // fall-back
            $title = $dom->find('title', 0);
            if ($title) {
                $response->setTitle($title->content);
            }
        }
    }
}
