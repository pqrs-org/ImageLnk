<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_wikipedia
{
    const language = null;
    const sitename = 'http://www.wikipedia.org/';

    public static function handle($url)
    {
        if (! preg_match('/^http:\/\/[^\/]+\.wikipedia\.org\/wiki\/.+/', $url, $matches)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        if (preg_match('/ class="fullImageLink".+?href="(.+?)"/', $html, $matches)) {
            $url = $matches[1];
            if (preg_match('%^//%', $url)) {
                $url = 'http:' . $url;
            }
            $response->addImageURL($url);

            if (preg_match('/id="fileinfotpl_desc".+?<td>(.*?)<\/td>/s', $html, $matches)) {
                $response->setTitle(preg_replace('/\s+/', ' ', strip_tags(trim($matches[1]))));
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_wikipedia');
