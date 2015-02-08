<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_natalie
{
    const language = 'Japanese';
    const sitename = 'http://natalie.mu/';

    public static function handle($url)
    {
        if (! preg_match('/^http:\/\/natalie\.mu\/[^\/]+\/gallery\//', $url)) {
            return false;
        }

        // ----------------------------------------
        $data = ImageLnk_Cache::get($url);
        $html = $data['data'];

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $title = ImageLnk_Helper::getTitle($html);
        if ($title !== false) {
            $response->setTitle($title);
        }

        if (preg_match('/<p class="image-full">(.+?)<\/p>/s', $html, $matches)) {
            foreach (ImageLnk_Helper::scanSingleTag('img', $matches[1]) as $img) {
                if (preg_match('/ src="(.+?)"/', $img, $m)) {
                    $response->addImageURL('http://natalie.mu' . $m[1]);
                    break;
                }
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_natalie');
