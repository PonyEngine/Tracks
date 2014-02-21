<?php
    function smarty_function_distance_in_miles($params, $smarty)
    {
    	$lat1=isset($params['lat1']) ? $params['lat1'] : null;
		$lon1=isset($params['lon1']) ? $params['lon1'] : null;
		$lat2=isset($params['lat2']) ? $params['lat2'] : null;
		$lon2=isset($params['lon2']) ? $params['lon2'] : null;
		$miles = true;
        		
	   	if ($lat1!=null && $lon1!=null && $lat2!=null && $lon2!=null ){
        	$distance = SavCo_FunctionsGen::GetDistance($lat1, $lon1, $lat2, $lon2, $miles);
			$distance='approx '.$distance.' miles away';
		}else{
			$distance = "";
		}
		
		
       return $distance;
    }
?>