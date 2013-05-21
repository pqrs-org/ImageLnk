<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_twitter {
  const language = NULL;
  const sitename = 'http://twitter.com/';

  public static function handle($url) {
    if (! preg_match('%^https?://([^/]+)?twitter.com/.*/(status|statuses)/(\d+)%', $url, $matches)) {
      return FALSE;
    }

    $id   = $matches[3];

    // ----------------------------------------
    $url = "http://api.twitter.com/1/statuses/show.json?id={$id}&include_entities=true&contributor_details=true";

    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];
    $info = json_decode($data['data']);

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $response->setTitle('twitter: ' . $info->user->name . ': ' . $info->text);
    foreach ($info->entities->media as $m) {
      $response->addImageURL($m->media_url);
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_twitter');
