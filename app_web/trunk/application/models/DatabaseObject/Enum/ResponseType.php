<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SavCo
 * Date: 5/19/13
 * Time: 11:39 AM
 * To change this template use File | Settings | File Templates.
 */
class DatabaseObject_Enum_ResponsetType extends DatabaseObject_Enum{
    protected static $constTable='enumrt_responsetypes';
    protected static $constId='id';
    protected static $constName='name';
    protected static $constPre='rt';

    public function __construct($db)
    {
        parent::__construct($db,DatabaseObject_Enum_ResponsetType::$constTable,DatabaseObject_Enum_ResponsetType::$constId);
        $this->add(DatabaseObject_Enum_ResponsetType::$constName);
        $this->add('tsCreated',time());
        $this->add('tsModified',null);
    }

    public static function GetEnums($db){
        $enums=array();
        $theIdField=DatabaseObject_Enum_ResponsetType::$constId;
        $theNameField=DatabaseObject_Enum_ResponsetType::$constName;
        $select=sprintf("SELECT DISTINCT %s,%s FROM %s",DatabaseObject_Enum_ResponsetType::$constId,DatabaseObject_Enum_ResponsetType::$constName,DatabaseObject_Enum_BetType::$constTable);
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        foreach ($rowset as $row){
            $enums[$row[$theNameField]]=(string)DatabaseObject_Enum_ResponsetType::$constPre.$row[$theIdField];
        }
        return $enums;
    }

}