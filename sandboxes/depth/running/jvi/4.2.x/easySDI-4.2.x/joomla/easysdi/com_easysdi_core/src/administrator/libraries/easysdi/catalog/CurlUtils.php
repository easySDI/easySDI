<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CurlUtils
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset="UTF-8"', 'charset="UTF-8"'));
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

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

}
