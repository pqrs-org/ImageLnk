<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_4gamer {
  const language = 'Japanese';
  const sitename = 'http://www.4gamer.net/';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/www\.4gamer\.net(\/.+)\/screenshot\.html(\?num=(\d+))?$/', $url, $matches)) {
      return FALSE;
    }

    $id = preg_quote($matches[1], '/');
    $number = '001';
    if (isset($matches[3])) {
      $number = $matches[3];
    }

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];
    $html = @iconv("EUC-JP", "UTF-8//IGNORE", $html);

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $response->setTitle(ImageLnk_Helper::getTitle($html));
    if (preg_match_all("/href=\"({$id}\/SS\/{$number}\.[^\"]+)\"/", $html, $matches)) {
      $response->addImageURL('http://www.4gamer.net' . $matches[1][0]);
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_4gamer');
