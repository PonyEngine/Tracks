<?php
    function smarty_function_distance_of_time($params, $smarty)
    {
        
        $fromUnixTime   = isset($params['fromUnixTime']) ? $params['fromUnixTime'] : null;		
		
	   	if ($fromUnixTime){
        	$wordDisplay  = SavCo_FunctionsGen::distanceOfTimeInWords($fromUnixTime, time(), true);
		}else{
        	$wordDisplay=''; //Logged this error
		}
		
       return $wordDisplay.' ago';
    }
?>