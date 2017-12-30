<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

require_once sprintf('%s/../../lib/ImageLnk.php', dirname(__FILE__));

class ImageLnkAPI_get
{
    public function control()
    {
        $info = array();

        if (isset($_REQUEST['url'])) {
            $url = $_REQUEST['url'];
            $info['pageurl'] = $url;

            $response = ImageLnk::getImageInfo($url);
            if ($response) {
                $info['title']     = $response->getTitle();
                $info['referer']   = $response->getReferer();
                $info['backlink']  = $response->getBackLink();
                $info['imageurls'] = $response->getImageURLs();
            }
        }

        header('Content-Type: application/json');
        print json_encode($info);
    }
}

$imagelnk = new ImageLnkAPI_get();
$imagelnk->control();
