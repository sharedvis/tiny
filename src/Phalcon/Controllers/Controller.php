<?php
/**
 * Base controller for tiny phalcon app
 *
 * @author Rolies Deby <rolies106@gmail.com>
 * @version 0.1.0
 * @package Tiny
*/
namespace Tiny\Phalcon\Controllers;

use Phalcon\Mvc\Controller as PhalconController;

class Controller extends PhalconController
{
    /**
     * Translation function
     * @var object
     */
    public $t = null;

    /**
     * Initialize controller
     * @return void
     */
    public function init()
    {
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');
    }

    /**
     * Initialize controller
     * @return void
     */
    public function initialize()
    {
        $this->t = $this->getTranslation();
    }

    /**
     * Return json as default
     * @param  int      $code   HTTP Status code
     * @param  mixed    $data   Array of data or model object
     * @return object Response
     */
    public function returnJson($code, $data, $extra_data = [])
    {
        $return = ['status' => $code];
        $return['data'] = null;

        if (is_object($data))
        {
            if ($messages = $data->getMessages())
            {
                $error = [];

                foreach ($messages as $message) {
                    $fields = $message->getField();

                    if (is_array($fields))
                    {
                        foreach ($fields as $field) {
                            $error[$field] = $message->getMessage();
                        }
                    } else {

                        $error[$message->getField()] = $message->getMessage();
                    }
                }

                $return['data'] = ['errors' => $error];
            } else {
                $data_return = [];

                if (!empty($data)) {
                    foreach ($data as $key => $record) {
                        if (is_object($record))
                            $data_return[] = $record->toArray();
                        else
                            $data_return[$key] = $record;
                    }
                }

                $return['data'] = $data_return;
            }

        } else if (is_array($data)) {

            $return['data'] = $data;

            if (isset($return['data']))
                $return['data'] = array_merge($return['data'], $extra_data);
            else
                $return['data'] = $extra_data;
        }

        // Just send data without status
        $return = $return['data'];

        $this->response
             ->setStatusCode($code)
             ->sendHeaders()
             ->setContent(json_encode($return))
             ->setContentType('application/json', 'UTF-8');

        // $this->response->send();
    }

    /**
     * Forward to error page
     * @param  int      $code   HTTP Status code
     * @param  string   $code   HTTP Status code
     * @param  int  $code   HTTP Status code
     * @return mixed
     */
    public function showError($code, $message = null, $error = null)
    {
        $params = ["message" => $message, "error" => $error];

        if (is_object($message))
        {
            if (method_exists($message, 'getMessages'))
            {
                $error = [];

                $errors = $message->getMessages();

                if (!empty($errors))
                {
                    foreach ($errors as $error_message) {
                        $fields = $error_message->getField();

                        if (is_array($fields))
                        {
                            foreach ($fields as $field) {
                                $error[$field] = $error_message->getMessage();
                            }
                        } else {
                            $error[$error_message->getField()] = $error_message->getMessage();
                        }
                    }
                }

                $params = ['errors' => $error];
            }
        } else if (is_array($message)) {
            $params = $message;
        }

        switch ($code) {
            case 403:
            case 404:
            case 500:
                // if (!$this->dispatcher->wasForwarded()) {
                    $this->dispatcher->forward(
                        [
                            "controller" => "errors",
                            "action" => "show$code",
                            "params" => $params
                        ]
                    );

                    return false;
                // }
                break;

            default:
                // if (!$this->dispatcher->wasForwarded()) {
                    $this->dispatcher->forward(
                        [
                            "controller" => "errors",
                            "action" => "showError",
                            "params" => [
                                "code" => $code,
                                'params' => $params
                            ]
                        ]
                    );

                    return false;
                // }
                break;
        }
    }

    /**
     * Get translation array
     * @return object
     */
    public function getTranslation()
    {
        // Get language code from request
        $language = strtoupper($this->getDI()->get('request')->getHeader('Lang'));

        // Ask browser what is the best language
        if (empty($language))
            $language = $this->request->getBestLanguage();

        // Check if we have a translation file for that lang
        if (file_exists(APP_PATH . "/app/messages/" . $language . ".php"))
        {
            require APP_PATH . "/app/messages/" . $language . ".php";
        } else {
            // Fallback to some default
            require APP_PATH . "/app/messages/id-ID.php";
        }

        // Return a translation object
        return new \Phalcon\Translate\Adapter\NativeArray(
            array(
                "content" => $messages
            )
        );
    }

    /**
     * Get current user model
     * @return object
     */
    public function getCurrentUser()
    {
        $user_session = $this->getCurrentRedisSession();

        if (!empty($user_session))
            return $user_session;

        $user_session = $this->getRequestUser();

        if (!empty($user_session))
            return $user_session;

        return null;
    }

    /**
     * Get request user information that sent in header
     * @param boolean   $remove_prefix  Remove Users- prefix
     * @return array
     */
    public function getRequestUser($remove_prefix = true)
    {
        // All headers
        $headers = $this->request->getHeaders();
        $return = [];

        if (!empty($headers))
        {
            foreach ($headers as $key => $value)
            {
                // Get all headers with Users- prefix
                if (substr($key, 0, 6) === "Users-")
                {
                    $key = str_replace("-", "_", strtolower($key));

                    if ($remove_prefix)
                        $key = substr($key, 6, strlen($key));

                    $return[$key] = $value;
                }
            }
        }

        return (!empty($return)) ? (object) $return : null;
    }

    /**
     * Get current redis auth detail by access token
     * @return object   stdClass
     */
    public function getCurrentRedisSession()
    {
        $access_token = $this->getDI()->get('request')->getHeader('AccessToken');

        if (!empty($access_token))
        {
            // Get data from redis
            if ($this->getDI()->has('token'))
                $redis = $this->getDI()->get('token');
            else
                $redis = $this->getDI()->get('session');

            $data = $redis->get($access_token);

            return json_decode($data);
        }

        return false;
    }

    /**
     * Find out is current request is from internal app request
     * @return boolean
     */
    public function isInternalRequest()
    {
        return $this->request->getHeader('Is-Internal-Request');
    }

    /**
     * Get requestor app id
     * @return boolean
     */
    public function getRequestorAppId()
    {
        return $this->request->getHeader('Internal-Request-App-Id');
    }
}
