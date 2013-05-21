<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_dengeki {
  const language = 'Japanese';
  const sitename = 'http://news.dengeki.com/';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/news\.dengeki\.com(\/.+?\/)img.html/', $url, $matches)) {
      return FALSE;
    }

    $id = preg_quote($matches[1], '/');

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $response->setTitle(ImageLnk_Helper::getTitle($html));

    if (preg_match("/src=\"({$id}.*?)\"/", $html, $matches)) {
      $response->addImageURL('http://news.dengeki.com' . $matches[1]);
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_dengeki');
