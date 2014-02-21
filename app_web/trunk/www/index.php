<?
set_time_limit(0);
define('ZFW_VERSION','1.10.8');
define ('LIB_PREFIX','../library');
define ('APP_PATH',realpath(dirname(__FILE__).'/../'));

$paths = array(APP_PATH,
    APP_PATH.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'models',
    APP_PATH.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'include',
    APP_PATH.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'templater',
    APP_PATH.DIRECTORY_SEPARATOR.LIB_PREFIX,
    APP_PATH.DIRECTORY_SEPARATOR.LIB_PREFIX.DIRECTORY_SEPARATOR.'ZendFramework-'.ZFW_VERSION.DIRECTORY_SEPARATOR.'library',
    get_include_path());

set_include_path(implode(PATH_SEPARATOR,$paths));
require_once ('Zend/Loader/Autoloader.php');

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

//Setup the application logger
$logger= new Zend_Log(new Zend_Log_Writer_Null());

try{

    $config=new Zend_Config_Ini('../application/config.ini','dev');

    Zend_Registry::set('config',$config);

    /* LOGGING */
    $logger1= new Zend_Log(new SavCoLogger($config->logging->file->everytime));
    Zend_Registry::set('logEverytime',$logger1);

    $logger2= new Zend_Log(new SavCoLogger($config->logging->file->event));
    Zend_Registry::set('logEvent',$logger2);



    try{
        //$writer= new EmailLogger($config->logging->email);//_ test this$_SERVER['SERVER_ADMIN']
        //$writer->addFilter(new Zend_Log_Filter_Priority(Zend_Log::CRIT));
        //$logger2->addWriter($writer);
    }catch(exception $ex){
        //invalid email address
    }

    //DEVICE AWARENESS
    /*require_once($config->paths->wurfl.'WURFLManagerFactory.php');
    $wurflConfigFile = $config->paths->resources. 'wurfl-config.xml';
    $wurflConfig = new WURFL_Configuration_XmlConfig($wurflConfigFile);
    $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
    $wurflManager = $wurflManagerFactory->create();
    $requestingDevice = $wurflManager->getDeviceForHttpRequest($_SERVER);
    Zend_Registry::set('requestingDevice',$requestingDevice);
    */


    /*BARCODE */
    //Zend_Barcode_Object_ObjectAbstract::setBarcodeFont(); //maybe move to bootstrap

    /*SET DATABASE */
    $db=Zend_Db::factory($config->database);
    $db->getConnection();
    Zend_Db_Table::setDefaultAdapter($db);
    Zend_Registry::set('db',$db);

    $auth =Zend_Auth::getInstance();
    $auth->setStorage(new Zend_Auth_Storage_Session());


    //Handle the user request
    //require_once($config->paths->plugins.'/CustomControllerAclManager.php');
    $frontController = Zend_Controller_Front::getInstance();
    $frontController->setControllerDirectory($config->paths->controllers);

    /* PLUGINS */
    $frontController->registerPlugin(new CustomControllerAclManager($auth));


    //VANITY ROUTES
    //Bets  http://tracks.ponyengine.com/@nfl/1

    $socialShareRoute = new Zend_Controller_Router_Route_Regex('^\@([A-Za-z][A-Za-z0-9\.]+)(?:([/][a-zA-Z0-9\'\.]+))(?:([/][a-zA-Z0-9\.]+))?',
        array( 'controller' => 'socialcanvas',
            'action'     =>'index'),
        array(
            1 => 'objecttype_name',
            2 => 'ubid',
            3 => 'gamestate',
        )
    );

    /* $socialShareRoute = new Zend_Controller_Router_Route_Regex('^\@([A-Za-z][A-Za-z0-9\.]+)?',
         array( 'controller' => 'socialcanvas',
               'action'     =>'index'),
         array(
             1 => 'brand'
         )
     );*/


    $frontController->getRouter()->addRoute('socialcanvas', $socialShareRoute);

    /* HANDLES THE USERS REQUEST*/
    //setup view render for our templates based on Device Awarness
    $vr= new Zend_Controller_Action_Helper_ViewRenderer();
    $vr->setView(new Templater());
    $vr->setViewSuffix('tpl');
    Zend_Controller_Action_HelperBroker::addHelper($vr);


    //DEBUGGING DEV NEEDS
    $frontController->throwExceptions($config->debug->exceptionStatus);

    //Disable Strict Standards
    if(!$config->debug->showStrict){
        ini_set('display_errors', '0');
    }

    $new_size = '6M';
    ini_set('upload_max_filesize',$new_size);

    //Dispatching
    $frontController->dispatch();
}catch (exception $ex){
    echo $ex->getMessage();
    /*if ($config->debug->exceptionStatus){
        echo $ex->getMessage();
        exit;
    }else{
        $logger->emerg($ex->getMessage());
        header('Location:http://boni.ponyengine.com/maintenance');
        exit;
    }*/
    echo $ex->getMessage();
}
