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
            $elapsedTime = time() - filemtime($path);
            if ($elapsedTime < 3600) {
                $json = json_decode(file_get_contents($path));
                self::$api->setAuthorizationResponse($json->authorizationResponse);
                self::$api->setAccessToken($json->accessToken);
                self::$api->setRefreshToken($json->refreshToken);
                return;
            }
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
        if (preg_match('%artworks/(\d+)%', $urlInfo['path'], $matches)) {
            $id = $matches[1];

            self::login();

            $detail = self::$api->illust_detail($id);

            return new ImageLnk_Fetcher_Pixiv_Response(json_encode($detail));
        }

        return new ImageLnk_Fetcher_Pixiv_Response('');
    }
}
