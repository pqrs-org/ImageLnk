<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_ameblo {
  const language = 'Japanese';
  const sitename = 'http://ameblo.jp/';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/([^\/]*\.)?ameblo\.jp\/.+\/image-/', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $response->setTitle(ImageLnk_Helper::getTitle($html));
    if (preg_match('%<div id="originalImgUrl">(.+?)</div>%s', $html, $matches)) {
      $response->addImageURL($matches[1]);
    } elseif (preg_match('/<div id="imageMain">.*?<img .*?src="(.+?)"/s', $html, $matches)) {
      $response->addImageURL($matches[1]);
    } elseif (preg_match('/"current": {.+?"imgUrl":"(.+?)"/s', $html, $matches)) {
      $response->addImageURL('http://stat.ameba.jp' . $matches[1]);
    } else {
      foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $imgtag) {
        if (preg_match('/ id="imageMain"/', $imgtag) ||
            preg_match('/ id="centerImg"/', $imgtag)) {
          if (preg_match('/ src="(.+?)"/', $imgtag, $matches)) {
            $response->addImageURL($matches[1]);
          }
        }
      }
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_ameblo');
