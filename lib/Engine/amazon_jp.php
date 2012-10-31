<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkEngine_amazon_jp {
  const language = 'Japanese';
  const sitename = 'http://www.amazon.co.jp/';

  public static function handle($url) {
    if (! preg_match('%^http://www\.amazon\.co\.jp/%', $url)) {
      return FALSE;
    }

    // ----------------------------------------
    $data = ImageLnkCache::get($url);
    $html = $data['data'];
    $html = @iconv("SJIS", "UTF-8//IGNORE", $html);

    $response = new ImageLnkResponse();
    $response->setReferer($url);

    $response->setTitle(ImageLnkHelper::getTitle($html));

    foreach (ImageLnkHelper::scanSingleTag('img', $html) as $img) {
      if (preg_match('% id="prodImage"%s', $img) ||
          preg_match('%\sid="original-main-image"%s', $img)) {
        if (preg_match('% src="(.+?)"%s', $img, $m)) {
          $response->addImageURL($m[1]);
        }
      }
    }

    if (count($response->getImageURLs()) == 0) {
      return FALSE;
    }

    return $response;
  }
}
ImageLnkEngine::push('ImageLnkEngine_amazon_jp');
