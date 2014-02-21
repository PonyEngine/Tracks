<?

	class Templater extends Zend_View_Abstract
	{
		protected $_path;
		protected $_engine;
		protected $_requestingDevice;
		public function __construct()
		{	$config=Zend_Registry::get('config');
			require_once($config->paths->smartyClass);
				
			//TEMPLATES
			$this->_engine= new Smarty();
			$registry = Zend_Registry::getInstance();
			if (isset($registry->requestingDevice)) {
				//Select Template based on device
				//$this->printDeviceData($registry->requestingDevice);
				$view=$this->viewChooser($registry->requestingDevice);	
			}else{
				$view='web';
			}
			$this->_engine->template_dir=$config->paths->templates->$view;
			$this->_engine->compile_dir=$config->paths->templates_c->$view;
			$this->_engine->plugins_dir=array($config->paths->templateplugins,'plugins');			
		}

        public function preCacheTemplates(){
            //Try Catch Block
            $theDir=$this->getEngine()->template_dir;
            $dh=opendir($theDir);
            $this->recurseDirCache($theDir,$dh,$dir=array());
            closedir($dh);
            //Consume File
        }

       public function recurseDirCache($theDir,$dh,$dir=array()){

            while(($file = readdir($dh)) !=false){
                $fullPath=$theDir.$file.'/';
                if(strlen($file)<3)continue;
                if (is_dir($fullPath)){
                    //Add To Array and dig deeper
                    $dir[]=$file;
                    $nh=opendir($fullPath);
                    $this->recurseDirCache($fullPath,$nh,$dir);
                    closedir($nh);
                }else{
                    if(preg_match('!\.tpl!',$file)){
                        $dirImplode=count($dir)>0?implode('/',$dir):'';
                        $fullFile=$dirImplode.'/'.$file;
                        $this->_engine->fetch(strtolower($fullFile));
                    }
                }
            } //while loop
       }

		public function printDeviceData($requestingDevice){
			echo '<ul>';
			echo '<li>ID:'.$requestingDevice->id.'</li>';
			echo '<li>Brand Name:'.$requestingDevice->getCapability("brand_name").'</li>';
			echo '<li>Model Name:'.$requestingDevice->getCapability("model_name").'</li>';
			echo '<li>Xhtml Preferred Markup:'.$requestingDevice->getCapability("preferred_markup").'</li>';
			echo '<li>Resolution Width:'.$requestingDevice->getCapability("resolution_width").'</li>';
			echo '<li>Resolution Height:'.$requestingDevice->getCapability("resolution_height").'</li>';
			echo '</ul>';
		}
		
		private function viewChooser($requestingDevice){
			
			switch($requestingDevice->getCapability("model_name")){
			 	case '3.0': //firefox
					$view='web';
				break;
				case 'iPhone':
					$view='iphone';
				break;
				
				default:
					$view='web';
				break;
				
			}
					
			return $view;
			
		}
		
		
		public function getEngine()
		{
			return $this->_engine;
		}
		
		public function __set($key,$val){
			$this->_engine->assign($key,$val);
		}
		
		public function __get($key){
			return $this->_engine->get_template_vars($key);
		}

		public function __isset($key){
			return $this->_engine->get_template_vars($key)!==null;
		}	
		
		public function __unset($key){
			return $this->_engine->clear_assign($key);
		}
		
		public function assign($spec,$value=null){
			if (is_array($spec)){
				$this->_engine->assign($spec);
				return;
			}
			$this->_engine->assign($spec,$value);
		}
		
		public function clearVars(){
			$this->_engine->clear_all_assign();
		}
		
		public function render($name){
			return $this->_engine->fetch(strtolower($name));
		}
		
		public function _run()
		{}
	
	}


?>