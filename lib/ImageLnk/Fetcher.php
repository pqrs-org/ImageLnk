<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Fetcher {
  const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.65 Safari/537.31';

  protected static function setHeader($request) {
    $request->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
    $request->setHeader('Accept-Charset', 'UTF-8,*;q=0.5');
    $request->setHeader('Accept-Encoding', 'gzip,deflate,sdch');
    $request->setHeader('Accept-Language', 'en,en-US;q=0.8,ja;q=0.6');
    $request->setHeader('Cache-Control', 'max-age=0');
    $request->setHeader('Connection', 'keep-alive');
    $request->setHeader('User-Agent', self::USER_AGENT);
  }

  protected static function getCookieCacheFilePath($site) {
    return ImageLnk_Cache::getCacheDirectory() . '/cookiejar/' . sha1($site);
  }

  protected static function getConfig() {
    return array(
      'timeout' => 60,
      'ssl_verify_peer' => false,
      );
  }

  // ======================================================================
  public static function fetch($url, $referer = NULL) {
    if (preg_match('/^http:\/\/[^\/]*pixiv\.net\//', $url)) {
      return ImageLnk_Fetcher_Pixiv::fetch($url, $referer);
    }

    // --------------------------------------------------
    $config = self::getConfig();
    $config['follow_redirects'] = true;
    $request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET, $config);
    $request->setHeader('User-Agent', self::USER_AGENT);

    // For some sites (itmedia, ...), we need to set referer.
    if ($referer === NULL) {
      $referer = $url;
    }
    $request->setHeader('Referer', $referer);

    $response = $request->send();
    return $response;
  }
}
