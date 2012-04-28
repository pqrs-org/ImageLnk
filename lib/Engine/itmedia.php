<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_itmedia {
  const language = 'Japanese';
  const sitename = 'http://www.itmedia.co.jp/';

  public static function handle($url) {
    if (! preg_match('/^http:\/\/image\.itmedia\.co\.jp\/l\/im\/(.+)$/', $url, $matches)) {
      return FALSE;
    }

    $filename = preg_quote($matches[1], '/');

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];
    $html = @iconv("SHIFT_JIS", "UTF-8//IGNORE", $html);

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    if (preg_match('/<h1>(.+?)<\/h1>/', $html, $matches)) {
      $response->setTitle(strip_tags($matches[1]));
    }
    foreach (ImageLnkHelper::scanSingleTag('img', $html) as $img) {
      if (preg_match(sprintf('/ src="([^"]+?\/%s)"/s', $filename), $img, $matches)) {
        $response->addImageURL($matches[1]);
      }
    }

    if (preg_match_all('%<a (.+?)>%', $html, $matches)) {
      foreach ($matches[1] as $a) {
        if (preg_match('%onclick="designCnt\(\'largeImgMain\'\);"%i', $a)) {
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
ImageLnkEngine::push('ImageLnkEngine_itmedia');
