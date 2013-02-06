<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_twitpic {
  const language = NULL;
  const sitename = 'http://twitpic.com/';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/twitpic\.com\/+([^\/]+)/', $url, $matches)) {
      return FALSE;
    }

    $id = $matches[1];

    // ----------------------------------------
    if (! preg_match('/\/full$/', $url)) {
      $url = "http://twitpic.com/$id/full";
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    $response->setTitle(ImageLnkHelper::getTitle($html));
    if (preg_match('%<div id="media-full">(.+?)</div>%s', $html, $matches)) {
      foreach (ImageLnkHelper::scanSingleTag('img', $matches[1]) as $img) {
        if (preg_match('/ src="(.+?)"/', $img, $m)) {
          $response->addImageURL($m[1]);
        }
      }
    }

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_twitpic');
