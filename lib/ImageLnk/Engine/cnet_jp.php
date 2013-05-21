<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_cnet_jp {
  const language = 'Japanese';
  const sitename = 'http://japan.cnet.com/';

  private static function handle_l_img_main($response, $html) {
    $matches = NULL;
    if (! preg_match('%<div id="l_img_main">(.+)<!-- //block story -->%s', $html, $matches)) {
      return FALSE;
    }

    foreach (ImageLnk_Helper::scanSingleTag('img', $matches[1]) as $img) {
      if (preg_match('/ src="(.+?)"/', $img, $m)) {
        $response->addImageURL('http://japan.cnet.com' . $m[1]);
      }
    }

    if (preg_match('%<div class="caption">(.+?)</div>%', $matches[1], $m)) {
      $response->setTitle($response->getTitle() . ': ' . $m[1]);
    }

    return TRUE;
  }

  private static function handle_story_photoreport($response, $html) {
    $matches = NULL;
    if (! preg_match('%<div class="story_photoreport">(.+)<!--block_story-->%s', $html, $matches)) {
      return FALSE;
    }

    foreach (ImageLnk_Helper::scanSingleTag('img', $matches[1]) as $img) {
      if (preg_match('/ src="(.+?)"/', $img, $m)) {
        $response->addImageURL('http://japan.cnet.com' . $m[1]);
      }
      if (preg_match('/ alt="(.+?)"/', $img, $m)) {
        $response->setTitle($response->getTitle() . ': ' . $m[1]);
      }
      break;
    }

    return TRUE;
  }

  public static function handle($url) {
    if (! preg_match('%^http://japan\.cnet\.com/%', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $response->setTitle(ImageLnk_Helper::getTitle($html));

    if (self::handle_l_img_main($response, $html)) {
      return $response;
    }

    if (self::handle_story_photoreport($response, $html)) {
      return $response;
    }

    return NULL;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_cnet_jp');
