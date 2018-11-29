<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_uncyclopedia_ja
{
    const language = 'Japanese';
    const sitename = 'http://ja.uncyclopedia.info/';

    public static function handle($url)
    {
        if (! preg_match('|^https?://ja.uncyclopedia.info/wiki/.+|', $url, $matches)
            && ! preg_match('|^https?://ansaikuropedia.org/wiki/.+|', $url, $matches)
        ) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        if (preg_match('/ class="fullImageLink".+?href="(.+?)"/', $html, $matches)) {
            $response->addImageURL('https:' . $matches[1]);

            if (preg_match('/id="fileinfotpl_desc".+?<td>(.*?)<\/td>/s', $html, $matches)) {
                $response->setTitle(preg_replace('/\s+/', ' ', strip_tags(trim($matches[1]))));
            }
        }

        if (! $response->getTitle()) {
            // fall-back
            $title = ImageLnk_Helper::getTitle($html);
            if ($title) {
                $response->setTitle($title);
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_uncyclopedia_ja');
