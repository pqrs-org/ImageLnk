<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

require_once sprintf('%s/../lib/ImageLnk.php', dirname(__FILE__));

class ImageLnkCLI_get {
  public function control() {
    ImageLnk_Config::set('cache_directory', '/var/tmp/ImageLnkCLI');

    if (! isset($_SERVER['argv'][1])) {
      print "Usage: {$_SERVER['argv'][0]} url\n";
      exit(1);
    }

    $url = $_SERVER['argv'][1];
    $response = ImageLnk::getImageInfo($url);
    print 'Title: ' . $response->getTitle() . "\n";
    print 'Referer: ' . $response->getReferer() . "\n";
    print 'ImageURLs: ' . "\n";
    foreach ($response->getImageURLs() as $imageurl) {
      print "$imageurl\n";
    }
  }
}

$imagelnk = new ImageLnkCLI_get();
$imagelnk->control();
