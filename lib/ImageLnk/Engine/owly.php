<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_owly {
  const language = NULL;
  const sitename = 'http://ow.ly/';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/ow\.ly\/i\/+([^\/]+)/', $url, $matches)) {
      return FALSE;
    }

    $id = preg_quote($matches[1], '/');

    // ----------------------------------------
    if (! preg_match('/\/original$/', $url)) {
      $url = "http://ow.ly/i/{$id}/original";
    }

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $response->setTitle(ImageLnk_Helper::getTitle($html));

    foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $img) {
      if (preg_match(sprintf('/src="(http:\/\/static\.ow\.ly\/photos\/original\/%s.*?)"/s', $id), $img, $m)) {
        $response->addImageURL($m[1]);
        if (preg_match('/alt="(.+?)"/s', $img, $m)) {
          $response->setTitle($response->getTitle() . ': ' . $m[1]);
        }
        break;
      }
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_owly');
