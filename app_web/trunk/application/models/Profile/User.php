<?
		class Profile_User extends Profile
		{
			//I will posibly add extra pieces like time capture
			//httplocation etc to see where info is coming from.
			public function __construct($db, $user_id= null)
			{
				parent::__construct($db,'users_profile');
				
				if($user_id > 0){
					$this->setUser_id($user_id);
				}
				
			}
			
			public function setUser_id($user_id)
			{
				$filters= array('user_id'=>(int) $user_id);
				$this->_filters=$filters;
			}
			
	}