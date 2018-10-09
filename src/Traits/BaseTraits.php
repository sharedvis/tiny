<?php
/**
 * API base commands
 *
 * @package Tiny
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Traits;

use Unirest\Request;

use Rolies106\Hmac\Manager;
use Rolies106\Hmac\Adapters\HashHmac;

use Tiny\Traits\SessionTraits;
use Tiny\Traits\TokenTraits;

trait BaseTraits
{
    use SessionTraits, TokenTraits;

    /**
     * @var array A list of supported HTTP methods
     */
    protected $_http_methods = array('GET', 'POST', 'PUT', 'DELETE');

    /**
     * @var string Default path to API resource server
     */
    protected $_host = 'http://api.infomedia.store/';

    /**
     * Application ID
     * @var string
     */
    protected $_app_id;

    /**
     * Application Secret
     * @var string
     */
    protected $_app_secret;

    /**
     * HMac configuration
     * @var string
     */
    protected $_hmac_config;

    /**
     * Session name for UID
     * @var object
     */
    protected $_session_uid_name = 'TINY_UID';

    /**
     * Session name for AccessToken
     * @var object
     */
    protected $_session_access_token_name = 'TINY_ACCESS_TOKEN';

    /**
     * @var string Data request type
     */
    protected $_data_request_type;

    /**
     * @var object Current class instance
     */
    protected static $_instance;

    /**
     * DSL wrapper to make a GET request.
     *
     * @param   string  $uri        Target URL
     * @param   array   $args       Associative array of passed arguments
     * @param   array   $headers    List of passed headers
     * @return  array               Processed response
     */
    public function sendGet($uri, $args = array(), $headers = array(), $extra = array()) {
        return $this->_request($uri, $args, $headers, 'GET', $extra);
    }

    /**
     * DSL wrapper to make a POST request.
     *
     * @param   string  $uri        Target URL
     * @param   array   $args       Associative array of passed arguments
     * @param   array   $headers    List of passed headers
     * @param   array   $extra      Extra parameter
     * @return  array               Processed response
     */
    public function sendPost($uri, $args = array(), $headers = array(), $extra = array()) {
        return $this->_request($uri, $args, $headers, 'POST', $extra);
    }

    /**
     * DSL wrapper to make a PUT request.
     *
     * @param   string  $uri        Target URL
     * @param   array   $args       Associative array of passed arguments
     * @param   array   $headers    List of passed headers
     * @param   array   $extra      Extra parameter
     * @return  array               Processed response
     */
    public function sendPut($uri, $args = array(), $headers = array(), $extra = array()) {
        return $this->_request($uri, $args, $headers, 'PUT', $extra);
    }

    /**
     * DSL wrapper to make a DELETE request.
     *
     * @param   string  $uri        Target URL
     * @param   array   $args       Associative array of passed arguments
     * @param   array   $headers    List of passed headers
     * @return  array               Processed response
     */
    public function sendDelete($uri, $args = array(), $headers = array()) {
        return $this->_request($uri, $args, $headers, 'DELETE');
    }

    /**
     * Generate full endpoint url based on host and endpoint
     *
     * @param string    $extra  Extra string that will append in the end of url
     * @return string Full endpoint url
     */
    public function getEndpoint($endpoint)
    {
        return rtrim($this->_host, '/') . '/' . trim($endpoint, '/');
    }

    /**
     * Save user_id to session for reuse later
     *
     * @param int $uid User id that returned from API
     */
    public function setUserId($uid)
    {
        $this->setSession($this->_session_uid_name, $uid);

        return $this;
    }

    /**
     * Get user_id from session
     *
     * @return int
     */
    public function getUserId()
    {
        if ($uid = $this->getSession($this->_session_uid_name)) {
            return $uid;
        }

        return false;
    }

    /**
     * Save access_token to session for reuse later
     *
     * @param int $access_token access_token that returned from API
     */
    public function setAccessToken($access_token)
    {
        $this->setSession($this->_session_access_token_name, $access_token);

        return $this;
    }

    /**
     * Get access_token from session
     *
     * @return int
     */
    public function getAccessToken()
    {
        if ($access_token = $this->getSession($this->_session_access_token_name)) {
            return $access_token;
        }

        return false;
    }

    /**
     * Forward all user detail that sent
     * @return void
     */
    public function forwardUserDetailRequest()
    {
        // All headers
        $headers = $this->getAllHeaders();
        $return = [];

        if (!empty($headers))
        {
            foreach ($headers as $key => $value)
            {
                // Get all headers with Users- prefix
                if (substr($key, 0, 6) === "Users-")
                {
                    $return[$key] = $value;
                }
            }
        }

        return $return;
    }

    /**
     * Generating HMAC headers by current request
     *
     * @param array     $args   Data that will be combined as hmac
     * @return array
     */
    public function generateHmac($args)
    {
        $manager = new Manager(new HashHmac);

        // Set the hmac configuration
        $manager->config($this->_hmac_config);

        // Set hmac key
        $manager->key($this->_app_secret);

        // Set data
        if (is_array($args)) {
            $manager->data(http_build_query($args));
        } else {
            $manager->data($args);
        }

        // Set time to be checked
        $manager->time(time());

        try {
            $manager->encode();
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        $hmac = $manager->toArray();

        return [
            'App-ID' => $this->_app_id,
            'Time' => $hmac['time'],
            'Hmac' => $hmac['hmac']
        ];
    }

    /**
     * Set data request type
     * @param string    $type   Data request type, for now its only support 'json'
     */
    public function setDataRequestType($type)
    {
        if (in_array($type, ['json']))
            $this->_data_request_type = $type;

        return $this;
    }

    /**
     * DSL wrapper to make a HTTP request based on supported HTTP methods.
     *
     * @param   string  $uri        Target URL
     * @param   array   $args       Associative array of passed arguments
     * @param   array   $headers    List of passed headers
     * @param   string  $method     HTTP method
     * @return  array               Processed response
     */
    protected function _request($uri, $args, $headers = array(), $method = 'GET', $extra = array())
    {
        $uri = rtrim($uri, '/');

        $url = $this->getEndpoint($uri);

        // Adding protocol
        $str_protocol = substr(trim($url), 0, strpos($url, ":"));
        if (!in_array($str_protocol, ['http', 'https'])){
            $url = trim($str_protocol.'://' . $uri, '/');
        }

        // Generating hmac headers
        if ($hmac = $this->generateHmac($args)) {
            $headers = array_merge($headers, $hmac);
        }

        // Include UserID if exists
        if ($uid = $this->getUserId()) {
            $headers = array_merge($headers, ['UserID' => $uid]);
        }

        // For AccessToken
        if ($access_token = $this->getAccessToken()) {
            $headers = array_merge($headers, ['AccessToken' => $access_token]);
        }

        // For user detail that sent in headers
        // ONLY FOR INTERNAL APPLICATION
        // $headers = array_merge($this->forwardUserDetailRequest(), $headers);

        // Processing files upload
        switch ($method) {
            case 'POST':
            case 'PUT':
                // Alter all _FILES variable
                $args = $this->_alterFilesUpload($args);

                break;
        }

        // Add information from which page request has been made
        if (isset($_SERVER['REQUEST_URI']))
        {
            $url_request_parts = explode("?", $_SERVER['REQUEST_URI']);
            $headers['Request-Uri-Origin'] = (isset($url_request_parts[0])) ? $url_request_parts[0] : $_SERVER['REQUEST_URI'];
        }

        switch ($this->_data_request_type) {
            case 'json':
                $headers['Content-Type'] = 'application/json';
                break;

            default:
                # nothing to do
                break;
        }

        // Processing the request
        switch ($method) {
            case 'GET':
                if (!empty($args)) {
                    $url = $url . '?' . http_build_query($args);
                    $uri = $uri . '?' . http_build_query($args);
                }

                // Fix URI must using slash at the beginning of URL
                $uri = ltrim($uri, '/');
                $uri = '/' . $uri;

                // Generate hmac based on requestUri because args is not sent inside request body
                if ($hmac = $this->generateHmac($uri)) {
                    $headers = array_merge($headers, $hmac);
                }
                $request = Request::get($url, $headers, $extra);
                break;
            case 'POST':
                $request = Request::post($url, $headers, $this->generateDataRequest($method, $args), $extra);
                break;
            case 'PUT':
                $request = Request::put($url, $headers, $this->generateDataRequest($method, $args), $extra);
                break;
            case 'DELETE':
                if (!empty($args)) {
                    $url = $url . '?' . http_build_query($args);
                    $uri = $uri . '?' . http_build_query($args);
                }

                // Fix URI must using slash at the beginning of URL
                $uri = ltrim($uri, '/');
                $uri = '/' . $uri;

                // Generate hmac based on requestUri because args is not sent inside request body
                if ($hmac = $this->generateHmac($uri)) {
                    $headers = array_merge($headers, $hmac);
                }
                $request = Request::delete($url, $headers);
                break;
            default:
                throw new InvalidArgumentException(sprintf(
                    'Unsupported %s HTTP method. It should match one of %s keywords.',
                    $method, implode(', ', $this->_http_methods)
                ));
        }

        $body = $request->raw_body;
        // $info['headers'] = $request->headers;
        $info['status_code'] = $request->code;
        $info['success'] = (($request->code >= 200) && ($request->code < 300));
        // $info['redirects'] = $request->redirects;
        $info['url'] = $url;

        return $this->_response($body, $info, $method);
    }

    /**
     * Get all request headers
     * @return array
     */
    protected function getAllHeaders()
    {
        if(!function_exists('getallheaders'))
        {
            foreach($_SERVER as $name => $value)
            {
                if(substr($name, 0, 5) == 'HTTP_')
                {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }

            return $headers;
        }

        return getallheaders();
    }

    /**
     * Generate data request before sending it
     * @param  string $method   Request method
     * @param  array  $data     Collection of data that will be sent
     * @return string           Either json string or HTTP query string
     */
    protected function generateDataRequest($method, $data = [])
    {
        switch ($method) {
            case 'POST':
            case 'PUT':
                switch ($this->_data_request_type) {
                    case 'json':
                        if (is_array($data))
                            return json_encode($data);
                        else
                            return $data;
                        break;

                    default:
                        if (is_array($data) && !$this->_is_upload)
                            return http_build_query($data);
                        else
                            return $data;
                        break;
                }
                break;

            default:
                return http_build_query($data);
                break;
        }
    }

    /**
     * Typically process response returned from API request.
     *
     * @param   string  $body   Body of response returned from API request
     * @param   array   $info   Headers of response returned from API request
     * @return  array           Processed response
     */
    protected function _response($body, $info, $method) {
        $body_decode = json_decode($body);

        // Return decoded body instead of json string
        if (!json_last_error())
            $body = $body_decode;

        return array(
            $body, $info, $method
        );
    }
}
