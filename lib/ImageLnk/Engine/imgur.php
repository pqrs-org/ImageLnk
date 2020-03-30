<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_imgur
{
    const LANGUAGE = null;
    const SITENAME = 'https://imgur.com/';

    public static function handle($url)
    {
        if (!preg_match('%^https?://imgur.com/gallery/(.+)%', $url, $matches)) {
            return false;
        }

        $id = $matches[1];

        // ----------------------------------------
        $data = ImageLnk_Cache::get('https://api.imgur.com/post/v1/posts/' . $id, [
            'Authorization' => 'Client-ID ' . ImageLnk_Config::v('imgur_client_id'),
        ]);

        $json = json_decode($data['data']);

        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $response->setTitle($json->title);
        $response->addImageURL('https://i.imgur.com/' . $json->cover_id . '.jpg');

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_imgur');
