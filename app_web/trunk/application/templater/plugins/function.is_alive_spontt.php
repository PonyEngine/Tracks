<?php
    function smarty_function_is_alive_spontt($params, $smarty)
    { 
	    $fromUnixTime   = isset($params['fromUnixTime']) ? $params['fromUnixTime'] : null;		
		
	 
       return DatabaseObject_Spontt::isALiveSpontt($fromUnixTime);
    }
?>