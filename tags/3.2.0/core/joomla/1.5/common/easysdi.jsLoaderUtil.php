
<?php 
jimport('joomla.error.log');

class JSLOADER_UTIL  {


	private static $propArray =null;
	private static $log = null;

	private static $jsloader_Instance =null;

	private function __construct() {
		
		$exists = file_exists(JPATH_ADMINISTRATOR.DS."components".DS."com_easysdi_core".DS."common".DS."jsconfig.json");		
		$prop = trim(file_get_contents(JPATH_ADMINISTRATOR.DS."components".DS."com_easysdi_core".DS."common".DS."jsconfig.json"));
		self::$propArray=json_decode($prop,true);
		self::$log = &JLog::getInstance();
		

	}
	public static function getInstance()
	{
		if (!self::$jsloader_Instance)
		{
			self::$jsloader_Instance = new JSLOADER_UTIL();
		}

		return self::$jsloader_Instance;
	}

	public function getPath($component, $lib, $relPath="" ){
		try{
			$version  = self::$propArray[trim($component)][trim($lib)];
			return  "administrator/components/com_easysdi_core/common/".$lib."/".$version."/".$relPath;
			
		}catch(Exception $e){
			
			$log->addEntry(array('comment' => $e->getTraceAsString(), 'status' => 500));
			return "";
		}
	}


}
?>