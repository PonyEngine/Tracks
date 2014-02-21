<?
class DatabaseObject_Enum extends DatabaseObject{
    protected static $constTable='';
    protected static $constId='';
    protected static $constName='';
    protected static $constPre='';
    public function __construct($db)
    {
        parent::__construct($db,DatabaseObject_Enum::$constTable,DatabaseObject_Enum::$constId);
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

    }
}