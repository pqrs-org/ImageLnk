<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_URL {
  public static function getRedirectedURL($url, $nest = 0) {
    stream_context_set_default(array(
                                 'http' => array(
                                   'method' => 'HEAD'
                                   )
                                 ));
    $headers = get_headers($url, 1);
    if ($headers !== false && isset($headers['Location'])) {
      return self::getRedirectedURL($headers['Location']);
    }
    return $url;
  }
}
