<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SavCo
 * Date: 5/19/13
 * Time: 11:41 AM
 * To change this template use File | Settings | File Templates.
 */

class DatabaseObject_Enum_UserType extends DatabaseObject_Enum{
    protected static $constTable='enumot_usertypes';
    protected static $constId='id';
    protected static $constName='name';
    protected static $constPre='ut';

    public function __construct($db)
    {
        parent::__construct($db,DatabaseObject_Enum_UserType::$constTable,DatabaseObject_Enum_UserType::$constId);
        $this->add(DatabaseObject_Enum_UserType::$constName);
        $this->add('tsCreated',time());
        $this->add('tsModified',null);
    }

    public static function GetEnums($db){
        $enums=array();
        $theIdField=DatabaseObject_Enum_UserType::$constId;
        $theNameField=DatabaseObject_Enum_UserType::$constName;
        $select=sprintf("SELECT DISTINCT %s,%s FROM %s",DatabaseObject_Enum_UserType::$constId,DatabaseObject_Enum_UserType::$constName,DatabaseObject_Enum_UserType::$constTable);
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        foreach ($rowset as $row){
            $enums[$row[$theNameField]]=(string)DatabaseObject_Enum_UserType::$constPre.$row[$theIdField];
        }
        return $enums;
    }

}