<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Curl helper.
 */
class CurlHelper {

    private $simplified = false;
    private $authentication = false;
    private $ch = null;
    private $jInput = null;
    private $url = null;
    private $headers = array('Content-Type: text/xml; charset="UTF-8"', 'charset="UTF-8"', 'Expect:');
    private $cookies = null;
    private $method = 'GET';
    private $protocol = false;
    private $get = null;
    private $post = null;
    private $filename = null;
    private $returnonerror = false;
    private $contentType = null;
    private $headersAlreadySent = false;

    function __construct($returnonerror = false) {
        //Caller may wants to perform specific action on curl call error
        $this->returnonerror = $returnonerror;
    }

    /**
     * head - performs a HEAD Request
     */
    public function head(JInput $jInput) {
        $this->jInput = $jInput;
        $this->method = 'HEAD';

        $this->getHEADParameters();
        $this->getCookies();

        return $this->perform();
    }

    /**
     * URLChecker - similar to head but return an simplified object
     */
    public function URLChecker() {
        $this->simplified = true;
        $this->head(JFactory::getApplication()->input);
    }

    /**
     * get - performs a GET Request
     */
    public function get($input, $close = true) {
        $this->close = $close;
        $this->method = 'GET';

        if (is_array($input)) {
            $this->getArrayParameters($input);
        } else {
            $this->jInput = $input;
            $this->getGETParameters();
        }
        $this->perform();
    }

    /**
     * post - performs a POST Request
     */
    public function post(JInput $jInput, $close = true) {
        $this->close = $close;
        $this->method = 'POST';

        $this->getPOSTParameters();
        $this->getCookies();

        $this->perform();
    }

    /**
     * put - performs a PUT Request
     */
    public function put(JInput $jInput) {
        /**
         * @todo develop this method
         */
        return;
        /*  $this->method = 'PUT';

          $this->getPUTParameters();
          $this->getCookies();

          $this->perform(); */
    }

    /**
     * delete - performs a DELETE Request
     */
    public function delete(JInput $jInput) {
        /**
         * @todo develop this method
         */
        return;
        /*   $this->method = 'DELETE';

          $this->getDELETEParameters();
          $this->getCookies();

          $this->perform(); */
    }

    /**
     * run - perfoms a Request
     * Request type is predicted from the original request and may be overriden by the method property
     */
    public function run(JInput $jInput, $close = true) {
        $this->close = $close;
        $this->jInput = $jInput;
        $this->method = $this->jInput->getMethod();

        $this->getGETParameters();
        $this->getPOSTParameters();

        $this->perform();
    }

