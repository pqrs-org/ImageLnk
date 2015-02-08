<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

require_once sprintf('%s/../lib/ImageLnk.php', dirname(__FILE__));

class ImageLnkCLI_sites
{
    public function control()
    {
        foreach (ImageLnk::getSites() as $s) {
            print $s . "\n";
        }
    }
}

$imagelnk = new ImageLnkCLI_sites();
$imagelnk->control();
