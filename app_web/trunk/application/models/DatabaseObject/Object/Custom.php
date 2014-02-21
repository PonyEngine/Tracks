<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SavCo
 * Date: 6/11/13
 * Time: 10:15 AM
 * To change this template use File | Settings | File Templates.
 */

class DatabaseObject_Object_Custom extends DatabaseObject_Object{
    protected $_constTable='customobjects';

    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function load($id, $field = null)
    {
        //$x = fmod($id, 1);
        //Prepare by removing the first number
        $idSplit=explode('.',strval($id));

        parent::load((int)$idSplit[1],$field);
        return $this;
    }

    public function  getId(){
        $dbId=parent::getId();
        return sprintf('1.%d',$dbId);
    }
}