    private function perform() {
        $this->getCookies();
        $this->init();

        switch ($this->method) {
            case 'HEAD':
                if ($this->protocol === 'sftp') {
                    curl_setopt($this->ch, CURLOPT_WRITEFUNCTION, function($ch, $data) {
                        return -1;
                    });
                    curl_setopt($this->ch, CURLOPT_HEADER, false);
                    curl_setopt($this->ch, CURLOPT_NOBODY, false);
                } else {
                    curl_setopt($this->ch, CURLOPT_HEADER, true);
                    curl_setopt($this->ch, CURLOPT_NOBODY, true);
                }
                break;
            case 'GET':
                curl_setopt($this->ch, CURLOPT_POST, 0);
                break;
            case 'POST':
                curl_setopt($this->ch, CURLOPT_POST, 1);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->post);
                break;
            case 'PUT':
                break;
            case 'DELETE':
                break;
        }
        if ($this->simplified) {
            $this->sendSimplified();
        } else {
            $this->send();
        }
    }

    private function initSSL() {
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT);
    }

    private function initFTP() {
        curl_setopt($this->ch, CURLOPT_FTP_CREATE_MISSING_DIRS, false);
        curl_setopt($this->ch, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_DEFAULT);
    }

    private function initProtocol() {
        $this->protocol = parse_url($this->url, PHP_URL_SCHEME);
        if (!$this->protocol) {
            $this->protocol = 'http';
        }

        switch (strtoupper($this->protocol)) {
            case 'HTTP':
                curl_setopt($this->ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP);
                break;
            case 'HTTPS':
                $this->initSSL();
                curl_setopt($this->ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
                break;
            case 'FTP':
                $this->initFTP();
                curl_setopt($this->ch, CURLOPT_PROTOCOLS, CURLPROTO_FTP);
                break;
            case 'FTPS':
                $this->initFTP();
                $this->initSSL();
                curl_setopt($this->ch, CURLOPT_PROTOCOLS, CURLPROTO_FTPS);
                break;
            case 'SFTP':
                $this->initFTP();
                curl_setopt($this->ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
                break;
        }
    }

    private function initURL() {
        if (strlen($this->get)) {
            $this->url .= '?' . $this->get;
        }
        $this->ch = curl_init($this->url);
    }

    private function init() {
        $this->initURL();

        $this->initProtocol();

        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);

        if (!is_null($this->cookies)) {
            curl_setopt($this->ch, CURLOPT_COOKIE, $this->cookies);
        }

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_ENCODING, "");

        // Authentication
        if (is_array($this->authentication)) {
            curl_setopt($this->ch, CURLOPT_UNRESTRICTED_AUTH, true);
            $httpAuth = isset($this->authentication['authtype']) ? constant('CURLAUTH_' . strtoupper($this->authentication['authtype'])) : CURLAUTH_ANY;
            curl_setopt($this->ch, CURLOPT_HTTPAUTH, $httpAuth);
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->authentication['user'] . ':' . $this->authentication['password']);
        }
    }

    private function sendSimplified() {
        header("Access-Control-Allow-Origin: *"); // AJOUT Damien

        $content = trim(curl_exec($this->ch));
        $data = curl_getinfo($this->ch);

        curl_close($this->ch);

        $response = array(
            'success' => false,
            'code' => 0,
            'message' => 'An unpredictable error occured',
            'content' => array(),
            'data' => $data
        );

        if (strpos($this->protocol, 'http') !== false) { // http/https case
            foreach (preg_split("/((\r?\n)|(\r\n?)|(&#13;?&#10;)|(&#13;&#10;?))/", $content) as $line) {
                array_push($response['content'], $line);
                if (preg_match('/HTTP.*(\d{3}) (.+)/', $line, $matches)) {
                    $response['success'] = ((int) $matches[1] >= 200 && (int) $matches[1] < 400) ? true : false;
                    $response['code'] = (int) $matches[1];
                    $response['message'] = $matches[2];
                    // in case of redirection, we have to take the last response
                    // Then we don't break the loop !
                }
            }
        } elseif ($this->protocol === 'sftp') {
            $response['success'] = $data['download_content_length'] == 0 ? false : true;
            $response['message'] = $content;
        } elseif (strpos($this->protocol, 'ftp') !== false) { // ftp/ftps cases
            $response['success'] = ((int) $data['http_code'] >= 200 && (int) $data['http_code'] < 400 && $data['download_content_length'] > -1) ? true : false;
            $response['code'] = (int) $data['http_code'];
            $response['message'] = $content;
        }

        header('Content-type: application/json');
        echo json_encode($response);
        die();
    }

    private function send() {

        //Set callbakc functions last (dependency on other params), except for sftp head
        //if ($this->protocol !== 'sftp' && $this->method !== 'HEAD') {
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, array($this, 'curlCB_readHeader'));
            curl_setopt($this->ch, CURLOPT_WRITEFUNCTION, array($this, 'curlCB_readBody'));
        //}

        if (!curl_exec($this->ch)) {
            //If the caller wants to handle the error, we return false
            if ($this->returnonerror) {
                return false;
            }
            //Else, we send an HTTP error to the client
            header('HTTP/1.1 502 Bad Gateway', true, 502);
            echo JText::_('COM_EASYSDI_CORE_CURL_502_MESSAGE');
            curl_close($this->ch);
            die();
        }

        curl_close($this->ch);

        //echo $content;
        die();
    }

    /*
     * Callback function for CURL header read : CURLOPT_HEADERFUNCTION
     * Return the number of bytes actually written or return -1 to signal error to
     * the library (it will  cause it to abort the transfer with a CURLE_WRITE_ERROR
     * return code).
     */

    private function curlCB_readHeader($ch, $string) {

        //process header if protocol is HTTP(S)
        if (strpos($this->protocol, 'http') !== false) {

            //get curl infos
            $infos = curl_getinfo($ch);

            //check HTTP code, force fail in case of error code
            if ($infos['http_code'] < 200 || $infos['http_code'] >= 400) {
                return -1;
            }

            //update content_type if any
            if (isset($infos['content_type'])) {
                $this->contentType = $infos['content_type'];
            }
        }

        return strlen($string);
    }

    /*
     * Callback function for CURL body write : CURLOPT_WRITEFUNCTION
     * Return the number of bytes actually taken care of.  If that amount differs 
     * from the amount passed to your function, it'll signal an error to the 
     * library and it will abort the transfer and return CURLE_WRITE_ERROR.
     */

    private function curlCB_readBody($ch, $string) {
        //at first call, sent headers to client
        if (!$this->headersAlreadySent) {
            $this->sendHeaders();
        }

        //dump content to client
        echo $string;
        @ob_flush();  // flush output
        @flush();

        return strlen($string);
    }

    /**
     * Send HTTP headers to client (if set) :
     * Content-Type, Content-Length and Content-Disposition
     */
    private function sendHeaders() {

        //if there is no content type, and protocol is not HTTP(S) force 'application/octetstream'
        //to avoid having the browser wanting to interpret a potentially binary file
        if (!isset($this->contentType) && strpos($this->protocol, 'http') == false) {
            $this->contentType = 'application/octetstream';
        }

        //if we have a content type, set it
        if (isset($this->contentType)) {
            header('Content-Type: ' . $this->contentType);
        }

        //Optional parameters to overwrite header filename, force attachement disposition
        if (isset($this->filename)) {
            header('Content-Disposition: attachement; filename="' . $this->filename . '"');
        }

        $this->headersAlreadySent = true;
        
        //close joomla session to unblock navigation
        //we don't have to read/write session infos from now
        $session = JFactory::getSession();
        $session->close();
    }

    /**
     * Gets the cookies from the request
     */
    private function getCookies() {
        if (!isset($this->jInput)) {
            return;
        }
        $data = array();
        foreach ($this->jInput->cookie->getArray() as $cookieName => $cookieValue) {
            array_push($data, $cookieName . '=' . $cookieValue);
        }
        $this->cookies = implode('; ', $data);
    }

    private function getParameters(&$data) {
        if (isset($data['option'])) {
            unset($data['option']);
        }

        if (isset($data['task'])) {
            unset($data['task']);
        }

        if (isset($data['url'])) {
            $this->url = $data['url'];
            unset($data['url']);
        }

        if (isset($data['user'])) {
            $this->authentication = array('user' => $data['user']);
            unset($data['user']);
        }

        if (isset($data['password'])) {
            $this->authentication['password'] = $data['password'];
            unset($data['password']);
        }

        if (is_array($this->authentication) && isset($data['authtype'])) {
            $this->authentication['authtype'] = $data['authtype'];
        }

        if (isset($data['method'])) {
            $this->method = $data['method'];
            unset($data['method']);
        }

        if (isset($data['filename'])) {
            $this->filename = $data['filename'];
            unset($data['filename']);
        }
    }

    private function getArrayParameters($data) {
        $this->getParameters($data);
        $this->buildGETParameters($data);
    }

    private function getGETParameters() {
        $data = $this->jInput->get->getArray();
        $this->getParameters($data);
        $this->buildGETParameters($data);
    }

    private function buildGETParameters($data) {
        if (count($data)) {
            $query = array();
            foreach ($data as $key => $value) {
                array_push($query, $key . '=' . $value);
            }
            if (count($query)) {
                $this->get = implode('&', $query);
            }
        }
    }

    private function getPOSTParameters() {
        $data = $this->jInput->post->getArray();

        if (empty($data)) {
            if ($this->method == 'POST') {
                $this->post = file_get_contents("php://input");
            }
            return;
        }

        $this->getParameters($data);

        if (count($data)) {
            $query = array();
            foreach ($data as $key => $value) {
                array_push($query, $key . '=' . $value);
            }
            if (count($query)) {
                $this->post = implode('&', $query);
            }
        }
    }

    private function getHEADParameters() {
        $data = $this->jInput->getArray();

        $this->getParameters($data);

        if (count($data)) {
            $query = array();
            foreach ($data as $key => $value) {
                if (!is_null($value)) {
                    array_push($query, $key . '=' . $value);
                }
            }
            if (count($query)) {
                $this->get = implode('&', $query);
            }
        }
    }

}
