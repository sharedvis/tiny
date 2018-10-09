<?php

namespace Tiny\Phalcon\Libraries;

use \Phalcon\Mvc\Router;

/**
* Phalcon Router extend
*/
class TinyRouter extends Router
{
    /**
     * Extending router add to convert array value of paths to json string
     *
     * @param string $pattern
     * @param mixed $paths
     * @param mixed $httpMethods
     * @param mixed $position
     * @return \Phalcon\Mvc\Router\RouteInterface
     */
    public function add($pattern, $paths = null, $httpMethods = null, $position = Router::POSITION_LAST)
    {
        $paths = $this->_parsePath($paths);

        parent::add($pattern, $paths, $httpMethods, $position);
    }

    /**
     * Parse array path, and convert all array value to json string
     *
     * @param  mixed $paths     Path controller and action
     * @return mixed
     */
    public function _parsePath($paths)
    {
        if (is_array($paths) && !empty($paths)) {
            foreach ($paths as $key => $value) {
                if (is_array($value))
                    $paths[$key] = json_encode($value);
            }
        }

        return $paths;
    }
}
