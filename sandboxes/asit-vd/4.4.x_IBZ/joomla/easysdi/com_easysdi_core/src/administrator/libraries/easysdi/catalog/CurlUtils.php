<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

class CurlUtils {

    const POST = 'POST';
    const GET = 'GET';

    /**
     * 
     * @param string $type Method of the request POST, GET
     * @param string $url 
     * @param string $xmlBody Rquest body
     * @return string response
     */
    public static function CURLRequest($type, $url, $username = '', $password = '', $xmlBody = '') {
        // Get COOKIE as key=value
        $cookiesList = array();
        foreach ($_COOKIE as $key => $val) {
            $cookiesList[] = $key . "=" . $val;
        }
        $cookies = implode(";", $cookiesList);

        $ch = curl_init($url);
        // Configuration
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
        // cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
        // cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
        // This allows to work around servers that do not support that header.
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset="UTF-8"', 'charset="UTF-8"','Expect:'));
        // We're emptying the 'Expect' header, saying to the server: please accept the body right now.
        
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");

        // Specific POST
        if ($type == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            //curl_setopt($ch, CURLOPT_MUTE, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmlBody");
        }
        // Specific GET
        else if ($type == "GET") {
            curl_setopt($ch, CURLOPT_POST, 0);
        }

        //User authentication
        if (empty($username) && empty($password)) {
            $params = JComponentHelper::getParams('com_easysdi_contact');
            $serviceaccount_id = $params->get('serviceaccount');
            $juser = JFactory::getUser($serviceaccount_id);

            $username = $juser->username;
            $password = $juser->password;
        }

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);

        curl_close($ch);

        return $output;
    }

}
