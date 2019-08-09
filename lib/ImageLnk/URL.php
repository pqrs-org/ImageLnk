<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_URL
{
    public static function getRedirectedURL($url, $nest = 0)
    {
        stream_context_set_default(
            [
                'http' => [
                    'method' => 'HEAD',
                ],
            ]
        );
        $headers = get_headers($url, 1);
        if ($headers !== false && isset($headers['Location'])) {
            $location = $headers['Location'];
            if (is_array($location)) {
                $location = $location[0];
            }
            if (preg_match('/^\//', $location)) {
                preg_match('#^(.+://.+?)/#', $url, $matches);
                $location = $matches[1] . $location;
            }
            return self::getRedirectedURL($location);
        }
        return $url;
    }
}
