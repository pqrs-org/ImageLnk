<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_lockerz {
  const language = NULL;
  const sitename = 'http://lockerz.com/';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/(pics\.)?lockerz.com\/s\//', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    $response->setTitle(ImageLnkHelper::getTitle($html));

    foreach (ImageLnkHelper::scanSingleTag('img', $html) as $imgtag) {
      if (preg_match('/ id="photo"/', $imgtag)) {
        if (preg_match('/ src="(.+?)"/', $imgtag, $matches)) {
          $response->addImageURL($matches[1]);
        }
      }
    }

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_lockerz');
