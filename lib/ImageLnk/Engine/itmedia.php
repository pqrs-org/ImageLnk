<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Engine_itmedia {
  const language = 'Japanese';
  const sitename = 'http://www.itmedia.co.jp/';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/image\.itmedia\.co\.jp\/l\/im\/(.+)$/', $url, $matches)) {
      return FALSE;
    }

    $filename = preg_quote($matches[1], '/');

    // ----------------------------------------
    $data = ImageLnk_Cache::get($url);
    $html = $data['data'];
    $html = @iconv("SHIFT_JIS", "UTF-8//IGNORE", $html);

    $response = new ImageLnk_Response();
    $response->setReferer($url);

    if (preg_match('/<h1>(.+?)<\/h1>/', $html, $matches)) {
      $response->setTitle(strip_tags($matches[1]));
    }
    foreach (ImageLnk_Helper::scanSingleTag('img', $html) as $img) {
      if (preg_match(sprintf('/ src="([^"]+?\/%s)"/s', $filename), $img, $matches)) {
        $response->addImageURL($matches[1]);
      }
    }

    if (preg_match_all('%<a (.+?)>(.+?)</a>%', $html, $matches)) {
      foreach ($matches[1] as $k => $a) {
        if (preg_match('/記事に戻る/', $matches[2][$k])) {
          if (preg_match('%href="(.+?)"%', $a, $m)) {
            $response->setBackLink($m[1]);
            break;
          }
        }
      }
    }

    return $response;
  }
}
ImageLnk_Engine::push('ImageLnk_Engine_itmedia');
