<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

use KubAT\PhpSimple\HtmlDomParser;

class ImageLnk_Engine_instagram
{
    const LANGUAGE = null;
    const SITENAME = 'https://www.instagram.com/';

    public static function handle($url)
    {
        if (! preg_match('%^https://www\.instagram\.com/p/([^/]+)%', $url, $matches)) {
            return false;
        }

        $shortcode = $matches[1];

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $dom = HtmlDomParser::str_get_html($html);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        foreach ($dom->find('script') as $script) {
            if (preg_match('/^window._sharedData = (.+);/', $script->innertext, $matches)) {
                $sharedData = json_decode($matches[1]);

                $caption = $sharedData->entry_data->PostPage[0]->graphql->shortcode_media->edge_media_to_caption->edges[0]->node->text;
                $username = $sharedData->entry_data->PostPage[0]->graphql->shortcode_media->owner->username;
                $src = $sharedData->entry_data->PostPage[0]->graphql->shortcode_media->display_url;

                $response->setTitle(sprintf('@%s on Instagram: “%s”', $username, $caption));
                $response->addImageURL($src);
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_instagram');
