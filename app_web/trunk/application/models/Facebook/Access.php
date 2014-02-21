<?


class Facebook_Access extends Facebook
{
    public function getExtendedAccessToken(){

        try {
            // need to circumvent json_decode by calling _oauthRequest
            // directly, since response isn't JSON format.
            $access_token_response =
                $this->_oauthRequest(
                    $this->getUrl('graph', '/oauth/access_token'), array(
                        'client_id' => $this->getAppId(),
                        'client_secret' => $this->getAppSecret(),
                        'grant_type'=>'fb_exchange_token',
                        'fb_exchange_token'=>$this->getAccessToken()
                    )
                );
        } catch (FacebookApiException $e) {
            // most likely that user very recently revoked authorization.
            // In any event, we don't have an access token, so say so.
            return false;
        }

        if (empty($access_token_response)) {
            return false;
        }

        $response_params = array();
        parse_str($access_token_response, $response_params);
        if (!isset($response_params['access_token'])) {
            return false;
        }

        return $response_params['access_token'];
    }


    public function buildPostWithCampaign(DatabaseObject_UserCampaign $theCampaign,$config){
        if(!$theCampaign) return NULL;
        $theCampaignUser=new DatabaseObject_User($theCampaign->getDb());

        $string="";
        if($theCampaignUser->load($theCampaign->user_id)){
            $width=50;
            $height=50;
            $fileObject=$theCampaignUser->logoImage;

            //Place in procedure
            $picLink=$fileObject->fullpath_createThumbnail($width, $height,$config);

        }else{
            $string=sprintf('http://%s/images/%s',
                $config->webhost,
                "yes.png");
        }
        $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
        $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
        $picLink=str_replace($entities, $replacements, urlencode($string));
        //echo $picLink;

        $phpNativeCampaignObject = Zend_Json::decode($theCampaign->campaignJSON, Zend_Json::TYPE_OBJECT);
        $thePost = array(
            'message'       => $phpNativeCampaignObject->campaignIntro,
            'name'          => $phpNativeCampaignObject->campaignTitle,
            'caption'       => $phpNativeCampaignObject->campaignIntro,
            'link'          => "http://$config->webhost/*$theCampaign->campaignProfileName",
            'description'   => $phpNativeCampaignObject->campaignDescription,
            'picture'       => $picLink,
            'actions' => array(
                array(
                    'name' => 'Check It Out',
                    'link' => "http://$config->webhost/*$theCampaign->campaignProfileName"
                )
            )
        );
        return $thePost;
    }

    public function buildTempPostWithJSON($campJSON,$config){
        $phpNativeCampaignObject = Zend_Json::decode($campJSON, Zend_Json::TYPE_OBJECT);
        // $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
        //$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
        $htmlString=sprintf("http://%s/%s",$config->webhost,$phpNativeCampaignObject->campaignTmpImagPath);
        //$imgLink=str_replace($entities, $replacements, urlencode($htmlString));

        //We do not need to record the data on the test responses. Therefore, we will send link directly to user.

        $thePost = array(
            'message'       => $phpNativeCampaignObject->campaignIntro,
            'name'          => $phpNativeCampaignObject->campaignTitle,
            'caption'       => $phpNativeCampaignObject->campaignIntro,
            'link'          => $phpNativeCampaignObject->campaignURL,
            'description'   => $phpNativeCampaignObject->campaignDescription,
            'actions' => array(
                array(
                    'name' => 'Check It Out',
                    'link' => $phpNativeCampaignObject->campaignURL
                )
            )
        );
        if($htmlString)$thePost['picture']=$htmlString;
        return $thePost;
    }

}