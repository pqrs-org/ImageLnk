<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_dropbox {
  const language = NULL;
  const sitename = 'https://www.dropbox.com/';

  public static function handle($url) {
    if (! preg_match('%^https://www\.dropbox\.com/%', $url, $matches)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $response->setTitle(ImageLnk_Helper::getTitle($html));

    foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $img) {
      if (preg_match('% class="content-shadow"%s', $img)) {
        if (preg_match('% src="(.+?)"%s', $img, $m)) {
          $imageurl = $m[1];
          if (preg_match('%^https://photos-%', $imageurl)) {
            $response->addImageURL($imageurl);
          }
        }
      }
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_dropbox');
