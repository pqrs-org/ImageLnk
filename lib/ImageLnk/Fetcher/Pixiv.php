<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Fetcher_Pixiv extends ImageLnk_Fetcher
{
    private static function login()
    {
        // If the authentication setting is not changed from the default value,
        // we don't try login.
        if (ImageLnk_Config::v('auth_pixiv_id') == ImageLnk_Config::v('auth_pixiv_id', ImageLnk_Config::GET_DEFAULT_VALUE)
            && ImageLnk_Config::v('auth_pixiv_password') == ImageLnk_Config::v('auth_pixiv_password', ImageLnk_Config::GET_DEFAULT_VALUE)
        ) {
            return false;
        }

        $loginurl = 'http://www.pixiv.net/login.php';
        $request = new HTTP_Request2($loginurl, HTTP_Request2::METHOD_POST, self::getConfig());
        self::setHeader($request);
        $request->setCookieJar(true);

        $request->addPostParameter(
            array(
                'mode' => 'login',
                'pixiv_id' => ImageLnk_Config::v('auth_pixiv_id'),
                'pass'     => ImageLnk_Config::v('auth_pixiv_password'),
                'skip'     => '1',
            )
        );
        $response = $request->send();

        if ($response->getHeader('location') == 'http://www.pixiv.net/mypage.php') {
            $jar = $request->getCookieJar();
            $jar->serializeSessionCookies(true);
            ImageLnk_Cache::writeToCacheFile(self::getCookieCacheFilePath("pixiv"), $jar->serialize());
            return true;
        } else {
            return false;
        }
    }

    private static function fetch_page($url, $referer)
    {
        $jar = new HTTP_Request2_CookieJar();
        $serialized = ImageLnk_Cache::readFromCacheFile(self::getCookieCacheFilePath("pixiv"));
        if ($serialized) {
            $jar->unserialize($serialized);
        }

        // ------------------------------------------------------------
        $config = self::getConfig();
        $config['follow_redirects'] = true;
        $request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET, $config);
        self::setHeader($request);
        $request->setCookieJar($jar);

        // We need to set properly referer for mode=big,manga_big pages.
        if (preg_match('/member_illust\.php\?mode=big/', $url)) {
            $request->setHeader('Referer', preg_replace('/mode=big/', 'mode=medium', $url));
        }
        if (preg_match('/member_illust\.php\?mode=manga_big/', $url)) {
            $newreferer = preg_replace('/mode=manga_big/', 'mode=manga', $url);
            $newreferer = preg_replace('/&page=\d+/', '', $newreferer);
            $request->setHeader('Referer', $newreferer);
        } else {
            if ($referer !== null) {
                $request->setHeader('Referer', $referer);
            }
        }

        $response = $request->send();

        $jar = $request->getCookieJar();
        $jar->serializeSessionCookies(true);
        //ImageLnk_Cache::writeToCacheFile(self::getCookieCacheFilePath("pixiv"), serialize($jar));

        return $response;
    }

    private static function isLogin($html)
    {
        if (preg_match("/pixiv\.user\.id = '';/", $html)
            || preg_match('/pixiv\.user\.loggedIn = false;/', $html)
            || preg_match('/class="login-form"/', $html)
        ) {
            return false;
        }
        return true;
    }

    public static function fetch($url, $referer = null)
    {
        $response = self::fetch_page($url, $referer);

        // Try login if needed.
        if (! self::isLogin($response->getBody())) {
            if (self::login()) {
                $response = self::fetch_page($url, $referer);
                if (! self::isLogin($response->getBody())) {
                    throw new ImageLnk_Exception('failed to login');
                }
            } else {
                throw new ImageLnk_Exception();
            }
        }

        return $response;
    }
}
