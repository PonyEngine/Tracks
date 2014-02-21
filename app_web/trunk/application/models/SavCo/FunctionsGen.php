<?
    class SavCo_FunctionsGen extends SavCo
    {
        public function __construct($db)
        {
            parent::__construct();
            $this->db = Zend_Registry::get('db2');
        }

        public static function is_decimal($val)
        {
            return is_numeric($val) && floor($val) != $val;
        }

        public static function TextBetweenTags($string, $tagname) {
            $matches='';
            $pattern = "/<$tagname ?.*>(.*)<\/$tagname>/";
            preg_match_all($pattern, $string, $matches);
            return $matches[1];
        }

        public static function Bubblesort($array=array(),$property=null){
                if (!$length = count($array)) {
                    return $array;
                }
                for ($outer = 0; $outer < $length; $outer++) {
                    for ($inner = 0; $inner < $length; $inner++) {
                        if ($array[$outer][$property] > $array[$inner][$property]) {
                            $tmp = $array[$outer];
                            $array[$outer] = $array[$inner];
                            $array[$inner] = $tmp;
                        }
                    }
                }
            return $array;
        }

        public static function SumOfArray($array){
            $total=0;
            foreach($array as $item){
               $total=$total+$item;
            }

            return $total;
        }


		public function escapeString($string){
			$string2 = $string;
			$string2 = str_replace("&", "&amp;", $string2);
			$string2 = str_replace("<", "&lt;", $string2);
			$string2 = str_replace(">", "&gt;", $string2);
			$string2 = str_replace("'", "&apos;", $string2);
			$string2 = str_replace("\"", "&quot;", $string2);

			return $string2;
		}

		public static function FormatPhone($ph){
			$onlynums = preg_replace('/[^0-9]/','',$ph);
        	if (strlen($onlynums)==10) { $areacode = substr($onlynums, 0,3);
        	      $exch = substr($onlynums,3,3);
         	     $num = substr($onlynums,6,4);
              $ph = "(".$areacode.") " . $exch . "-" . $num;        
          }
		  return $ph;
     	}

		function replaceHtml($text){
			return ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",
                     "<a href=\"\\0\">\\0</a>", $text);
		}
	
	
		static public function createRandomPassword($length=6) {
	    	$chars = "23456789abcdefghijklmnopqrstuvwxyz";
    		srand((double)microtime()*1000000);
    		$i = 0;
    		$pass = null ;

    		while ($i <= $length) {
       		 	$num = rand() % 35;
       		 	$tmp = substr($chars, $num, 1);
       		 	$pass = $pass . $tmp;
      	  		$i++;
    		}
    		return $pass;
		}
	
		function generateRandID(){  //looks like a possibility of duplicates
      		return md5(generateRandStr(16));
   		}
   
		function generateRandomness($length=6,$level=2){
   			list($usec, $sec) = explode(' ', microtime());
  			 srand((float) $sec + ((float) $usec * 100000));

   			$validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
  			$validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
   			$validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

   			$password  = "";
   			$counter   = 0;

   			while ($counter < $length) {
     			$actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);

     			// All character must be different
     			if (!strstr($password, $actChar)) {
       	 			$password .= $actChar;
        			$counter++;
     			}
   			}

   			return $password;
		}

  		/**
    	* generateRandStr - Generates a string made up of randomized
    	* letters (lower and upper case) and digits, the length
    	* is a specified parameter.
    	*/
   		function generateRandStr($length){
      		$randstr = "";
      		for($i=0; $i<$length; $i++){
        		$randnum = mt_rand(0,61);
				if($randnum < 10){
            		$randstr .= chr($randnum+48);
         		}else if($randnum < 36){
            		$randstr .= chr($randnum+55);
         		}else{
            		$randstr .= chr($randnum+61);
         		}
      		}
      		return $randstr;
   		}

	
		function encrypt($string, $key) {
			$result = '';
			for($i=0; $i<strlen($string); $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % strlen($key))-1, 1);
				$char = chr(ord($char)+ord($keychar));
				$result.=$char;
			}
			return base64_encode($result);
		}

	function decrypt($string, $key) {
			$result = '';
			$string = base64_decode($string);

			for($i=0; $i<strlen($string); $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % strlen($key))-1, 1);
				$char = chr(ord($char)-ord($keychar));
				$result.=$char;
			}
			return $result;
	} 

	function distanceOfTimeInWords($fromTime, $toTime = 0, $showLessThanAMinute = false) {
	    $distanceInSeconds = round(abs($toTime - $fromTime));
	    $distanceInMinutes = round($distanceInSeconds / 60);
       
        if ( $distanceInMinutes <= 1 ) {
            if ( !$showLessThanAMinute ) {
                return ($distanceInMinutes == 0) ? 'less than a minute' : '1 minute';
            } else {
                if ( $distanceInSeconds < 5 ) {
                    return 'less than 5 seconds';
                }
                if ( $distanceInSeconds < 10 ) {
                    return 'less than 10 seconds';
                }
                if ( $distanceInSeconds < 20 ) {
                    return 'less than 20 seconds';
                }
                if ( $distanceInSeconds < 40 ) {
                    return 'half a minute';
                }
                if ( $distanceInSeconds < 60 ) {
                    return 'less than a minute';
                }
               
                return '1 minute';
            }
        }
        if ( $distanceInMinutes < 45 ) {
            return $distanceInMinutes . ' minutes';
        }
        if ( $distanceInMinutes < 90 ) {
            return '1 hour';
        }
        if ( $distanceInMinutes < 1440 ) {
            return round(floatval($distanceInMinutes) / 60.0) . ' hours';
        }
        if ( $distanceInMinutes < 2880 ) {
            return '1 day';
        }
        if ( $distanceInMinutes < 43200 ) {
            return  round(floatval($distanceInMinutes) / 1440) . ' days';
        }
        if ( $distanceInMinutes < 86400 ) {
            return '1 month';
        }
        if ( $distanceInMinutes < 525600 ) {
            return round(floatval($distanceInMinutes) / 43200) . ' months';
        }
        if ( $distanceInMinutes < 1051199 ) {
            return '1 year';
        }
       
        return 'over ' . round(floatval($distanceInMinutes) / 525600) . ' years';
	}	

	function unique_filename()
  	{
		//NO extenions
  		// explode the IP of the remote client into four parts
  		$ipbits = explode(".", $_SERVER["REMOTE_ADDR"]);
  		// Get both seconds and microseconds parts of the time
  		list($usec, $sec) = explode(" ",microtime());

  		// Fudge the time we just got to create two 16 bit words
  		$usec = (integer) ($usec * 65536);
  		$sec = ((integer) $sec) & 0xFFFF;

  		// Fun bit - convert the remote client's IP into a 32 bit
  		// hex number then tag on the time.
  		// Result of this operation looks like this xxxxxxxx-xxxx-xxxx
  		$uid = sprintf("%08x-%04x-%04x",($ipbits[0] << 24)
         | ($ipbits[1] << 16)
         | ($ipbits[2] << 8)
         | $ipbits[3], $sec, $usec);

  		// Tag on the extension and return the filename
  		return $uid;
  } 
  
  
  	public static function GetDistance($lat1, $lng1, $lat2, $lng2, $miles = true)
	{	
		$pi80 = M_PI / 180;
		$lat1 *= $pi80;
		$lng1 *= $pi80;
		$lat2 *= $pi80;
		$lng2 *= $pi80;
 
		$r = 6372.797; // mean radius of Earth in km
		$dlat = $lat2 - $lat1;
		$dlng = $lng2 - $lng1;
		$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$km = $r * $c;
 
		$distance=($miles ? ($km * 0.621371192) : $km);
		return number_format($distance,2,'.','');
	}

        static public function RestGETURL($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $contents = curl_exec ($ch);
            curl_close ($ch);
            return $contents;
        }

        static public function RestPOSTURL($url,$fields){
            $ch = curl_init();
            $fieldsCount=1;
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);

            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
    static public function GetDirections($lat1,$lon1,$lat2,$lon2,$hasSensor=true){
	    //http://code.google.com/apis/maps/documentation/directions/
	    //origin (required) — The address or textual latitude/longitude value from which you wish to calculate directions. *
	    //destination (required) — The address or textual latitude/longitude value from which you wish to calculate directions.*
	    //mode (optional, defaults to driving) — specifies what mode of transport to use when calculating directions. Valid values are specified in Travel Modes.
	    // waypoints (optional) specifies an array of waypoints. Waypoints alter a route by routing it through the specified location(s). A waypoint is specified as either a latitude/longitude coordinate or as an address which will be geocoded. (For more information on waypoints, see Using Waypoints in Routes below.)
	    //alternatives (optional), if set to true, specifies that the Directions service may provide more than one route alternative in the response. Note that providing route alternatives may increase the response time from the server.
	    //avoid (optional) indicates that the calculated route(s) should avoid the indicated features. Currently, this parameter supports the following two arguments:
	          //o tolls indicates that the calculated route should avoid toll roads/bridges.
	          //o highways indicates that the calculated route should avoid highways.
	    //  units (optional) — specifies what unit system to use when displaying results. Valid values are specified in Unit Systems below.
	    //region (optional) — The region code, specified as a ccTLD ("top-level domain") two-character value. (For more information see Region Biasing below.)
	   //language (optional) — The language in which to return results. See the supported list of domain languages. Note that we often update supported languages so this list may not be exhaustive. If language is not supplied, the Directions service will attempt to use the native language of the browser wherever possible. See Region Biasing for more information.
	   //sensor (required) — Indicates whether or not the directions request comes from a device with a location sensor. This value must be either true or false.
    	$_googleURL="http://maps.googleapis.com/maps/api/directions/json";
		$_origin=preg_replace( '/\s+/', '',sprintf('%s,%s',$lat1,$lon1));
    	$_desitiniation=preg_replace( '/\s+/', '',sprintf('%s,%s',$lat2,$lon2));
    	$_hasSensor=$hasSensor?'true':'false';
    	
    	$url=sprintf("%s?origin=%s&destination=%s&sensor=%s",
    				$_googleURL,
    				$_origin,
    				$_desitiniation,
    				$_hasSensor);
    					
    	$_directions=SavCo_FunctionsGen::restGETURL($url);
    	return $_directions;
    }
     static public function Format_bytes($a_bytes)
        {
            if ($a_bytes < 1024) {
                return $a_bytes .' B';
            } elseif ($a_bytes < 1048576) {
                return round($a_bytes / 1024, 2) .' KiB';
            } elseif ($a_bytes < 1073741824) {
                return round($a_bytes / 1048576, 2) . ' MiB';
            } elseif ($a_bytes < 1099511627776) {
                return round($a_bytes / 1073741824, 2) . ' GiB';
            } elseif ($a_bytes < 1125899906842624) {
                return round($a_bytes / 1099511627776, 2) .' TiB';
            } elseif ($a_bytes < 1152921504606846976) {
                return round($a_bytes / 1125899906842624, 2) .' PiB';
            } elseif ($a_bytes < 1180591620717411303424) {
                return round($a_bytes / 1152921504606846976, 2) .' EiB';
            } elseif ($a_bytes < 1208925819614629174706176) {
                return round($a_bytes / 1180591620717411303424, 2) .' ZiB';
            } else {
                return round($a_bytes / 1208925819614629174706176, 2) .' YiB';
            }
        }


        public static function array2XML($arr,$root) {
            $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><{$root}></{$root}>");
            $f = create_function('$f,$c,$a','
        foreach($a as $v) {
            if(isset($v["@text"])) {
                $ch = $c->addChild($v["@tag"],$v["@text"]);
            } else {
                $ch = $c->addChild($v["@tag"]);
                if(isset($v["@items"])) {
                    $f($f,$ch,$v["@items"]);
                }
            }
            if(isset($v["@attr"])) {
                foreach($v["@attr"] as $attr => $val) {
                    $ch->addAttribute($attr,$val);
                }
            }
        }');
            $f($f,$xml,$arr);
            return $xml->asXML();
        }

        /* make a URL small */
        public static function GenerateBitlyUrl($url,$login,$appkey,$format = 'xml',$version = '2.0.1')
        {
            //create the URL
            $bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;

            //get the url
            //could also use cURL here
            $response = file_get_contents($bitly);

            //parse depending on desired format
            if(strtolower($format) == 'json')
            {
                $json = @json_decode($response,true);
                return $json['results'][$url]['shortUrl'];
            }
            else //xml
            {
                $xml = simplexml_load_string($response);
                return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
            }
        }

        public static function ListFiles($dir,$recurseLevel=0,$includeDir=true) {
            $recurseLevel++;
            //Customize to store directories as well
            //Needs to compare what is in the to
            if($dh = opendir($dir)) {

                $files = Array();
                $inner_files = Array();

                while($file = readdir($dh)) {
                    if($file != "." && $file != ".." && $file[0] != '.') {
                        if(is_dir($dir . "/" . $file)) {
                            if((strcmp($file,"data")==0 && $recurseLevel==1)||(strcmp($file,"tmp")==0 && $recurseLevel==2)){ //make certain it is not the data directory on the first level
                                  null; //don't process
                            }else{
                                if($includeDir){
                                    //Get Directory Info
                                    $thisFile=$dir . "/" . $file;
                                    $theFile['fullPath']=$thisFile;
                                    $theFile['name']=sprintf("%s/",basename($thisFile));
                                    $theFile['type']="1";
                                    $theFile['size']=SavCo_FunctionsGen::Format_bytes(filesize($thisFile));
                                    $theFile['time']=filemtime($thisFile);
                                    $theFile['level']=$recurseLevel;
                                    array_push($files, $theFile);
                                }

                                $inner_files = SavCo_FunctionsGen::ListFiles($dir . "/" . $file,$recurseLevel);
                                if(is_array($inner_files)) $files = array_merge($files, $inner_files);
                            }
                        } else {
                            //Push specifically the filename,fileType,fileSize,fileTime,
                            $thisFile=$dir . "/" . $file;
                            $theFile['fullPath']=$thisFile;
                            $theFile['name']=basename($thisFile);
                            $theFile['type']="0";
                            $theFile['size']=SavCo_FunctionsGen::Format_bytes(filesize($thisFile));
                            $theFile['time']=filemtime($thisFile);
                            $theFile['level']=$recurseLevel;
                            array_push($files, $theFile);
                        }
                    }
                }

                closedir($dh);
                return $files;
            }
        }

        function LineDiff($old, $new){
            $maxlen=0;

            foreach($old as $oindex => $ovalue){
                $nkeys = array_keys($new, $ovalue);
                foreach($nkeys as $nindex){
                    $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                        $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                    if($matrix[$oindex][$nindex] > $maxlen){
                        $maxlen = $matrix[$oindex][$nindex];
                        $omax = $oindex + 1 - $maxlen;
                        $nmax = $nindex + 1 - $maxlen;
                    }
                }
            }
            if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
            return array_merge(
                SavCo_FunctionsGen::LineDiff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
                array_slice($new, $nmax, $maxlen),
                SavCo_FunctionsGen::LineDiff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
        }

        public static function ZipPathsofFilesDB($dir,$dbName='',$pathOfZip){
            //Get Paths of Files
            $paths=array();
            $paths=SavCo_FunctionsGen::ListFiles($dir,0,false);

            //Pull Database Schema at the dir level

            //Add Database to the paths

            //Zip Files
            if(SavCo_FunctionsGen::CreateZip($paths,$pathOfZip,true)){
                //Delete the DB Schema that was created if it was created

            }
            return true;
        }



        /* creates a compressed zip file */
        public static function CreateZip($files = array(),$destination = '',$overwrite = false) {
            //if the zip file already exists and overwrite is false, return false
            if(file_exists($destination) && !$overwrite) { return false; }
            //vars
            $valid_files = array();
            //if files were passed in...
            if(is_array($files)) {
                //cycle through each file
                foreach($files as $file) {
                    //make sure the file exists
                    if(file_exists($file)) {
                        $valid_files[] = $file;
                    }
                }
            }
            //if we have good files...
            if(count($valid_files)) {
                //create the archive
                $zip = new ZipArchive();
                if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                    return false;
                }
                //add the files
                foreach($valid_files as $file) {
                    $zip->addFile($file,$file);
                }
                //debug
                //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

                //close the zip -- done!
                $zip->close();

                //check to make sure the file exists
                return file_exists($destination);
            }
            else
            {
                return false;
            }
        }

        public static function Hex2rgb($hex) {
            $hex = str_replace("#", "", $hex);

            if(strlen($hex) == 3) {
                $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                $b = hexdec(substr($hex,2,1).substr($hex,2,1));
            } else {
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
            }
            $rgb = array($r/255, $g/255, $b/255);
            //return implode(",", $rgb); // returns the rgb values separated by commas
            return $rgb; // returns an array with the rgb values
        }

        public static function  GetImageFromURL ($url){
            // $url = $_GET['url'];

            $url = str_replace("http:/","http://",$url);

            $allowed = array('jpg','gif','png');
            $pos = strrpos($url, ".");
            $str = substr($url,($pos + 1));

            $ch = curl_init();
            $timeout = 0;
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

// Getting binary data
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

            $image = curl_exec($ch);
            curl_close($ch);
// output to browser
            $im = @imagecreatefromstring($image);

            $tw = @imagesx($im);
            if(!$tw){
                // Font directory + font name
                $font = '../../fonts/Austrise.ttf';
                // Size of the font
                $fontSize = 18;
                // Height of the image
                $height = 32;
                // Width of the image
                $width = 250;
                // Text
                $str = 'Couldn\'t get image.';
                $img_handle = imagecreate ($width, $height) or die ("Cannot Create image");
                // Set the Background Color RGB
                $backColor = imagecolorallocate($img_handle, 255, 255, 255);
                // Set the Text Color RGB
                $txtColor = imagecolorallocate($img_handle, 20, 92, 137);
                $textbox = imagettfbbox($fontSize, 0, $font, $str) or die('Error in imagettfbbox function');
                $x = ($width - $textbox[4])/2;
                $y = ($height - $textbox[5])/2;
                imagettftext($img_handle, $fontSize, 0, $x, $y, $txtColor, $font , $str) or die('Error in imagettftext function');
                header('Content-Type: image/jpeg');
                imagejpeg($img_handle,NULL,100);
                imagedestroy($img_handle);
            }else{
                if($str == 'jpg' || $str == 'jpeg')
                    header("Content-type: image/jpeg");
                if($str == 'gif')
                    header("Content-type: image/gif");
                if($str == 'png')
                    header("Content-type: image/png");
                $th = imagesy($im);
                $thumbWidth = 200;
                if($tw <= $thumbWidth){
                    $thumbWidth = $tw;
                }
                $thumbHeight = $th * ($thumbWidth / $tw);
                $thumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight);
                if($str == 'gif'){
                    $colorTransparent = imagecolortransparent($im);
                    imagefill($thumbImg, 0, 0, $colorTransparent);
                    imagecolortransparent($thumbImg, $colorTransparent);
                }
                if($str == 'png'){
                    imagealphablending($thumbImg, false);
                    imagesavealpha($thumbImg,true);
                    $transparent = imagecolorallocatealpha($thumbImg, 255, 255, 255, 127);
                    imagefilledrectangle($thumbImg, 0, 0, $thumbWidth, $thumbHeight, $transparent);
                }
                imagecopyresampled($thumbImg, $im, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $tw, $th);


                if($str == 'jpg' || $str == 'jpeg'){
                    return  imagejpeg($thumbImg, NULL, 100);
                }
                if($str == 'gif'){
                    return imagegif($thumbImg);
                }
                if($str == 'png'){
                    imagealphablending($thumbImg,TRUE);
                    return imagepng($thumbImg, NULL, 9, PNG_ALL_FILTERS);
                }

                imagedestroy($thumbImg);
            }
        }

        public static function GetNameParts($fullname){
            $fullNameArr=array();
            $fullNameExpl=null;
            if($fullname){
                $fullNameExpl=explode(' ',$fullname);

                if($fullNameExpl[0])$fullNameArr['first']=$fullNameExpl[0];

                if(count($fullNameExpl)<2){
                    $fullNameArr['last']="";
                }

                if(count($fullNameExpl)==2){
                    if($fullNameExpl[1])$fullNameArr['last']=$fullNameExpl[1];
                }

                if(count($fullNameExpl)>2){
                    $lastnameInd=count($fullNameExpl)-1;
                    $fullNameArr['last']=$fullNameExpl[$lastnameInd];
                }
            }
            return $fullNameArr;
        }

        public static function  SetResponseCode($code=NULL){
            if (!function_exists('http_response_code')) {
                if ($code !== NULL) {
                    switch ($code) {
                        case 100: $text = 'Continue'; break;
                        case 101: $text = 'Switching Protocols'; break;
                        case 200: $text = 'OK'; break;
                        case 201: $text = 'Created'; break;
                        case 202: $text = 'Accepted'; break;
                        case 203: $text = 'Non-Authoritative Information'; break;
                        case 204: $text = 'No Content'; break;
                        case 205: $text = 'Reset Content'; break;
                        case 206: $text = 'Partial Content'; break;
                        case 300: $text = 'Multiple Choices'; break;
                        case 301: $text = 'Moved Permanently'; break;
                        case 302: $text = 'Moved Temporarily'; break;
                        case 303: $text = 'See Other'; break;
                        case 304: $text = 'Not Modified'; break;
                        case 305: $text = 'Use Proxy'; break;
                        case 400: $text = 'Bad Request'; break;
                        case 401: $text = 'Unauthorized'; break;
                        case 402: $text = 'Payment Required'; break;
                        case 403: $text = 'Forbidden'; break;
                        case 404: $text = 'Not Found'; break;
                        case 405: $text = 'Method Not Allowed'; break;
                        case 406: $text = 'Not Acceptable'; break;
                        case 407: $text = 'Proxy Authentication Required'; break;
                        case 408: $text = 'Request Time-out'; break;
                        case 409: $text = 'Conflict'; break;
                        case 410: $text = 'Gone'; break;
                        case 411: $text = 'Length Required'; break;
                        case 412: $text = 'Precondition Failed'; break;
                        case 413: $text = 'Request Entity Too Large'; break;
                        case 414: $text = 'Request-URI Too Large'; break;
                        case 415: $text = 'Unsupported Media Type'; break;
                        case 500: $text = 'Internal Server Error'; break;
                        case 501: $text = 'Not Implemented'; break;
                        case 502: $text = 'Bad Gateway'; break;
                        case 503: $text = 'Service Unavailable'; break;
                        case 504: $text = 'Gateway Time-out'; break;
                        case 505: $text = 'HTTP Version not supported'; break;
                        default:
                            exit('Unknown http status code "' . htmlentities($code) . '"');
                            break;
                    }

                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

                    header($protocol . ' ' . $code . ' ' . $text);

                    $GLOBALS['http_response_code'] = $code;

                } else {

                    $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

                }

                return $code;

            }
        }
    }