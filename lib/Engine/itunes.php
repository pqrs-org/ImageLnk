<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_itunes {
  const language = NULL;
  const sitename = 'http://itunes.apple.com/';

  public static function handle($url) {
    if (! preg_match('%^https?://itunes\.apple\.com/%', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    $response->setTitle(ImageLnkHelper::getTitle($html));

    if (preg_match('/<div id="left-stack">(.+)/s', $html, $matches)) {
      foreach (ImageLnkHelper::scanSingleTag('img', $matches[1]) as $img) {
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
ImageLnkEngine::push('ImageLnkEngine_itunes');
