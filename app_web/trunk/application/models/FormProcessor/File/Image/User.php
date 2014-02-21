<?php
class FormProcessor_File_Image_User extends FormProcessor_File_Image
{
    public function __construct(DatabaseObject_User $user,$filePostName,$isInternalTmp=true)
    {
            $userImage= new DatabaseObject_File_Image_User($user);
        parent::__construct($userImage,$filePostName,$isInternalTmp);
    }
}
?>