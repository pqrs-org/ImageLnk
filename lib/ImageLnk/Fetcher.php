<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Fetcher
{
    const USER_AGENT = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36";

    protected static function setHeader($request)
    {
        $request->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        $request->setHeader('Accept-Charset', 'UTF-8,*;q=0.5');
        $request->setHeader('Accept-Encoding', 'gzip,deflate,sdch');
        $request->setHeader('Accept-Language', 'en,en-US;q=0.8,ja;q=0.6');
        $request->setHeader('Cache-Control', 'max-age=0');
        $request->setHeader('Connection', 'keep-alive');
        $request->setHeader('User-Agent', self::USER_AGENT);
    }

    protected static function getCookieCacheFilePath($site)
    {
        return ImageLnk_Cache::getCacheDirectory() . '/cookiejar/' . sha1($site);
    }

    protected static function getConfig()
    {
        return array(
            'timeout' => 60,
            'ssl_verify_peer' => false,
        );
    }

    // ======================================================================
    public static function fetch($url, $header = [])
    {
        if (preg_match('/^https?:\/\/[^\/]*pixiv\.net\//', $url)) {
            return ImageLnk_Fetcher_Pixiv::fetch($url, $header);
        }

        // --------------------------------------------------
        $config = self::getConfig();
        $config['follow_redirects'] = true;
        $request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET, $config);
        $request->setHeader('User-Agent', self::USER_AGENT);

        foreach ($header as $k => $v) {
            $request->setHeader($k, $v);
        }

        // For some sites (itmedia, ...), we need to set referer.
        $referer = $header['Referer'] ?? null;
        if ($referer === null) {
            $referer = $url;
        }
        $request->setHeader('Referer', $referer);

        $response = $request->send();
        return $response;
    }
}
