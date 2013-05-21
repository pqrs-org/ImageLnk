<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Fetcher {
  const USER_AGENT = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_7) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.68 Safari/534.24";

  private static function getCookieCacheFilePath($site) {
    return ImageLnk_Cache::getCacheDirectory() . '/cookie/' . sha1($site);
  }

  private static function getConfig() {
    return array(
      'timeout' => 60,
      'ssl_verify_peer' => false,
      );
  }

  // ======================================================================
  // For pixiv
  private static function set_pixiv_header($request) {
    $request->setHeader('User-Agent', self::USER_AGENT);
    $request->setHeader('Accept', 'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
    $request->setHeader('Cache-Control', 'max-age=0');
  }

  private static function fetch_pixiv_login() {
    // If the authentication setting is not changed from the default value,
    // we don't try login.
    if (ImageLnk_Config::v('auth_pixiv_id') == ImageLnk_Config::v('auth_pixiv_id', ImageLnk_Config::GET_DEFAULT_VALUE) &&
        ImageLnk_Config::v('auth_pixiv_password') == ImageLnk_Config::v('auth_pixiv_password', ImageLnk_Config::GET_DEFAULT_VALUE)) {
      return false;
    }

    $loginurl = 'http://www.pixiv.net/login.php';
    $request = new HTTP_Request2($loginurl, HTTP_Request2::METHOD_POST, self::getConfig());
    self::set_pixiv_header($request);

    $request->addPostParameter(array(
                                 'mode' => 'login',
                                 'pixiv_id' => ImageLnk_Config::v('auth_pixiv_id'),
                                 'pass'     => ImageLnk_Config::v('auth_pixiv_password'),
                                 'skip'     => '1',
                                 ));
    $response = $request->send();

    if ($response->getHeader('location') == 'http://www.pixiv.net/mypage.php') {
      ImageLnk_Cache::writeToCacheFile(self::getCookieCacheFilePath("pixiv"), serialize($response->getCookies()));
      return true;
    } else {
      return false;
    }
  }

  private static function fetch_pixiv_page($url, $referer) {
    $cookies = ImageLnk_Cache::readFromCacheFile(self::getCookieCacheFilePath("pixiv"));
    if ($cookies === FALSE) {
      $cookies = array();
    } else {
      $cookies = unserialize($cookies);
    }

    // ------------------------------------------------------------
    $config = self::getConfig();
    $config['follow_redirects'] = true;
    $request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET, $config);
    self::set_pixiv_header($request);

    // We need to set properly referer for mode=big,manga_big pages.
    if (preg_match('/member_illust\.php\?mode=big/', $url)) {
      $request->setHeader('Referer', preg_replace('/mode=big/', 'mode=medium', $url));
    }
    if (preg_match('/member_illust\.php\?mode=manga_big/', $url)) {
      $newreferer = preg_replace('/mode=manga_big/', 'mode=manga', $url);
      $newreferer = preg_replace('/&page=\d+/', '', $newreferer);
      $request->setHeader('Referer', $newreferer);
    } else {
      if ($referer !== NULL) {
        $request->setHeader('Referer', $referer);
      }
    }

    foreach ($cookies as $c) {
      if (! isset($c['expires']) ||
          time() < strtotime($c['expires'])) {
        $request->addCookie($c['name'], $c['value']);
      }
    }

    $response = $request->send();
    return $response;
  }

  private static function fetch_pixiv($url, $referer) {
    $response = self::fetch_pixiv_page($url, $referer);
    $html = $response->getBody();

    // Try login if needed.
    if (preg_match("/pixiv\.user\.id = '';/", $html) ||
        preg_match('/pixiv\.user\.loggedIn = false;/', $html) ||
        preg_match('/class="login-form"/', $html)) {
      if (self::fetch_pixiv_login()) {
        $response = self::fetch_pixiv_page($url, $referer);
      } else {
        throw new ImageLnk_Exception();
      }
    }

    return $response;
  }

  // ======================================================================
  public static function fetch($url, $referer = NULL) {
    if (preg_match('/^http:\/\/[^\/]*pixiv\.net\//', $url)) {
      return self::fetch_pixiv($url, $referer);
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
