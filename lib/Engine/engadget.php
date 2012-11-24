<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_engadget {
  const language = NULL;
  const sitename = 'http://www.engadget.com/galleries/';

  public static function handle($url) {
    if (! preg_match('%^http://www\.engadget\.com/gallery/%', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    $response->setTitle(ImageLnkHelper::getTitle($html));

    if (preg_match('%<!-- M:body-gallery-image -->(.+?)<!-- /M -->%s', $html, $matches)) {
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
ImageLnkEngine::push('ImageLnkEngine_engadget');
