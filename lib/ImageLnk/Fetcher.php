<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Fetcher
{
    const USER_AGENT = "Mozilla/5.0 (Macintosh; Intel Mac OS X 11_2_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36";

    protected static function getCookieCacheFilePath($site)
    {
        return ImageLnk_Cache::getCacheDirectory() . '/cookiejar/' . sha1($site);
    }

    // ======================================================================
    public static function fetch($url, $header = [])
    {
        if (preg_match('/^https?:\/\/[^\/]*pixiv\.net\//', $url)) {
            return ImageLnk_Fetcher_Pixiv::fetch($url, $header);
        }

        // --------------------------------------------------
        $headers = array_merge(
            [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Charset' => 'UTF-8,*;q=0.5',
                'Accept-Encoding' => 'gzip,deflate,sdch',
                'Accept-Language' => 'en,en-US;q=0.8,ja;q=0.6',
                'Cache-Control' => 'max-age=0',
                'Connection' => 'keep-alive',
                'User-Agent' => self::USER_AGENT,
            ],
            $header
        );

        // For some sites (itmedia, ...), we need to set referer.
        $referer = $headers['Referer'] ?? null;
        if ($referer === null) {
            $headers['Referer'] = $url;
        }

        $query = [];
        if (preg_match('%^https?://pbs\.twimg\.com/%', $url) ||
            preg_match('%^https?://\d+\.media\.tumblr\.com/%', $url)
        ) {
            $query['url'] = $url;
            $query['secret'] = ImageLnk_Config::v('twimg_proxy_secret');
            $url = ImageLnk_Config::v('twimg_proxy');
        }

        $options = [
            'timeout' => 60,
            'verify' => false,
            'headers' => $headers,
        ];
        if (count($query) > 0) {
            $options['query'] = $query;
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->request(
            'GET',
            $url,
            $options,
        );

        return $response;
    }
}
