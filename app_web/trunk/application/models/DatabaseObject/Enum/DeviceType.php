<?
class DatabaseObject_Enum_DeviceType extends DatabaseObject_Enum{
    protected static $constTable='enumdt_devicetypes';
    protected static $constId='id';
    protected static $constName='name';
    protected static $constPre='dt';

    public function __construct($db)
    {
        parent::__construct($db,DatabaseObject_Enum_DeviceType::$constTable,DatabaseObject_Enum_DeviceType::$constId);
        $this->add(DatabaseObject_Enum_DeviceType::$constName);
        $this->add('tsCreated',time());
        $this->add('tsModified',null);
    }

    public static function GetEnums($db){
        $enums=array();
        $theIdField=DatabaseObject_Enum_DeviceType::$constId;
        $theNameField=DatabaseObject_Enum_DeviceType::$constName;
        $select=sprintf("SELECT DISTINCT %s,%s FROM %s",DatabaseObject_Enum_DeviceType::$constId,DatabaseObject_Enum_DeviceType::$constName,DatabaseObject_Enum_DeviceType::$constTable);
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        foreach ($rowset as $row){
            $enums[$row[$theNameField]]=(string)DatabaseObject_Enum_DeviceType::$constPre.$row[$theIdField];
        }
        return $enums;
    }

}