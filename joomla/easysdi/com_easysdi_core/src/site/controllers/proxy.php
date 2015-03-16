<?php

/**
 * @version     4.3.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Resource controller class.
 */
class Easysdi_coreControllerProxy extends Easysdi_coreController{
    
    private $method;
    private $post = array();
    private $get = array();
    private $cookies = array();
    
    private $url;
    private $option;
    protected $task;
    private $response;
    
    public function run(){
        
        $jInput = JFactory::getApplication()->input;
        
        $this->method = $jInput->getMethod();
        
        $this->getGETParameters($jInput);
        $this->getPOSTParameters($jInput);
        $this->getCookies($jInput);
        
        $this->prepareURL();
        
        $this->sendCURLRequest();
        
        $this->sendResponse();
    }
    
    private function getGETParameters(JInput $jInput){
        $data = $jInput->get->getArray();
        
        if(isset($data['option'])){
            $this->option = $data['option'];
            unset($data['option']);
        }
        
        if(isset($data['task'])){
            $this->task = $data['task'];
            unset($data['task']);
        }
        
        if(isset($data['url'])){
            $this->url = $data['url'];
            unset($data['url']);
        }
        
        if(isset($data['method'])){
            $this->method = $data['method'];
            unset($data['method']);
        }
        
        if(count($data)){
            $query = array();
            foreach($data as $key => $value){
                array_push($query, $key.'='.$value);
            }
            $this->get = implode('&', $query);
        }
    }
    
    private function getPOSTParameters(JInput $jInput){
        $data = $jInput->post->getArray();
        
        if(empty($data)){
            if($this->method == 'POST'){
                $this->post = file_get_contents("php://input");
            }
            return;
        }
        
        if(isset($data['url'])){
            $this->url = $data['url'];
            unset($data['url']);
        }
        
        if(isset($data['method'])){
            $this->method = $data['method'];
            unset($data['method']);
        }
        
        if(count($data)){
            $query = array();
            foreach($data as $key => $value){
                array_push($query, $key.'='.$value);
            }
            $this->post = implode('&', $query);
        }
    }
    
    private function getCookies(JInput $jInput){
        $data = array();
        foreach($jInput->cookie->getArray() as $cookieName => $cookieValue){
            array_push($data, $cookieName.'='.$cookieValue);
        }
        $this->cookies = implode('; ', $data);
    }
    
    private function prepareURL(){
        if(count($this->get)){
            $this->url .= '?'.$this->get;
        }
    }
    
    private function sendCURLRequest(){
        $ch = curl_init($this->url);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset="UTF-8"', 'charset="UTF-8"','Expect:'));
        
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        
        switch($this->method){
            case 'POST':
                $this->sendPOSTCURLRequest($ch);
                break;
            
            case 'GET':
            default:
                $this->sendGETCURLRequest($ch);
        }
        
        $response = new stdClass();
        $response->content = curl_exec($ch);
        $response->data = curl_getinfo($ch);
        $this->response = $response;
        
        curl_close($ch);
    }
    
    private function sendGETCURLRequest(&$ch){
        curl_setopt($ch, CURLOPT_POST, 0);
    }
    
    private function sendPOSTCURLRequest(&$ch){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post);
    }
    
    private function sendResponse(){
        // if no content type defined, set as text/plain
        if($this->response->data['content_type'] == ''){
            $this->response->data['content_type'] = 'text/plain';
        }
        
        // if no charset defined, force utf-8
        if(strpos($this->response->data['content_type'], 'charset') == false){
            list($ct, $trash) = explode(';', $this->response->data['content_type']);
            $this->response->data['content_type'] = trim($ct).'; charset=utf-8';
        }
        
        // set headers then output response
        header('Content-Type: '.$this->response->data['content_type']);
        echo trim($this->response->content);
        die();
    }
}