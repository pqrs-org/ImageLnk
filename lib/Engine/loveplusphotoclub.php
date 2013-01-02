<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_loveplusphotoclub {
  const language = 'Japanese';
  const sitename = 'http://www.loveplusphotoclub.konami.jp/';

  public static function handle($url) {
    if (! preg_match('%^http://www\.loveplusphotoclub\.konami\.jp/post/%', $url, $matches)) {
      return FALSE;
    }

    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    $response->setTitle(ImageLnkHelper::getTitle($html));
    if (preg_match('%(http://www\.loveplusphotoclub\.konami\.jp/cgm/ecommerce/loveplus/images/large/.+?)"%', $html, $matches)) {
      $response->addImageURL($matches[1]);
    }

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_loveplusphotoclub');
