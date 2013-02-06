<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_engadget_jp {
  const language = 'Japanese';
  const sitename = 'http://japanese.engadget.com/gallery';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/japanese\.engadget\.com\/photos\//', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    $response->setTitle(ImageLnkHelper::getTitle($html));

    if (preg_match('%<div class="gallery-image-dermis">(.+?)</div>%s', $html, $matches)) {
      foreach (ImageLnkHelper::scanSingleTag('img', $matches[1]) as $img) {
        if (preg_match('/ src="(.+?)"/', $img, $m)) {
          $response->addImageURL($m[1]);
          break;
        }
      }
    }

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_engadget_jp');
