<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnkHelper {
  public static function scanSingleTag($name, $html, $regexpoption = 's') {
    if (preg_match_all("/<{$name} .+?>/{$regexpoption}", $html, $matches) === FALSE) return array();
    return $matches[0];
  }

  public static function getTitle($html) {
    if (preg_match('/<title( .*?)?>(.*?)<\/title>/is', $html, $matches)) {
      return $matches[2];
    } else {
      return FALSE;
    }
  }

  public static function collapseWhiteSpaces($text) {
    return preg_replace("/[\t\r\n ]+/", ' ', $text);
  }

  public static function setResponseFromOpenGraph($response, $html) {
    foreach (self::scanSingleTag('meta', $html) as $meta) {
      if (preg_match('/ property=[\'"]og:title[\'"]/', $meta)) {
        if (preg_match('/ content=[\'"](.+?)[\'"]/is', $meta, $matches)) {
          $response->setTitle($matches[1]);
        }
      }

      if (preg_match('/ property=[\'"]og:image[\'"]/', $meta)) {
        if (preg_match('/ content=[\'"](.+?)[\'"]/is', $meta, $matches)) {
          $response->addImageURL($matches[1]);
        }
      }
    }

    if (! $response->getTitle()) {
      // fall-back
      $title = self::getTitle($html);
      if ($title) {
        $response->setTitle($title);
      }
    }
  }
}
