<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_wikipedia
{
    const language = null;
    const sitename = 'https://www.wikipedia.org/';

    public static function handle($url)
    {
        if (! preg_match('/^https:\/\/[^\/]+\.wikipedia\.org\/wiki\/.+/', $url, $matches)) {
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
                $url = 'https:' . $url;
            }
            $response->addImageURL($url);

            if (preg_match('#<td class="description">(.*?)</td>#s', $html, $matches)) {
                $response->setTitle(preg_replace('/\s+/', ' ', strip_tags(trim($matches[1]))));
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_wikipedia');
