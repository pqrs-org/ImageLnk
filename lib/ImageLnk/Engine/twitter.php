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

        $tmhOAuth = new tmhOAuth(
            array(
                'consumer_key' => ImageLnk_Config::v('twitter_consumer_key'),
                'consumer_secret' => ImageLnk_Config::v('twitter_consumer_secret'),
                'user_token' => ImageLnk_Config::v('twitter_user_token'),
                'user_secret' => ImageLnk_Config::v('twitter_user_secret'),
            )
        );
        $code = $tmhOAuth->request(
            'GET', $tmhOAuth->url('1.1/statuses/show.json'), array(
                'id' => $id,
                'include_entities' => '1',
                'tweet_mode' => 'extended',
            )
        );

        $cacheFilePath = ImageLnk_Cache::getCacheFilePathFromURL($url);
        ImageLnk_Cache::writeToCacheFile($cacheFilePath, $tmhOAuth->response['response']);

        if ($code == 200) {
            $info = json_decode($tmhOAuth->response['response']);

            $response->setTitle('twitter: ' . $info->user->name . ': ' . $info->full_text);
            if (isset($info->extended_entities->media)) {
                foreach ($info->extended_entities->media as $m) {
                    $response->addImageURL($m->media_url . ':large');
                }
            }
        }

        return $response;
    }
}
ImageLnk_Engine::push('ImageLnk_Engine_twitter');
