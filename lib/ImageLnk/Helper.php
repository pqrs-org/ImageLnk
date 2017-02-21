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

    public static function dom($html)
    {
        $dom = HtmlDomParser::str_get_html($html);
        if (!$dom) {
            return false;
        }

        $content_type = $dom->find('meta[http-equiv=content-type]', 0);
        if ($content_type) {
            if (preg_match('/charset=shift_jis/i', $content_type->getAttribute('content'))) {
                $html = @iconv("SHIFT_JIS", "UTF-8//IGNORE", $html);
                return HtmlDomParser::str_get_html($html);
            }
            if (preg_match('/charset=euc-jp/i', $content_type->getAttribute('content'))) {
                $html = @iconv("EUC-JP", "UTF-8//IGNORE", $html);
                return HtmlDomParser::str_get_html($html);
            }
        }

        if ($dom->find('meta[charset=EUC-JP]', 0)) {
            $html = @iconv("EUC-JP", "UTF-8//IGNORE", $html);
            return HtmlDomParser::str_get_html($html);
        }

        return $dom;
    }

    public static function setResponseFromOpenGraph($response, $html)
    {
        $dom = self::dom($html);
        if (!$dom) {
            return;
        }

        $title = $dom->find('meta[property=og:title]', 0);
        if ($title) {
            $response->setTitle($title->content);
        }

        $image = $dom->find('meta[property=og:image:secure_url]', 0);
        if (!$image) {
            $image = $dom->find('meta[property=og:image]', 0);
        }
        if ($image) {
            // blacklist
            if ($image->content != 'http://www.yomiuri.co.jp/img/yol_icon.jpg' &&
                $image->content != 'http://www3.nhk.or.jp/news/img/fb_futa_600px.png' &&
                $image->content != 'http://image.itmedia.co.jp/images/logo/1200x630_500x500_pcuser.gif' &&
                $image->content != 'https://chosei.gnavi.co.jp/img/pc/90x90_line.png' &&
                ! preg_match('#//www.facebook.com/images/fb_icon_#', $image->content) &&
                ! preg_match('#//cdn.qiita.com/assets/#', $image->content) &&
                ! preg_match('#//images\.srad\.jp/topics/#', $image->content) &&
                ! preg_match('#//static\.osdn\.jp/magazine/osdnmag_#', $image->content) &&
                ! preg_match('#//image\.gihyo\.co\.jp/assets/images/ICON/#', $image->content) &&
                ! preg_match('/\/apple-touch-icon@2.png/', $image->content) /* stackoverflow.com */ &&
                true) {
                $response->addImageURL($image->content);
            }
        }

        if (! $response->getTitle()) {
            // fall-back
            $title = $dom->find('title', 0);
            if ($title) {
                $response->setTitle($title->plaintext);
            }
        }
    }
}
