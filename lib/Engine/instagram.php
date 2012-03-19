<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_instagram {
  const language = NULL;
  const sitename = 'http://instagr.am/';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/instagr\.am\/p\//', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    ImageLnkHelper::setResponseFromOpenGraph($response, $html);
    // Decode \xXX
    $response->setTitle(preg_replace ('/\\\\x([0-9a-fA-F]{2})/e', "pack('H*',utf8_decode('\\1'))", $response->getTitle()));

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_instagram');
