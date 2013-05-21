<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_youtube {
  const language = NULL;
  const sitename = 'http://www.youtube.com/';

  public static function handle($url) {
    if (! preg_match('/^https?:\/\/www\.youtube\.com\/watch\?(.+)/', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    ImageLnk_Helper::setResponseFromOpenGraph($response, $html);

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_youtube');
