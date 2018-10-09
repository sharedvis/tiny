<?php

namespace Tiny\Helper;

class GlobalFunction {

    public static function getConvensionName($camel)
    {
        $snake = preg_replace_callback('/[A-Z]/', function ($match){
            return '_' . strtolower($match[0]);
        }, $camel);
        $snake = str_replace("_task","",$snake);
        return ltrim($snake, '_');
    }
}