<?php

namespace Tiny\Phalcon\Libraries;

use \Phalcon\Http\Request;

/**
* Phalcon Router extend
*/
class TinyRequest extends Request
{
    /**
     * Extending request get put so it could override value
     *
     * @param  string  $name          Key name
     * @param  string  $filters       Variable filter validation
     * @param  mixed   $defaultValue  Default value if empty
     * @param  boolean $notAllowEmpty Allow value empty
     * @param  boolean $noRecursive   No recursive
     * @return mixed
     */
    public function getPut($name = null, $filters = null, $defaultValue = null, $notAllowEmpty = false, $noRecursive = false)
    {
        // $accept = $this->getBestAccept();
        $contentType = $this->getHeader('Content-Type');

        switch ($contentType) {
            case "application/json":
                $postJson = ($this->getJsonRawBody(true)) ?: [];

                return $this->getHelper($postJson, $name, $filters, $defaultValue, $notAllowEmpty, $noRecursive);
                break;

            default:
                $original = parent::getPut($name, $filters, $defaultValue, $notAllowEmpty, $noRecursive);

                if (!empty($original))
                    return $original;

                break;
        }
    }

    /**
     * Extending request get post so it could override value
     *
     * @param  string  $name          Key name
     * @param  string  $filters       Variable filter validation
     * @param  mixed   $defaultValue  Default value if empty
     * @param  boolean $notAllowEmpty Allow value empty
     * @param  boolean $noRecursive   No recursive
     * @return mixed
     */
    public function getPost($name = null, $filters = null, $defaultValue = null, $notAllowEmpty = false, $noRecursive = false)
    {
        // $accept = $this->getBestAccept();
        $contentType = $this->getHeader('Content-Type');

        switch ($contentType) {
            case "application/json":
                $postJson = ($this->getJsonRawBody(true)) ?: [];

                return $this->getHelper($postJson, $name, $filters, $defaultValue, $notAllowEmpty, $noRecursive);
                break;

            default:
                $original = parent::getPost($name, $filters, $defaultValue, $notAllowEmpty, $noRecursive);

                if (!empty($original) || is_string($original))
                    return $original;
                break;
        }
    }
}
