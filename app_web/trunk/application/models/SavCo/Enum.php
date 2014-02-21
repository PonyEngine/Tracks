}<?php
	class SavCo_Enum extends SavCo{

        public static function GetNameIdArr($theConstTable,$theConstType) {
            $userProfileFieldNameIDArr='';
            if (!$userProfileFieldNameIDArr){
                $userProfileFieldNameIDArr=SavCo_Enum::SetNameIdArr($theConstTable,$theConstType);
            }

            return $userProfileFieldNameIDArr;
        }

        public static function SetNameIdArr($theConstTable,$theConstType) {
            $db = Zend_Registry::get('db');
            $select ="SELECT * FROM $theConstTable";
            $stmt=$db->query($select);
            $rowset=$stmt->fetchAll();

            $userProfileFieldIDArr=array();
            $userProfileFieldNameArr=array();

            $constType=SavCo_Enum::$contType;
            foreach ($rowset as $row){
                array_push($userProfileFieldIDArr,$theConstType.$row['profileField_id']);
                array_push($userProfileFieldNameArr,$row['profileField_name']);
            }
            $userProfileFieldNameIDArr=array_combine($userProfileFieldNameArr,$userProfileFieldIDArr);
            //$cache->save($this->stateIDNameArr);
            return $userProfileFieldNameIDArr;
        }
 }