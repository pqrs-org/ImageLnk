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

    foreach (ImageLnkHelper::scanSingleTag('img', $html) as $img) {
      if (preg_match('% id="prodImage"%s', $img) ||
          preg_match('%\sid="original-main-image"%s', $img)) {
        if (preg_match('% src="(.+?)"%s', $img, $m)) {
          $response->addImageURL($m[1]);
        }
      }
    }

    if (count($response->getImageURLs()) == 0) {
      return FALSE;
    }

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_amazon');
