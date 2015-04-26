<?php //-*- Mode: php; indent-tabs-mode: nil; Coding: utf-8; -*-

use \Symfony\Component\Yaml\Yaml;

class ImageLnk_Config
{
    private static $default_ = null;
    private static $data_ = null;

    const GET_CURRENT_VALUE = 0;
    const GET_DEFAULT_VALUE = 1;

    public static function static_initialize()
    {
        try {
            // load default values
            self::$data_    = Yaml::parse(
                file_get_contents(
                    sprintf(
                        '%s/config/config.default.yaml',
                        dirname(dirname(dirname(__FILE__)))
                    )
                )
            );
            self::$default_ = self::$data_;

            // load customized values
            $filepath = sprintf('%s/config/config.yaml', dirname(dirname(dirname(__FILE__))));
            if (file_exists($filepath)) {
                $newvalue = Yaml::parse(file_get_contents($filepath));
                if ($newvalue) {
                    foreach ($newvalue as $k => $v) {
                        if (isset(self::$data_[$k])) {
                            self::$data_[$k] = $v;
                        }
                    }
                }
            }

        } catch (Exception $e) {
            throw new ImageLnk_Exception('failed to load config: ' . $e->getMessage());
        }
    }

    public static function v($key, $mode = self::GET_CURRENT_VALUE)
    {
        if (! isset(self::$data_[$key])) {
            throw new ImageLnk_Exception("invalid config key: $key");
        }

        if ($mode == self::GET_DEFAULT_VALUE) {
            return self::$default_[$key];
        }
        return self::$data_[$key];
    }

    public static function set($key, $newvalue)
    {
        if (! isset(self::$data_[$key])) {
            throw new ImageLnk_Exception("invalid config key: $key");
        }
        self::$data_[$key] = $newvalue;
    }
}
