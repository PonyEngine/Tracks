<?php
    function smarty_function_geturl($params, $smarty)
    {
        $action     = isset($params['action']) ? $params['action'] : null;
        $controller = isset($params['controller']) ? $params['controller'] : null;

        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('url');

        $request = Zend_Controller_Front::getInstance()->getRequest();

        //$url  = rtrim($request->getBaseUrl(), '/') . '/';
		
		if ($action||$controller){
        	$url  = rtrim('http://'.$_SERVER['HTTP_HOST'], '/') ;
			$url .= $helper->simple($action, $controller);
		}else{
        	$url=$_SERVER['REQUEST_URI'];
		}
        return $url;
    }
?>