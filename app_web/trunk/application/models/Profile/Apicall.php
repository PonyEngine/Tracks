<?
class Profile_Apicall extends Profile
{
    //I will possibly add extra pieces like time capture
    //httplocation etc to see where info is coming from.
    public function __construct($db, $apicall_id= null)
    {
        parent::__construct($db,'apicalls_profile');

        if($apicall_id > 0){
            $this->setApicall_id($apicall_id);
        }

    }

    public function setApicall_id($apicall_id)
    {
        $filters= array('apicall_id'=>(int) $apicall_id);
        $this->_filters=$filters;
    }

}