<?php

abstract class PhysicalService {
	public $id;
	public $name;
	public $url;
	public $rawXml;
	protected $compliance;
	protected $connector;
	protected $xmlCapabilities;
	protected $user;
	protected $password;
	
	abstract public function populate();
	abstract public function sortLists();
	abstract public function loadData($data);
	abstract public function setLayerAsConfigured($layerList);
	
	public function __construct ($id, $url, $connector) {
		$this->id = $id;
		$this->url = $url;
		$this->connector = $connector;
	}
	
	/**
	 * Request the Capabilities of the server and store them.
	 * 
	 * @param string $rawXML : optional xmlString that contains the capabilities of the server (no request is made if that param is set)
	 * 
	 * @return bool : true on success, false on error
	 */
	public function getCapabilities ($rawXML = null) {
		$xmlString = $rawXML;
		if (!isset($rawXML)) {
			$completeUrl = $this->url . "?REQUEST=GetCapabilities&SERVICE=".$this->connector;
			if(isset($this->compliance)){
				$completeUrl .= "&version=".$this->compliance;
			}
			
			$session 	= curl_init($completeUrl);
			if (!empty($this->user)  && !empty($this->password)) {
				curl_setopt($session, CURLOPT_HTTPHEADER, Array('Authorization: Basic '.base64_encode($this->user.':'.$this->password)));
			}
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			$xmlString = curl_exec($session);
			$http_status = curl_getinfo($session, CURLINFO_HTTP_CODE);
			curl_close($session);
			
			//HTTP status error
			if($http_status != '200') {
				echo $completeUrl;
				return false;
			}
		}
		
		$xmlCapa = simplexml_load_string($xmlString);
		
		$namespaces = $xmlCapa->getNamespaces(true);
		foreach ($namespaces as $key => $value) {
			if($key == '') {
				$xmlCapa->registerXPathNamespace ("dflt",$value);
			}
			else {
				$xmlCapa->registerXPathNamespace ($key,$value);
			}
		}
		
		$this->xmlCapabilities = $xmlCapa;
		return true;
	}
	
}