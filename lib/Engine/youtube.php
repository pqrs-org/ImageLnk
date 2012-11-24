<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_youtube {
  const language = NULL;
  const sitename = 'http://www.youtube.com/';

  public static function handle($url) {
    if (! preg_match('/^https?:\/\/www\.youtube\.com\/watch\?(.+)/', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    ImageLnkHelper::setResponseFromOpenGraph($response, $html);

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_youtube');
