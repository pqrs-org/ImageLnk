<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use KubAT\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_youtube
{
    const LANGUAGE = null;
    const SITENAME = 'https://www.youtube.com/';

    public static function handle($url)
    {
        if (!preg_match('%^https://www\.youtube\.com%', $url)) {
            return false;
        }

        // ----------------------------------------
        $urlInfo = parse_url($url);
        if (!isset($urlInfo['query'])) {
            return false;
        }

        $query = [];
        parse_str($urlInfo['query'], $query);

        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        if (preg_match('%<script >var ytplayer = ytplayer \|\| \{\};ytplayer.config = (.+?);ytplayer.load = %s', $html, $matches)) {
            $json = json_decode($matches[1]);
            $playerResponse = json_decode($json->args->player_response);
            $response->setTitle($playerResponse->videoDetails->title);
        } else {
            $response->setTitle(ImageLnk_Helper::getTitle($html));
        }

        $response->addImageURL('https://i.ytimg.com/vi/' . $query['v'] . '/maxresdefault.jpg');

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_youtube');
