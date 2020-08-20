<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_twitter
{
    const LANGUAGE = null;
    const SITENAME = 'https://twitter.com/';

    public static function handle($url)
    {
        if (!preg_match('%^https?://([^/]+)?twitter.com/.*/(status|statuses)/(\d+)%', $url, $matches)) {
            return false;
        }

        $id = $matches[3];

        // ----------------------------------------
        $response = new ImageLnk_Response();
        $response->setReferer($url);

        $client = new GuzzleHttp\Client();
        $apiResponse = $client->request(
            'GET',
            'https://api.twitter.com/2/tweets/' . $id,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' .ImageLnk_Config::v('twitter_bearer'),
                ],
                'query' => [
                    'expansions' => 'author_id,attachments.media_keys',
                    'media.fields' => 'url',
                ],
            ]
        );

        $info = json_decode($apiResponse->getBody());

        $cacheFilePath = ImageLnk_Cache::getCacheFilePathFromURL('internal:twitter:' . $id);
        ImageLnk_Cache::writeToCacheFile($cacheFilePath, json_encode($info));

        if ($info !== false) {
            $user = null;
            foreach ($info->includes->users as $u) {
                if ($info->data->author_id === $u->id) {
                    $user = $u;
                    break;
                }
            }

            if ($user !== null) {
                $response->setTitle('twitter: ' . $user->name . ': ' . $info->data->text);
                if (isset($info->includes->media)) {
                    foreach ($info->includes->media as $m) {
                        $response->addImageURL($m->url . ':large');
                    }
                }
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_twitter');
