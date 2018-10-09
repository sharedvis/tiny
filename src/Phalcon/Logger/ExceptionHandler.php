<?php

namespace Tiny\Phalcon\Logger;

/**
* Tiny exception handler
*/
class ExceptionHandler
{
    /**
     * Default exception handler for Tusk
     * @param  mixed $exception Mixed of exception object
     * @return void
     */
    public function exceptionHandler($exception)
    {
        if (env('SENTRY_ENABLE', 'false') == 'true')
        {
            \Phalcon\Di::getDefault()->get('sentry')->processLogs($exception);
        }
    }
}
