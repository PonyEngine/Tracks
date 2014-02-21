<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SavCo
 * Date: 5/19/13
 * Time: 11:37 AM
 * To change this template use File | Settings | File Templates.
 */
class DatabaseObject_Enum_ObjectType extends DatabaseObject_Enum{
    protected static $constTable='enumbt_objecttypes';
    protected static $constId='id';
    protected static $constName='name';
    protected static $constPre='ot';

    public function __construct($db)
    {
        parent::__construct($db,DatabaseObject_Enum_ObjectType::$constTable,DatabaseObject_Enum_ObjectType::$constId);
        $this->add(DatabaseObject_Enum_ObjectType::$constName);
        $this->add('tsCreated',time());
        $this->add('tsModified',null);
    }

    public static function GetEnums($db){
        $enums=array();
        $theIdField=DatabaseObject_Enum_BetType::$constId;
        $theNameField=DatabaseObject_Enum_BetType::$constName;
        $select=sprintf("SELECT DISTINCT %s,%s FROM %s",DatabaseObject_Enum_ObjectType::$constId,DatabaseObject_Enum_ObjectType::$constName,DatabaseObject_Enum_ObjectType::$constTable);
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        foreach ($rowset as $row){
            $enums[$row[$theNameField]]=(string)DatabaseObject_Enum_ObjectType::$constPre.$row[$theIdField];
        }
        return $enums;
    }

}