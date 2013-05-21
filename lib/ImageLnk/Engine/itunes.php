<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_itunes {
  const language = NULL;
  const sitename = 'http://itunes.apple.com/';

  public static function handle($url) {
    if (! preg_match('%^https?://itunes\.apple\.com/%', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $response->setTitle(ImageLnk_Helper::getTitle($html));

    if (preg_match('/<div id="left-stack">(.+)/s', $html, $matches)) {
      foreach (ImageLnk_Helper::scanSingleTag('img', $matches[1]) as $img) {
        if (preg_match('/ class="artwork"/', $img)) {
          if (preg_match('/ src="(.+?)"/', $img, $m)) {
            $response->addImageURL($m[1]);
            break;
          }
        }
      }
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_itunes');
