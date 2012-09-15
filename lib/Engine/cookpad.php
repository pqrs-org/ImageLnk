<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_cookpad {
  const language = 'Japanese';
  const sitename = 'http://cookpad.com/';

  public static function handle($url) {
    if (! preg_match('%^http://cookpad\.com/recipe/\d+%', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    $response->setTitle(ImageLnkHelper::getTitle($html));

    if (preg_match("%<div class='clearfix' id='main-photo'>(.+?)</div>%s", $html, $matches)) {
      foreach (ImageLnkHelper::scanSingleTag('img', $matches[1]) as $img) {
        if (preg_match('/ src="(.+?)"/s', $img, $m)) {
          $response->addImageURL($m[1]);
        }
      }
    }

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_cookpad');
