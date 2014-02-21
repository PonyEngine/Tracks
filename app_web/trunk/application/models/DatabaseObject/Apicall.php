<?
class DatabaseObject_Apicall extends DatabaseObject{
    protected static $constTable='apicalls';
    protected static $constId='apicall_id';
    public $profile= null;

    public function __construct($db)
    {
        parent::__construct($db,DatabaseObject_Apicall::$constTable,DatabaseObject_Apicall::$constId);
        $this->add('apicall_name');
        $this->add('apicall_string',null);
        $this->add('apicall_status',null);
        $this->add('apicall_type',null);
        $this->add('apiresponse_type',null);
        $this->add('tsCreated',time());
        $this->add('tsModified',null);


        $this->profile= new Profile_Apicall($db);
        }

    protected function postLoad()
    {
        //Profile
        $this->profile->setApicall_id($this->getId());
        $this->profile->load();

        return true;
    }

    protected function postInsert(){
        $this->profile->setAlbum_id($this->getId());
        $this->profile->save(false);

        return true;
    }

    protected function postUpdate()
    {
        $this->profile->save(false);
        return true;
    }


    protected function preDelete()
    {
        $message='Deleting';
        SavCo_ConstantArr::getLogEvent()>notice($message,1);

        $this->profile->delete();

        return true;
    }

    public static function GetApicallsWithIds($ids=array()){
        //If Array is empty bring back all users
        $objectArr=array();
        $db=SavCo_ConstantArr::getDbase();
        $id=DatabaseObject_Apicall::$constId;

        $table=DatabaseObject_Apicall::$constTable;
        $select = "SELECT $id FROM $table  WHERE apicall_status=1 ";
        if(count($ids)>0){
            $comma_separated = implode(",", $ids);
            $select .=" AND $id IN ($comma_separated)";
        }else{

        }
        $select .="ORDER BY  apicall_status DESC ";

        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();
        foreach ($rowset as $row){
            $anId=$row[$id];
            $anObject= new DatabaseObject_Apicall($db);
            $anObject->load($anId);
            $objectArr[]=$anObject;
        }

        return $objectArr;
    }

}