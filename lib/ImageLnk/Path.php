<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Path
{
    static public function combine()
    {
        $path = '';
        foreach (func_get_args() as $values) {
            if (! is_array($values)) {
                $values = array($values);
            }
            foreach ($values as $v) {
                if ($path !== '') {
                    $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                }
                $path .= $v;
            }
        }
        return $path;
    }
}
