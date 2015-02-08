<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

require_once sprintf('%s/../../lib/ImageLnk.php', dirname(__FILE__));

class ImageLnkAPI_sites
{
    public function control()
    {
        header('Content-Type: application/json');
        print json_encode(array('sites' => ImageLnk::getSites()));
    }
}

$imagelnk = new ImageLnkAPI_sites();
$imagelnk->control();
