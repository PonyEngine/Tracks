<?
	class SavCo_FunctionsAdmin extends SavCo
    {
	public function getEventLog($config){
		//Consume File
		$fh= @fopen($config->logging->file->event,"r");
		
		if($fh){
			while(!feof($fh)){
				$buffer= fgets($fh);
				$eventLineArr=explode(':',$buffer);
				$eventLogArr[]=$eventLineArr;
				//Check and make sure it is locked or stop it from being continuos.
			}
			fclose($fh);
		}

		    //print_r($eventLogArr);
		    return eventLogArr;

        }

        public function preCacheTemplates($config){

        }

        public static function CreateToken($namespace = '') {
            static $guid = '';
            $uid = uniqid("", true);
            $data = $namespace;
            $data .= $_SERVER['REQUEST_TIME'];
            $data .= $_SERVER['HTTP_USER_AGENT'];
            //$data .= $_SERVER['LOCAL_ADDR'];
           // $data .= $_SERVER['LOCAL_PORT'];
            $data .= $_SERVER['REMOTE_ADDR'];
            $data .= $_SERVER['REMOTE_PORT'];
            $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
            $guid = '{' .
                substr($hash,  0,  8) .
                '-' .
                substr($hash,  8,  4) .
                '-' .
                substr($hash, 12,  4) .
                '-' .
                substr($hash, 16,  4) .
                '-' .
                substr($hash, 20, 12) .
                '}';
            return $guid;
        }

       public static function  dropAllTables(){
         $dropCmd="DROP TABLE `albums`, `albums_images`, `albums_profile`, `apicalls`, `apicalls_profile`, `artists`, `artists_profile`, `_cache_badwords`, `_cache_userlimits`, `enumaf_addressfield`, `enumat_addresstype`, `enumbr_branchprofilefields`, `enumbt_branchtype`, `enumgd_geodevice`, `enumg_genre`, `enumps_privacysetting`, `enumsp_spaceprofilefields`, `enumss_spacestatus`, `enumup_userprofilefields`, `enumut_usertype`, `spaces`, `spaces_images`, `spaces_locations`, `spaces_playlistitem`, `spaces_playlistitems_bump`, `spaces_profile`, `spaces_tags`, `tracks`, `tracks_audio`, `tracks_profile`, `users`, `users_images`, `users_interest_barcodes`, `users_interest_websites`, `users_locations`, `users_presences`, `users_profile`, `users_sessions`, `users_tags`, `_cities`, `_cityidstateid`, `_comingsoons`, `_states`, `_zipcodes`";
        }

    }