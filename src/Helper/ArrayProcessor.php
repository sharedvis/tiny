<?php

namespace Tiny\Helper;

class ArrayProcessor {

    /**
    * Find array value by key recursively
    *
    * @param array  $array      Array data
    * @param string $keySearch  Array key to be search
    * @return mixed
    */
    public static function findByKey($array, $keySearch)
    {
        foreach ($array as $key => $item)
        {
            if ($key == $keySearch) {
                return $item;
            } else {
                if (is_array($item)) {
                   self::findByKey($item, $keySearch);
                }
            }
        }

        return false;
    }
}
