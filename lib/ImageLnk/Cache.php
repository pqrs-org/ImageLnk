<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Cache
{
    public static function getCacheDirectory()
    {
        return sprintf('%s/%s', ImageLnk_Config::v('cache_directory'), sha1(__FILE__));
    }

    public static function getCacheFilePathFromURL($url)
    {
        $hash = sha1($url);

        $path = self::getCacheDirectory() . '/page/';

        for ($i = 0; $i < 4; ++$i) {
            $path .= substr($hash, $i, 1) . '/';
        }

        $path .= $hash;
        return $path;
    }

    public static function writeToCacheFile($path, $data)
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            if (mkdir($directory, 0700, true) === false) {
                throw new ImageLnk_Exception();
            }
        }

        $outfile = tempnam($directory, 'ImageLnk');
        if ($outfile === false) {
            throw new ImageLnk_Exception();
        }
        if (file_put_contents($outfile, $data) === false) {
            throw new ImageLnk_Exception();
        }
        if (rename($outfile, $path) === false) {
            throw new ImageLnk_Exception();
        }
    }

    public static function readFromCacheFile($path)
    {
        if (! is_file($path)) {
            return false;
        }
        if (time() - filemtime($path) > 60 * ImageLnk_Config::v('cache_expire_minutes')) {
            return false;
        }
        return file_get_contents($path);
    }

    public static function get($url, $referer = null)
    {
        $path = self::getCacheFilePathFromURL($url);

        $cache = self::readFromCacheFile($path);

        if ($cache !== false) {
            $data = unserialize($cache);
            $data['from_cache'] = true;

        } else {
            $response = ImageLnk_Fetcher::fetch($url, $referer);

            $data['from_cache'] = false;
            $data['data'] = $response->getBody();
            $data['response'] = $response->getHeader();

            self::writeToCacheFile($path, serialize($data));
        }

        return $data;
    }
}
