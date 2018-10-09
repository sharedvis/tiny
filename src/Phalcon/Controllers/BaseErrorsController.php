<?php
/**
 * Base error controller for tiny phalcon app
 *
 * @author Rolies Deby <rolies106@gmail.com>
 * @version 0.1.0
 * @package Tiny
*/
namespace Tiny\Phalcon\Controllers;

class BaseErrorsController extends Controller
{
    protected $_default_error_message = 'You just got lost in Brokeback Mountain. Good luck! :)';

    public function indexAction() {}

    public function show404Action($message = null, $error = null)
    {
        $return = ['message' => $this->t->_($this->_default_error_message)];

        if (!empty($message))
            $return['message'] = $message;

        if (!empty($error))
            $return['error'] = $error;

        return $this->returnJson(404, $return);
    }

    public function show403Action($message = null, $error = null)
    {
        $return = ['message' => $this->t->_('You can not access this page')];

        if (!empty($message))
            $return['message'] = $message;

        if (!empty($error))
            $return['error'] = $error;

        return $this->returnJson(403, $return);
    }

    public function show422Action($message = null, $error = null)
    {
        $return = ['message' => $this->t->_('Data validation is failed')];

        if (!empty($message))
            $return['message'] = $message;

        if (!empty($error))
            $return['error'] = $error;

        return $this->returnJson(422, $return);
    }

    public function show500Action($message = null, $error = null)
    {
        $return = ['message' => $this->t->_('Ooopss... we will fix this ASAP')];

        if (!empty($message))
            $return['message'] = $message;

        if (env('APP_ENV') == 'dev' || env('APP_ENV') == 'test')
            $return['error'] = $error;

        return $this->returnJson(500, $return);
    }

    public function showErrorAction($code = 404, $params = null)
    {
        return $this->returnJson($code, $params);
    }
}
