<?php
class DatabaseObject_File_Image_User extends DatabaseObject_File_Image
{
    protected $_table='users_images';
    protected $_fileDir='users';

    public function __construct($owner)
    {
        $this->_owner=$owner;
        $this->_ownerIdField='owner_id';
        $this->_databaseTable="users_images";

        parent::__construct($owner->getDb(),$this->_fileDir);
    }

}