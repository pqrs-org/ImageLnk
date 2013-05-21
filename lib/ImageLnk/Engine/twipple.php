<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_twipple {
  const language = 'Japanese';
  const sitename = 'http://twipple.jp/';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/p\.twipple\.jp\//', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $response->setTitle(ImageLnk_Helper::getTitle($html));
    if (preg_match('% href="(http://p.twpl.jp/show/orig/.+?)"%s', $html, $matches)) {
      $response->addImageURL($matches[1]);
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_twipple');
