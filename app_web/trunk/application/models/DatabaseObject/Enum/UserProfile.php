<?
class DatabaseObject_Enum_UserProfile extends DatabaseObject_Enum{
    protected static $constTable='enumup_userprofilefields';
    protected static $constId='profileField_id';
    protected static $constName='profileField_name';
    protected static $constPre='up';

    public function __construct($db)
    {
        parent::__construct($db,DatabaseObject_Enum_UserProfile::$constTable,DatabaseObject_Enum_UserProfile::$constId);
        $this->add(DatabaseObject_Enum_UserProfile::$constName);
        $this->add('tsCreated',time());
        $this->add('tsModified',null);
    }

    protected function postLoad()
    {

        return true;
    }

    protected function postInsert(){
        return true;
    }

    protected function postUpdate()
    {
        return true;
    }

    protected function preDelete()
    {
        return true;
    }

    public static function GetEnums($db){
        $enums=array();
        $theIdField=DatabaseObject_Enum_UserProfile::$constId;
        $theNameField=DatabaseObject_Enum_UserProfile::$constName;
        $select=sprintf("SELECT DISTINCT %s,%s FROM %s",DatabaseObject_Enum_UserProfile::$constId,DatabaseObject_Enum_UserProfile::$constName,DatabaseObject_Enum_UserProfile::$constTable);
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        foreach ($rowset as $row){
            $enums[$row[$theNameField]]=(string)DatabaseObject_Enum_UserProfile::$constPre.$row[$theIdField];
        }
        return $enums;
    }

}