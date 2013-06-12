<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

require_once ImageLnk_Path::combine(dirname(__FILE__), '..', '..', '..', 'vendor', 'tmhOAuth', 'tmhOAuth.php');
require_once ImageLnk_Path::combine(dirname(__FILE__), '..', '..', '..', 'vendor', 'tmhOAuth', 'tmhUtilities.php');

class ImageLnk_Engine_twitter {
  const language = NULL;
  const sitename = 'http://twitter.com/';

  public static function handle($url) {
    if (! preg_match('%^https?://([^/]+)?twitter.com/.*/(status|statuses)/(\d+)%', $url, $matches)) {
      return FALSE;
    }

    $id   = $matches[3];

    // ----------------------------------------
    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $tmhOAuth = new tmhOAuth(array(
                               'consumer_key'    => ImageLnk_Config::v('twitter_consumer_key'),
                               'consumer_secret' => ImageLnk_Config::v('twitter_consumer_secret'),
                               'user_token'      => ImageLnk_Config::v('twitter_user_token'),
                               'user_secret'     => ImageLnk_Config::v('twitter_user_secret'),
                               ));
    $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/show.json'), array(
                                 'id' => $id,
                                 'include_entities' => '1',
                                 ));
    if ($code == 200) {
      $info = json_decode($tmhOAuth->response['response']);

      $response->setTitle('twitter: ' . $info->user->name . ': ' . $info->text);
      foreach ($info->entities->media as $m) {
        $response->addImageURL($m->media_url);
      }
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_twitter');
