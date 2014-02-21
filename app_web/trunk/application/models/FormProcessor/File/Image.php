<?php
class FormProcessor_File_Image extends FormProcessor_File
{
    protected $image;
    public function __construct($theFilePostName,$isInternalTmp=true)
    {
        parent::__construct($this->image,$theFilePostName,$isInternalTmp); //This file object is image could be audio,etc.
        //Add Specific Validators Here
        $this->_adapter->addValidator('ImageSize', false,array(
            'minwidth' => $this->image->imageMinwidth(),
            'maxwidth' => $this->image->imageMaxwidth(),
            'minheight' =>$this->image->imageMinheight(),
            'maxheight' =>$this->image->imageMaxheight()));
    }
}
?>