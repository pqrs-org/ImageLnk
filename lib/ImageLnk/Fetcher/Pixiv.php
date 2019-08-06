<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Fetcher_Pixiv_Response
{
    private $data = '';
    private $headers = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getBody()
    {
        return $this->data;
    }

    public function getHeader()
    {
        return $this->headers;
    }
}

class ImageLnk_Fetcher_Pixiv extends ImageLnk_Fetcher
{
    private static $api = null;

    private static function getTokenCacheFilePath()
    {
        return ImageLnk_Cache::getCacheDirectory() . '/token/pixiv.json';
    }

    public static function login()
    {
        if (self::$api !== null) {
            return;
        }

        self::$api = new PixivAppAPI();

        $path = self::getTokenCacheFilePath();
        if (file_exists($path)) {
            $json = json_decode(file_get_contents($path));
            self::$api->setAuthorizationResponse($json->authorizationResponse);
            self::$api->setAccessToken($json->accessToken);
            self::$api->setRefreshToken($json->refreshToken);
            return;
        }

        self::$api->login(
            ImageLnk_Config::v('auth_pixiv_id'),
            ImageLnk_Config::v('auth_pixiv_password')
        );
        ImageLnk_Cache::writeToCacheFile(
            $path,
            json_encode([
                'authorizationResponse' => self::$api->getAuthorizationResponse(),
                'accessToken' => self::$api->getAccessToken(),
                'refreshToken' => self::$api->getRefreshToken(),
            ])
        );
    }

    public static function fetch($url, $referer = null)
    {
        $urlInfo = parse_url($url);
        $query = [];
        parse_str($urlInfo['query'], $query);

        if (!isset($query['illust_id'])) {
            return new ImageLnk_Fetcher_Pixiv_Response('');
        }

        self::login();

        // Retry if error. (e.g., token expired)
        $detail = self::$api->illust_detail($query['illust_id']);
        if (isset($detail['error']) || !isset($detail['illust'])) {
            $path = self::getTokenCacheFilePath();
            if (file_exists($path)) {
                unlink($path);
                self::login();
            }
            $detail = self::$api->illust_detail($query['illust_id']);
        }

        return new ImageLnk_Fetcher_Pixiv_Response(json_encode($detail));
    }
}
