<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_amazon {
  const language = NULL;
  const sitename = 'http://www.amazon.com/';

  public static function handle($url) {
    if (! preg_match('%^http://www\.amazon\.com/%', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    $response->setTitle(ImageLnkHelper::getTitle($html));

    if (preg_match('%var colorImages = ({"initial":.+?);%', $html, $m)) {
      $images = json_decode($m[1]);
      foreach ($images->initial as $i) {
        $response->addImageURL($i->hiRes);
      }
    }

    if (count($response->getImageURLs()) == 0) {
      return FALSE;
    }

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_amazon');
