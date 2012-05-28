<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_viame {
  const language = NULL;
  const sitename = 'http://via.me/';

  public static function handle($url) {
    if (! preg_match('%^http://via\.me/%', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    ImageLnkHelper::setResponseFromOpenGraph($response, $html);
    $response->setTitle(ImageLnkHelper::collapseWhiteSpaces(ImageLnkHelper::getTitle($html)));

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_viame');
