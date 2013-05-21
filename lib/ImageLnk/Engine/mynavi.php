<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_mynavi {
  const language = 'Japanese';
  const sitename = 'http://news.mynavi.jp/';

  public static function handle($url) {
    if (! preg_match('%http://news\.mynavi\.jp/photo/%', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    $response->setTitle(ImageLnk_Helper::getTitle($html));

    if (preg_match_all('%<a (.+?)>(.+?)</a>%s', $html, $matches)) {
      foreach ($matches[1] as $k => $a) {
        if (preg_match('%id="photo-link"%', $a)) {
          if (preg_match('% src="(.+?)"%', $matches[2][$k], $m)) {
            $response->addImageURL('http://news.mynavi.jp' . $m[1]);
            break;
          }
        }
      }
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_mynavi');
